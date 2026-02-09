<?php
/**
 * Plugin Name: WowAddons - Product Addons for WooCommerce
 * Description: The ultimate WooCommerce product addons plugin to add extra product options, including, swatches, image uploads, text area, and more!
 * Version:     1.5.10
 * Author:      WPXPO
 * Author URI:  https://www.wpxpo.com/about
 * Text Domain: product-addons
 * License:     GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WowAddons
 */

use PRAD\Includes\Blocks\Blocks_Bootstrap;
use PRAD\Includes\Common\Functions;
use PRAD\Includes\Initialization;

defined( 'ABSPATH' ) || exit;

// Define Vars.
define( 'PRAD_VER', '1.5.10' );
define( 'PRAD_URL', plugin_dir_url( __FILE__ ) );
define( 'PRAD_BASE', plugin_basename( __FILE__ ) );
define( 'PRAD_PATH', plugin_dir_path( __FILE__ ) );

spl_autoload_register( 'prad_autoloader' );

if ( ! function_exists( 'product_addons' ) ) {
	function product_addons() {
		return new Functions();
	}
}

add_action( 'plugins_loaded', 'prad_init', 10 );
function prad_init() {
	new Initialization();
	$bootstrap = Blocks_Bootstrap::get_instance();
	$bootstrap->init();
}

function prad_autoloader( $class_name ) {
	$namespace = 'PRAD\\';
	$base_dir  = trailingslashit( PRAD_PATH );

	$len = strlen( $namespace );
	if ( strncmp( $namespace, $class_name, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class_name, $len );
	$segments       = explode( '\\', $relative_class );

	$file_name = array_pop( $segments );
	$subfolder = strtolower(
		implode(
			'/',
			array_map(
				function ( $segment ) {
					return str_replace( '_', '-', $segment );
				},
				$segments
			)
		)
	);

	$prefix    = ( strpos( $subfolder, 'traits' ) !== false ) ? 'trait-' : 'class-';
	$file_name = strtolower(
		preg_replace(
			'/([a-z])([A-Z])/',
			'$1-$2',
			str_replace( '_', '-', $file_name )
		)
	);

	$file = rtrim( $base_dir . $subfolder, '/' ) . '/' . $prefix . $file_name . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
