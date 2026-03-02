<?php //phpcs:ignore
/**
 * Main Render Blocks Controller
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks;

use PRAD\Includes\Blocks\Renderers\Block_Renderer;
use PRAD\Includes\Common\Formula\Array_Expression_Engine;
use PRAD\Includes\Services\Product_Blocks_Service;
use PRAD\Includes\Xpo;

defined( 'ABSPATH' ) || exit;

/**
 * Main Render Blocks Class
 */
class Render_Product_Fields {

	/**
	 * Block renderer instance
	 *
	 * @var Block_Renderer
	 */
	private Block_Renderer $renderer;

	/**
	 * Product blocks service
	 *
	 * @var Product_Blocks_Service
	 */
	private Product_Blocks_Service $blocks_service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->renderer       = new Block_Renderer();
		$this->blocks_service = new Product_Blocks_Service();

		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks(): void {
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'before_add_to_cart_button' ), 100 );
		add_filter( 'woocommerce_product_get_gallery_image_ids', array( $this, 'prad_add_custom_gallery_image' ), 99, 2 );
	}

	/**
	 * Render blocks before add to cart button
	 */
	public function before_add_to_cart_button(): void {
		global $product;

		// $this->run_the_formula_tester();

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$product_id  = $product->get_id();
		$blocks_data = $this->blocks_service->get_product_blocks_data( $product_id );

		if ( empty( $blocks_data['blocks'] ) ) {
			return;
		}

		// Enqueue necessary assets.
		// $this->assets->enqueue_frontend_assets().;
		do_action( 'prad_enqueue_block_css' );
		do_action( 'prad_enqueue_block_js' );
		if ( wp_doing_ajax() || wp_is_serving_rest_request() ) {
			do_action( 'prad_load_script_on_ajax' );
		}

		// Render the complete addon wrapper.
		echo $this->render_addon_wrapper( $product, $blocks_data );
	}

	/**
	 * Render complete addon wrapper.
	 *
	 * @param WC_Product $product Product object.
	 * @param array      $blocks_data Blocks data array.
	 * @return string HTML output.
	 */
	private function render_addon_wrapper( $product, array $blocks_data ): string {
		$product_id = $product->get_id();

		$html  = '<div class="prad-addons-wrapper prad-loading">';
		$html .= '<div class="prad-loader"></div>';

		// Hidden fields for price calculation.
		$html .= $this->render_hidden_fields( $product, $blocks_data );

		// Render blocks.
		foreach ( $blocks_data['blocks'] as $addon_id => $addon_blocks ) {
			$html .= sprintf(
				'<div class="prad-blocks-container prad-relative" data-productid="%s" data-optionid="%s">',
				esc_attr( $product_id ),
				esc_attr( $addon_id )
			);

			// Edit link for administrators.
			if ( current_user_can( Xpo::prad_old_view_permisson_handler() ) ) {
				$html .= sprintf(
					'<a class="prad-absolute prad-fron-edit-addon prad-z-99" target="_blank" href="%s">%s</a>',
					esc_url( admin_url( 'admin.php?page=prad-dashboard#lists/' . $addon_id ) ),
					esc_html__( 'Edit Addon', 'product-addons' )
				);
			}

			// Render addon blocks.
			$html .= $this->renderer->render_blocks( $addon_blocks, $product_id );

			$html .= '</div>';
		}

		// Price summary.
		$html .= $this->render_price_summary( $product );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render hidden fields for JavaScript functionality.
	 *
	 * @param WC_Product $product Product object.
	 * @param array      $blocks_data Blocks data array.
	 * @return string HTML output.
	 */
	private function render_hidden_fields( $product, array $blocks_data ): string {
		$product_id = $product->get_id();

		// Get price data.
		$price_data = $this->blocks_service->get_product_price_data( $product );

		$html = '';

		if ( $product->has_attributes() ) {
			$attributes   = $product->get_attributes();
			$product_type = $product->get_type();

			if ( ! empty( $attributes ) ) {
				$data_attributes = array();

				foreach ( $attributes as $attribute ) {
					$attribute_name = $attribute->get_name();
					if ( $attribute->is_taxonomy() ) {
						$terms = wc_get_product_terms(
							$product->get_id(),
							$attribute_name,
							array( 'fields' => 'all' )
						);
						foreach ( $terms as $term ) {
							$data_attributes[ $attribute_name ][ $term->slug ] = (int) $term->term_id;
						}
					}
				}
				$html .= sprintf(
					'<span class="prad-field-none" id="prad-product-attributes" data-product-type="%s" data-attributes="%s"></span>',
					esc_attr( $product_type ),
					esc_attr( wp_json_encode( $data_attributes ) )
				);
			}
		}

		// Variations data for variable products.
		if ( ! empty( $price_data['variations'] ) ) {
			$html .= sprintf(
				'<span class="prad-field-none" id="prad_variations_list" data-variations="%s"></span>',
				esc_attr( wp_json_encode( $price_data['variations'] ) )
			);

			$html .= sprintf(
				'<span class="prad-field-none" id="prad_variations_list_percentage" data-variations="%s"></span>',
				esc_attr( wp_json_encode( $price_data['variations_percentage'] ) )
			);
		}

		// Base price data.
		$html .= sprintf(
			'<span class="prad-field-none" id="prad_base_price">%s</span>',
			esc_html( $price_data['base_price'] )
		);

		$html .= sprintf(
			'<span class="prad-field-none" id="prad_base_price_percentage">%s</span>',
			esc_html( $price_data['base_price_percentage'] )
		);

		// Hidden form fields.
		$html .= '<input type="hidden" name="prad_selection" id="prad_selection" />';
		$html .= '<input type="hidden" name="prad_products_selection" id="prad_products_selection" />';

		$product_dynamic_data = array(
			'product_weight' => $product->get_weight() ?? 0,
			'product_length' => $product->get_length() ?? 0,
			'product_width'  => $product->get_width() ?? 0,
			'product_height' => $product->get_height() ?? 0,
		);

		$html .= sprintf(
			'<input type="hidden" name="prad_product_shipping_dynamic" id="prad_product_shipping_dynamic" value="%s" />',
			esc_attr( wp_json_encode( $product_dynamic_data ) )
		);

		$html .= sprintf(
			'<input type="hidden" name="prad_option_published_ids" id="prad_option_published_ids" value="%s"/>',
			esc_attr( wp_json_encode( $blocks_data['published_ids'] ) )
		);

		return $html;
	}

	/**
	 * Render price summary section.
	 *
	 * @param WC_Product $product Product object.
	 * @return string HTML output.
	 */
	private function render_price_summary( $product ): string {
		$base_price                = $this->blocks_service->get_product_base_price( $product );
		$enable_addons_price       = Xpo::get_prad_settings_item( 'enableAddonsPriceText', true );
		$enable_addons_price_total = Xpo::get_prad_settings_item( 'enableAddonsPriceTotalText', true );
		if ( $enable_addons_price === false && $enable_addons_price_total === false ) {
			return '';
		}

		$addons_label = esc_html( Xpo::get_prad_settings_item( 'addonsPriceText', 'Addons Price' ) );
		$total_label  = esc_html( Xpo::get_prad_settings_item( 'totalPriceText', 'Total Price' ) );

		$addons_price_html = sprintf(
			'<div class="prad-price-row">
				<strong class="prad-label">%s:</strong>
				<span id="prad_option_price" class="prad-value">%s</span>
			</div>',
			$addons_label,
			wc_price( 0 )
		);

		$total_price_html = sprintf(
			'<div class="prad-price-row">
				<strong class="prad-label">%s:</strong>
				<span id="prad_option_total_price" class="prad-value">%s</span>
			</div>',
			$total_label,
			wc_price( $base_price )
		);

		if ( $enable_addons_price === false ) {
			$addons_price_html = '';
		}
		if ( $enable_addons_price_total === false ) {
			$total_price_html = '';
		}

		return sprintf(
			'<div class="prad-product-price-summary prad-mt-48">%s%s</div>',
			$addons_price_html,
			$total_price_html
		);
	}

	/**
	 * Get blocks for a specific product (public method for external access).
	 *
	 * @param int $product_id Product ID.
	 * @return array Blocks data array.
	 */
	public function get_product_blocks( int $product_id ): array {
		return $this->blocks_service->get_product_blocks_data( $product_id );
	}

	/**
	 * Render blocks programmatically (for use in other contexts).
	 *
	 * @param array $blocks_data Blocks data array.
	 * @param int   $product_id Product ID.
	 * @return string HTML output.
	 */
	public function render_blocks_html( array $blocks_data, int $product_id ): string {
		return $this->renderer->render_blocks( $blocks_data, $product_id );
	}

	/**
	 * Add custom gallery images for product blocks.
	 *
	 * @param array      $gallery_image_ids Gallery image IDs.
	 * @param WC_Product $product Product object.
	 * @return array Modified gallery image IDs.
	 */
	public function prad_add_custom_gallery_image( $gallery_image_ids, $product ) {
		if ( is_product() ) {
			$published_options = $this->blocks_service->get_product_blocks_data( $product->get_id() );
			if ( empty( $published_options['published_ids'] ) ) {
				return $gallery_image_ids;
			}

			$image_data = get_option( 'prad_product_image_update_data', array() );
			if ( empty( $image_data ) ) {
				return $gallery_image_ids;
			}

			$custom_image_id = array();
			foreach ( $image_data as $k => $ids ) {
				if ( in_array( $k, $published_options['published_ids'] ) ) {
					$custom_image_id = array_merge( $custom_image_id, $ids );
				}
			}

			$gallery_image_ids = array_values( array_unique( array_merge( $gallery_image_ids, $custom_image_id ) ) );

		}

		return $gallery_image_ids;
	}

	public function run_the_formula_tester() {
		$expression_sets = array(
			array(
				'expression' => 'if(1==1, 11, 122)',
				'dynamics'   => array(
					'product_quantity'                 => 3,
					'Label.selected-sum_formula_value' => 44,
				),
				'expected'   => 40,
			),
			array(
				'expression' => 'min(111, 32, 55, 123) + if([product_quantity] < 2, 20, ([product_quantity] * 15)) + [Label.selected-sum_formula_value]',
				'dynamics'   => array(
					'product_quantity'                 => 3,
					'Label.selected-sum_formula_value' => 44,
				),
				'expected'   => 121,
			),
			array(
				'expression' => 'if([product_price] >= 100, [product_price] * 0.1, 0)',
				'dynamics'   => array(
					'product_price' => 150,
				),
				'expected'   => 15,
			),
			array(
				'expression' => 'round(([product_weight] * [product_quantity]) / 2)',
				'dynamics'   => array(
					'product_weight'   => 1.6,
					'product_quantity' => 3,
				),
				'expected'   => 2,
			),
			array(
				'expression' => 'ceil([product_length] / [product_width])',
				'dynamics'   => array(
					'product_length' => 10,
					'product_width'  => 3,
				),
				'expected'   => 4,
			),
			array(
				'expression' => 'floor(pow([product_height], 2) / 10)',
				'dynamics'   => array(
					'product_height' => 7,
				),
				'expected'   => 4,
			),
			array(
				'expression' => 'abs([delta]) + max(1, 2, [x])',
				'dynamics'   => array(
					'delta' => -12.5,
					'x'     => 10,
				),
				'expected'   => 22.5,
			),
			array(
				'expression' => 'if(([a] > 0) & ([b] > 0), [a] + [b], 0)',
				'dynamics'   => array(
					'a' => 2,
					'b' => 5,
				),
				'expected'   => 7,
			),
			array(
				'expression' => 'if(([count] = 0) || ([Label.selected-any] = 0), 100, 0)',
				'dynamics'   => array(
					'count'              => 1,
					'Label.selected-any' => 0,
				),
				'expected'   => 100,
			),
			array(
				'expression' => 'min([v1], [v2], [v3]) + max([v1], [v2], [v3])',
				'dynamics'   => array(
					'v1' => 21.2,
					'v2' => 10,
					'v3' => 15,
				),
				'expected'   => 31.2,
			),
			array(
				'expression' => 'if([Label.selected-any] != 0, [Label.selected-count] * [unit], 0)',
				'dynamics'   => array(
					'Label.selected-any'   => 1,
					'Label.selected-count' => 4,
					'unit'                 => 7.5,
				),
				'expected'   => 30,
			),
			array(
				'expression' => 'round(([product_price] / [product_quantity]) + pow(2, 3))',
				'dynamics'   => array(
					'product_price'    => 99,
					'product_quantity' => 4,
				),
				'expected'   => 33,
			),
			array(
				'expression' => 'if([Label.options.opt_label.checked] = 1, [Label.options.opt_label.formula_value], 0)',
				'dynamics'   => array(
					'Label.options.opt_label.checked' => 1,
					'Label.options.opt_label.formula_value' => 12.3,
				),
				'expected'   => 12.3,
			),
			array(
				'expression' => 'max(0, ([product_price] - [discount]))',
				'dynamics'   => array(
					'product_price' => 80,
					'discount'      => 90,
				),
				'expected'   => 0,
			),
			array(
				'expression' => 'if(([product_width] >= 10) & ([product_length] >= 20), ceil(([product_width] * [product_length]) / 50), 1)',
				'dynamics'   => array(
					'product_width'  => 12,
					'product_length' => 25,
				),
				'expected'   => 6,
			),
			array(
				'expression' => 'if(([product_quantity] > 1) & ([Label.selected-sum_formula_value] > 0), round(min([product_price], 200) / [product_quantity]) + floor([Label.selected-sum_formula_value] / 10), 0)',
				'dynamics'   => array(
					'product_quantity'                 => 3,
					'product_price'                    => 250,
					'Label.selected-sum_formula_value' => 44,
				),
				'expected'   => 71,
			),
			array(
				'expression' => 'min(111, 32, 55, 123) + if([product_quantity] = 0, 21, ([product_quantity] * 15)) + [Label.selected-sum_formula_value]',
				'dynamics'   => array(
					'Label.options.opt_label.checked'  => 1,
					'product_price'                    => 100,
					'Label.selected-any'               => 0,
					'Label.selected-sum_formula_value' => 44,
					'product_quantity'                 => 3,
				),
				'expected'   => 97,
			),
		);

		foreach ( $expression_sets as $index => $set ) {

			$result = Array_Expression_Engine::evaluate_expression_safe(
				$set['expression'],
				$set['dynamics']
			);

			echo '<hr>';
			echo '<strong>Expression #' . esc_html( $index + 1 ) . '</strong><br>';
			echo '<code>' . esc_html( $set['expression'] ) . '</code><br>';
			echo '<pre>';
			echo '<strong>Result: </strong>' . print_r( $result, true ) . '    <strong>Expected: </strong>' . print_r( $set['expected'], true );
			echo '<br><strong>Matched: </strong>';
			echo print_r( $set['expected'] === $result ? 'Matching' : '<span style="background:red; color: white;"> Not Matching </span>', true );
			echo '</pre>';
		}
	}
}
