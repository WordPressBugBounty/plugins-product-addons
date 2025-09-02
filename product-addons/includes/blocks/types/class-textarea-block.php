<?php
/**
 * Textarea Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Textarea Block Class
 */
class Textarea_Block extends Abstract_Block {

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'textarea';
	}

	/**
	 * Render the textarea block
	 *
	 * @return string
	 */
	public function render(): string {
		$options = $this->get_field_options();
		if ( empty( $options ) ) {
			return '';
		}

		$price_info  = $this->get_price_info( $options[0] );
		$css_classes = array(
			'prad-parent',
			'prad-block-textarea',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);

		$attributes = array_merge(
			$this->get_common_attributes(),
			array(
				'class'      => $this->build_css_classes( $css_classes ),
				'data-ptype' => $price_info['type'],
				'data-val'   => $price_info['price'],
			)
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_header( $price_info );
		$html .= $this->render_description();
		$html .= $this->render_textarea( $price_info );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render header section with title and price
	 *
	 * @param object $item Textarea item
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
			$html .= $this->render_title_section();
		}

		$html .= $this->render_price_html( $price_info, 'with_title' );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render textarea element
	 *
	 * @param array $price_info Price information
	 * @return string
	 */
	private function render_textarea( array $price_info ): string {
		$block_id    = $this->get_block_id();
		$placeholder = $this->get_property( 'placeholder', '' );
		$min         = $this->get_property( 'min', 0 );
		$max         = $this->get_property( 'max', 100 );
		$rows        = $this->get_property( 'step', 1 );
		$value       = $this->get_property( 'value', '' );

		$textarea_attributes = array(
			'class'       => 'prad-block-input prad-w-full',
			'id'          => $block_id . '-prad-textarea-field',
			'placeholder' => $placeholder,
			'minlength'   => $min,
			'maxlength'   => $max,
			'rows'        => $rows,
			'rows'        => $rows,
			'data-val'    => $price_info['price'],
		);

		return sprintf(
			'<textarea %s>%s</textarea>',
			$this->build_attributes( $textarea_attributes ),
			esc_textarea( $value )
		);
	}
}
