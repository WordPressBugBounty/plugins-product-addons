<?php // phpcs:ignore
/**
 * Initialization Action.
 *
 * @package PRAD
 * @since 1.0.0
 */
namespace PRAD;

defined( 'ABSPATH' ) || exit;

/**
 * Initialization class.
 */
class PRAD_Initialization {

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
		require_once PRAD_PATH . 'includes/class-deactive.php';
		require_once PRAD_PATH . 'includes/class-post-type.php';
		require_once PRAD_PATH . 'includes/class-render-blocks.php';
		require_once PRAD_PATH . 'includes/class-prad-analytics.php';
		require_once PRAD_PATH . 'includes/class-xpo.php';

		require_once PRAD_PATH . 'includes/admin/class-options.php';
		require_once PRAD_PATH . 'includes/admin/class-notice.php';
		require_once PRAD_PATH . 'includes/admin/durbin/class-durbin-client.php';
		require_once PRAD_PATH . 'includes/admin/product/class-product-edit.php';
		require_once PRAD_PATH . 'includes/admin/class-our-plugins.php';

		require_once PRAD_PATH . 'includes/order/class-cart-page.php';
		require_once PRAD_PATH . 'includes/order/class-checkout-page.php';

		require_once PRAD_PATH . 'includes/common/class-hooks.php';
		require_once PRAD_PATH . 'includes/rest_api/class-request-api.php';
		require_once PRAD_PATH . 'includes/compatibility/class-compatibility.php';
		require_once PRAD_PATH . 'includes/compatibility/class-shop-compatibilty.php';
		require_once PRAD_PATH . 'includes/common/class-safe-math-evaluator.php';

		new \PRAD\Deactive();
		new \PRAD\PostType();
		new \PRAD\Render_Blocks();
		new \PRAD\PRAD_Analytics();
		new \PRAD\Includes\Xpo();

		new \PRAD\Options();
		new \PRAD\Notice();
		new \PRAD\Product_Edit();
		new \PRAD\Includes\OurPlugins();

		new \PRAD\Cart_Page();
		new \PRAD\Checkout_Page();

		new \PRAD\Hooks();
		new \PRAD\RequestAPI();
		new \PRAD\Compatibility();
		new \PRAD\Includes\Compatibility\ShopCompatibilty();
		new \PRAD\Includes\Common\SafeMathEvaluator();
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
					'prad_backend',
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
