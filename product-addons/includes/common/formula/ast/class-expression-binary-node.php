<?php // phpcs:ignore
/**
 * Binary operator node.
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula\Ast;

use PRAD\Includes\Common\Formula\Abstract_Expression_Engine;
use PRAD\Includes\Common\Formula\Expression_Exception;

defined( 'ABSPATH' ) || exit;

final class Expression_Binary_Node implements Expression_Node {
	/** @var string */
	private $op;
	/** @var Expression_Node */
	private $left;
	/** @var Expression_Node */
	private $right;

	public function __construct( $op, Expression_Node $left, Expression_Node $right ) {
		$this->op    = (string) $op;
		$this->left  = $left;
		$this->right = $right;
	}

	public function evaluate( Abstract_Expression_Engine $engine, array $context = array() ) {
		$op = $this->op;

		if ( '||' === $op ) {
			return $engine->to_bool( $this->left->evaluate( $engine, $context ) ) || $engine->to_bool( $this->right->evaluate( $engine, $context ) );
		}
		if ( '&' === $op ) {
			return $engine->to_bool( $this->left->evaluate( $engine, $context ) ) && $engine->to_bool( $this->right->evaluate( $engine, $context ) );
		}

		$left  = $this->left->evaluate( $engine, $context );
		$right = $this->right->evaluate( $engine, $context );

		if ( '>' === $op ) {
			return $engine->to_number( $left ) > $engine->to_number( $right );
		}
		if ( '<' === $op ) {
			return $engine->to_number( $left ) < $engine->to_number( $right );
		}
		if ( '>=' === $op ) {
			return $engine->to_number( $left ) >= $engine->to_number( $right );
		}
		if ( '<=' === $op ) {
			return $engine->to_number( $left ) <= $engine->to_number( $right );
		}
		if ( '!=' === $op ) {
			return $engine->to_number( $left ) != $engine->to_number( $right );
		}
		if ( '=' === $op ) {
			return $engine->to_number( $left ) == $engine->to_number( $right );
		}

		$l = $engine->to_number( $left );
		$r = $engine->to_number( $right );

		if ( '+' === $op ) {
			return $l + $r;
		}
		if ( '-' === $op ) {
			return $l - $r;
		}
		if ( '*' === $op ) {
			return $l * $r;
		}
		if ( '/' === $op ) {
			if ( 0.0 === $r ) {
				throw new Expression_Exception( 'Division by zero.' );
			}
			return $l / $r;
		}

		throw new Expression_Exception( 'Unsupported operator: ' . $op );
	}
}
