<?php
/**
 * Block Assets Service
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Block Assets Management Class
 */
class Block_Assets {

	/**
	 * Registered block types
	 *
	 * @var array
	 */
	private array $registered_blocks = array();

	/**
	 * Enqueued assets cache
	 *
	 * @var array
	 */
	private array $enqueued_assets = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks(): void {
		// add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'prad_enqueue_block_css', array( $this, 'enqueue_block_styles' ) );
		add_action( 'prad_enqueue_block_js', array( $this, 'enqueue_block_scripts' ) );
		add_action( 'prad_load_script_on_ajax', array( $this, 'load_scripts_for_ajax' ) );
	}

	/**
	 * Register all block assets
	 */
	public function register_assets(): void {
		// Register core block styles
		wp_register_style(
			'prad-blocks-core',
			PRAD_URL . 'assets/css/blocks-core.css',
			array(),
			PRAD_VER
		);

		// Register core block scripts
		wp_register_script(
			'prad-blocks-core',
			PRAD_URL . 'assets/js/blocks-core.js',
			array( 'jquery' ),
			PRAD_VER,
			true
		);

		// Register individual block assets
		$this->register_individual_block_assets();

		do_action( 'prad_assets_registered' );
	}

	/**
	 * Register assets for individual block types
	 */
	private function register_individual_block_assets(): void {
		$block_assets = array(
			'number'       => array(
				'css'  => 'blocks/number.css',
				'js'   => 'blocks/number.js',
				'deps' => array( 'prad-blocks-core' ),
			),
			'text'         => array(
				'css'  => 'blocks/text.css',
				'js'   => 'blocks/text.js',
				'deps' => array( 'prad-blocks-core' ),
			),
			'select'       => array(
				'css'  => 'blocks/select.css',
				'js'   => 'blocks/select.js',
				'deps' => array( 'prad-blocks-core', 'select2' ),
			),
			'section'      => array(
				'css'  => 'blocks/section.css',
				'js'   => 'blocks/section.js',
				'deps' => array( 'prad-blocks-core' ),
			),
			'checkbox'     => array(
				'css'  => 'blocks/checkbox.css',
				'js'   => 'blocks/checkbox.js',
				'deps' => array( 'prad-blocks-core' ),
			),
			'radio'        => array(
				'css'  => 'blocks/radio.css',
				'js'   => 'blocks/radio.js',
				'deps' => array( 'prad-blocks-core' ),
			),
			'date'         => array(
				'css'  => 'blocks/date.css',
				'js'   => 'blocks/date.js',
				'deps' => array( 'prad-blocks-core', 'jquery-ui-datepicker' ),
			),
			'upload'       => array(
				'css'  => 'blocks/upload.css',
				'js'   => 'blocks/upload.js',
				'deps' => array( 'prad-blocks-core', 'plupload-all' ),
			),
			'color_picker' => array(
				'css'  => 'blocks/color-picker.css',
				'js'   => 'blocks/color-picker.js',
				'deps' => array( 'prad-blocks-core', 'wp-color-picker' ),
			),
		);

		foreach ( $block_assets as $block_type => $assets ) {
			// Register CSS
			if ( ! empty( $assets['css'] ) ) {
				$css_file = PRAD_PATH . 'assets/css/' . $assets['css'];
				if ( file_exists( $css_file ) ) {
					wp_register_style(
						'prad-block-' . $block_type,
						PRAD_URL . 'assets/css/' . $assets['css'],
						array( 'prad-blocks-core' ),
						PRAD_VER
					);
				}
			}

			// Register JS
			if ( ! empty( $assets['js'] ) ) {
				$js_file = PRAD_PATH . 'assets/js/' . $assets['js'];
				if ( file_exists( $js_file ) ) {
					wp_register_script(
						'prad-block-' . $block_type,
						PRAD_URL . 'assets/js/' . $assets['js'],
						$assets['deps'] ?? array( 'prad-blocks-core' ),
						PRAD_VER,
						true
					);
				}
			}
		}

		// Allow plugins to register additional block assets
		do_action( 'prad_register_block_assets', $this );
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets(): void {
		// Always enqueue core assets
		wp_enqueue_style( 'prad-blocks-core' );
		wp_enqueue_script( 'prad-blocks-core' );

		// Localize core script
		wp_localize_script(
			'prad-blocks-core',
			'pradBlocks',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'prad_nonce' ),
				'currency' => array(
					'symbol'             => get_woocommerce_currency_symbol(),
					'position'           => get_option( 'woocommerce_currency_pos' ),
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
				),
				'i18n'     => array(
					'required_field'    => __( 'This field is required.', 'product-addons' ),
					'invalid_email'     => __( 'Please enter a valid email address.', 'product-addons' ),
					'invalid_url'       => __( 'Please enter a valid URL.', 'product-addons' ),
					'file_too_large'    => __( 'File size is too large.', 'product-addons' ),
					'invalid_file_type' => __( 'Invalid file type.', 'product-addons' ),
					'validation_failed' => __( 'Please fix the errors above.', 'product-addons' ),
				),
			)
		);
	}

	/**
	 * Enqueue assets for specific block types
	 *
	 * @param array $block_types
	 */
	public function enqueue_block_type_assets( array $block_types ): void {
		foreach ( $block_types as $block_type ) {
			$this->enqueue_single_block_assets( $block_type );
		}
	}

	/**
	 * Enqueue assets for a single block type
	 *
	 * @param string $block_type
	 */
	public function enqueue_single_block_assets( string $block_type ): void {
		if ( isset( $this->enqueued_assets[ $block_type ] ) ) {
			return; // Already enqueued
		}

		$css_handle = 'prad-block-' . $block_type;
		$js_handle  = 'prad-block-' . $block_type;

		// Enqueue CSS if registered
		if ( wp_style_is( $css_handle, 'registered' ) ) {
			wp_enqueue_style( $css_handle );
		}

		// Enqueue JS if registered
		if ( wp_script_is( $js_handle, 'registered' ) ) {
			wp_enqueue_script( $js_handle );

			// Add block-specific localization
			$this->localize_block_script( $block_type );
		}

		$this->enqueued_assets[ $block_type ] = true;

		do_action( 'prad_block_assets_enqueued', $block_type );
	}

	/**
	 * Add block-specific script localization
	 *
	 * @param string $block_type
	 */
	private function localize_block_script( string $block_type ): void {
		$js_handle     = 'prad-block-' . $block_type;
		$localize_data = array();

		switch ( $block_type ) {
			case 'date':
				$localize_data = array(
					'dateFormat' => get_option( 'date_format' ),
					'firstDay'   => get_option( 'start_of_week' ),
					'monthNames' => array(
						__( 'January', 'product-addons' ),
						__( 'February', 'product-addons' ),
						__( 'March', 'product-addons' ),
						__( 'April', 'product-addons' ),
						__( 'May', 'product-addons' ),
						__( 'June', 'product-addons' ),
						__( 'July', 'product-addons' ),
						__( 'August', 'product-addons' ),
						__( 'September', 'product-addons' ),
						__( 'October', 'product-addons' ),
						__( 'November', 'product-addons' ),
						__( 'December', 'product-addons' ),
					),
					'dayNames'   => array(
						__( 'Sunday', 'product-addons' ),
						__( 'Monday', 'product-addons' ),
						__( 'Tuesday', 'product-addons' ),
						__( 'Wednesday', 'product-addons' ),
						__( 'Thursday', 'product-addons' ),
						__( 'Friday', 'product-addons' ),
						__( 'Saturday', 'product-addons' ),
					),
				);
				break;

			case 'upload':
				$localize_data = array(
					'maxFileSize'  => wp_max_upload_size(),
					'allowedTypes' => get_allowed_mime_types(),
					'uploadUrl'    => admin_url( 'admin-ajax.php?action=prad_upload_file' ),
				);
				break;

			case 'select':
				$localize_data = array(
					'select2' => array(
						'placeholder' => __( 'Select an option...', 'product-addons' ),
						'noResults'   => __( 'No results found', 'product-addons' ),
						'searching'   => __( 'Searching...', 'product-addons' ),
					),
				);
				break;
		}

		if ( ! empty( $localize_data ) ) {
			$var_name = 'pradBlock' . ucfirst( str_replace( '_', '', $block_type ) );
			wp_localize_script( $js_handle, $var_name, $localize_data );
		}
	}

	/**
	 * Enqueue block styles
	 */
	public function enqueue_block_styles(): void {
		wp_enqueue_style( 'prad-blocks-core' );

		// Enqueue additional styles if needed
		do_action( 'prad_enqueue_additional_styles' );
	}

	/**
	 * Enqueue block scripts
	 */
	public function enqueue_block_scripts(): void {
		wp_enqueue_script( 'prad-blocks-core' );

		// Enqueue additional scripts if needed
		do_action( 'prad_enqueue_additional_scripts' );
	}

	/**
	 * Load scripts for AJAX requests
	 */
	public function load_scripts_for_ajax(): void {
		// Output script tags directly for AJAX responses
		$this->output_inline_styles();
		$this->output_inline_scripts();
	}

	/**
	 * Output inline styles for AJAX
	 */
	private function output_inline_styles(): void {
		echo '<style type="text/css">';
		echo '/* PRAD Blocks Core Styles for AJAX */';

		// Include critical CSS inline
		$critical_css = $this->get_critical_css();
		echo $critical_css;

		echo '</style>';
	}

	/**
	 * Output inline scripts for AJAX
	 */
	private function output_inline_scripts(): void {
		echo '<script type="text/javascript">';
		echo '/* PRAD Blocks Core Scripts for AJAX */';

		// Include critical JavaScript inline
		$critical_js = $this->get_critical_js();
		echo $critical_js;

		echo '</script>';
	}

	/**
	 * Get critical CSS for inline output
	 *
	 * @return string
	 */
	private function get_critical_css(): string {
		$css_file = PRAD_PATH . 'assets/css/blocks-critical.css';

		if ( file_exists( $css_file ) ) {
			return file_get_contents( $css_file );
		}

		// Fallback critical CSS
		return '
        .prad-addons-wrapper { margin: 20px 0; }
        .prad-block-required { color: #e74c3c; }
        .prad-block-error { border-color: #e74c3c !important; }
        .prad-block-input { width: 100%; padding: 8px; border: 1px solid #ddd; }
        .prad-section-header { cursor: pointer; padding: 10px; background: #f8f8f8; }
        ';
	}

	/**
	 * Get critical JavaScript for inline output
	 *
	 * @return string
	 */
	private function get_critical_js(): string {
		$js_file = PRAD_PATH . 'assets/js/blocks-critical.js';

		if ( file_exists( $js_file ) ) {
			return file_get_contents( $js_file );
		}

		// Fallback critical JS
		return '
        if (typeof pradBlocksInit === "undefined") {
            function pradBlocksInit() {
                // Basic price calculation
                jQuery(document).on("change", ".prad-block-input", function() {
                    pradUpdateTotalPrice();
                });
                
                // Accordion functionality
                jQuery(document).on("click", ".prad-accordion-header", function() {
                    var body = jQuery(this).siblings(".prad-section-body");
                    body.toggleClass("prad-active");
                });
            }
            
            function pradUpdateTotalPrice() {
                var total = 0;
                jQuery(".prad-block-input").each(function() {
                    var price = parseFloat(jQuery(this).data("val")) || 0;
                    var value = jQuery(this).val();
                    if (value) {
                        total += price * (parseFloat(value) || 1);
                    }
                });
                
                var basePrice = parseFloat(jQuery("#prad_base_price").text()) || 0;
                var totalPrice = basePrice + total;
                
                jQuery("#prad_option_price").html(pradFormatPrice(total));
                jQuery("#prad_option_total_price").html(pradFormatPrice(totalPrice));
            }
            
            function pradFormatPrice(price) {
                return "₹" + price.toFixed(2);
            }
            
            jQuery(document).ready(function() {
                pradBlocksInit();
            });
        }
        ';
	}

	/**
	 * Get asset dependencies for a block type
	 *
	 * @param string $block_type
	 * @return array
	 */
	public function get_block_dependencies( string $block_type ): array {
		$dependencies = array(
			'select'       => array( 'select2' ),
			'date'         => array( 'jquery-ui-datepicker' ),
			'upload'       => array( 'plupload-all' ),
			'color_picker' => array( 'wp-color-picker' ),
			'range'        => array( 'jquery-ui-slider' ),
		);

		return $dependencies[ $block_type ] ?? array();
	}

	/**
	 * Register third-party dependencies
	 */
	public function register_dependencies(): void {
		// Select2
		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script(
				'select2',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
				array( 'jquery' ),
				'4.0.13',
				true
			);

			wp_register_style(
				'select2',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
				array(),
				'4.0.13'
			);
		}

		// Flatpickr for date/time
		if ( ! wp_script_is( 'flatpickr', 'registered' ) ) {
			wp_register_script(
				'flatpickr',
				'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js',
				array(),
				'4.6.13',
				true
			);

			wp_register_style(
				'flatpickr',
				'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css',
				array(),
				'4.6.13'
			);
		}
	}

	/**
	 * Enqueue conditional assets based on block usage
	 *
	 * @param array $used_blocks
	 */
	public function enqueue_conditional_assets( array $used_blocks ): void {
		foreach ( $used_blocks as $block_type ) {
			$dependencies = $this->get_block_dependencies( $block_type );

			foreach ( $dependencies as $dependency ) {
				if ( wp_script_is( $dependency, 'registered' ) ) {
					wp_enqueue_script( $dependency );
				}

				if ( wp_style_is( $dependency, 'registered' ) ) {
					wp_enqueue_style( $dependency );
				}
			}

			$this->enqueue_single_block_assets( $block_type );
		}
	}

	/**
	 * Get asset version based on file modification time
	 *
	 * @param string $file_path
	 * @return string
	 */
	public function get_asset_version( string $file_path ): string {
		if ( file_exists( $file_path ) ) {
			return filemtime( $file_path );
		}

		return PRAD_VER;
	}

	/**
	 * Optimize and minify CSS
	 *
	 * @param string $css
	 * @return string
	 */
	public function minify_css( string $css ): string {
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		return $css;
	}

	/**
	 * Optimize and minify JavaScript
	 *
	 * @param string $js
	 * @return string
	 */
	public function minify_js( string $js ): string {
		// Basic JS minification - remove comments and extra whitespace
		$js = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js );
		$js = preg_replace( '/\/\/.*$/m', '', $js );
		$js = preg_replace( '/\s+/', ' ', $js );

		return trim( $js );
	}

	/**
	 * Check if assets should be loaded
	 *
	 * @return bool
	 */
	public function should_load_assets(): bool {
		// Don't load on admin pages
		if ( is_admin() ) {
			return false;
		}

		// Only load on product pages or pages with shortcode
		if ( is_product() || $this->has_prad_shortcode() ) {
			return true;
		}

		// Load on cart/checkout if products have addons
		if ( is_cart() || is_checkout() ) {
			return $this->cart_has_addons();
		}

		return apply_filters( 'prad_should_load_assets', false );
	}

	/**
	 * Check if current page has PRAD shortcode
	 *
	 * @return bool
	 */
	private function has_prad_shortcode(): bool {
		global $post;

		if ( ! $post ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'prad_blocks' ) ||
				has_shortcode( $post->post_content, 'product_addons' );
	}

	/**
	 * Check if cart has products with addons
	 *
	 * @return bool
	 */
	private function cart_has_addons(): bool {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return false;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['prad_selection'] ) && ! empty( $cart_item['prad_selection'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get all enqueued assets
	 *
	 * @return array
	 */
	public function get_enqueued_assets(): array {
		return $this->enqueued_assets;
	}

	/**
	 * Clear enqueued assets cache
	 */
	public function clear_assets_cache(): void {
		$this->enqueued_assets = array();
	}

	/**
	 * Add inline CSS for a specific block
	 *
	 * @param string $block_type
	 * @param string $css
	 */
	public function add_inline_block_css( string $block_type, string $css ): void {
		$handle = 'prad-block-' . $block_type;

		if ( wp_style_is( $handle, 'enqueued' ) || wp_style_is( $handle, 'done' ) ) {
			wp_add_inline_style( $handle, $css );
		} else {
			// Store for later addition
			add_action(
				'wp_enqueue_scripts',
				function () use ( $handle, $css ) {
					if ( wp_style_is( $handle, 'enqueued' ) ) {
						wp_add_inline_style( $handle, $css );
					}
				},
				20
			);
		}
	}

	/**
	 * Add inline JavaScript for a specific block
	 *
	 * @param string $block_type
	 * @param string $js
	 */
	public function add_inline_block_js( string $block_type, string $js ): void {
		$handle = 'prad-block-' . $block_type;

		if ( wp_script_is( $handle, 'enqueued' ) || wp_script_is( $handle, 'done' ) ) {
			wp_add_inline_script( $handle, $js );
		} else {
			// Store for later addition
			add_action(
				'wp_enqueue_scripts',
				function () use ( $handle, $js ) {
					if ( wp_script_is( $handle, 'enqueued' ) ) {
						wp_add_inline_script( $handle, $js );
					}
				},
				20
			);
		}
	}
}
