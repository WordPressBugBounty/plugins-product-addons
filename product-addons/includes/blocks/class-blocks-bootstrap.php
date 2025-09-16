<?php
/**
 * Blocks Bootstrap Class
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks;

use PRAD\Includes\Blocks\Factories\Block_Factory;
use PRAD\Includes\Services\Product_Blocks_Service;

defined( 'ABSPATH' ) || exit;

/**
 * Bootstrap class to initialize the blocks system
 */
class Blocks_Bootstrap {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Block system initialized flag
	 *
	 * @var bool
	 */
	private bool $initialized = false;

	/**
	 * Get singleton instance
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize the blocks system
	 */
	public function init(): void {
		if ( $this->initialized ) {
			return;
		}

		// Register default blocks
		// $this->register_default_blocks();

		// Initialize main render blocks class
		$this->init_render_blocks();

		$this->initialized = true;

		do_action( 'prad_blocks_bootstrap_initialized' );
	}

	/**
	 * Register additional block types if they exist
	 */
	private function register_default_blocks(): void {
		$additional_blocks = array(
			'textfield' => 'PRAD\Includes\Blocks\Types\Textfield_Block',
			'radio'     => 'PRAD\Blocks\Types\Radio_Block',
		);

		foreach ( $additional_blocks as $type => $class ) {
			if ( class_exists( $class ) ) {
				Block_Factory::register_block( $type, $class );
			}
		}

		// Pro blocks (if available)
		if ( product_addons()->is_pro_feature_available() ) {
			$this->register_pro_blocks();
		}
	}

	/**
	 * Register pro blocks if available
	 */
	private function register_pro_blocks(): void {
		$pro_blocks = array(
			'switch'       => 'PRAD\Blocks\Types\Switch_Block',
			'color_switch' => 'PRAD\Blocks\Types\Color_Switch_Block',
			'img_switch'   => 'PRAD\Blocks\Types\Image_Switch_Block',
			'button'       => 'PRAD\Blocks\Types\Button_Block',
		);

		foreach ( $pro_blocks as $type => $class ) {
			if ( class_exists( $class ) ) {
				Block_Factory::register_block( $type, $class );
			}
		}
	}

	/**
	 * Initialize main render blocks controller
	 */
	private function init_render_blocks(): void {
		if ( class_exists( 'PRAD\Includes\Blocks\Render_Product_Fields' ) ) {
			new Render_Product_Fields();
		}
	}

	/**
	 * Maybe clear cache when posts are saved
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 */
	public function maybe_clear_cache( int $post_id, \WP_Post $post ): void {
		// Clear cache for addon posts
		if ( $post->post_type === 'prad_addon' ) {
			$this->clear_addon_cache( $post_id );
		}

		// Clear cache for product posts
		if ( $post->post_type === 'product' ) {
			$this->clear_product_cache( $post_id );
		}
	}

	/**
	 * Clear cache when posts are deleted
	 *
	 * @param int $post_id
	 */
	public function maybe_clear_cache_on_delete( int $post_id ): void {
		$post_type = get_post_type( $post_id );

		if ( $post_type === 'prad_addon' ) {
			$this->clear_addon_cache( $post_id );
		} elseif ( $post_type === 'product' ) {
			$this->clear_product_cache( $post_id );
		}
	}

	/**
	 * Clear addon cache
	 *
	 * @param int $addon_id
	 */
	private function clear_addon_cache( int $addon_id ): void {
		if ( class_exists( 'PRAD\Services\Product_Blocks_Service' ) ) {
			$service = new Product_Blocks_Service();
			$service->invalidate_addon_cache( $addon_id );
		}
	}

	/**
	 * Clear product cache
	 *
	 * @param int $product_id
	 */
	public function clear_product_cache( int $product_id ): void {
		if ( class_exists( 'PRAD\Services\Product_Blocks_Service' ) ) {
			$service = new Product_Blocks_Service();
			$service->clear_product_cache( $product_id );
		}
	}

	/**
	 * Get initialization status
	 *
	 * @return bool
	 */
	public function is_initialized(): bool {
		return $this->initialized;
	}

	/**
	 * Get registered block types
	 *
	 * @return array
	 */
	public function get_registered_blocks(): array {
		return Block_Factory::get_registered_blocks();
	}

	/**
	 * Force re-initialization (useful for testing)
	 */
	public function force_reinit(): void {
		$this->initialized = false;
		$this->init();
	}
}
