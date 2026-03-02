<?php // phpcs:ignore
/**
 * Array-backed expression engine.
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula;

defined( 'ABSPATH' ) || exit;

/**
 * Resolves [placeholders] from an associative array.
 */
class Array_Expression_Engine extends Abstract_Expression_Engine {

	/**
	 * @param string $expression Expression to evaluate.
	 * @param array  $dynamics   Map of placeholder name => value.
	 *
	 * @return mixed
	 * @throws Expression_Exception
	 */
	public static function evaluate_expression( $expression, array $dynamics = array() ) {
		$engine = new self();
		return $engine->evaluate( $expression, $dynamics );
	}

	/**
	 * Evaluate an expression, returning 0 when the expression is invalid.
	 *
	 * Useful when you prefer a soft-fail behavior instead of surfacing parser errors.
	 *
	 * @param string $expression Expression to evaluate.
	 * @param array  $dynamics   Map of placeholder name => value.
	 *
	 * @return mixed
	 */
	public static function evaluate_expression_safe( $expression, array $dynamics = array() ) {
		try {
			return self::evaluate_expression( $expression, $dynamics );
		} catch ( Expression_Exception $e ) {
			return 0;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_dynamic_value( $name, array $context = array() ) {
		if ( array_key_exists( $name, $context ) ) {
			return $context[ $name ];
		}
		// By convention, missing variables are 0.
		return 0;
	}
}
