<?php
/**
 * Select Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Select Block Class
 */
class Select_Block extends Abstract_Block {

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'select';
	}

	/**
	 * Render the select block
	 *
	 * @return string
	 */
	public function render(): string {
		$options = $this->get_field_options();

		if ( empty( $options ) ) {
			return '';
		}

		$attributes = array_merge(
			$this->get_common_attributes(),
			$this->get_select_attributes()
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_title_section();
		$html .= $this->render_description();
		$html .= $this->render_select_container( $options );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get checkbox specific attributes
	 *
	 * @return array
	 */
	private function get_select_attributes(): array {
		$attributes          = array();
		$css_classes         = array(
			'prad-parent',
			'prad-block-select',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);
		$attributes['class'] = $this->build_css_classes( $css_classes );

		return $attributes;
	}


	/**
	 * Render select container with options
	 *
	 * @param array $options Select options
	 * @return string
	 */
	private function render_select_container( array $options ): string {
		$html  = '<div class="prad-custom-select prad-w-full">';
		$html .= $this->render_select_box();
		$html .= $this->render_options_list( $options );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render select box (trigger)
	 *
	 * @return string
	 */
	private function render_select_box(): string {
		$html  = '<div class="prad-select-box prad-block-input prad-block-content" readonly="readonly">';
		$html .= '<div class="prad-select-box-item">' . esc_html__( 'Select an option', 'product-addons' ) . '</div>';
		$html .= '<div class="prad-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none">';
		$html .= '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6"></path>';
		$html .= '</svg></div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render options list
	 *
	 * @param array $options Select options
	 * @return string
	 */
	private function render_options_list( array $options ): string {
		$html = '<div class="prad-select-options">';

		foreach ( $options as $index => $item ) {
			$price_info = $this->get_price_info( $item );

			$option_attributes = array(
				'class'      => 'prad-select-option',
				'data-value' => $price_info['price'],
				'data-label' => $item['value'],
				'data-index' => $index,
				'data-ptype' => $item['type'] ?? 'no_cost',
			);

			$html .= sprintf( '<div %s>', $this->build_attributes( $option_attributes ) );
			$html .= '<div class="prad-d-flex prad-item-center prad-gap-8">';

			// Option content
			$html .= '<div class="prad-block-content prad-d-flex prad-item-center">';
			$html .= $this->maybe_render_option_image( $item );
			$html .= sprintf(
				'<div class="prad-ellipsis-2" title="%1$s">%2$s</div>',
				esc_attr( $item['value'] ),
				wp_kses( $item['value'], $this->allowed_html_tags )
			);
			$html .= '</div>';

			// Price if not free
			if ( isset( $item['type'] ) && $item['type'] !== 'no_cost' ) {
				$html .= '<div class="prad-block-price prad-text-upper">';
				$html .= wp_kses( $price_info['html'], $this->allowed_html_tags );
				$html .= '</div>';
			}

			$html .= '</div></div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render option image if available
	 *
	 * @param object $item Option item
	 * @return string
	 */
	private function maybe_render_option_image( $item ): string {
		if ( ! isset( $item['img'] ) || ! $item['img'] || ! product_addons()->is_pro_feature_available() ) {
			return '';
		}

		return sprintf(
			'<img class="prad-block-item-img" src="%s" alt="Item" />',
			esc_url( $item['img'] )
		);
	}
}
