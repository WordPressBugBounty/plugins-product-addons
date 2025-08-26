<?php
/**
 * Plugin Name: WowAddons - Product Addons for WooCommerce
 * Description: The ultimate WooCommerce product addons plugin to add extra product options, including, swatches, image uploads, text area, and more!
 * Version:     1.0.14
 * Author:      WPXPO
 * Author URI:  https://www.wpxpo.com/about
 * Text Domain: product-addons
 * License:     GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WowAddons
 */

defined( 'ABSPATH' ) || exit;

// Define Vars.
define( 'PRAD_VER', '1.0.14' );
define( 'PRAD_URL', plugin_dir_url( __FILE__ ) );
define( 'PRAD_BASE', plugin_basename( __FILE__ ) );
define( 'PRAD_PATH', plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'product_addons' ) ) {
	function product_addons() {
		require_once PRAD_PATH . 'includes/common/Functions.php';
		return new \PRAD\Includes\Functions();
	}
}

// Plugin Initialization.
if ( ! class_exists( 'PRAD_Initialization' ) ) {
	require_once PRAD_PATH . 'includes/class-initialization.php';
	new \PRAD\PRAD_Initialization();
}
