<?php // phpcs:ignore
/**
 * Initialization Action.
 *
 * @package PRAD
 * @since 1.0.0
 */
namespace PRAD\Includes;

use PRAD\Includes\Admin\Notice;
use PRAD\Includes\Admin\Options;
use PRAD\Includes\Admin\OurPlugins;
use PRAD\Includes\Admin\Product\ProductEdit;
use PRAD\Includes\Common\Hooks;
use PRAD\Includes\Common\SafeMathEvaluator;
use PRAD\Includes\Compatibility\Compatibility;
use PRAD\Includes\Compatibility\ShopCompatibilty;
use PRAD\Includes\Order\CartPage;
use PRAD\Includes\Order\CheckoutPage;
use PRAD\Includes\Restapi\RequestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Initialization class.
 */
class Initialization {

	/**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
	public function __construct() {
		$this->requires();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_callback' ) );
		add_action( 'activated_plugin', array( $this, 'activation_redirect' ) );
	}

	/**
	 * Necessary Requires Class
	 *
	 * @since v.1.0.0
	 * @return void
	 */
	public function requires() {

		new Deactive();
		new PostType();
		new RenderBlocks();
		new Analytics();
		new Xpo();

		new Options();
		new Notice();

		new ProductEdit();
		new OurPlugins();

		new CartPage();
		new CheckoutPage();

		new Hooks();
		new RequestApi();
		new Compatibility();
		new ShopCompatibilty();
		new SafeMathEvaluator();
	}


	/**
	 * Only Backend CSS and JS Scripts
	 *
	 * @since v.1.0.0
	 * @return void
	 */
	public function admin_scripts_callback() {
		global $pagenow;
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; //phpcs:ignore
		wp_enqueue_style( 'prad-admin-style', PRAD_URL . 'assets/css/prad-admin.css', array(), PRAD_VER );
		wp_enqueue_script( 'prad-admin-script', PRAD_URL . 'assets/js/prad-admin.js', array( 'jquery' ), PRAD_VER, true );

		if ( 'admin.php' === $pagenow ) {
			wp_localize_script(
				'prad-admin-script',
				'prad_admin',
				array(
					'license' => get_option( 'edd_prad_license_key' ),
				)
			);
			if ( 'prad-dashboard' === $page ) {
				$user_info = get_userdata( get_current_user_id() );
				wp_enqueue_style( 'prad-editor-css', PRAD_URL . 'assets/css/wowaddons-backend.css', array(), PRAD_VER );
				wp_enqueue_style( 'prad-blocks-css', PRAD_URL . 'assets/css/wowaddons-blocks.css', array(), PRAD_VER );
				wp_enqueue_script( 'prad-editor-script', PRAD_URL . 'assets/js/wowaddons.js', array( 'wp-api-fetch' ), PRAD_VER, true );
				wp_enqueue_script( 'prad-date-script', PRAD_URL . 'assets/js/wowdate.js', array( 'jquery' ), PRAD_VER, true );
				wp_enqueue_media();
				wp_localize_script(
					'prad-editor-script',
					'pradBackendData',
					array_merge(
						array(
							'url'            => PRAD_URL,
							'db_url'         => admin_url( 'admin.php?page=prad-dashboard#' ),
							'ajax'           => admin_url( 'admin-ajax.php' ),
							'version'        => PRAD_VER,
							'isActive'       => product_addons()->is_lc_active(),
							'license'        => get_option( 'edd_prad_license_key' ),
							'nonce'          => wp_create_nonce( 'prad-nonce' ),
							'decimal_sep'    => get_option( 'woocommerce_price_decimal_sep', '.' ),
							'num_decimals'   => get_option( 'woocommerce_price_num_decimals', '2' ),
							'currency_pos'   => get_option( 'woocommerce_currency_pos', 'left' ),
							'currencySymbol' => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$',
							'userInfo'       => array(
								'name'  => $user_info->first_name ? $user_info->first_name . ( $user_info->last_name ? ' ' . $user_info->last_name : '' ) : $user_info->user_login,
								'email' => $user_info->user_email,
							),
							'helloBar'       => product_addons()->get_transient_without_cache( 'prad_helloBar' ),
						),
						product_addons()->get_wow_products_details()
					)
				);
				wp_set_script_translations( 'prad-editor-script', 'product-addons', PRAD_PATH . 'languages/' );
			}
		}
	}

	/**
	 * Redirect After Active Plugin
	 *
	 * @since v.1.0.0
	 *
	 * @param string $plugin Plugin name.
	 *
	 * @return NULL
	 */
	public function activation_redirect( $plugin ) {
		if ( 'product-addons/product-addons.php' === $plugin ) {
			if ( wp_doing_ajax() || is_network_admin() || isset( $_GET['activate-multi'] ) || isset( $_POST['action'] ) && 'activate-selected' == $_POST['action'] ) { // phpcs:ignore
				return;
			}
			exit( wp_safe_redirect( admin_url( 'admin.php?page=prad-dashboard#dashboard' ) ) ); // phpcs:ignore
		}
	}
}
