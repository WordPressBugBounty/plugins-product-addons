<?php //phpcs:ignore
/**
 * Main Render Blocks Controller
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks;

use PRAD\Includes\Blocks\Renderers\Block_Renderer;
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

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$product_id  = $product->get_id();
		$blocks_data = $this->blocks_service->get_product_blocks_data( $product_id );

		if ( empty( $blocks_data['blocks'] ) ) {
			return;
		}

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
}
