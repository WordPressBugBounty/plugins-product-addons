<?php //phpcs:ignore
namespace PRAD\Includes\Admin;

use PRAD\Includes\Admin\Durbin\DurbinClient;
use PRAD\Includes\Xpo;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Notice
 */
class Notice {

	/**
	 * Notice Constructor
	 */

	private $notice_version        = 'v155';
	private $notice_js_css_applied = false;
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices_callback' ) );
		add_action( 'admin_init', array( $this, 'set_dismiss_notice_callback' ) );

		// Woocommerce Install Action
		add_action( 'wp_ajax_prad_install', array( $this, 'install_activate_plugin' ) );
	}

	/**
	 * Admin Notices Callback
	 *
	 * @return void
	 */
	public function admin_notices_callback() {
		$this->other_plugin_install_notice_callback( 'required' );
		$this->prad_dashboard_notice_callback();
		$this->prad_dashboard_durbin_notice_callback();
	}

	/**
	 * Admin Dashboard Notice Callback
	 *
	 * @return void
	 */
	public function prad_dashboard_notice_callback() {
		$this->prad_dashboard_content_notice();
		$this->prad_dashboard_banner_notice();
	}

	/**
	 * Dashboard Banner Notice
	 *
	 * @return void
	 */
	public function prad_dashboard_banner_notice() {
		$prad_db_nonce  = wp_create_nonce( 'prad-dashboard-nonce' );
		$banner_notices = array(
			array(
				'key'        => 'prad_new_year_sale_26_v1',
				'start'      => '2026-01-01 00:00 Asia/Dhaka', // format YY-MM-DD always set time 00:00 and zone Asia/Dhaka
				'end'        => '2026-01-06 23:59 Asia/Dhaka', // format YY-MM-DD always set time 23:59 and zone Asia/Dhaka
				'banner_src' => PRAD_URL . 'assets/img/dashboard_banner/new_year_sale_26_v1.png',
				'url'        => Xpo::generate_utm_link(
					array(
						'utmKey' => 'banner_notice',
					)
				),
				'visibility' => ! Xpo::is_lc_active(),
			),
			array(
				'key'        => 'prad_new_year_sale_26_v2',
				'start'      => '2026-01-17 00:00 Asia/Dhaka', // format YY-MM-DD always set time 00:00 and zone Asia/Dhaka
				'end'        => '2026-01-22 23:59 Asia/Dhaka', // format YY-MM-DD always set time 23:59 and zone Asia/Dhaka
				'banner_src' => PRAD_URL . 'assets/img/dashboard_banner/new_year_sale_26_v2.png',
				'url'        => Xpo::generate_utm_link(
					array(
						'utmKey' => 'banner_notice',
					)
				),
				'visibility' => ! Xpo::is_lc_active(),
			),
			array(
				'key'        => 'prad_new_year_sale_26_v3',
				'start'      => '2026-02-02 00:00 Asia/Dhaka', // format YY-MM-DD always set time 00:00 and zone Asia/Dhaka
				'end'        => '2026-02-07 23:59 Asia/Dhaka', // format YY-MM-DD always set time 23:59 and zone Asia/Dhaka
				'banner_src' => PRAD_URL . 'assets/img/dashboard_banner/new_year_sale_26_v3.png',
				'url'        => Xpo::generate_utm_link(
					array(
						'utmKey' => 'banner_notice',
					)
				),
				'visibility' => ! Xpo::is_lc_active(),
			),
		);

		foreach ( $banner_notices as $key => $notice ) {
			$notice_key = isset( $notice['key'] ) ? $notice['key'] : $this->notice_version;
			if ( isset( $_GET['disable_prad_notice'] ) && $notice_key === $_GET['disable_prad_notice'] ) {
				continue;
			} else {
				$current_time = gmdate( 'U' );
				$notice_start = gmdate( 'U', strtotime( $notice['start'] ) );
				$notice_end   = gmdate( 'U', strtotime( $notice['end'] ) );
				if ( $current_time >= $notice_start && $current_time <= $notice_end && $notice['visibility'] ) {

					$notice_transient = Xpo::get_transient_without_cache( 'prad_get_pro_notice_' . $notice_key );

					if ( 'off' !== $notice_transient ) {
						if ( ! $this->notice_js_css_applied ) {
							$this->prad_banner_notice_css();
							$this->notice_js_css_applied = true;
						}
						$query_args = array(
							'disable_prad_notice' => $notice_key,
							'prad_db_nonce'       => $prad_db_nonce,
						);
						if ( isset( $notice['repeat_interval'] ) && $notice['repeat_interval'] ) {
							$query_args['prad_interval'] = $notice['repeat_interval'];
						}
						?>
					<div class="prad-notice-wrapper notice wc-install prad-free-notice">
						<div class="wc-install-body prad-image-banner">
							<a class="wc-dismiss-notice" href="
							<?php
							echo esc_url(
								add_query_arg(
									$query_args
								)
							);
							?>
							"><?php esc_html_e( 'Dismiss', 'product-addons' ); ?></a>
							<a class="prad-btn-image" target="_blank" href="<?php echo esc_url( $notice['url'] ); ?>">
								<img loading="lazy" src="<?php echo esc_url( $notice['banner_src'] ); ?>" alt="Discount Banner"/>
							</a>
						</div>
					</div>
						<?php
					}
				}
			}
		}
	}

	/**
	 * Dashboard Content Notice
	 *
	 * @return void
	 */
	public function prad_dashboard_content_notice() {

		$content_notices = array(
			array(
				'key'                => 'prad_dashboard_content_notice_newyr26_v1',
				'start'              => '2026-01-09 00:00 Asia/Dhaka',
				'end'                => '2026-01-14 23:59 Asia/Dhaka',
				'url'                => Xpo::generate_utm_link(
					array(
						'utmKey' => 'content_notice',
					)
				),
				'visibility'         => ! Xpo::is_lc_active(),
				'content_heading'    => __( 'New Year Sales Offers:', 'product-addons' ),
				'content_subheading' => __( 'WowAddons offers are live - Enjoy %s on this extra options plugin for WooCommerce.', 'product-addons' ),
				'discount_content'   => ' up to 60% OFF',
				'border_color'       => '#86a62c',
				'icon'               => PRAD_URL . 'assets/img/icons/60_red.svg',
				'button_text'        => __( 'Claim Your Discount!', 'product-addons' ),
				'is_discount_logo'   => true,
			),
			array(
				'key'                => 'prad_dashboard_content_notice_newyr26_v2',
				'start'              => '2026-01-25 00:00 Asia/Dhaka',
				'end'                => '2026-01-30 23:59 Asia/Dhaka',
				'url'                => Xpo::generate_utm_link(
					array(
						'utmKey' => 'content_notice',
					)
				),
				'visibility'         => ! Xpo::is_lc_active(),
				'content_heading'    => __( 'New Year Sales Alert:', 'product-addons' ),
				'content_subheading' => __( 'WowAddons is on Sale - Enjoy %s  on this  extra options plugin for WooCommerce.', 'product-addons' ),
				'discount_content'   => ' up to 60% OFF',
				'border_color'       => '#86a62c', // product default border color.
				'icon'               => PRAD_URL . 'assets/img/icons/60_red.svg',
				'button_text'        => __( 'Claim Your Discount!', 'product-addons' ),
				'is_discount_logo'   => true,
			),
			array(
				'key'                => 'prad_dashboard_content_notice_newyr26_v3',
				'start'              => '2026-02-10 00:00 Asia/Dhaka',
				'end'                => '2026-02-15 23:59 Asia/Dhaka',
				'url'                => Xpo::generate_utm_link(
					array(
						'utmKey' => 'content_notice',
					)
				),
				'visibility'         => ! Xpo::is_lc_active(),
				'content_heading'    => __( 'Fresh New Year Deals:', 'product-addons' ),
				'content_subheading' => __( 'WowAddons is on Sale - Enjoy %s  on this extra options plugin for WooCommerce.', 'product-addons' ),
				'discount_content'   => ' up to 60% OFF',
				'border_color'       => '#86a62c', // product default border color.
				'icon'               => PRAD_URL . 'assets/img/icons/60_red.svg',
				'button_text'        => __( 'Claim Your Discount!', 'product-addons' ),
				'is_discount_logo'   => true,
			),

		);

		$prad_db_nonce = wp_create_nonce( 'prad-dashboard-nonce' );

		foreach ( $content_notices as $key => $notice ) {
			$notice_key = isset( $notice['key'] ) ? $notice['key'] : $this->notice_version;
			if ( isset( $_GET['disable_prad_notice'] ) && $notice_key === $_GET['disable_prad_notice'] ) {
				continue;
			} else {
				$border_color = $notice['border_color'];

				$current_time = gmdate( 'U' );
				$notice_start = gmdate( 'U', strtotime( $notice['start'] ) );
				$notice_end   = gmdate( 'U', strtotime( $notice['end'] ) );
				if ( $current_time >= $notice_start && $current_time <= $notice_end && $notice['visibility'] ) {
					$notice_transient = Xpo::get_transient_without_cache( 'prad_get_pro_notice_' . $notice_key );

					if ( 'off' !== $notice_transient ) {
						if ( ! $this->notice_js_css_applied ) {
							$this->prad_banner_notice_css();
							$this->notice_js_css_applied = true;
						}
						$query_args = array(
							'disable_prad_notice' => $notice_key,
							'prad_db_nonce'       => $prad_db_nonce,
						);
						if ( isset( $notice['repeat_interval'] ) && $notice['repeat_interval'] ) {
							$query_args['prad_interval'] = $notice['repeat_interval'];
						}

						$url = isset( $notice['url'] ) ? $notice['url'] : Xpo::generate_utm_link(
							array(
								'utmKey' => 'content_notice',
							)
						);

						?>
					<div class="prad-notice-wrapper notice data_collection_notice" 
					style="border-left: 3px solid <?php echo esc_attr( $border_color ); ?>;"
					> 
						<?php
						if ( $notice['is_discount_logo'] ) {
							?>
								<div class="prad-notice-discout-icon"> <img src="<?php echo esc_url( $notice['icon'] ); ?>"/>  </div>
							<?php
						} else {
							?>
								<div class="prad-notice-icon"> <img src="<?php echo esc_url( $notice['icon'] ); ?>"/>  </div>
							<?php
						}
						?>
						
						<div class="prad-notice-content-wrapper">
							<div class="">
								<strong><?php printf( esc_html( $notice['content_heading'] ) ); ?> </strong>
						<?php
						printf(
							wp_kses_post( $notice['content_subheading'] ),
							'<strong>' . esc_html( $notice['discount_content'] ) . '</strong>'
						);
						?>
							</div>
							<div class="prad-notice-buttons">
							<?php if ( isset( $notice['is_discount_logo'] ) && $notice['is_discount_logo'] ) : ?>
									<a class="prad-discount_btn" href="<?php echo esc_url( $url ); ?>" target="_blank">
										<?php echo esc_html( $notice['button_text'] ); ?>
									</a>
								<?php else : ?>
									<a class="prad-notice-btn button button-primary" href="<?php echo esc_url( $url ); ?>" target="_blank" style="background-color: <?php echo ! empty( $notice['background_color'] ) ? esc_attr( $notice['background_color'] ) : '#86a62c'; ?>;">
									<?php echo esc_html( $notice['button_text'] ); ?>
										
									</a>
								<?php endif; ?>
							</div>
						</div>
						<a href=
							<?php
							echo esc_url(
								add_query_arg(
									$query_args
								)
							);
							?>
						class="prad-notice-close"><span class="prad-notice-close-icon dashicons dashicons-dismiss"> </span></a>
					</div>
								<?php
					}
				}
			}
		}
	}

	/**
	 * Set Notice Dismiss Callback
	 *
	 * @return void
	 */
	public function set_dismiss_notice_callback() {

		// Durbin notice dismiss.
		if ( isset( $_GET['prad_durbin_key'] ) && $_GET['prad_durbin_key'] ) {
			$durbin_key = sanitize_text_field( $_GET['prad_durbin_key'] );
			Xpo::set_transient_without_cache( 'prad_durbin_notice_' . $durbin_key, 'off' );
		}
		if ( isset( $_GET['prad_get_durbin'] ) && 'get' === $_GET['prad_get_durbin'] ) {
			DurbinClient::send( DurbinClient::ACTIVATE_ACTION );
		}

		// Install notice dismiss
		if ( isset( $_GET['prad_install_key'] ) && $_GET['prad_install_key'] ) {
			$install_key = sanitize_text_field( $_GET['prad_install_key'] );
			Xpo::set_transient_without_cache( 'prad_install_notice_' . $install_key, 'off' );
		}

		if ( isset( $_GET['disable_prad_notice'] ) ) {
			$notice_key = sanitize_text_field( $_GET['disable_prad_notice'] );
			if ( isset( $_GET['prad_interval'] ) && '' != $_GET['prad_interval'] ) {
				$interval = (int) $_GET['prad_interval'];
				Xpo::set_transient_without_cache( 'prad_get_pro_notice_' . $notice_key, 'off', $interval );
			} else {
				Xpo::set_transient_without_cache( 'prad_get_pro_notice_' . $notice_key, 'off' );
			}
		}
	}

	/**
	 * Admin Banner CSS File
	 *
	 * @since v.1.0.7
	 * @param NULL
	 * @return STRING
	 */
	public function prad_banner_notice_css() {
		?>
		<style id="prad-notice-css" type="text/css">
			.prad-notice-wrapper {
				border: 1px solid #c3c4c7;
				border-left: 3px solid #037fff;
				margin: 15px 0px !important;
				display: flex;
				align-items: center;
				background: #ffffff;
				width: 100%;
				padding: 10px 0px;
				position: relative;
				box-sizing: border-box;
			}
			.prad-notice-wrapper.notice, .prad-free-notice.wc-install.notice {
				margin: 10px 0px;
				width: calc( 100% - 20px );
			}
			.wrap .prad-notice-wrapper.notice, .wrap .prad-free-notice.wc-install {
				width: 100%;
			}
			.prad-notice-icon {
				margin-left: 15px;
			}
			.prad-notice-discout-icon {
				margin-left: 5px;
			}
			.prad-notice-icon img {
				max-width: 42px;
				height: 70px;
			}
			.prad-notice-discout-icon img {
				height: 70px;
				width: 70px;
			}
			.prad-notice-btn {
				font-weight: 600;
				text-transform: uppercase !important;
				padding: 2px 10px !important;
				background-color: #86a62c ;
				border: none !important;
			}
			.prad-discount_btn {
				background-color: #ffffff;
				text-decoration: none;
				border: 1px solid #86a62c;
				padding: 5px 10px;
				border-radius: 5px;
				font-weight: 500;
				text-transform: uppercase;
				color: #86a62c !important;
			}
			.prad-notice-content-wrapper {
				display: flex;
				flex-direction: column;
				gap: 8px;
				font-size: 14px;
				line-height: 20px;
				margin-left: 15px;
			}
			.prad-notice-buttons {
				display: flex;
				align-items: center;
				gap: 15px;
			}
			.prad-notice-dont-save-money {
				font-size: 12px;
			}
			.prad-notice-close {
				position: absolute;
				right: 2px;
				top: 5px;
				text-decoration: unset;
				color: #b6b6b6;
				font-family: dashicons;
				font-size: 16px;
				font-style: normal;
				font-weight: 400;
				line-height: 20px;
			}
			.prad-notice-close-icon {
				font-size: 14px;
			}
			.prad-free-notice.wc-install {
				display: flex;
				align-items: center;
				background: #fff;
				margin-top: 20px;
				width: 100%;
				box-sizing: border-box;
				border: 1px solid #ccd0d4;
				padding: 4px;
				border-radius: 4px;
				border-left: 3px solid #86a62c;
				line-height: 0;
			}   
			.prad-free-notice.wc-install img {
				margin-right: 0; 
				max-width: 100%;width: 100%;
			}
			.prad-free-notice .wc-install-body {
				-ms-flex: 1;
				flex: 1;
				position: relative;
				padding: 10px;
			}
			.prad-free-notice .wc-install-body.prad-image-banner{
				padding: 0px;
			}
			.prad-free-notice .wc-install-body h3 {
				margin-top: 0;
				font-size: 24px;
				margin-bottom: 15px;
			}
			.prad-install-btn {
				margin-top: 15px;
				display: inline-block;
			}
			.prad-free-notice .wc-install .dashicons{
				display: none;
				animation: dashicons-spin 1s infinite;
				animation-timing-function: linear;
			}
			.prad-free-notice.wc-install.loading .dashicons {
				display: inline-block;
				margin-top: 12px;
				margin-right: 5px;
			}
			.prad-free-notice .wc-install-body h3 {
				font-size: 20px;
				margin-bottom: 5px;
			}
			.prad-free-notice .wc-install-body > div {
				max-width: 100%;
				margin-bottom: 10px;
			}
			.prad-free-notice .button-hero {
				padding: 8px 14px !important;
				min-height: inherit !important;
				line-height: 1 !important;
				box-shadow: none;
				border: none;
				transition: 400ms;
			}
			.prad-free-notice .prad-btn-notice-pro {
				background: #2271b1;
				color: #fff;
			}
			.prad-free-notice .prad-btn-notice-pro:hover,
			.prad-free-notice .prad-btn-notice-pro:focus {
				background: #185a8f;
			}
			.prad-free-notice .button-hero:hover,
			.prad-free-notice .button-hero:focus {
				border: none;
				box-shadow: none;
			}
			@keyframes dashicons-spin {
				0% {
					transform: rotate( 0deg );
				}
				100% {
					transform: rotate( 360deg );
				}
			}
			.prad-free-notice .wc-dismiss-notice {
				color: #fff;
				background-color: #000000;
				padding-top: 0px;
				position: absolute;
				right: 0;
				top: 0px;
				padding: 10px 10px 14px;
				border-radius: 0 0 0 4px;
				display: inline-block;
				transition: 400ms;
			}
			.prad-free-notice .wc-dismiss-notice:hover {
				color:red;
			}
			.prad-free-notice .wc-dismiss-notice .dashicons{
				display: inline-block;
				text-decoration: none;
				animation: none;
				font-size: 16px;
			}
			/* ===== Eid Banner Css ===== */
			.prad-free-notice .wc-install-body {
				background: linear-gradient(90deg,rgb(0,110,188) 0%,rgb(2,17,196) 100%);
			}
			.prad-free-notice p{
				color: #fff;
				margin: 5px 0px;
				font-size: 16px;
				font-weight: 300;
				letter-spacing: 1px;
			}
			.prad-free-notice p.prad-enjoy-offer {
				display: inline;
				font-weight: bold;
				
			}
			.prad-free-notice .prad-get-now {
				font-size: 14px;
				color: #fff;
				background: #14a8ff;
				padding: 8px 12px;
				border-radius: 4px;
				text-decoration: none;
				margin-left: 10px;
				position: relative;
				top: -4px;
				transition: 400ms;
			}
			.prad-free-notice .prad-get-now:hover{
				background: #068fe0;
			}
			.prad-free-notice .prad-dismiss {
				color: #fff;
				background-color: #000964;
				padding-top: 0px;
				position: absolute;
				right: 0;
				top: 0px;
				padding: 10px 8px 12px;
				border-radius: 0 0 0 4px;
				display: inline-block;
				transition: 400ms;
			}
			.prad-free-notice .prad-dismiss:hover {
				color: #d2d2d2;
			}
			/*----- PRAD Into Notice ------*/
			.notice.notice-success.prad-notice {
				border-left-color: #4D4DFF;
				padding: 0;
			}
			.prad-notice-container {
				display: flex;
			}
			.prad-notice-container a{
				text-decoration: none;
			}
			.prad-notice-container a:visited{
				color: white;
			}
			.prad-notice-container img {
				height: 100px; 
				width: 100px;
			}
			.prad-notice-image {
				padding-top: 15px;
				padding-left: 12px;
				padding-right: 12px;
				background-color: #f4f4ff;
			}
			.prad-notice-image img{
				max-width: 100%;
			}
			.prad-notice-content {
				width: 100%;
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 8px;
			}
			.prad-notice-prad-button {
				max-width: fit-content;
				padding: 8px 15px;
				font-size: 16px;
				color: white;
				background-color: #4D4DFF;
				border: none;
				border-radius: 2px;
				cursor: pointer;
				margin-top: 6px;
				text-decoration: none;
			}
			.prad-notice-heading {
				font-size: 18px;
				font-weight: 500;
				color: #1b2023;
			}
			.prad-notice-content-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}
			.prad-notice-close .dashicons-no-alt {
				font-size: 25px;
				height: 26px;
				width: 25px;
				cursor: pointer;
				color: #585858;
			}
			.prad-notice-close .dashicons-no-alt:hover {
				color: red;
			}
			.prad-notice-content-body {
				font-size: 14px;
				color: #343b40;
			}
			.prad-notice-wholesalex-button:hover {
				background-color: #6C6CFF;
				color: white;
			}
			span.prad-bold {
				font-weight: bold;
			}
			a.prad-pro-dismiss:focus {
				outline: none;
				box-shadow: unset;
			}
			.prad-free-notice .loading, .prad-notice .loading {
				width: 16px;
				height: 16px;
				border: 3px solid #FFF;
				border-bottom-color: transparent;
				border-radius: 50%;
				display: inline-block;
				box-sizing: border-box;
				animation: rotation 1s linear infinite;
				margin-left: 10px;
			}
			a.prad-notice-prad-button:hover {
				color: #fff !important;
			}
			@keyframes rotation {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}
		</style>
		<?php
	}

	/**
	 * The Durbin Html
	 *
	 * @return STRING | HTML
	 */
	public function prad_dashboard_durbin_notice_callback() {
		$durbin_key = 'prad_durbin_dc12245';
		if (
			isset( $_GET['prad_durbin_key'] ) ||
			'off' === Xpo::get_transient_without_cache( 'prad_durbin_notice_' . $durbin_key ) ||
			defined( 'PRAD_PRO_VER' )
		) {
			return;
		}
		if ( ! $this->notice_js_css_applied ) {
			$this->prad_banner_notice_css();
			$this->notice_js_css_applied = true;
		}
		?>
		<style>
			.prad-consent-box {
				width: 656px;
				padding: 16px;
				border: 1px solid #070707;
				border-left-width: 4px;
				border-radius: 4px;
				background-color: #fff;
				position: relative;
			}
			.prad-consent-content {
				display: flex;
				justify-content: space-between;
				align-items: flex-end;
				gap: 26px;
			}

			.prad-consent-text-first {
				font-size: 14px;
				font-weight: 600;
				color: #070707;
			}
			.prad-consent-text-last {
				margin: 4px 0 0;
				font-size: 14px;
				color: #070707;
			}

			.prad-consent-accept {
				background-color: #070707;
				color: #fff;
				border: none;
				padding: 6px 10px;
				border-radius: 4px;
				cursor: pointer;
				font-size: 12px;
				font-weight: 600;
				text-decoration: none;
			}
			.prad-consent-accept:hover {
				background-color:rgb(38, 38, 38);
				color: #fff;
			}
		</style>

		<div class="prad-consent-box prad-notice-wrapper notice data_collection_notice">
			<div class="prad-consent-content">
			<div class="prad-consent-text">
			<div class="prad-consent-text-first"><?php esc_html_e( 'Want to help make WowAddons even more awesome?', 'product-addons' ); ?></div>
			<div class="prad-consent-text-last">
					<?php esc_html_e( 'Allow us to collect diagnostic data and usage information. see ', 'product-addons' ); ?>
			<a href="https://www.wpxpo.com/data-collection-policy/" target="_blank" ><?php esc_html_e( 'what we collect.', 'product-addons' ); ?></a>
			</div>
			</div>
			<a
									class="prad-consent-accept"
									href=
					<?php
									echo esc_url(
										add_query_arg(
											array(
												'prad_durbin_key' => $durbin_key,
												'prad_get_durbin'  => 'get',
											)
										)
									);
					?>
									class="prad-notice-close"
			><?php esc_html_e( 'Accept & Close', 'product-addons' ); ?></a>
			</div>
			<a href=
					<?php
								echo esc_url(
									add_query_arg(
										array(
											'prad_durbin_key' => $durbin_key,
										)
									)
								);
					?>
								class="prad-notice-close"
			>
				<span class="prad-notice-close-icon dashicons dashicons-dismiss"> </span></a>
			</div>
		<?php
	}

	/**
	 * Woocommerce Notice HTML
	 *
	 * @since v.1.0.0
	 * @return STRING | HTML
	 */
	public function other_plugin_install_notice_callback( $type = '' ) {
		$install_key_tran = 'woocommerce';
		if ( 'required' !== $type ) {
			if ( isset( $_GET['prad_install_key'] ) ||
				'off' === Xpo::get_transient_without_cache( 'prad_install_notice_' . $install_key_tran, )
			) {
				return;
			}
		}

		if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
			return;
		}
		$this->install_notice_css();
		$this->install_notice_js();
		?>
			<div class="prad-pro-notice prad-wc-install wc-install">
				<img width="100" src="<?php echo esc_url( PRAD_URL . 'assets/img/woocommerce.png' ); ?>" alt="logo" />
				<div class="prad-install-body">
					<h3><?php esc_html_e( 'Welcome to WowAddons.', 'product-addons' ); ?></h3>
					<p><?php esc_html_e( 'WowAddons is a WooCommerce-based plugin. So you need to installed & activate WooCommerce to start using WowAddons.', 'product-addons' ); ?></p>
					<div class="prad-install-btn-wrap">
						<a class="wc-install-btn prad-install-btn button button-primary" data-plugin-slug="woocommerce" href="#"><span class="dashicons dashicons-image-rotate"></span><?php file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ? esc_html_e( ' Activate WooCommerce', 'product-addons' ) : esc_html_e( ' Install WooCommerce', 'product-addons' ); ?></a>
						<?php if ( 'required' !== $type ) : ?>
							<a href="<?php echo esc_url( add_query_arg( array( 'prad_install_key' => $install_key_tran ) ) ); ?>" class="prad-install-cancel wc-dismiss-notice">
								<?php esc_html_e( 'Discard', 'product-addons' ); ?>
							</a>
						<?php endif; ?>
					</div>
					<div id="installation-msg"></div>
				</div>
			</div>
		<?php
	}

	/**
	 * Plugin Install and Active Action
	 *
	 * @since v.1.6.8
	 * @return STRING | Redirect URL
	 */
	public function install_activate_plugin() {
		if ( ! isset( $_POST['install_plugin'] ) || ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error( esc_html__( 'Invalid request.', 'product-addons' ) );
		}

		$plugin_slug = sanitize_text_field( wp_unslash( $_POST['install_plugin'] ) );
		Xpo::install_and_active_plugin( $plugin_slug );

		if ( wp_doing_ajax() || is_network_admin() || isset( $_GET['activate-multi'] ) || isset( $_POST['action'] ) && 'activate-selected' == sanitize_text_field( $_POST['action'] ) ) { //phpcs:ignore
			return;
		}

		return wp_send_json_success( admin_url( 'admin.php?page=prad-dashboard#dashboard' ) );
	}

	/**
	 * Installation Notice CSS
	 *
	 * @since v.1.0.0
	 */
	public function install_notice_css() {
		?>
		<style type="text/css">
			.prad-wc-install {
				display: flex;
				align-items: center;
				background: #fff;
				margin-top: 30px !important;
				/*width: calc(100% - 65px);*/
				border: 1px solid #ccd0d4;
				padding: 4px !important;
				border-radius: 4px;
				border-left: 3px solid #46b450;
				line-height: 0;
				gap: 15px;
				padding: 15px 10px !important;
			}
			.prad-wc-install img {
				width: 100px;
			}
			.prad-install-body {
				-ms-flex: 1;
				flex: 1;
			}
			.prad-install-body.prad-image-banner {
				padding: 0px !important;
			}
			.prad-install-body.prad-image-banner img {
				width: 100%;
			}
			.prad-install-body>div {
				max-width: 450px;
				margin-bottom: 20px !important;
			}
			.prad-install-body h3 {
				margin: 0 !important;
				font-size: 20px;
				margin-bottom: 10px !important;
				line-height: 1;
			}
			.prad-pro-notice .wc-install-btn,
			.wp-core-ui .prad-wc-active-btn {
				display: inline-flex;
				align-items: center;
				padding: 3px 20px !important;
			}
			.prad-pro-notice.loading .wc-install-btn {
				opacity: 0.7;
				pointer-events: none;
			}
			.prad-wc-install.wc-install .dashicons {
				display: none;
				animation: dashicons-spin 1s infinite;
				animation-timing-function: linear;
			}
			.prad-wc-install.wc-install.loading .dashicons {
				display: inline-block;
				margin-right: 5px !important;
			}
			@keyframes dashicons-spin {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}
			.prad-wc-install .wc-dismiss-notice {
				position: relative;
				text-decoration: none;
				float: right;
				right: 5px;
				display: flex;
				align-items: center;
			}
			.prad-wc-install .wc-dismiss-notice .dashicons {
				display: flex;
				text-decoration: none;
				animation: none;
				align-items: center;
			}
			.prad-pro-notice {
				position: relative;
				border-left: 3px solid #86a62c;
			}
			.prad-pro-notice .prad-install-body h3 {
				font-size: 20px;
				margin-bottom: 5px !important;
			}
			.prad-pro-notice .prad-install-body>div {
				max-width: 800px;
				margin-bottom: 0 !important;
			}
			.prad-pro-notice .button-hero {
				padding: 8px 14px !important;
				min-height: inherit !important;
				line-height: 1 !important;
				box-shadow: none;
				border: none;
				transition: 400ms;
				background: #46b450;
			}
			.prad-pro-notice .button-hero:hover,
			.wp-core-ui .prad-pro-notice .button-hero:active {
				background: #389e41;
			}
			.prad-pro-notice .prad-btn-notice-pro {
				background: #e5561e;
				color: #fff;
			}
			.prad-pro-notice .prad-btn-notice-pro:hover,
			.prad-pro-notice .prad-btn-notice-pro:focus {
				background: #ce4b18;
			}
			.prad-pro-notice .button-hero:hover,
			.prad-pro-notice .button-hero:focus {
				border: none;
				box-shadow: none;
			}
			.prad-pro-notice .prad-promotional-dismiss-notice {
				background-color: #000000;
				padding-top: 0px !important;
				position: absolute;
				right: 0;
				top: 0px;
				padding: 10px 10px 14px !important;
				border-radius: 0 0 0 4px;
				border: 1px solid;
				display: inline-block;
				color: #fff;
			}
			.prad-eid-notice p {
				margin: 0 !important;
				color: #f7f7f7;
				font-size: 16px;
			}
			.prad-eid-notice p.prad-eid-offer {
				color: #fff;
				font-weight: 700;
				font-size: 18px;
			}
			.prad-eid-notice p.prad-eid-offer a {
				background-color: #ffc160;
				padding: 8px 12px !important;
				border-radius: 4px;
				color: #000;
				font-size: 14px;
				margin-left: 3px !important;
				text-decoration: none;
				font-weight: 500;
				position: relative;
				top: -4px;
			}
			.prad-eid-notice p.prad-eid-offer a:hover {
				background-color: #edaa42;
			}
			.prad-install-body .prad-promotional-dismiss-notice {
				right: 4px;
				top: 3px;
				border-radius: unset !important;
				padding: 10px 8px 12px !important;
				text-decoration: none;
			}
			.prad-notice {
				background: #fff;
				border: 1px solid #c3c4c7;
				border-left-color: #037FFF !important;
				border-left-width: 4px;
				border-radius: 4px 0px 0px 4px;
				box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
				padding: 0px !important;
				margin: 40px 20px 0 2px !important;
				clear: both;
			}
			.prad-notice .prad-notice-container {
				display: flex;
				width: 100%;
			}
			.prad-notice .prad-notice-container a {
				text-decoration: none;
			}
			.prad-notice .prad-notice-container a:visited {
				color: white;
			}
			.prad-notice .prad-notice-container img {
				width: 100%;
				max-width: 30px !important;
				padding: 12px !important;
			}
			.prad-notice .prad-notice-image {
				display: flex;
				align-items: center;
				flex-direction: column;
				justify-content: center;
				background-color: #f4f4ff;
			}
			.prad-notice .prad-notice-image img {
				max-width: 100%;
			}
			.prad-notice .prad-notice-content {
				width: 100%;
				margin: 5px !important;
				padding: 8px !important;
				display: flex;
				flex-direction: column;
				gap: 0px;
			}
			.prad-notice .prad-notice-prad-button {
				max-width: fit-content;
				text-decoration: none;
				padding: 7px 12px !important;
				font-size: 12px;
				color: white;
				border: none;
				border-radius: 2px;
				cursor: pointer;
				margin-top: 6px !important;
				background-color: #e5561e;
			}
			.prad-notice-heading {
				font-size: 18px;
				font-weight: 500;
				color: #1b2023;
			}
			.prad-notice-content-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}
			.prad-notice-close .dashicons-no-alt {
				font-size: 25px;
				height: 26px;
				width: 25px;
				cursor: pointer;
				color: #585858;
			}
			.prad-notice-close .dashicons-no-alt:hover {
				color: red;
			}
			.prad-notice-content-body {
				font-size: 12px;
				color: #343b40;
			}
			.prad-bold {
				font-weight: bold;
			}
			a.prad-pro-dismiss:focus {
				outline: none;
				box-shadow: unset;
			}
			.prad-free-notice .loading,
			.prad-notice .loading {
				width: 16px;
				height: 16px;
				border: 3px solid #FFF;
				border-bottom-color: transparent;
				border-radius: 50%;
				display: inline-block;
				box-sizing: border-box;
				animation: rotation 1s linear infinite;
				margin-left: 10px !important;
			}
			a.prad-notice-prad-button:hover {
				color: #fff !important;
			}
			.prad-notice .prad-link-wrap {
				margin-top: 10px !important;
			}
			.prad-notice .prad-link-wrap a {
				margin-right: 4px !important;
			}
			.prad-notice .prad-link-wrap a:hover {
				background-color: #ce4b18;
			}
			body .prad-notice .prad-link-wrap>a.prad-notice-skip {
				background: none !important;
				border: 1px solid #e5561e;
				color: #e5561e;
				padding: 6px 15px !important;
			}
			body .prad-notice .prad-link-wrap>a.prad-notice-skip:hover {
				background: #ce4b18 !important;
			}
			@keyframes rotation {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}

			.prad-install-btn-wrap {
				display: flex;
				align-items: stretch;
				gap: 10px;
			}
			.prad-install-btn-wrap .prad-install-cancel {
				position: static !important;
				padding: 3px 20px;
				border: 1px solid #a0a0a0;
				border-radius: 2px;
			}
		</style>
		<?php
	}

	/**
	 * Installation Notice JS
	 *
	 * @since v.1.0.0
	 */
	public function install_notice_js() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				'use strict';
				$(document).on('click', '.wc-install-btn.prad-install-btn', function(e) {
					e.preventDefault();
					const $that = $(this);
					console.log($that.attr('data-plugin-slug'));
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							install_plugin: $that.attr('data-plugin-slug'),
							action: 'prad_install'
						},
						beforeSend: function() {
							$that.parents('.wc-install').addClass('loading');
						},
						success: function(response) {
							window.location.reload()
						},
						complete: function() {
							// $that.parents('.wc-install').removeClass('loading');
						}
					});
				});
			});
		</script>
		<?php
	}
}
