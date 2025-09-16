<?php
/**
 * Product Blocks Service
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Product Blocks Service Class
 */
class Product_Blocks_Service {

	/**
	 * Cache for blocks data
	 *
	 * @var array
	 */
	private array $blocks_cache = array();

	/**
	 * Cache for price data
	 *
	 * @var array
	 */
	private array $price_cache = array();

	/**
	 * Get blocks data for a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_blocks_data( int $product_id ): array {
		// Check cache first
		if ( isset( $this->blocks_cache[ $product_id ] ) ) {
			return $this->blocks_cache[ $product_id ];
		}

		$result = array(
			'blocks'        => array(),
			'published_ids' => array(),
			'total_addons'  => 0,
		);

		// Get option IDs for this product
		$option_ids = $this->get_product_option_ids( $product_id );

		if ( empty( $option_ids ) ) {
			return $result;
		}

		foreach ( $option_ids as $option_id ) {
			$status = get_post_status( $option_id );

			if ( $status === 'publish' ) {
				$blocks_content = $this->get_addon_blocks_content( $option_id );

				if ( ! empty( $blocks_content ) ) {
					// Render addon CSS if needed
					$this->maybe_render_addon_css( $option_id );

					$result['blocks'][ $option_id ] = $blocks_content;
					$result['published_ids'][]      = $option_id;
					++$result['total_addons'];
				}
			} elseif ( ! $status ) {
				// Clean up deleted options
				do_action( 'prad_delete_option_product_meta', $option_id );
			}
		}

		// Cache the result
		$this->blocks_cache[ $product_id ] = $result;

		return $result;
	}

	/**
	 * Get option IDs assigned to a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	private function get_product_option_ids( int $product_id ): array {
		// Get options assigned to all products
		$option_all = $this->get_json_option( 'prad_option_assign_all', array() );

		// Get options assigned directly to this product
		$option_product = $this->get_json_meta( $product_id, 'prad_product_assigned_meta_inc', array() );

		// Get options excluded from this product
		$option_exclude = $this->get_json_meta( $product_id, 'prad_product_assigned_meta_exc', array() );

		// Get options from product terms (categories, tags, brands)
		$option_terms = $this->get_product_term_options( $product_id );

		// Merge and filter
		$merged     = array_unique( array_merge( $option_all, $option_terms, $option_product ) );
		$option_ids = array_diff( $merged, $option_exclude );

		// Sort for consistency
		sort( $option_ids );

		return apply_filters( 'prad_product_option_ids', $option_ids, $product_id );
	}

	/**
	 * Get options from product taxonomy terms
	 *
	 * @param int $product_id
	 * @return array
	 */
	private function get_product_term_options( int $product_id ): array {
		$option_terms = array();
		$taxonomies   = array( 'product_cat', 'product_tag', 'product_brand' );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $product_id, $taxonomy );

			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_options = $this->get_json_term_meta( $term->term_id, 'prad_term_assigned_meta_inc', array() );

					if ( is_array( $term_options ) ) {
						$option_terms = array_unique( array_merge( $option_terms, $term_options ) );
					}
				}
			}
		}

		return $option_terms;
	}

	/**
	 * Get addon blocks content
	 *
	 * @param int $addon_id
	 * @return array
	 */
	private function get_addon_blocks_content( int $addon_id ): array {
		$content = get_post_meta( $addon_id, 'prad_addons_blocks', true );

		if ( empty( $content ) ) {
			return array();
		}

		// Ensure proper JSON encoding/decoding
		if ( is_string( $content ) ) {
			$content = json_decode( $content, true );
		}

		if ( ! is_array( $content ) ) {
			return array();
		}

		return apply_filters( 'prad_addon_blocks_content', $content, $addon_id );
	}

	/**
	 * Maybe render addon CSS
	 *
	 * @param int $addon_id
	 */
	private function maybe_render_addon_css( int $addon_id ): void {
		$print_styles = wp_doing_ajax() ? 'print' : '';

		if ( function_exists( 'product_addons' ) ) {
			product_addons()->render_addon_css( $addon_id, $print_styles );
		}

		do_action( 'prad_render_addon_css', $addon_id, $print_styles );
	}

	/**
	 * Get product price data
	 *
	 * @param \WC_Product $product
	 * @return array
	 */
	public function get_product_price_data( \WC_Product $product ): array {
		$product_id = $product->get_id();

		// Check cache
		if ( isset( $this->price_cache[ $product_id ] ) ) {
			return $this->price_cache[ $product_id ];
		}

		$price_data = array(
			'base_price'            => $this->get_product_base_price( $product ),
			'base_price_percentage' => $this->get_product_base_price_percentage( $product ),
			'variations'            => array(),
			'variations_percentage' => array(),
		);

		// Get variation prices for variable products
		if ( $product->is_type( 'variable' ) ) {
			$variation_ids = $product->get_children();

			foreach ( $variation_ids as $variation_id ) {
				$price_data['variations'][ $variation_id ] = apply_filters(
					'prad_single_product_page_price',
					$variation_id
				);

				$price_data['variations_percentage'][ $variation_id ] = apply_filters(
					'prad_percentage_based_price_raw',
					$variation_id,
					'converts'
				);
			}
		}

		// Cache the result
		$this->price_cache[ $product_id ] = $price_data;

		return $price_data;
	}

	/**
	 * Get product base price
	 *
	 * @param \WC_Product $product
	 * @return float
	 */
	public function get_product_base_price( \WC_Product $product ): float {
		$price = apply_filters(
			'prad_single_product_page_price',
			$product->get_id()
		);

		return (float) $price;
	}

	/**
	 * Get product base price percentage
	 *
	 * @param \WC_Product $product
	 * @return float
	 */
	private function get_product_base_price_percentage( \WC_Product $product ): float {
		$price = apply_filters(
			'prad_percentage_based_price_raw',
			$product->get_id(),
			'converts'
		);

		return (float) $price;
	}

	/**
	 * Get blocks by product category
	 *
	 * @param int $category_id
	 * @return array
	 */
	public function get_blocks_by_category( int $category_id ): array {
		return $this->get_json_term_meta( $category_id, 'prad_term_assigned_meta_inc', array() );
	}

	/**
	 * Get blocks by product tag
	 *
	 * @param int $tag_id
	 * @return array
	 */
	public function get_blocks_by_tag( int $tag_id ): array {
		return $this->get_json_term_meta( $tag_id, 'prad_term_assigned_meta_inc', array() );
	}

	/**
	 * Check if product has any addons
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function product_has_addons( int $product_id ): bool {
		$blocks_data = $this->get_product_blocks_data( $product_id );
		return ! empty( $blocks_data['blocks'] );
	}

	/**
	 * Get addon statistics for a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_addon_stats( int $product_id ): array {
		$blocks_data = $this->get_product_blocks_data( $product_id );
		$stats       = array(
			'total_addons'       => $blocks_data['total_addons'],
			'block_types'        => array(),
			'total_blocks'       => 0,
			'required_blocks'    => 0,
			'conditional_blocks' => 0,
		);

		foreach ( $blocks_data['blocks'] as $addon_blocks ) {
			$this->analyze_blocks_stats( $addon_blocks, $stats );
		}

		return $stats;
	}

	/**
	 * Analyze blocks statistics
	 *
	 * @param array $blocks
	 * @param array &$stats
	 */
	private function analyze_blocks_stats( array $blocks, array &$stats ): void {
		foreach ( $blocks as $block ) {
			$type = $block['type'] ?? 'unknown';
			++$stats['total_blocks'];

			// Count by type
			if ( ! isset( $stats['block_types'][ $type ] ) ) {
				$stats['block_types'][ $type ] = 0;
			}
			++$stats['block_types'][ $type ];

			// Count required blocks
			if ( ! empty( $block['required'] ) ) {
				++$stats['required_blocks'];
			}

			// Count conditional blocks
			if ( ! empty( $block['en_logic'] ) ) {
				++$stats['conditional_blocks'];
			}

			// Recursively analyze inner blocks (for sections)
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->analyze_blocks_stats( $block['innerBlocks'], $stats );
			}
		}
	}

	/**
	 * Clear cache for a specific product
	 *
	 * @param int $product_id
	 */
	public function clear_product_cache( int $product_id ): void {
		unset( $this->blocks_cache[ $product_id ] );
		unset( $this->price_cache[ $product_id ] );

		do_action( 'prad_product_cache_cleared', $product_id );
	}

	/**
	 * Clear all cache
	 */
	public function clear_all_cache(): void {
		$this->blocks_cache = array();
		$this->price_cache  = array();

		do_action( 'prad_all_cache_cleared' );
	}

	/**
	 * Get and decode JSON option
	 *
	 * @param string $option_name
	 * @param array  $default
	 * @return array
	 */
	private function get_json_option( string $option_name, array $default = array() ): array {
		$value   = get_option( $option_name, '[]' );
		$decoded = json_decode( stripslashes( $value ), true );

		return is_array( $decoded ) ? $decoded : $default;
	}

	/**
	 * Get and decode JSON meta
	 *
	 * @param int    $post_id
	 * @param string $meta_key
	 * @param array  $default
	 * @return array
	 */
	private function get_json_meta( int $post_id, string $meta_key, array $default = array() ): array {
		$value = get_post_meta( $post_id, $meta_key, true );

		if ( empty( $value ) ) {
			return $default;
		}

		$decoded = json_decode( stripslashes( $value ), true );

		return is_array( $decoded ) ? $decoded : $default;
	}

	/**
	 * Get and decode JSON term meta
	 *
	 * @param int    $term_id
	 * @param string $meta_key
	 * @param array  $default
	 * @return array
	 */
	private function get_json_term_meta( int $term_id, string $meta_key, array $default = array() ): array {
		$value = get_term_meta( $term_id, $meta_key, true );

		if ( empty( $value ) ) {
			return $default;
		}

		$decoded = json_decode( stripslashes( $value ), true );

		return is_array( $decoded ) ? $decoded : $default;
	}

	/**
	 * Get products that use a specific addon
	 *
	 * @param int $addon_id
	 * @return array
	 */
	public function get_products_using_addon( int $addon_id ): array {
		global $wpdb;

		$products = array();

		// Check products with direct assignment
		$direct_products = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} 
             WHERE meta_key = 'prad_product_assigned_meta_inc' 
             AND meta_value LIKE %s",
				'%"' . $addon_id . '"%'
			)
		);

		$products = array_merge( $products, $direct_products );

		// Check if addon is in global assignment
		$global_addons = $this->get_json_option( 'prad_option_assign_all', array() );
		if ( in_array( $addon_id, $global_addons, true ) ) {
			// Get all products (this might be expensive for large stores)
			$all_products = $wpdb->get_col(
				"SELECT ID FROM {$wpdb->posts} 
                 WHERE post_type = 'product' AND post_status = 'publish'"
			);
			$products     = array_merge( $products, $all_products );
		}

		// Remove duplicates and excluded products
		$products = array_unique( $products );

		// Filter out products that explicitly exclude this addon
		$products = array_filter(
			$products,
			function ( $product_id ) use ( $addon_id ) {
				$excluded = $this->get_json_meta( $product_id, 'prad_product_assigned_meta_exc', array() );
				return ! in_array( $addon_id, $excluded, true );
			}
		);

		return array_values( $products );
	}

	/**
	 * Update product blocks cache when addon is updated
	 *
	 * @param int $addon_id
	 */
	public function invalidate_addon_cache( int $addon_id ): void {
		$affected_products = $this->get_products_using_addon( $addon_id );

		foreach ( $affected_products as $product_id ) {
			$this->clear_product_cache( $product_id );
		}

		do_action( 'prad_addon_cache_invalidated', $addon_id, $affected_products );
	}
}
