<?php // phpcs:ignore
/**
 * Unary operator node (prefix + / -).
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula\Ast;

use PRAD\Includes\Common\Formula\Abstract_Expression_Engine;
use PRAD\Includes\Common\Formula\Expression_Exception;

defined( 'ABSPATH' ) || exit;

final class Expression_Unary_Node implements Expression_Node {
	/** @var string */
	private $op;
	/** @var Expression_Node */
	private $right;

	public function __construct( $op, Expression_Node $right ) {
		$this->op    = (string) $op;
		$this->right = $right;
	}

	public function evaluate( Abstract_Expression_Engine $engine, array $context = array() ) {
		$val = $engine->to_number( $this->right->evaluate( $engine, $context ) );
		if ( '+' === $this->op ) {
			return +$val;
		}
		if ( '-' === $this->op ) {
			return -$val;
		}
		throw new Expression_Exception( 'Unsupported unary operator: ' . $this->op );
	}
}
