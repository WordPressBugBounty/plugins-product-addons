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
		$html .= $this->render_title_description_price_with_position( $price_info );
		$html .= $this->render_upload_section( $item, $price_info );
		$html .= $this->render_description_below_field();
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
						return in_array( $ext, array( 'jpg', 'jpeg', 'png' ), true );
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
		$html .= '<div class="">';
		$html .= '<div class="prad-block-upload-title prad-d-flex prad-item-center prad-gap-4 prad-w-full">';
		$html .= $this->render_upload_label( $block_id, $price_info, $accept_types );
		$html .= ' </div>';
		$html .= ' </div>';
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
		$drag_drop_text = $this->get_property( 'dragDropText', 'Click or drag and drop' );
		$html           = sprintf(
			'<label for="prad_block_upload_%s" class="prad-upload-container prad-drop-zone prad-border-none prad-bg-transparent prad-m-0 prad-w-full">',
			esc_attr( $block_id )
		);

		$html .= '<div class="prad-d-flex prad-item-center prad-gap-12">';

			// Upload icon
			$html         .= $this->render_upload_icon();
			$html         .= '<div class="prad-block-upload-text prad-block-upload-title">';
				$html     .= $this->render_file_input( $block_id, $price_info, $accept_types );
				$html     .= '<div class="prad-cursor-pointer">';
					$html .= esc_html( $drag_drop_text );
				$html     .= '</div>';
			$html         .= '</div>';

		$html .= '</div>';

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
		$upload_text = $this->get_property( 'uploadText', 'Upload' );
		return '<div class="prad-block-upload-icon prad-d-flex prad-item-center prad-gap-6">
		   <svg
				width="20"
				height="20"
				viewBox="0 0 20 20"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
			>
				<path
					d="M4.86323 8.44885C3.02865 8.8851 1.66406 10.5347 1.66406 12.5026C1.66406 14.8039 3.52948 16.6693 5.83073 16.6693C6.22531 16.6693 6.6074 16.6143 6.96948 16.5118M15.0203 8.44885C16.8549 8.8851 18.2191 10.5347 18.2191 12.5026C18.2191 14.8039 16.3536 16.6693 14.0524 16.6693C13.6578 16.6693 13.2757 16.6143 12.9141 16.5118M14.9974 8.33594C14.9974 5.57469 12.7586 3.33594 9.9974 3.33594C7.23615 3.33594 4.9974 5.57469 4.9974 8.33594M7.10781 11.6197L9.9974 8.72094L12.9691 11.6693M9.9974 15.8359C9.9974 15.8359 9.9974 11.9813 9.9974 9.77844"
					stroke="white"
					strokeWidth="1.04167"
					strokeLinecap="round"
					strokeLinejoin="round"
				/>
			</svg>
			<div>' . esc_html( $upload_text ) . '</div>
		</div>';
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
