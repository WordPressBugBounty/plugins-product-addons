<?php
/**
 * Price Handler Trait
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for handling price-related functionality
 */
trait Price_Handler {

	/**
	 * Get formatted price information
	 *
	 * @param object $item Price item object
	 * @return array
	 */
	protected function get_price_info( $item ) {
		if ( ! $item || ! isset( $item['type'], $item['regular'], $item['sale'] ) ) {
			return array(
				'type'        => 'no_cost',
				'price'       => 0,
				'html'        => '',
				'raw_regular' => 0,
				'raw_sale'    => 0,
			);
		}

		return apply_filters(
			'prad_blocks_price_both_show',
			$item['type'],
			$item['regular'],
			$item['sale'],
			$this->product_id
		);
	}

	/**
	 * Should price be displayed with title
	 *
	 * @param array $price_info
	 * @return bool
	 */
	protected function should_show_price_with_title( array $price_info ): bool {
		return $this->get_property( 'pricePosition', 'with_title' ) === 'with_title' &&
				$price_info['type'] !== 'no_cost';
	}

	/**
	 * Should price be displayed beside input/field
	 *
	 * @param array $price_info
	 * @return bool
	 */
	protected function should_show_price_beside_field( array $price_info ): bool {
		return $this->get_property( 'pricePosition', 'with_title' ) !== 'with_title' &&
				$price_info['type'] !== 'no_cost';
	}

	/**
	 * Render price HTML
	 *
	 * @param array  $price_info
	 * @param string $position
	 * @return string
	 */
	protected function render_price_html( array $price_info, string $position = 'beside' ): string {
		if ( $price_info['type'] === 'no_cost' || empty( $price_info['html'] ) ) {
			return '';
		}

		$css_classes = array( 'prad-block-price', 'prad-text-upper' );

		if ( $position === 'with_title' ) {
			$css_classes[] = 'prad-price-with-title';
		} else {
			$css_classes[] = 'prad-price-beside-field';
		}

		return sprintf(
			'<div class="%s">%s</div>',
			$this->build_css_classes( $css_classes ),
			wp_kses( $price_info['html'], $this->allowed_html_tags )
		);
	}

	/**
	 * Format price for display
	 *
	 * @param float  $price
	 * @param string $currency
	 * @return string
	 */
	protected function format_price( float $price, string $currency = '' ): string {
		if ( function_exists( 'wc_price' ) ) {
			return wc_price( $price );
		}

		$currency = $currency ?: get_woocommerce_currency_symbol();
		return $currency . number_format( $price, 2 );
	}

	/**
	 * Calculate total price based on quantity or multiplier
	 *
	 * @param float     $base_price
	 * @param int|float $multiplier
	 * @return float
	 */
	protected function calculate_total_price( float $base_price, $multiplier = 1 ): float {
		return $base_price * (float) $multiplier;
	}

	/**
	 * Get price position setting
	 *
	 * @return string
	 */
	protected function get_price_position(): string {
		return $this->get_property( 'pricePosition', 'with_title' );
	}
}
