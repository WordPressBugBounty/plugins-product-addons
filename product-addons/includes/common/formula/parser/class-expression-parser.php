<?php // phpcs:ignore
/**
 * Recursive descent expression parser.
 *
 * Precedence (high -> low):
 * - unary + -
 * - * /
 * - + -
 * - comparisons: > < >= <= != =
 * - & (logical AND)
 * - || (logical OR)
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula\Parser;

use PRAD\Includes\Common\Formula\Ast\Expression_Binary_Node;
use PRAD\Includes\Common\Formula\Ast\Expression_Function_Node;
use PRAD\Includes\Common\Formula\Ast\Expression_Number_Node;
use PRAD\Includes\Common\Formula\Ast\Expression_Node;
use PRAD\Includes\Common\Formula\Ast\Expression_Unary_Node;
use PRAD\Includes\Common\Formula\Ast\Expression_Variable_Node;
use PRAD\Includes\Common\Formula\Expression_Exception;
use PRAD\Includes\Common\Formula\Lexer\Expression_Token;

defined( 'ABSPATH' ) || exit;

/**
 * Parses a token stream into an AST.
 */
final class Expression_Parser {
	/** @var Expression_Token[] */
	private $tokens;
	/** @var int */
	private $i = 0;

	public function __construct( array $tokens ) {
		$this->tokens = $tokens;
	}

	/**
	 * @return Expression_Node
	 * @throws Expression_Exception
	 */
	public function parse() {
		$node = $this->parse_or();
		$this->expect( Expression_Token::T_EOF );
		return $node;
	}

	private function current() {
		return $this->tokens[ $this->i ];
	}

	private function advance() {
		++$this->i;
		return $this->tokens[ $this->i - 1 ];
	}

	private function match( $type, $value = null ) {
		$t = $this->current();
		if ( $t->type !== $type ) {
			return false;
		}
		if ( null !== $value && $t->value !== $value ) {
			return false;
		}
		$this->advance();
		return $t;
	}

	private function expect( $type, $value = null ) {
		$t = $this->current();
		if ( ! $this->match( $type, $value ) ) {
			$want = $type . ( null !== $value ? ( ':' . $value ) : '' );
			$got  = $t->type . ( null !== $t->value ? ( ':' . $t->value ) : '' );
			throw new Expression_Exception( 'Expected ' . $want . ' but got ' . $got . ' at position ' . $t->pos );
		}
		return $t;
	}

	private function parse_or() {
		$node = $this->parse_and();
		while ( $this->match( Expression_Token::T_OPERATOR, '||' ) ) {
			$right = $this->parse_and();
			$node  = new Expression_Binary_Node( '||', $node, $right );
		}
		return $node;
	}

	private function parse_and() {
		$node = $this->parse_compare();
		while ( $this->match( Expression_Token::T_OPERATOR, '&' ) ) {
			$right = $this->parse_compare();
			$node  = new Expression_Binary_Node( '&', $node, $right );
		}
		return $node;
	}

	private function parse_compare() {
		$node = $this->parse_add();

		while ( true ) {
			$t = $this->current();
			if ( Expression_Token::T_OPERATOR !== $t->type ) {
				break;
			}
			$op = $t->value;
			if ( ! in_array( $op, array( '>', '<', '>=', '<=', '!=', '=' ), true ) ) {
				break;
			}
			$this->advance();
			$right = $this->parse_add();
			$node  = new Expression_Binary_Node( $op, $node, $right );
		}

		return $node;
	}

	private function parse_add() {
		$node = $this->parse_mul();
		while ( true ) {
			if ( $this->match( Expression_Token::T_OPERATOR, '+' ) ) {
				$right = $this->parse_mul();
				$node  = new Expression_Binary_Node( '+', $node, $right );
				continue;
			}
			if ( $this->match( Expression_Token::T_OPERATOR, '-' ) ) {
				$right = $this->parse_mul();
				$node  = new Expression_Binary_Node( '-', $node, $right );
				continue;
			}
			break;
		}
		return $node;
	}

	private function parse_mul() {
		$node = $this->parse_unary();
		while ( true ) {
			if ( $this->match( Expression_Token::T_OPERATOR, '*' ) ) {
				$right = $this->parse_unary();
				$node  = new Expression_Binary_Node( '*', $node, $right );
				continue;
			}
			if ( $this->match( Expression_Token::T_OPERATOR, '/' ) ) {
				$right = $this->parse_unary();
				$node  = new Expression_Binary_Node( '/', $node, $right );
				continue;
			}
			break;
		}
		return $node;
	}

	private function parse_unary() {
		if ( $this->match( Expression_Token::T_OPERATOR, '+' ) ) {
			return new Expression_Unary_Node( '+', $this->parse_unary() );
		}
		if ( $this->match( Expression_Token::T_OPERATOR, '-' ) ) {
			return new Expression_Unary_Node( '-', $this->parse_unary() );
		}
		return $this->parse_primary();
	}

	private function parse_primary() {
		$t = $this->current();

		if ( $this->match( Expression_Token::T_NUMBER ) ) {
			return new Expression_Number_Node( $t->value );
		}

		if ( $this->match( Expression_Token::T_VARIABLE ) ) {
			return new Expression_Variable_Node( $t->value, $t->pos );
		}

		if ( $this->match( Expression_Token::T_IDENTIFIER ) ) {
			$name = $t->value;
			$pos  = $t->pos;

			// Identifier must be a function call.
			$this->expect( Expression_Token::T_LPAREN );
			$args = array();
			if ( ! $this->match( Expression_Token::T_RPAREN ) ) {
				$args[] = $this->parse_or();
				while ( $this->match( Expression_Token::T_COMMA ) ) {
					$args[] = $this->parse_or();
				}
				$this->expect( Expression_Token::T_RPAREN );
			}
			return new Expression_Function_Node( $name, $args, $pos );
		}

		if ( $this->match( Expression_Token::T_LPAREN ) ) {
			$node = $this->parse_or();
			$this->expect( Expression_Token::T_RPAREN );
			return $node;
		}

		throw new Expression_Exception( 'Unexpected token ' . $t->type . ' at position ' . $t->pos );
	}
}
