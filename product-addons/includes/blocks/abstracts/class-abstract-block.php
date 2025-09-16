<?php
/**
 * Abstract Block Base Class
 *
 * @package PRAD
 * @since 1.0.0
 */

namespace PRAD\Includes\Blocks\Abstracts;

use PRAD\Includes\Blocks\Interfaces\Block_Interface;
use PRAD\Includes\Traits\Attribute_Builder;
use PRAD\Includes\Traits\Price_Handler;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract base class for all blocks
 */
abstract class Abstract_Block implements Block_Interface {

	use Attribute_Builder;
	use Price_Handler;

	/**
	 * Block data
	 *
	 * @var array
	 */
	protected array $data;

	/**
	 * Product ID
	 *
	 * @var int
	 */
	protected int $product_id;

	/**
	 * Allowed HTML tags for wp_kses
	 *
	 * @var array
	 */
	protected array $allowed_html_tags;

	/**
	 * Constructor
	 *
	 * @param array $data Block configuration data
	 * @param int   $product_id WooCommerce product ID
	 */
	public function __construct( array $data, int $product_id ) {
		$this->data              = $data;
		$this->product_id        = $product_id;
		$this->allowed_html_tags = apply_filters( 'get_prad_allowed_html_tags', array() );

		$this->init();
	}

	/**
	 * Initialize block-specific setup
	 */
	protected function init(): void {
		// Override in child classes if needed
	}

	/**
	 * Get property from block data
	 *
	 * @param string $key Property key
	 * @param mixed  $default Default value
	 * @return mixed
	 */
	protected function get_property( string $key, $default = '' ) {
		return $this->data[ $key ] ?? $default;
	}

	/**
	 * Get property from block data
	 *
	 * @return array
	 */
	protected function get_field_options( $handle_pro = false ) {
		$options = $this->get_property( '_options', array() );

		if ( $handle_pro && ! product_addons()->is_pro_feature_available() && is_array( $options ) && count( $options ) > 3 ) {
			$options = array_slice( $options, 0, 3 );
		}
		return $options;
	}

	/**
	 * Get block ID
	 *
	 * @return string
	 */
	protected function get_block_id(): string {
		return $this->get_property( 'blockid', '' );
	}

	/**
	 * Get block label
	 *
	 * @return string
	 */
	protected function get_label(): string {
		return $this->get_property( 'label', '' );
	}

	/**
	 * Check if block is required
	 *
	 * @return bool
	 */
	protected function is_required(): bool {
		return (bool) $this->get_property( 'required', false );
	}

	/**
	 * Check if block is hidden
	 *
	 * @return bool
	 */
	protected function is_title_hidden(): bool {
		return (bool) $this->get_property( 'hide', false );
	}

	/**
	 * Get block description
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return $this->get_property( 'description', '' );
	}

	/**
	 * Get CSS class
	 *
	 * @return string
	 */
	protected function get_css_class(): string {
		return $this->get_property( 'class', '' );
	}

	/**
	 * Get section ID
	 *
	 * @return string
	 */
	protected function get_section_id(): string {
		return $this->get_property( 'sectionid', '' );
	}

	/**
	 * Check if logic is enabled
	 *
	 * @return bool
	 */
	protected function is_logic_enabled(): bool {
		return (bool) $this->get_property( 'en_logic', false );
	}

	/**
	 * Get field conditions
	 *
	 * @return array
	 */
	protected function get_field_conditions(): array {
		return $this->get_property( 'fieldConditions', array() );
	}

	/**
	 * Get common HTML attributes for the block
	 *
	 * @return array
	 */
	protected function get_common_attributes(): array {
		$css_classes = array(
			'prad-parent',
			'prad-block-' . $this->get_type(),
			'prad-block-' . $this->get_block_id(),
			$this->get_css_class(),
		);

		$data_attributes = array(
			'bid'             => $this->get_block_id(),
			'sectionid'       => $this->get_section_id(),
			'label'           => $this->get_label(),
			'btype'           => $this->get_type(),
			'enlogic'         => $this->is_logic_enabled() ? 'yes' : 'no',
			'required'        => $this->is_required() ? 'yes' : 'no',
			'fieldconditions' => $this->get_field_conditions(),
			'defval'          => $this->get_property( 'defval', null ),
		);

		return array_merge(
			array(
				'class' => $this->build_css_classes( $css_classes ),
				'id'    => 'prad-bid-' . $this->get_block_id(),
			),
			$this->build_data_attributes( $data_attributes )
		);
	}

	/**
	 * Render block title section
	 *
	 * @param array|null $price_info Price information
	 * @return string
	 */
	protected function render_title_section( ?array $price_info = null ): string {
		if ( $this->is_title_hidden() && ( ! $price_info || ! $this->should_show_price_with_title( $price_info ) ) ) {
			return '';
		}

		$html = '<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">';

		// Title and required indicator
		if ( ! $this->is_title_hidden() ) {
			$html .= $this->render_title_with_required();
		}

		// Price with title
		if ( $price_info && $this->should_show_price_with_title( $price_info ) ) {
			$html .= $this->render_price_html( $price_info, 'with_title' );
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render title with required indicator
	 *
	 * @return string
	 */
	protected function render_title_with_required(): string {
		$html  = '<div class="prad-relative prad-w-fit">';
		$html .= '<div class="prad-block-title">' . wp_kses( $this->get_label(), $this->allowed_html_tags ) . '</div>';

		if ( $this->is_required() ) {
			$html .= '<div class="prad-block-required prad-absolute">*</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render block description
	 *
	 * @return string
	 */
	protected function render_description(): string {
		if ( $this->is_title_hidden() || ! $this->get_description() ) {
			return '';
		}

		return sprintf(
			'<div class="prad-block-description prad-mb-12">%s</div>',
			wp_kses( $this->get_description(), $this->allowed_html_tags )
		);
	}

	/**
	 * Get block configuration
	 *
	 * @return array
	 */
	public function get_config(): array {
		return $this->data;
	}

	/**
	 * Check if block should be displayed based on conditions
	 *
	 * @return bool
	 */
	public function should_display(): bool {
		// Basic display logic - override in child classes for specific conditions
		if ( $this->is_title_hidden() ) {
			return false;
		}

		// Add logic evaluation here if needed
		if ( $this->is_logic_enabled() && ! empty( $this->get_field_conditions() ) ) {
			return $this->evaluate_field_conditions();
		}

		return true;
	}

	/**
	 * Evaluate field conditions for conditional display
	 *
	 * @return bool
	 */
	protected function evaluate_field_conditions(): bool {
		// Implement conditional logic evaluation
		// This would need to be implemented based on your specific requirements
		return true;
	}

	/**
	 * Basic validation - override in specific blocks
	 *
	 * @return bool
	 */
	public function validate(): bool {
		if ( $this->is_required() ) {
			$value = $this->get_property( 'value' );
			if ( empty( $value ) && $value !== '0' && $value !== 0 ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get price for this block
	 *
	 * @return float
	 */
	public function get_price(): float {
		$options = $this->get_property( '_options', array() );
		if ( ! empty( $options ) && isset( $options[0] ) ) {
			$item = $options[0];
			if ( isset( $item->regular ) ) {
				return (float) $item->regular;
			}
		}

		return 0.0;
	}

	/**
	 * Render block content using common template
	 *
	 * @param object  $item Item to render
	 * @param integer $index Item index
	 * @param array   $price_info Price information
	 * @param string  $variation_html Optional variation HTML
	 * @return string Rendered content
	 */
	protected function render_block_content( $item, int $index, array $price_info, string $variation_html = '' ): string {
		$blockid      = $this->get_block_id();
		$enable_count = $this->get_property( 'enableCount', false );
		$min          = $this->get_property( 'min', 1 );
		$max          = $this->get_property( 'max', 100 );
		$allowed_tags = $this->allowed_html_tags;

		ob_start();
		?>
		<div class="prad-d-flex prad-flex-column prad-item-center prad-gap-2 prad-text-center prad-mt-8 prad-block-content-wrapper prad-effect-container">
			<div>
				<div title="<?php echo wp_kses( $item->value, $allowed_tags ); ?>" class="prad-block-content prad-ellipsis-2">
					<?php echo wp_kses( $item->value, $allowed_tags ); ?>
				</div>
				<?php if ( $item->type != 'no_cost' ) : ?>
					<div class="prad-block-price prad-text-upper">
						<?php echo wp_kses( $price_info['html'], $allowed_tags ); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
			if ( $variation_html ) :
				echo $variation_html;
			endif;
			?>
			<?php if ( $enable_count ) : ?>
				<input
					id="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
					name="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
					type="number"
					placeholder="<?php echo esc_attr( $min ); ?>"
					value="<?php echo esc_attr( $min ); ?>"
					min="<?php echo esc_attr( $min ); ?>"
					max="<?php echo esc_attr( $max ); ?>"
					class="prad-block-input prad-quantity-input switcher-count prad-input prad-w-full"
					data-counter="<?php echo esc_attr( $blockid . $index ); ?>-switcher-count"
				/>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
