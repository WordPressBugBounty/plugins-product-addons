<?php
/**
 * Upload Block Implementation
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Types;

use PRAD\Includes\Blocks\Abstracts\Abstract_Block;

defined( 'ABSPATH' ) || exit;

/**
 * Upload Block Class
 */
class Upload_Block extends Abstract_Block {

	private $allowed_file_types = array();

	/**
	 * Get block type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'upload';
	}

	/**
	 * Render the upload block
	 *
	 * @return string
	 */
	public function render(): string {
		$options = $this->get_field_options();

		if ( empty( $options ) ) {
			return '';
		}

		$item       = $options[0];
		$price_info = $this->get_price_info( $item );

		$attributes = array_merge(
			$this->get_common_attributes(),
			$this->get_upload_attributes( $price_info )
		);

		$html  = sprintf( '<div %s>', $this->build_attributes( $attributes ) );
		$html .= $this->render_header( $item, $price_info );
		$html .= $this->render_description();
		$html .= $this->render_upload_section( $item, $price_info );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get checkbox specific attributes
	 *
	 * @return array
	 */
	private function get_upload_attributes( $price_info ): array {
		$attributes  = array();
		$css_classes = array(
			'prad-parent',
			'prad-block-upload',
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);

		$allowed = $this->get_property( 'allowedFileTypes', array() );
		if ( ! product_addons()->is_pro_feature_available() ) {
			$allowed = array_values(
				array_filter(
					$allowed,
					function ( $ext ) {
						return ( $ext === 'jpg' || $ext === 'png' || 'jpeg' );
					}
				)
			);
		}

		$this->allowed_file_types = $allowed;

		$attributes['data-ptype']         = $price_info['type'];
		$attributes['data-max_size']      = $this->get_property( 'maxSize', '' );
		$attributes['data-size_prefix']   = $this->get_property( 'sizePrefix', '' );
		$attributes['data-size_error']    = $this->get_property( 'sizeError', '' );
		$attributes['data-number_prefix'] = $this->get_property( 'numberPrefix', '' );
		$attributes['data-max_number']    = $this->get_property( 'maxNumber', '' );
		$attributes['data-number_error']  = $this->get_property( 'numberError', '' );
		$attributes['data-allowed']       = wp_json_encode( $allowed );
		$attributes['data-val']           = $price_info['price'];

		$attributes['class'] = $this->build_css_classes( $css_classes );

		return $attributes;
	}

	/**
	 * Render header section with title and price
	 *
	 * @param object $item Upload item
	 * @param array  $price_info Price information
	 * @return string
	 */
	private function render_header( $item, array $price_info ): string {
		$hide = $this->get_property( 'hide', false );

		if ( $hide && $item['type'] === 'no_cost' ) {
			return '';
		}

		$html = '<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">';

		if ( ! $hide ) {
			$html .= $this->render_title_with_required();
		}

		if ( $item['type'] !== 'no_cost' ) {
			$html .= sprintf(
				'<div class="prad-block-price prad-text-upper">%s</div>',
				wp_kses( $price_info['html'], $this->allowed_html_tags )
			);
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render upload section
	 *
	 * @param object $item Upload item
	 * @param array  $price_info Price information
	 * @return string
	 */
	private function render_upload_section( $item, array $price_info ): string {
		$block_id      = $this->get_block_id();
		$allowed_types = $this->allowed_file_types;
		$accept_types  = $this->get_accept_types( $allowed_types );

		$html  = '<div class="prad-upload-wrapper">';
		$html .= $this->render_upload_label( $block_id, $price_info, $accept_types );
		$html .= '<div class="prad-upload-result"></div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render upload label and dropzone
	 *
	 * @param string $block_id Block ID
	 * @param array  $price_info Price information
	 * @param string $accept_types Accepted file types
	 * @return string
	 */
	private function render_upload_label( string $block_id, array $price_info, string $accept_types ): string {
		$html = sprintf(
			'<label for="prad_block_upload_%s" class="prad-drop-zone prad-upload-container prad-border-none prad-bg-transparent prad-m-0">',
			esc_attr( $block_id )
		);

		// Upload icon
		$html .= $this->render_upload_icon();

		// Upload title and input
		$html .= '<div class="prad-block-upload-title prad-d-flex prad-item-center prad-gap-4 prad-w-fit prad-center-horizontal prad-mb-12">';
		$html .= $this->render_file_input( $block_id, $price_info, $accept_types );
		$html .= '<div class="prad-block-upload-link prad-cursor-pointer prad-color-text-dark prad-text-underline">';
		$html .= esc_html__( 'Click to upload', 'product-addons' );
		$html .= '</div><div>' . esc_html__( 'or drag and drop', 'product-addons' ) . '</div></div>';

		// Upload info texts
		$html .= $this->render_upload_info();

		$html .= '</label>';

		return $html;
	}

	/**
	 * Render file input
	 *
	 * @param string $block_id Block ID
	 * @param array  $price_info Price information
	 * @param string $accept_types Accepted file types
	 * @return string
	 */
	private function render_file_input( string $block_id, array $price_info, string $accept_types ): string {
		$input_attributes = array(
			'id'       => 'prad_block_upload_' . $block_id,
			'class'    => 'prad-input-hidden prad-upload-input',
			'type'     => 'file',
			'hidden'   => 'hidden',
			'data-val' => $price_info['price'],
			'multiple' => 'multiple',
		);

		if ( $accept_types ) {
			$input_attributes['accept'] = $accept_types;
		}

		return sprintf( '<input %s />', $this->build_attributes( $input_attributes ) );
	}

	/**
	 * Render upload icon
	 *
	 * @return string
	 */
	private function render_upload_icon(): string {
		return '<div class="prad-block-upload-icon prad-lh-0 prad-mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none">
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="3"
                    d="M17.625 27.65C12.488 28.87 8.667 33.49 8.667 39c0 6.444 5.223 11.667 11.667 11.667 1.105 0 2.174-.154 3.188-.441M46.065 27.65C51.2 28.87 55.02 33.49 55.02 39c0 6.444-5.223 11.667-11.667 11.667-1.105 0-2.174-.154-3.187-.441M46 27.333c0-7.731-6.268-14-14-14-7.731 0-14 6.269-14 14m5.91 9.195L32 28.41l8.321 8.256m-8.32 11.666V31.54"
                />
            </svg>
        </div>';
	}

	/**
	 * Render upload information text
	 *
	 * @return string
	 */
	private function render_upload_info(): string {
		$html = '';

		$size_prefix    = $this->get_property( 'sizePrefix', '' );
		$number_prefix  = $this->get_property( 'numberPrefix', '' );
		$allowed_prefix = $this->get_property( 'allowedPrefix', '' );
		$max_size       = $this->get_property( 'maxSize', '' );
		$max_number     = $this->get_property( 'maxNumber', '' );
		$allowed_types  = $this->allowed_file_types;

		if ( $size_prefix && $max_size ) {
			$html .= sprintf(
				'<div class="prad-block-upload-content prad-font-12 prad-color-text-medium prad-mb-4">%s</div>',
				esc_html( str_replace( '[max_size]', $max_size . 'MB', $size_prefix ) )
			);
		}

		if ( $number_prefix && $max_number ) {
			$html .= sprintf(
				'<div class="prad-block-upload-content prad-font-12 prad-color-text-medium">%s</div>',
				esc_html( str_replace( '[max_files]', $max_number, $number_prefix ) )
			);
		}

		if ( $allowed_prefix && ! empty( $allowed_types ) ) {
			$html .= sprintf(
				'<div class="prad-block-upload-type prad-font-12 prad-color-text-body prad-mt-8"><span>%s</span></div>',
				esc_html( str_replace( '[allowed_types]', implode( ', ', $allowed_types ), $allowed_prefix ) )
			);
		}

		return $html;
	}

	/**
	 * Get accept types string for file input
	 *
	 * @param array $allowed_types Array of allowed file extensions
	 * @return string
	 */
	private function get_accept_types( array $allowed_types ): string {
		if ( empty( $allowed_types ) || ! is_array( $allowed_types ) ) {
			return '';
		}

		return implode( ',', array_map( fn( $ext ) => '.' . trim( $ext ), $allowed_types ) );
	}
}
