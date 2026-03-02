<?php // phpcs:ignore
/**
 * Number literal node.
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula\Ast;

use PRAD\Includes\Common\Formula\Abstract_Expression_Engine;

defined( 'ABSPATH' ) || exit;

final class Expression_Number_Node implements Expression_Node {
	/** @var float */
	private $value;

	public function __construct( $value ) {
		$this->value = (float) $value;
	}

	public function evaluate( Abstract_Expression_Engine $engine, array $context = array() ) {
		return $this->value;
	}
}
