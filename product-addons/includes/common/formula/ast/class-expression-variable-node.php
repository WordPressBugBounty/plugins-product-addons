<?php // phpcs:ignore
/**
 * Dynamic placeholder node (e.g. [product_price]).
 *
 * @package PRAD
 * @since 1.5.8
 */

namespace PRAD\Includes\Common\Formula\Ast;

use PRAD\Includes\Common\Formula\Abstract_Expression_Engine;

defined( 'ABSPATH' ) || exit;

final class Expression_Variable_Node implements Expression_Node {
	/** @var string */
	private $name;
	/** @var int */
	private $pos;

	public function __construct( $name, $pos ) {
		$this->name = (string) $name;
		$this->pos  = (int) $pos;
	}

	public function evaluate( Abstract_Expression_Engine $engine, array $context = array() ) {
		return $engine->get_dynamic_value( $this->name, $context );
	}
}
