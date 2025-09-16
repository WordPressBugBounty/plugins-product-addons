<?php
/**
 * Telephone Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Telephone Block Class
 */
class Telephone_Block extends Abstract_Block {

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'telephone';
	}

	/**
	 * Render the telephone block
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
			'prad-block-telephone',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);
		$attributes  = array_merge(
			$this->get_common_attributes(),
			array(
				'class'      => $this->build_css_classes( $css_classes ),
				'data-ptype' => $price_info['type'],
				'data-val'   => $price_info['price'],
			)
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_title_section( $price_info );
		$html .= $this->render_description();
		$html .= $this->render_telephone_input( $price_info );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render telephone input section
	 *
	 * @param object $item Telephone item
	 * @param array  $price_info Price information
	 * @return string
	 */
	private function render_telephone_input( array $price_info ): string {
		$show_flag   = $this->get_property( 'showFlag', false );
		$placeholder = $this->get_property( 'placeholder', '' );
		$value       = $this->get_property( 'value', '' );
		$block_id    = $this->get_block_id();

		$html = '<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">';

		// Telephone container
		$tel_classes = array(
			'prad-tel-container',
			'prad-w-full',
			'prad-d-flex',
			'prad-item-center',
		);

		if ( $show_flag ) {
			$tel_classes[] = 'prad-tel-flag-active';
		}

		$html .= sprintf( '<div class="%s">', $this->build_css_classes( $tel_classes ) );

		// Flag selector
		if ( $show_flag ) {
			$html .= $this->render_flag_selector();
		}

		// Input wrapper
		$html .= '<div class="prad-tel-input-wrapper prad-d-flex prad-item-center prad-w-full">';

		if ( $show_flag ) {
			$html .= '<div class="prad-dial-code-show" data-selected="bd">+880</div>';
		}

		// Input field
		$input_attributes = array(
			'class'       => 'prad-w-full prad-block-input prad-input',
			'type'        => 'tel',
			'placeholder' => $placeholder,
			'id'          => $block_id . '-prad-telephone-field',
			'value'       => $value,
			'data-val'    => $price_info['price'],
		);

		$html .= sprintf( '<input %s />', $this->build_attributes( $input_attributes ) );
		$html .= '</div></div>';

		if ( $this->should_show_price_beside_field( $price_info ) ) {
			$html .= $this->render_price_html( $price_info, 'beside' );
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render flag selector
	 *
	 * @return string
	 */
	private function render_flag_selector(): string {
		$html = '<div class="prad-tel-country-wrapper prad-relative">';

		// Flag handler
		$html .= '<div class="prad-tel-flag-handler prad-d-flex prad-item-center prad-gap-8">';
		$html .= '<div class="prad-tel-flag prad-flag-selected"></div>';
		$html .= '<div class="prad-flag-arrow">';
		$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none">';
		$html .= '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6" />';
		$html .= '</svg></div></div>';

		// Country list container
		$html .= '<div class="prad-tel-country-list-container prad-absolute prad-bg-base2">';
		$html .= '<div class="prad-country-search">';
		$html .= sprintf(
			'<input type="text" class="prad-country-search-input" placeholder="%s" />',
			esc_attr__( 'Search', 'product-addons' )
		);
		$html .= '</div>';
		$html .= '<div class="prad-country-list prad-scrollbar"></div>';
		$html .= '</div></div>';

		return $html;
	}
}
