<?php
/**
 * Radio Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Radio Block Class
 */
class Radio_Block extends Abstract_Block {

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'radio';
	}

	/**
	 * Render the radio block
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
			$this->get_radio_attributes()
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_title_section();
		$html .= $this->render_description();
		$html .= $this->render_radio_group();
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get radio specific attributes
	 *
	 * @return array
	 */
	private function get_radio_attributes(): array {
		$css_classes = array(
			'prad-parent',
			'prad-block-radio',
			'prad-type-radio-input',
			'prad-switcher-count',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);
		return array(
			'class' => $this->build_css_classes( $css_classes ),
		);
	}
	/**
	 * Get column class based on columns count
	 *
	 * @return string
	 */
	private function get_column_class(): string {
		$columns = (int) $this->get_property( 'columns', 1 );
		return (string) min( max( $columns, 1 ), 3 );
	}

	/**
	 * Render radio group
	 *
	 * @return string
	 */
	private function render_radio_group(): string {
		$column_class = $this->get_column_class();

		$html = sprintf(
			'<div class="prad-input-container prad-column-%s">%s</div>',
			esc_attr( $column_class ),
			$this->render_radio_options()
		);

		return $html;
	}

	/**
	 * Render all radio options
	 *
	 * @return string
	 */
	private function render_radio_options(): string {
		$options = $this->get_field_options();
		$html    = '';

		foreach ( $options as $index => $item ) {
			$html .= $this->render_radio_item( $item, $index );
		}

		return $html;
	}

	/**
	 * Get radio item wrapper class
	 *
	 * @return string
	 */
	private function get_radio_item_wrapper_class(): string {
		$price_position = $this->get_property( 'pricePosition', '' );
		$justify        = $price_position === 'with_option' ? 'left' : 'between';

		return sprintf(
			'prad-radio-item-wrapper prad-d-flex prad-item-center prad-gap-8 prad-justify-%s',
			esc_attr( $justify )
		);
	}

	/**
	 * Get radio input attributes
	 *
	 * @param object  $item
	 * @param integer $index
	 * @param array   $price_info
	 * @return array
	 */
	private function get_radio_input_attributes( $item, int $index, array $price_info ): array {
		$enable_count = $this->get_property( 'enableCount', false );
		$blockid      = $this->get_block_id();

		return array(
			'class'        => 'prad-input-hidden',
			'type'         => 'radio',
			'id'           => $blockid . $index,
			'name'         => 'prad-radio-' . $blockid,
			'value'        => $price_info['price'],
			'data-ptype'   => $item['type'],
			'data-index'   => $index,
			'data-label'   => $item['value'],
			'data-count'   => $enable_count ? 'yes' : 'no',
			'data-counter' => $blockid . $index . '-switcher-count',
		);
	}

	/**
	 * Render single radio item
	 *
	 * @param object  $item
	 * @param integer $index
	 * @return string
	 */
	private function render_radio_item( $item, int $index ): string {
		$price_info    = $this->get_price_info( $item );
		$wrapper_class = $this->get_radio_item_wrapper_class();

		$html  = sprintf( '<div class="%s">', esc_attr( $wrapper_class ) );
		$html .= $this->render_radio_input_group( $item, $index, $price_info );

		if ( $item['type'] != 'no_cost' || $this->get_property( 'enableCount', false ) ) {
			$html .= $this->render_price_and_quantity( $item, $index, $price_info );
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render radio input with label
	 *
	 * @param object  $item
	 * @param integer $index
	 * @param array   $price_info
	 * @return string
	 */
	private function render_radio_input_group( $item, int $index, array $price_info ): string {
		$blockid      = $this->get_block_id();
		$allowed_tags = $this->allowed_html_tags;

		$html  = '<div class="prad-radio-item prad-d-flex prad-item-center prad-gap-10">';
		$html .= sprintf( '<input %s />', $this->build_attributes( $this->get_radio_input_attributes( $item, $index, $price_info ) ) );
		$html .= sprintf( '<label for="%s" class="prad-d-flex prad-item-center prad-gap-10">', esc_attr( $blockid . $index ) );
		$html .= '<div class="prad-radio-mark prad-realtive prad-br-round prad-selection-none"></div>';
		$html .= $this->render_radio_content( $item, $allowed_tags );
		$html .= '</label>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render radio content
	 *
	 * @param object $item
	 * @param array  $allowed_tags
	 * @return string
	 */
	private function render_radio_content( $item, array $allowed_tags ): string {
		$html = '<div class="prad-block-content prad-d-flex prad-item-center">';

		if ( isset( $item['img'] ) && $item['img'] && product_addons()->is_pro_feature_available() ) {
			$html .= sprintf(
				'<img class="prad-block-item-img" src="%s" alt="Item" />',
				esc_url( $item['img'] )
			);
		}

		$html .= sprintf(
			'<div title="%1$s" class="prad-ellipsis-2">%1$s</div>',
			wp_kses( $item['value'], $allowed_tags )
		);

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render price and quantity section
	 *
	 * @param object  $item
	 * @param integer $index
	 * @param array   $price_info
	 * @return string
	 */
	private function render_price_and_quantity( $item, int $index, array $price_info ): string {
		$enable_count = $this->get_property( 'enableCount', false );
		$columns      = $this->get_property( 'columns', 1 );
		$allowed_tags = $this->allowed_html_tags;

		$html = '<div class="prad-d-flex prad-item-center prad-gap-12">';

		if ( $item['type'] != 'no_cost' ) {
			$html .= sprintf(
				'<div class="prad-block-price prad-text-upper">%s</div>',
				wp_kses( $price_info['html'], $allowed_tags )
			);
		}

		if ( $enable_count && $columns == 1 ) {
			$html .= $this->render_quantity_input( $index );
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get quantity input attributes
	 *
	 * @param integer $index
	 * @return array
	 */
	private function get_quantity_input_attributes( int $index ): array {
		$blockid = $this->get_block_id();
		$min     = $this->get_property( 'min', 1 );
		$max     = $this->get_property( 'max', 100 );

		return array(
			'id'           => 'prad_quantity_' . $blockid . $index,
			'name'         => 'prad_quantity_' . $blockid . $index,
			'type'         => 'number',
			'placeholder'  => $min,
			'value'        => $min,
			'min'          => $min,
			'max'          => $max,
			'class'        => 'prad-block-input prad-quantity-input switcher-count prad-input',
			'data-counter' => $blockid . $index . '-switcher-count',
		);
	}

	/**
	 * Render quantity input
	 *
	 * @param integer $index
	 * @return string
	 */
	private function render_quantity_input( int $index ): string {
		return sprintf( '<input %s />', $this->build_attributes( $this->get_quantity_input_attributes( $index ) ) );
	}
}
