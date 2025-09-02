<?php //phpcs:ignore
/**
 * Class Hooks
 *
 * @package WowAddons
 */

namespace PRAD\Includes\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Hooks class.
 */
class Hooks {

	private $p_id = '';
	private $prad_product;
	private $tax_display_shop;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'prad_blocks_price_both_show', array( $this, 'handle_prad_blocks_price_both_show' ), 10, 4 );
		add_action( 'prad_delete_option_product_meta', array( $this, 'delete_option_product_meta_callback' ), 10, 1 );

		add_action( 'prad_enqueue_block_css', array( $this, 'enqueue_block_css_callback' ), 10 );
		add_action( 'prad_enqueue_block_js', array( $this, 'enqueue_block_js_callback' ), 10 );

		add_filter( 'get_prad_allowed_html_tags', array( $this, 'prad_allowed_html_tags' ), 10, 1 );

		add_filter( 'prad_raw_tax_currency_compitable_price', array( $this, 'handle_prad_raw_tax_currency_compitable_price' ), 10, 1 );
		add_filter( 'prad_raw_tax_compitable_price', array( $this, 'prad_get_price_including_tax' ), 10, 1 );

		add_action( 'prad_load_script_on_ajax', array( $this, 'handle_prad_load_script_on_ajax' ), 99 );
	}

	public function handle_prad_load_script_on_ajax() {

		// Load Needed JS
		$prad_option_front = $this->get_prad_option_front_data();

		echo '<script id="prad-option-front-data" type="text/javascript">';
			echo 'var prad_option_front = ' . wp_json_encode( $prad_option_front ) . ';';
		echo '</script>';

		$front_script = PRAD_URL . 'assets/js/wowactions-min.js';
		echo '<script src="' . esc_url( $front_script ) . '" id="prad-front-script-js-2"></script>';

		$data_script = PRAD_URL . 'assets/js/wowdate.js';
		echo '<script src="' . esc_url( $data_script ) . '" id="prad-front-date-js-2"></script>';

		$flag_script = PRAD_URL . 'assets/js/wowflag.js';
		echo '<script src="' . esc_url( $flag_script ) . '" id="prad-flag-script-js-2"></script>';

		// Load Needed CSS.
		$frontend_css = file_get_contents( PRAD_URL . 'assets/css/wowaddons-frontend.css' );
		echo '<style id="prad-frontend-css-inline">' . wp_strip_all_tags( $frontend_css ) . '</style>';

		$block_css = file_get_contents( PRAD_URL . 'assets/css/wowaddons-blocks.css' );
		echo '<style id="prad-blocks-css-inline">' . wp_strip_all_tags( $block_css ) . '</style>';

		$global_css = get_option( 'prad_global_style_css', '' );
		echo '<style id="prad-global-css-inline">' . wp_strip_all_tags( $global_css ) . '</style>';
	}

	/**
	 * Enqueues the front-end script for PRAD and localizes necessary data.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_block_js_callback() {
		wp_enqueue_script( 'prad-front-script', PRAD_URL . 'assets/js/wowactions-min.js', array( 'wp-api-fetch', 'jquery', 'wp-i18n' ), PRAD_VER, true );
		wp_enqueue_script( 'prad-date-script', PRAD_URL . 'assets/js/wowdate.js', array( 'jquery' ), PRAD_VER, true );
		wp_enqueue_script( 'prad-flag-script', PRAD_URL . 'assets/js/wowflag.js', array( 'jquery' ), PRAD_VER, true );
		wp_localize_script(
			'prad-front-script',
			'prad_option_front',
			$this->get_prad_option_front_data()
		);
		wp_set_script_translations( 'prad-front-script', 'product-addons', PRAD_PATH . 'languages/' );
	}
	/**
	 * Enqueues the front-end styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_block_css_callback() {
		wp_enqueue_style( 'prad-frontend-css', PRAD_URL . 'assets/css/wowaddons-frontend.css', array(), PRAD_VER );
		wp_enqueue_style( 'prad-blocks-css', PRAD_URL . 'assets/css/wowaddons-blocks.css', array(), PRAD_VER );

		$css = get_option( 'prad_global_style_css', '' );
		if ( $css ) {
			wp_register_style( 'prad-global-css', false ); // phpcs:ignore
			wp_enqueue_style( 'prad-global-css' );
			wp_add_inline_style( 'prad-global-css', $css );
		}
	}

	/**
	 * Render Sale price.
	 *
	 * @param float $sale       The sale price of the product.
	 *
	 * @return bool True if both prices should be shown, false otherwise.
	 */
	public function handle_prad_sale_price_return( $sale ) {
		if ( ! product_addons()->is_pro_feature_available() ) {
			$sale = null;
		}
		return $sale ? floatval( $sale ) : null;
	}

	/**
	 * Determines whether to display both regular and sale prices for PRAD blocks.
	 *
	 * @param string $type       The type of price display logic.
	 * @param float  $regular    The regular price of the product.
	 * @param float  $sale       The sale price of the product.
	 * @param int    $product_id The ID of the product.
	 *
	 * @return string price html.
	 */
	public function handle_prad_blocks_price_html_return( $type, $regular, $sale, $product_id ) {
		$regular = floatval( $regular );
		$sale    = $this->handle_prad_sale_price_return( $sale );
		$type    = $type ? $type : 'fixed';

		switch ( $type ) {
			case 'percentage':
				$price_product = apply_filters(
					'prad_percentage_based_price_raw',
					$product_id,
					'revert'
				);
				$regular_c     = $regular ? ( ( $price_product * $regular ) / 100 ) : null;
				$sale_c        = $sale ? ( ( $price_product * $sale ) / 100 ) : null;
				return $this->get_price_html( $regular_c, $sale_c, $product_id );
			case 'per_char':
				return $this->get_price_html( $regular, $sale, $product_id );
			case 'per_unit':
				return $this->get_price_html( $regular, $sale, $product_id );
			case 'no_cost':
				return '<span class="pricex prad-d-none">10</span>';
			default:
				return $this->get_price_html( $regular, $sale, $product_id );
		}
	}

	/**
	 * Determines whether to display both regular and sale prices for PRAD blocks.
	 *
	 * @param string $type       The type of price display logic.
	 * @param float  $regular    The regular price of the product.
	 * @param float  $sale       The sale price of the product.
	 * @param int    $product_id The ID of the product.
	 *
	 * @return bool True if both prices should be shown, false otherwise.
	 */
	public function handle_prad_blocks_price_both_show( $type, $regular, $sale, $product_id ) {
		$type = $type ? $type : 'fixed';
		return array(
			'type'  => $type,
			'price' => $this->handle_prad_blocks_price_return( $type, $regular, $sale, $product_id ),
			'html'  => $this->handle_prad_blocks_price_html_return( $type, $regular, $sale, $product_id ),
		);
	}

	/**
	 * Handles the price and sale calculations for PRAD blocks.
	 *
	 * @param string $type       The type of price calculation.
	 * @param float  $regular    The regular price of the product.
	 * @param float  $sale       The sale price of the product.
	 *
	 * @return float The calculated price based on the type.
	 */
	public function handle_prad_blocks_price_return( $type, $regular, $sale, $product_id ) {
		$sale      = $this->handle_prad_sale_price_return( $sale );
		$type      = $type ? $type : 'fixed';
		$to_return = 0;
		switch ( $type ) {
			case 'no_cost':
				$to_return = 0;
				break;
			default:
				$regular   = floatval( $regular );
				$sale      = $sale ? $sale : null;
				$to_return = $sale ?? $regular;
		}
		return 'percentage' === $type ? $to_return : $this->handle_prad_raw_tax_currency_compitable_price(
			array(
				'price'      => $to_return,
				'product_id' => $product_id,
				'source'     => 'product_page',
			)
		);
	}

	/**
	 * Generate HTML for Pricing
	 *
	 * @param float      $regular Regular price.
	 * @param float|null $sale    Sale price (optional).
	 *
	 * @return string HTML representation of the pricing.
	 */
	private function get_price_html( $regular, $sale, $product_id ) {
		$regular = $regular ? $this->handle_prad_raw_tax_currency_compitable_price(
			array(
				'price'      => $regular,
				'product_id' => $product_id,
				'source'     => 'product_page',
			)
		) : 0;
		$html    = '';
		// $html            = '<span class="pricex">' . $regular . $currency_symbol . '</span>';
		$html = '<span class="pricex">' . wc_price( $regular ) . '</span>';
		if ( $sale ) {
			$sale = $this->handle_prad_raw_tax_currency_compitable_price(
				array(
					'price'      => $sale,
					'product_id' => $product_id,
					'source'     => 'product_page',
				)
			);
			if ( $regular ) {
				$html = '<span class="pricex"><del>' . wc_price( $regular ) . '</del> <ins>' . wc_price( $sale ) . '</ins></span>';
			} else {
				$html = '<span class="pricex">' . wc_price( $sale ) . '</span>';
			}
		}

		return wp_kses(
			$html,
			$this->prad_allowed_html_tags()
		);
	}

	/**
	 * Set ALlowed Html
	 *
	 * @since 1.0.0
	 *
	 * @param array $extras Allowed htmls.
	 *
	 * @return array
	 */
	public function prad_allowed_html_tags( $extras = array() ) {
		$allowed = array(
			'del'      => array(),
			'ins'      => array(),
			'select'   => array(
				'multiple' => true,
				'data-*'   => true,
			),
			'option'   => array(
				'value'  => true,
				'data-*' => true,
			),
			'strong'   => array(),
			'b'        => array(),
			'input'    => array(
				'data-*'       => true,
				'type'         => true,
				'value'        => true,
				'placeholder'  => true,
				'name'         => true,
				'id'           => true,
				'min'          => true,
				'max'          => true,
				'format'       => true,
				'class'        => true,
				'step'         => true,
				'disabled'     => true,
				'readonly'     => true,
				'required'     => true,
				'maxlength'    => true,
				'minlength'    => true,
				'pattern'      => true,
				'autocomplete' => true,
				'accept'       => true,
			),
			'textarea' => array(
				'data-*'       => true,
				'type'         => true,
				'value'        => true,
				'placeholder'  => true,
				'name'         => true,
				'id'           => true,
				'min'          => true,
				'max'          => true,
				'rows'         => true,
				'format'       => true,
				'class'        => true,
				'disabled'     => true,
				'readonly'     => true,
				'required'     => true,
				'maxlength'    => true,
				'minlength'    => true,
				'pattern'      => true,
				'autocomplete' => true,
				'accept'       => true,
			),
			'svg'      => array(
				'xmlns'        => true,
				'width'        => true,
				'height'       => true,
				'viewbox'      => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'g'        => array(
				'fill'            => true,
				'stroke'          => true,
				'opacity'         => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'stroke-width'    => true,
				'clip-path'       => true,
			),
			'path'     => array(
				'd'               => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'stroke-width'    => true,
				'clip-rule'       => true,
			),
			'rect'     => array(
				'rx'           => true,
				'width'        => true,
				'height'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'defs'     => array(),
			'clipPath' => array(
				'id' => true,
			),
			'style'    => array(
				'id'     => true,
				'type'   => true,
				'media'  => true,
				'title'  => true,
				'scoped' => true,
				'data-*' => true,
			),
		);

		return array_merge( wp_kses_allowed_html( 'post' ), $allowed, $extras );
	}

	/**
	 * Delete Product Meta
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_id Option id.
	 *
	 * @return void
	 */
	public function delete_option_product_meta_callback( $option_id ) {
		if ( ! $option_id ) {
			return;
		}
		$assigned_data = json_decode( stripslashes( get_post_meta( $option_id, 'prad_base_assigned_data', true ) ), true );
		if ( $assigned_data ) {
			if ( 'all_product' === $assigned_data['aType'] ) {       /* Remove options for All Products */
				$option_settings = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );

				if ( is_array( $option_settings ) ) {
					if ( in_array( $option_id, $option_settings, false ) ) { //phpcs:ignore
						$option_settings = array_values( array_diff( $option_settings, array( $option_id ) ) );
					}
				} else {
					$option_settings = array();
				}
				update_option( 'prad_option_assign_all', wp_json_encode( $option_settings ) );
			} else {
				if ( is_array( $assigned_data['includes'] ) && count( $assigned_data['includes'] ) > 0 ) {
					foreach ( $assigned_data['includes'] as $key => $include ) {
						$meta_inc = array();
						if ( 'specific_product' === $assigned_data['aType'] ) {
							$meta_inc = json_decode( stripslashes( get_post_meta( $include, 'prad_product_assigned_meta_inc', true ) ), true );
						} elseif ( 'specific_category' === $assigned_data['aType'] || 'specific_tag' === $assigned_data['aType'] || 'specific_brand' === $assigned_data['aType'] ) {
							$meta_inc = json_decode( stripslashes( get_term_meta( $include, 'prad_term_assigned_meta_inc', true ) ), true );
						}
						if ( is_array( $meta_inc ) ) {
							if ( in_array( $option_id, $meta_inc, false ) ) { //phpcs:ignore
								$meta_inc = array_values( array_diff( $meta_inc, array( $option_id ) ) );
							}
						} else {
							$meta_inc = array();
						}
						if ( 'specific_product' === $assigned_data['aType'] ) {
							update_post_meta( $include, 'prad_product_assigned_meta_inc', wp_json_encode( $meta_inc ) );
						} elseif ( 'specific_category' === $assigned_data['aType'] || 'specific_tag' === $assigned_data['aType'] || 'specific_brand' === $assigned_data['aType'] ) {
							update_term_meta( $include, 'prad_term_assigned_meta_inc', wp_json_encode( $meta_inc ) );
						}
					}
				}
				/* Handle excludes */
				if ( is_array( $assigned_data['excludes'] ) && count( $assigned_data['excludes'] ) > 0 ) {
					foreach ( $assigned_data['excludes'] as $key => $exclude ) {
						$meta_exc = json_decode( stripslashes( get_post_meta( $exclude, 'prad_product_assigned_meta_exc', true ) ), true );
						if ( is_array( $meta_exc ) ) {
							if ( in_array( $option_id, $meta_exc, false ) ) { //phpcs:ignore
								$meta_exc = array_values( array_diff( $meta_exc, array( $option_id ) ) );
							}
						} else {
							$meta_exc = array();
						}
						update_post_meta( $exclude, 'prad_product_assigned_meta_exc', wp_json_encode( $meta_exc ) );
					}
				}
			}
		}
	}

	/**
	 * Delete Product Meta
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_id Option id.
	 *
	 * @return void
	 */
	public function handle_prad_raw_tax_currency_compitable_price( $args ) {
		$price = $this->prad_get_price_including_tax( $args );
		return $price ? product_addons()->get_currency_converted_price( $price ) : 0;
	}

	/**
	 * Delete Product Meta
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_id Option id.
	 *
	 * @return void
	 */
	public function prad_get_price_including_tax( $args ) {
		$price      = isset( $args['price'] ) ? $args['price'] : 0;
		$product_id = isset( $args['product_id'] ) ? $args['product_id'] : 0;
		$source     = isset( $args['source'] ) ? $args['source'] : '';

		if ( ! $product_id ) {
			return $price ? $price : 0;
		}

		if ( (int) $this->p_id !== (int) $product_id ) {
			$this->prad_product = wc_get_product( $product_id );
			$this->p_id         = $product_id;
		}

		if ( ! $this->prad_product ) {
			return $price ? $price : 0;
		}

		if ( 'product_page' === $source ) {
			if ( ! $this->tax_display_shop ) {
				$this->tax_display_shop = get_option( 'woocommerce_tax_display_shop' );
			}
			if ( 'excl' === $this->tax_display_shop ) {
				return $price ? $price : 0;
			}
		} elseif ( 'cart' === $source ) {
			return $price ? $price : 0;
		}

		$tax_applied_price = wc_get_price_including_tax(
			$this->prad_product,
			array(
				'qty'   => 1,
				'price' => $price,
			)
		);
		return $tax_applied_price ? $tax_applied_price : 0;
	}

	public function get_prad_option_front_data() {
		return array_merge(
			array(
				'url'            => PRAD_URL,
				'nonce'          => wp_create_nonce( 'prad-nonce' ),
				'isActive'       => product_addons()->is_lc_active(),
				'thousand_sep'   => get_option( 'woocommerce_price_thousand_sep', ',' ),
				'decimal_sep'    => get_option( 'woocommerce_price_decimal_sep', '.' ),
				'num_decimals'   => get_option( 'woocommerce_price_num_decimals', '2' ),
				'currency_pos'   => get_option( 'woocommerce_currency_pos', 'left' ),
				'currencySymbol' => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$',
			),
			product_addons()->get_currency_converted_data(),
		);
	}
}
