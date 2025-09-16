<?php
/**
 * Block Renderer
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Renderers;

use PRAD\Includes\Blocks\Factories\Block_Factory;

defined( 'ABSPATH' ) || exit;

/**
 * Main block renderer class
 */
class Block_Renderer {

	/**
	 * Render multiple blocks
	 *
	 * @param array $blocks_data Array of block configuration data
	 * @param int   $product_id Product ID
	 * @return string Rendered HTML
	 */
	public function render_blocks( array $blocks_data, int $product_id ): string {
		if ( empty( $blocks_data ) ) {
			return '';
		}

		$output          = '';
		$rendered_blocks = 0;

		foreach ( $blocks_data as $index => $block_data ) {
			$rendered_html = $this->render_single_block( $block_data, $product_id, $index );

			if ( ! empty( $rendered_html ) ) {
				$output .= $rendered_html;
				++$rendered_blocks;
			}
		}

		// Apply filters to final output
		$output = apply_filters( 'prad_rendered_blocks_output', $output, $blocks_data, $product_id );

		do_action( 'prad_blocks_rendered', $rendered_blocks, $product_id );

		return $output;
	}

	/**
	 * Render a single block
	 *
	 * @param array $block_data Block configuration data
	 * @param int   $product_id Product ID
	 * @param int   $index Block index in the collection
	 * @return string Rendered HTML
	 */
	public function render_single_block( array $block_data, int $product_id, int $index = 0 ): string {
		$type = $block_data['type'] ?? '';

		if ( empty( $type ) ) {
			do_action( 'prad_empty_block_type', $block_data, $product_id );
			return '';
		}

		// Create block instance
		$block = Block_Factory::create_block( $type, $block_data, $product_id );

		if ( ! $block ) {
			do_action( 'prad_block_creation_failed_render', $type, $block_data, $product_id );
			return '';
		}

		// // Check if block should be displayed
		// if ( ! $block->should_display() ) {
		// do_action( 'prad_block_hidden', $block, $block_data, $product_id );
		// return '';
		// }

		// // Validate block before rendering
		// if ( ! $block->validate() ) {
		// do_action( 'prad_block_validation_failed', $block, $block_data, $product_id );
		// return '';
		// }

		try {
			// Render the block
			$html = $block->render();

			// Apply filters to individual block output
			$html = apply_filters( 'prad_block_rendered', $html, $block, $product_id );
			$html = apply_filters( "prad_block_rendered_{$type}", $html, $block, $product_id );

			do_action( 'prad_after_block_render', $block, $html, $product_id );

			return $html;

		} catch ( \Exception $e ) {
			error_log(
				sprintf(
					'PRAD Block Render Error: Failed to render block type "%s". Error: %s',
					$type,
					$e->getMessage()
				)
			);

			do_action( 'prad_block_render_exception', $e, $block, $product_id );

			return '';
		}
	}

	/**
	 * Render blocks with caching
	 *
	 * @param array  $blocks_data
	 * @param int    $product_id
	 * @param string $cache_key Optional cache key
	 * @return string
	 */
	public function render_blocks_cached( array $blocks_data, int $product_id, string $cache_key = '' ): string {
		if ( empty( $cache_key ) ) {
			$cache_key = 'prad_blocks_' . $product_id . '_' . md5( serialize( $blocks_data ) );
		}

		// Try to get from cache first
		$cached_output = wp_cache_get( $cache_key, 'prad_rendered_blocks' );

		if ( $cached_output !== false ) {
			do_action( 'prad_blocks_cache_hit', $cache_key, $product_id );
			return $cached_output;
		}

		// Render blocks
		$output = $this->render_blocks( $blocks_data, $product_id );

		// Cache the output for 1 hour
		wp_cache_set( $cache_key, $output, 'prad_rendered_blocks', HOUR_IN_SECONDS );

		do_action( 'prad_blocks_cached', $cache_key, $output, $product_id );

		return $output;
	}

	/**
	 * Get rendering statistics
	 *
	 * @param array $blocks_data
	 * @param int   $product_id
	 * @return array
	 */
	public function get_render_stats( array $blocks_data, int $product_id ): array {
		$stats = array(
			'total_blocks'    => count( $blocks_data ),
			'rendered_blocks' => 0,
			'hidden_blocks'   => 0,
			'failed_blocks'   => 0,
			'block_types'     => array(),
		);

		foreach ( $blocks_data as $block_data ) {
			$type = $block_data['type'] ?? 'unknown';

			if ( ! isset( $stats['block_types'][ $type ] ) ) {
				$stats['block_types'][ $type ] = 0;
			}
			++$stats['block_types'][ $type ];

			$block = Block_Factory::create_block( $type, $block_data, $product_id );

			if ( ! $block ) {
				++$stats['failed_blocks'];
				continue;
			}

			if ( ! $block->should_display() ) {
				++$stats['hidden_blocks'];
				continue;
			}

			++$stats['rendered_blocks'];
		}

		return $stats;
	}
}
