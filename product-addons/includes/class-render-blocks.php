<?php //phpcs:ignore
/**
 * Render_Blocks Action.
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD;

defined( 'ABSPATH' ) || exit;

/**
 * Render_Blocks class.
 */
class Render_Blocks {


	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'before_add_to_cart_button' ), 100 );
		add_filter( 'woocommerce_product_get_gallery_image_ids', array( $this, 'prad_add_custom_gallery_image' ), 99, 2 );
	}

	public function prad_add_custom_gallery_image( $gallery_image_ids, $product ) {

		$published_options = $this->get_prad_option_ids( $product->get_id() );
		if ( empty( $published_options ) ) {
			return $gallery_image_ids;
		}

		$image_data = get_option( 'prad_product_image_update_data', array() );
		if ( empty( $image_data ) ) {
			return $gallery_image_ids;
		}

		$custom_image_id = array();
		foreach ( $image_data as $k => $ids ) {
			if ( in_array( $k, $published_options ) ) {
				$custom_image_id = array_merge( $custom_image_id, $ids );
			}
		}

		$gallery_image_ids = array_values( array_unique( array_merge( $gallery_image_ids, $custom_image_id ) ) );

		return $gallery_image_ids;
	}

	public function get_prad_option_ids( $product_id ) {
		$option_all = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
		$option_all = is_array( $option_all ) ? $option_all : array();

		$option_product = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_inc', true ) ), true );
		$option_product = is_array( $option_product ) ? $option_product : array();

		$option_exclude = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_exc', true ) ), true );
		$option_exclude = is_array( $option_exclude ) ? $option_exclude : array();

		$option_term = array();
		// Merge option IDs from product_cat, product_tag, and product_brand taxonomies.
		$taxonomies = array( 'product_cat', 'product_tag', 'product_brand' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $product_id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $term->term_id, 'prad_term_assigned_meta_inc', true ) ), true );
					if ( is_array( $meta_inc ) ) {
						$option_term = array_unique( array_merge( $option_term, $meta_inc ) );
					}
				}
			}
		}

		$merged     = array_unique( array_merge( $option_all, $option_term, $option_product ) );
		$option_ids = array_diff( $merged, $option_exclude );

		$option_data   = array();
		$published_ids = array();
		if ( is_array( $option_ids ) && ! empty( $option_ids ) ) {
			foreach ( $option_ids as $k => $opt_id ) {
				$status = get_post_status( $opt_id );
				if ( 'publish' === $status ) {
					$content = get_post_meta( $opt_id, 'prad_addons_blocks', true );
					$content = wp_json_encode( $content );
					$content = json_decode( $content );

					if ( ! empty( $content ) ) {
						product_addons()->render_addon_css( $opt_id );
						$option_data[ $opt_id ] = $content;
						$published_ids[]        = $opt_id;
					}
				}
				if ( ! $status ) {
					do_action( 'prad_delete_option_product_meta', $opt_id );
				}
			}
		}
		return $published_ids;
	}

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 *
	 * @param object $orginal_object Array of options.
	 * @param string $property Array of options.
	 * @param string $default_value Array of options.
	 *
	 * @return string||array
	 */
	public function get_property( $orginal_object, $property, $default_value = '' ) {
		return isset( $orginal_object->$property ) ? $orginal_object->$property : $default_value;
	}

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $option_data Array of blocks.
	 * @param string $productid blocks .
	 * @param string $output the blocks .
	 *
	 * @return string
	 */
	public function build_prad_blocks( $option_data, $productid, $output = '' ) {
		$pro_lock               = ! product_addons()->handle_all_pro_block();
		$prad_allowed_html_tags = apply_filters( 'get_prad_allowed_html_tags', array() );
		foreach ( $option_data as $single_option ) {
			$name      = $this->get_property( $single_option, 'type', '' );
			$file_path = product_addons()->handle_all_pro_block() && in_array( $name, array( 'switch', 'color_switch', 'img_switch', 'upload', 'button' ) ) ? PRAD_PRO_PATH . "frontend/blocks/$name.php" : PRAD_PATH . "includes/blocks/$name.php";
			$args      = array();

			ob_start();
			if ( 'section' === $name ) {
				$show_accordion = $this->get_property( $single_option, 'showAccordion', true );
				$hide           = $this->get_property( $single_option, 'hide', false );

				?>
					<div
						class="prad-parent prad-section-block prad-section-wrapper <?php echo esc_attr( $this->get_property( $single_option, 'class' ) ); ?>"
						id="prad-bid-<?php echo esc_attr( $this->get_property( $single_option, 'blockid' ) ); ?>"
						data-btype="section"
						data-bid="<?php echo esc_attr( $this->get_property( $single_option, 'blockid' ) ); ?>"
						data-sectionid="<?php echo esc_attr( $this->get_property( $single_option, 'sectionid' ) ); ?>"
						data-label="<?php echo esc_attr( $this->get_property( $single_option, 'label' ) ); ?>"
						data-enlogic="<?php echo esc_attr( $this->get_property( $single_option, 'en_logic' ) ? 'yes' : 'no' ); ?>"
						data-fieldconditions="<?php echo esc_attr( wp_json_encode( $this->get_property( $single_option, 'fieldConditions' ) ) ); ?>">
						<div class="prad-section-header prad-accordion-header prad-cursor-<?php echo esc_attr( $show_accordion ? 'pointer' : 'default' ); ?> prad-section-head-<?php echo esc_attr( ( $show_accordion || ! $hide ) ? 'active' : 'inactive' ); ?>">
							<?php if ( ! $hide ) : ?>
								<div class="prad-relative prad-w-fit prad-section-title">
									<div class="prad-block-title">
										<?php
										$section_label = $this->get_property( $single_option, 'label' );
										echo wp_kses( $section_label, $prad_allowed_html_tags );
										?>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $hide && $show_accordion ) : ?>
								<div>
								</div>
							<?php endif; ?>
							<?php if ( $show_accordion ) : ?>
								<div class="prad-section-accordion">
									<div
										data-active="active"
										class="prad-accordion-icon prad-active">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none">
											<path
												stroke="currentColor"
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="1.5"
												d="m1 1 6 6 6-6" />
										</svg>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div
							class="prad-section-body <?php echo esc_attr( ( $show_accordion ) ? 'prad-section-accordian' : '' ); ?> <?php echo esc_attr( ( $show_accordion || ! $hide ) ? 'prad-block-border-top' : '' ); ?> prad-active"
							style="max-height: 100%">
							<div class="prad-section-container">
								<?php
								echo product_addons()->get_wp_kses_content( $this->build_prad_blocks( $this->get_property( $single_option, 'innerBlocks' ), $productid, '' ) );
								?>
							</div>
						</div>
					</div>
				<?php
			} elseif ( file_exists( $file_path ) ) {
				$def_args = array(
					'productid'              => $productid,
					'btype'                  => $name,
					'class'                  => $this->get_property( $single_option, 'class' ),
					'blockid'                => $this->get_property( $single_option, 'blockid' ),
					'sectionid'              => $this->get_property( $single_option, 'sectionid' ),
					'hide'                   => $this->get_property( $single_option, 'hide' ),
					'description'            => $this->get_property( $single_option, 'description', '' ),
					'required'               => $this->get_property( $single_option, 'required' ),
					'label'                  => $this->get_property( $single_option, 'label' ),
					'_options'               => $this->get_property( $single_option, '_options', array() ),
					'en_logic'               => $this->get_property( $single_option, 'en_logic' ),
					'fieldConditions'        => $this->get_property( $single_option, 'fieldConditions', array() ),
					'prad_allowed_html_tags' => $prad_allowed_html_tags,
				);
				switch ( $name ) {
					case 'select':
						$args = array(
							'defval' => wp_json_encode( $this->get_property( $single_option, 'defval', null ) ),
						);
						break;
					case 'shortcode':
					case 'color_picker':
						$args = array(
							'value'         => $this->get_property( $single_option, 'value' ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_title' ),
							'defval'        => wp_json_encode( $this->get_property( $single_option, 'defaultColor', null ) ),
						);
						break;
					case 'switch':
						$args = array(
							'enableCount'   => $pro_lock ? false : $this->get_property( $single_option, 'enableCount' ),
							'min'           => $this->get_property( $single_option, 'min', 1 ),
							'max'           => $this->get_property( $single_option, 'max', 100 ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_distributed' ),
							'countData'     => $this->get_property( $single_option, 'countData' ),
							'defval'        => wp_json_encode( $this->get_property( $single_option, 'defval', null ) ),
						);
						break;
					case 'color_switch':
					case 'img_switch':
						$lists = $this->get_property( $single_option, '_options', array() );
						if ( $pro_lock && is_array( $lists ) && count( $lists ) > 3 ) {
							$lists = array_slice( $lists, 0, 3 );
						}
						$args = array(
							'layout'             => $this->get_property( $single_option, 'layout', '_default' ),
							'layoutVisibility'   => $this->get_property( $single_option, 'layoutVisibility', 'always_show' ),
							'multiple'           => $this->get_property( $single_option, 'multiple', false ),
							'enableCount'        => $pro_lock ? false : $this->get_property( $single_option, 'enableCount' ),
							'min'                => $this->get_property( $single_option, 'min', 1 ),
							'max'                => $this->get_property( $single_option, 'max', 100 ),
							'countData'          => $this->get_property( $single_option, 'countData' ),
							'defval'             => wp_json_encode( $this->get_property( $single_option, 'defval', null ) ),
							'_options'           => $lists,
							'minSelect'          => $this->get_property( $single_option, 'minSelect', '' ),
							'maxSelect'          => $this->get_property( $single_option, 'maxSelect', '' ),
							'updateProductImage' => $this->get_property( $single_option, 'updateProductImage', false ),
						);
						break;
					case 'radio':
					case 'checkbox':
						$args = array(
							'columns'       => $this->get_property( $single_option, 'columns' ),
							'enableCount'   => $pro_lock ? false : $this->get_property( $single_option, 'enableCount' ),
							'min'           => $this->get_property( $single_option, 'min', 1 ),
							'max'           => $this->get_property( $single_option, 'max', 100 ),
							'minSelect'     => $this->get_property( $single_option, 'minSelect', '' ),
							'maxSelect'     => $this->get_property( $single_option, 'maxSelect', '' ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_distributed' ),
							'countData'     => $this->get_property( $single_option, 'countData' ),
							'defval'        => wp_json_encode( $this->get_property( $single_option, 'defval', null ) ),
						);
						break;
					case 'button':
						$lists = $this->get_property( $single_option, '_options', array() );
						if ( $pro_lock && is_array( $lists ) && count( $lists ) > 3 ) {
							$lists = array_slice( $lists, 0, 3 );
						}
						$args = array(
							'multiple'  => $this->get_property( $single_option, 'multiple', false ),
							'vertical'  => $this->get_property( $single_option, 'vertical', false ),
							'defval'    => wp_json_encode( $this->get_property( $single_option, 'defval', null ) ),
							'_options'  => $lists,
							'minSelect' => $this->get_property( $single_option, 'minSelect', '' ),
							'maxSelect' => $this->get_property( $single_option, 'maxSelect', '' ),
						);
						break;
					case 'date':
					case 'time':
						$args = array(
							'format'        => $this->get_property( $single_option, 'dateFormat', '' ),
							'disable_dates' => wp_json_encode( $this->get_property( $single_option, 'disableDates', '[]' ) ),
							'disable_days'  => wp_json_encode( $this->get_property( $single_option, 'disableDays', '[]' ) ),
							'max_date'      => $this->get_property( $single_option, 'maxDate', '' ),
							'min_date'      => $this->get_property( $single_option, 'minDate', '' ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_title' ),
							'max_time'      => $this->get_property( $single_option, 'maxTime', '' ),
							'min_time'      => $this->get_property( $single_option, 'minTime', '' ),
						);
						break;
					case 'range':
						$args = array(
							'defval'    => $this->get_property( $single_option, 'value', null ),
							'multiline' => $this->get_property( $single_option, 'multiline', true ),
							'min'       => $this->get_property( $single_option, 'min', 0 ),
							'max'       => $this->get_property( $single_option, 'max', 100 ),
							'step'      => $this->get_property( $single_option, 'step', 1 ),
						);
						break;
					case 'upload':
						$allowed = $this->get_property( $single_option, 'allowedFileTypes', array() );
						if ( $pro_lock ) {
							$allowed = array_values(
								array_filter(
									$allowed,
									function ( $ext ) {
										return ( $ext === 'jpg' || $ext === 'png' );
									}
								)
							);
						}

						$args = array(
							'placeholder'        => $this->get_property( $single_option, 'placeholder' ),
							'allowed_file_types' => $allowed,
							'max_size'           => $this->get_property( $single_option, 'maxSize', '' ),
							'size_prefix'        => $this->get_property( $single_option, 'sizePrefix', '' ),
							'size_error'         => $this->get_property( $single_option, 'sizeError', '' ),
							'max_number'         => $this->get_property( $single_option, 'maxNumber', '' ),
							'number_prefix'      => $this->get_property( $single_option, 'numberPrefix', '' ),
							'number_error'       => $this->get_property( $single_option, 'numberError', '' ),
							'allowed_prefix'     => $this->get_property( $single_option, 'allowedPrefix', '' ),
						);
						break;
					case 'number':
					case 'url':
					case 'email':
					case 'textfield':
					case 'textarea':
						$args = array(
							'placeholder'   => $this->get_property( $single_option, 'placeholder' ),
							'value'         => $this->get_property( $single_option, 'value' ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_title' ),
							'min'           => $this->get_property( $single_option, 'min', 1 ),
							'max'           => $this->get_property( $single_option, 'max', 100 ),
							'step'          => $this->get_property( $single_option, 'step', 1 ),
						);
						break;
					case 'telephone':
						$args = array(
							'placeholder'   => $this->get_property( $single_option, 'placeholder' ),
							'value'         => $this->get_property( $single_option, 'value' ),
							'pricePosition' => $this->get_property( $single_option, 'pricePosition', 'with_title' ),
							'show_flag'     => $this->get_property( $single_option, 'showFlag', false ),
						);
						break;
					case 'heading':
						$args = array(
							'heading_text' => $this->get_property( $single_option, 'value' ),
							'heading_tag'  => $this->get_property( $single_option, 'tag' ),
						);
						break;
					case 'spacer':
						$args = array(
							'height' => $this->get_property( $single_option, 'height' ),
						);
						break;
					case 'separator':
						$args = array(
							'height' => $this->get_property( $single_option, 'height' ),
							'width'  => $this->get_property( $single_option, 'width' ),
						);
						break;
					case 'products':
						$args = array(
							'columns'           => $this->get_property( $single_option, 'columns' ),
							'enableCount'       => $pro_lock ? false : $this->get_property( $single_option, 'enableCount' ),
							'min'               => $this->get_property( $single_option, 'min', 1 ),
							'max'               => $this->get_property( $single_option, 'max', 100 ),
							'minSelect'         => $this->get_property( $single_option, 'minSelect', '' ),
							'maxSelect'         => $this->get_property( $single_option, 'maxSelect', '' ),
							'countData'         => $this->get_property( $single_option, 'countData' ),
							'layout'            => $this->get_property( $single_option, 'layout', '_default' ),
							'layout_visibility' => $this->get_property( $single_option, 'layoutVisibility', 'always_show' ),
							'multiple'          => $this->get_property( $single_option, 'multiple', false ),
							'block_type'        => $this->get_property( $single_option, 'blockType', '' ),
							'manual_products'   => $this->get_property( $single_option, 'manualProducts', array() ),
							'merge_variation'   => $this->get_property( $single_option, 'mergeVariation', false ),
						);
						break;
					case 'custom_formula':
						$formula_data = (object) $this->get_property( $single_option, 'formulaData', array() );
						$args         = array(
							'formula_data' => $formula_data,
						);
						break;
				}

				$args = wp_parse_args(
					$args,
					$def_args,
				);
				extract($args); // phpcs:ignore
				include $file_path;
			}
			$output .= ob_get_clean();
		}
		return $output;
	}

	/**
	 * Add custom template before Add to cart button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function before_add_to_cart_button() {
		global $product;
		$product_id                    = $product->get_id();
		$product_base_price            = apply_filters(
			'prad_single_product_page_price',
			$product_id
		);
		$product_base_price_percentage = apply_filters(
			'prad_percentage_based_price_raw',
			$product_id,
			'converts'
		);

		$variations            = array();
		$variations_percentage = array();
		if ( $product->is_type( 'variable' ) ) {
			$variation_ids = $product->get_children();

			foreach ( $variation_ids as $variation_id ) {
				$variations[ $variation_id ]            = apply_filters(
					'prad_single_product_page_price',
					$variation_id
				);
				$variations_percentage[ $variation_id ] = apply_filters(
					'prad_percentage_based_price_raw',
					$variation_id,
					'converts'
				);

			}
		}

		$option_all = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
		$option_all = is_array( $option_all ) ? $option_all : array();

		$option_product = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_inc', true ) ), true );
		$option_product = is_array( $option_product ) ? $option_product : array();

		$option_exclude = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_exc', true ) ), true );
		$option_exclude = is_array( $option_exclude ) ? $option_exclude : array();

		$option_term = array();
		// Merge option IDs from product_cat, product_tag, and product_brand taxonomies.
		$taxonomies = array( 'product_cat', 'product_tag', 'product_brand' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $product_id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $term->term_id, 'prad_term_assigned_meta_inc', true ) ), true );
					if ( is_array( $meta_inc ) ) {
						$option_term = array_unique( array_merge( $option_term, $meta_inc ) );
					}
				}
			}
		}

		$merged     = array_unique( array_merge( $option_all, $option_term, $option_product ) );
		$option_ids = array_diff( $merged, $option_exclude );
		sort( $option_ids );

		$option_data   = array();
		$published_ids = array();
		if ( is_array( $option_ids ) && ! empty( $option_ids ) ) {
			foreach ( $option_ids as $k => $opt_id ) {
				$status = get_post_status( $opt_id );
				if ( 'publish' === $status ) {
					$content = get_post_meta( $opt_id, 'prad_addons_blocks', true );
					$content = wp_json_encode( $content );
					$content = json_decode( $content );

					if ( ! empty( $content ) ) {
						product_addons()->render_addon_css( $opt_id, wp_doing_ajax() ? 'print' : '' );
						$option_data[ $opt_id ] = $content;
						$published_ids[]        = $opt_id;
					}
				}
				if ( ! $status ) {
					do_action( 'prad_delete_option_product_meta', $opt_id );
				}
			}
		}

		if ( ! empty( $option_data ) ) {
			do_action( 'prad_enqueue_block_css' );
			do_action( 'prad_enqueue_block_js' );
			if ( wp_doing_ajax() ) {
				do_action( 'prad_load_script_on_ajax' );
			}
			$prad_allowed_html_tags = apply_filters( 'get_prad_allowed_html_tags', array() );

			$prad_blocks      = '<div class="prad-addons-wrapper">';
				$prad_blocks .= '<span class="prad-field-none" id="prad_variations_list" data-variations="' . esc_attr( wp_json_encode( $variations ) ) . '"></span>';
				$prad_blocks .= '<span class="prad-field-none" id="prad_variations_list_percentage" data-variations="' . esc_attr( wp_json_encode( $variations_percentage ) ) . '"></span>';
				$prad_blocks .= '<span class="prad-field-none" id="prad_base_price">' . esc_html( $product_base_price ) . '</span>';
				$prad_blocks .= '<span class="prad-field-none" id="prad_base_price_percentage">' . esc_html( $product_base_price_percentage ) . '</span>';
				$prad_blocks .= '<input type="hidden" name="prad_selection" id="prad_selection" />';
				$prad_blocks .= '<input type="hidden" name="prad_products_selection" id="prad_products_selection" />';
				$prad_blocks .= '<input type="hidden" name="prad_option_published_ids" id="prad_option_published_ids" value="' . esc_html( wp_json_encode( $published_ids ) ) . '"/>';

			foreach ( $option_data as $k => $optn ) {
				$prad_blocks .= '<div  class="prad-blocks-container prad-relative" data-productid="' . esc_attr( $product_id ) . '" data-optionid="' . esc_attr( $k ) . '">';
				if ( current_user_can( apply_filters( 'prad_demo_capability_check', 'manage_options' ) ) ) {
					$prad_blocks .= '<a class="prad-absolute prad-fron-edit-addon prad-z-99" target="_blank" href="' . admin_url( 'admin.php?page=prad-dashboard#lists/' ) . $k . '">Edit Addon</a>';
				}
					$prad_blocks .= $this->build_prad_blocks( $optn, $product_id, '' );
				$prad_blocks     .= '</div>';
			}

				$prad_blocks         .= '<div class="prad-mb-32 prad-mt-48 prad-product-price-summary">';
					$prad_blocks     .= '<div>';
						$prad_blocks .= '<strong>' . __( 'Addons Price ', 'product-addons' ) . '&nbsp;&nbsp;:&nbsp;</strong>';
						$prad_blocks .= '<span id="prad_option_price">' . wc_price( 0 ) . '</span>';
					$prad_blocks     .= '</div>';
					$prad_blocks     .= '<div>';
						$prad_blocks .= '<strong>' . __( 'Total ', 'product-addons' ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;</strong>';
						$prad_blocks .= '<span id="prad_option_total_price">' . wc_price( $product_base_price ) . '</span>';
					$prad_blocks     .= '</div>';
				$prad_blocks         .= '</div>';
			$prad_blocks             .= '</div>';

			echo product_addons()->get_wp_kses_content( $prad_blocks );
		}
	}

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $blocksarray Array of blocks.
	 *
	 * @return array
	 */
	public function remove_style_property( $blocksarray ) {
		if ( is_object( $blocksarray ) ) {
			$blocksarray = clone $blocksarray; // Clone object to avoid modifying original.
			foreach ( $blocksarray as $key => $value ) {
				if ( '_style' === $key ) {
					unset( $blocksarray->$key );
				} else {
					$blocksarray->$key = $this->remove_style_property( $value );
				}
			}
		} elseif ( is_array( $blocksarray ) ) {
			foreach ( $blocksarray as $key => $value ) {
				$blocksarray[ $key ] = $this->remove_style_property( $value );
			}
		}
		return $blocksarray;
	}

	/**
	 * Swatch content.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $option_array Array of blocks.
	 * @param string $blockid Array of blocks.
	 *
	 * @return any
	 */
	public static function prad_render_block_content( $item, $index, $blockid, $price_obj, $enableCount, $min, $max, $allowed_html_tags, $variation_select_html = '' ) {
		ob_start();
		?>
			<div class="prad-d-flex prad-flex-column prad-item-center prad-gap-2 prad-text-center prad-mt-8 prad-block-content-wrapper prad-effect-container">
				<div>
					<div title="<?php echo wp_kses( $item->value, $allowed_html_tags ); ?>" class="prad-block-content prad-ellipsis-2">
						<?php echo wp_kses( $item->value, $allowed_html_tags ); ?>
					</div>
					<?php if ( $item->type != 'no_cost' ) { ?>
						<div class="prad-block-price prad-text-upper">
							<?php echo wp_kses( $price_obj['html'], $allowed_html_tags ); ?>
						</div>
					<?php } ?>
				</div>
				<?php
				if ( $variation_select_html ) :
					echo $variation_select_html;
				endif;
				?>
				<?php if ( $enableCount ) : ?>
					<input
						id="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
						name="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
						type="number"
						placeholder="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						value="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						min="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						class="prad-block-input prad-quantity-input switcher-count prad-input prad-w-full"
						data-counter="<?php echo esc_attr( $blockid . $index ); ?>-switcher-count" />
				<?php endif; ?>
			</div>
		<?php
		return ob_get_clean();
	}
}
