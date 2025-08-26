<?php //phpcs:ignore

namespace PRAD\Includes\Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * ShopCompatibilty class.
 */
class ShopCompatibilty {
	private $product_options_checked = array();

	public function __construct() {
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'handle_add_to_cart_text' ), 9999, 2 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'handle_add_to_cart_url' ), 9999, 2 );
		add_filter( 'woocommerce_product_supports', array( $this, 'handle_product_support' ), 9999, 3 );
		add_filter( 'woocommerce_product_duplicate', array( $this, 'on_product_duplicate' ), 9999, 2 );
	}

	public function on_product_duplicate( $duplicate, $product ) {
		delete_post_meta( $duplicate->get_id(), 'prad_product_assigned_meta_inc' );
		delete_post_meta( $duplicate->get_id(), 'prad_product_assigned_meta_exc' );
	}

	public function handle_product_support( $support, $feature, $product ) {
		if ( $feature === 'ajax_add_to_cart' && $this->product_has_options( $product ) ) {
			$support = false;
		}
		return $support;
	}

	public function handle_add_to_cart_text( $btn_text, $product ) {
		if ( $this->product_has_options( $product ) ) {
			$btn_text = __( 'Select Options', 'product-addons' );
		}
		return $btn_text;
	}
	public function handle_add_to_cart_url( $btn_url, $product ) {
		if ( $this->product_has_options( $product ) ) {
			$btn_url = $product->get_permalink();
		}
		return $btn_url;
	}

	public function product_has_options( $product ) {
		if ( in_array( $product->get_type(), array( 'grouped', 'external' ) ) ) {
			return false;
		}

		$product_id = $product->get_id();
		$is_applied = isset( $this->product_options_checked[ $product_id ] ) ?
			$this->product_options_checked[ $product_id ]
			:
			$this->is_any_vaild_option_available( $product_id );

		$this->product_options_checked[ $product_id ] = $is_applied;

		return $is_applied;
	}

	public function is_any_vaild_option_available( $product_id ) {

		$option_all = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
		$option_all = is_array( $option_all ) ? $option_all : array();

		$option_product = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_inc', true ) ), true );
		$option_product = is_array( $option_product ) ? $option_product : array();

		$option_exclude = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_exc', true ) ), true );
		$option_exclude = is_array( $option_exclude ) ? $option_exclude : array();

		$option_term = array();
		$taxonomies  = array( 'product_cat', 'product_tag', 'product_brand' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $product_id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $term->term_id, 'prad_term_assigned_meta_inc', true ) ), true );
					if ( is_array( $meta_inc ) ) {
						$option_term = array_unique( array_merge( $option_term, $meta_inc ) );
					}
				}
			}
		}

		$merged     = array_unique( array_merge( $option_all, $option_term, $option_product ) );
		$option_ids = array_diff( $merged, $option_exclude );

		if ( is_array( $option_ids ) && ! empty( $option_ids ) ) {
			foreach ( $option_ids as $k => $opt_id ) {
				$status = get_post_status( $opt_id );
				if ( 'publish' === $status ) {
					$content = get_post_meta( $opt_id, 'prad_addons_blocks', true );
					$content = wp_json_encode( $content );
					$content = json_decode( $content );

					if ( ! empty( $content ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	// required field check
	public function has_required( $blocksarray ) {
		try {
			foreach ( $blocksarray as $field ) {
				if (
					isset( $field->required ) &&
					$field->required === true
				) {
					return true;
				}
				if ( isset( $field->innerBlocks ) ) {	//phpcs:ignore
					$result = $this->has_required( $field->innerBlocks );	//phpcs:ignore
					if ( null !== $result ) {
						return $result;
					}
				}
			}
		} catch ( \Exception $e ) {
			return false;
		}
		return false;
	}
}
