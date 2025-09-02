<?php
/**
 * Block Factory
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Factories;

use PRAD\Includes\Blocks\Interfaces\Block_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Factory class for creating block instances
 */
class Block_Factory {

	/**
	 * Registered block types
	 *
	 * @var array
	 */
	private static array $block_types = array();

	/**
	 * Block instances cache
	 *
	 * @var array
	 */
	private static array $instances = array();

	/**
	 * Register a block type
	 *
	 * @param string $type Block type identifier
	 * @param string $class_name Full class name
	 * @throws \InvalidArgumentException If class doesn't implement Block_Interface
	 */
	public static function register_block( string $type, string $class_name ): void {
		if ( ! class_exists( $class_name ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Block class %s does not exist', $class_name )
			);
		}

		if ( ! is_subclass_of( $class_name, Block_Interface::class ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Block class %s must implement Block_Interface', $class_name )
			);
		}

		self::$block_types[ $type ] = $class_name;

		do_action( 'prad_block_registered', $type, $class_name );
	}

	/**
	 * Create a block instance
	 *
	 * @param string $type Block type
	 * @param array  $data Block configuration data
	 * @param int    $product_id Product ID
	 * @return Block_Interface|null
	 */
	public static function create_block( string $type, array $data, int $product_id ): ?Block_Interface {

		// if ( ! isset( self::$block_types[ $type ] ) ) {
		// do_action( 'prad_unknown_block_type', $type, $data );
		// return null;
		// }

		// $class_name = self::$block_types[ $type ];

		$class_name = self::get_block_class_name_by_type( $type );

		if ( ! $class_name || ! class_exists( $class_name ) ) {
			return null;
		}

		try {
			$block = new $class_name( $data, $product_id );

			// Apply filters to allow modification
			$block = apply_filters( 'prad_block_created', $block, $type, $data, $product_id );
			$block = apply_filters( "prad_block_created_{$type}", $block, $data, $product_id );

			return $block;

		} catch ( \Exception $e ) {
			error_log(
				sprintf(
					'PRAD Block Factory Error: Failed to create block type "%s". Error: %s',
					$type,
					$e->getMessage()
				)
			);

			do_action( 'prad_block_creation_failed', $type, $data, $e );

			return null;
		}
	}

	/**
	 * Get ClassName by block type
	 *
	 * @return string
	 */
	public static function get_block_class_name_by_type( $type ) {
		$blocks_array = array(
			'textfield'      => 'PRAD\Includes\Blocks\Types\Textfield_Block',
			'section'        => 'PRAD\Includes\Blocks\Types\Section_Block',
			'radio'          => 'PRAD\Includes\Blocks\Types\Radio_Block',
			'checkbox'       => 'PRAD\Includes\Blocks\Types\Checkbox_Block',
			'custom_formula' => 'PRAD\Includes\Blocks\Types\Custom_Formula_Block',
			'switch'         => 'PRAD\Includes\Blocks\Types\Switch_Block',
			'select'         => 'PRAD\Includes\Blocks\Types\Select_Block',
			'products'       => 'PRAD\Includes\Blocks\Types\Products_Block',
			'upload'         => 'PRAD\Includes\Blocks\Types\Upload_Block',
			'button'         => 'PRAD\Includes\Blocks\Types\Button_Block',
			'img_switch'     => 'PRAD\Includes\Blocks\Types\Image_Switch_Block',
			'color_switch'   => 'PRAD\Includes\Blocks\Types\Color_Switch_Block',
			'color_picker'   => 'PRAD\Includes\Blocks\Types\Color_Picker_Block',
			'date'           => 'PRAD\Includes\Blocks\Types\Date_Block',
			'time'           => 'PRAD\Includes\Blocks\Types\Time_Block',
			'range'          => 'PRAD\Includes\Blocks\Types\Range_Block',
			'url'            => 'PRAD\Includes\Blocks\Types\Url_Block',
			'email'          => 'PRAD\Includes\Blocks\Types\Email_Block',
			'number'         => 'PRAD\Includes\Blocks\Types\Number_Block',
			'telephone'      => 'PRAD\Includes\Blocks\Types\Telephone_Block',
			'textarea'       => 'PRAD\Includes\Blocks\Types\Textarea_Block',
			'heading'        => 'PRAD\Includes\Blocks\Types\Heading_Block',
			'shortcode'      => 'PRAD\Includes\Blocks\Types\Shortcode_Block',
			'separator'      => 'PRAD\Includes\Blocks\Types\Separator_Block',
			'spacer'         => 'PRAD\Includes\Blocks\Types\Spacer_Block',
		);

		if ( product_addons()->is_pro_feature_available() ) {
			$blocks_array['button']       = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Button_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Button_Block' : $blocks_array['button'];
			$blocks_array['checkbox']     = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Checkbox_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Checkbox_Block' : $blocks_array['checkbox'];
			$blocks_array['color_switch'] = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Color_Switch_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Color_Switch_Block' : $blocks_array['color_switch'];
			$blocks_array['img_switch']   = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Image_Switch_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Image_Switch_Block' : $blocks_array['img_switch'];
			$blocks_array['switch']       = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Switch_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Switch_Block' : $blocks_array['switch'];
			$blocks_array['upload']       = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Upload_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Upload_Block' : $blocks_array['upload'];
			$blocks_array['radio']        = class_exists( 'PRAD_PRO_Block\Frontend\Blocks\Types\Radio_Block' ) ? 'PRAD_PRO_Block\Frontend\Blocks\Types\Radio_Block' : $blocks_array['radio'];
		}

		return $blocks_array[ $type ] ?? null;
	}

	/**
	 * Get all registered block types
	 *
	 * @return array
	 */
	public static function get_registered_blocks(): array {
		return self::$block_types;
	}

	/**
	 * Check if a block type is registered
	 *
	 * @param string $type Block type
	 * @return bool
	 */
	public static function is_registered( string $type ): bool {
		return isset( self::$block_types[ $type ] );
	}

	/**
	 * Unregister a block type
	 *
	 * @param string $type Block type
	 * @return bool True if unregistered, false if not found
	 */
	public static function unregister_block( string $type ): bool {
		if ( isset( self::$block_types[ $type ] ) ) {
			unset( self::$block_types[ $type ] );
			do_action( 'prad_block_unregistered', $type );
			return true;
		}

		return false;
	}

	/**
	 * Get class name for a block type
	 *
	 * @param string $type Block type
	 * @return string|null
	 */
	public static function get_block_class( string $type ): ?string {
		return self::$block_types[ $type ] ?? null;
	}

	/**
	 * Create multiple blocks from data array
	 *
	 * @param array $blocks_data Array of block data
	 * @param int   $product_id Product ID
	 * @return array Array of Block_Interface instances
	 */
	public static function create_blocks( array $blocks_data, int $product_id ): array {
		$blocks = array();

		foreach ( $blocks_data as $block_data ) {
			$type = $block_data['type'] ?? '';

			if ( empty( $type ) ) {
				continue;
			}

			$block = self::create_block( $type, $block_data, $product_id );

			if ( $block ) {
				$blocks[] = $block;
			}
		}

		return $blocks;
	}

	/**
	 * Clear all registered blocks (useful for testing)
	 */
	public static function clear_blocks(): void {
		self::$block_types = array();
		self::$instances   = array();
	}

	/**
	 * Get block types by category or filter
	 *
	 * @param callable|null $filter Optional filter callback
	 * @return array
	 */
	public static function get_blocks_by_filter( ?callable $filter = null ): array {
		if ( $filter === null ) {
			return self::$block_types;
		}

		return array_filter( self::$block_types, $filter, ARRAY_FILTER_USE_BOTH );
	}

	/**
	 * Register multiple blocks at once
	 *
	 * @param array $blocks Associative array of type => class_name
	 */
	public static function register_blocks( array $blocks ): void {
		foreach ( $blocks as $type => $class_name ) {
			self::register_block( $type, $class_name );
		}
	}
}
