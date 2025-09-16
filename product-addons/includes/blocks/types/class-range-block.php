<?php
/**
 * Range Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Range Block Class
 */
class Range_Block extends Abstract_Block {

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'range';
	}

	/**
	 * Render the range block
	 *
	 * @return string
	 */
	public function render(): string {
		$options = $this->get_field_options();

		if ( empty( $options ) ) {
			return '';
		}

		$price_info = $this->get_price_info( $options[0] );

		$attributes = array_merge(
			$this->get_common_attributes(),
			$this->get_range_attributes( $price_info )
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_header( $price_info );
		$html .= $this->render_description();
		$html .= $this->render_range_inputs( $price_info );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get range specific attributes
	 *
	 * @return array
	 */
	private function get_range_attributes( $price_info ): array {
		$css_classes = array(
			'prad-parent',
			'prad-block-range',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);

		$attributes['class']       = $this->build_css_classes( $css_classes );
		$attributes['data-val']    = $price_info['price'];
		$attributes['data-ptype']  = $price_info['type'];
		$attributes['data-defval'] = $this->get_property( 'value', null );

		return $attributes;
	}

	/**
	 * Render header section with title and price
	 *
	 * @param object $item Upload item
	 * @param array  $price_info Price information
	 * @return string
	 */
	private function render_header( array $price_info ): string {
		$hide = $this->get_property( 'hide', false );

		if ( $hide && $price_info['type'] === 'no_cost' ) {
			return '';
		}

		$html = '<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">';

		if ( ! $hide ) {
			$html .= $this->render_title_with_required();
		}

		$html .= $this->render_price_html( $price_info, 'with_title' );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Renders the range input elements
	 *
	 * @param array $price_info Price information
	 * @return string
	 */
	private function render_range_inputs( array $price_info ): string {
		$min           = $this->get_property( 'min', 0 );
		$max           = $this->get_property( 'max', 100 );
		$step          = $this->get_property( 'step', 1 );
		$default_value = $this->get_property( 'value', '' );

		$base_attributes = array(
			'min'      => $min,
			'max'      => $max,
			'step'     => $step,
			'id'       => $this->get_block_id() . '-prad-range-field',
			'data-val' => $price_info['price'],
		);

		// Add value if set
		if ( $default_value !== '' ) {
			$base_attributes['value'] = $default_value;
		}

		$html = '<div class="prad-range-input-container">';

		// Range input
		$range_attributes = array_merge(
			$base_attributes,
			array(
				'class' => 'prad-block-range-input prad-range-frontend',
				'type'  => 'range',
			)
		);

		// Number input
		$number_attributes = array_merge(
			$base_attributes,
			array(
				'class' => 'prad-block-input prad-input',
				'type'  => 'number',
			)
		);

		$html .= sprintf( '<input %s />', $this->build_attributes( $range_attributes ) );
		$html .= sprintf( '<input %s />', $this->build_attributes( $number_attributes ) );
		$html .= '</div>';

		return $html;
	}
}
