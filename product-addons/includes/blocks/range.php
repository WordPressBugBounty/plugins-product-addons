<?php
namespace PRAD;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;

$item      = $_options[0];
$price_obj = apply_filters( 'prad_blocks_price_both_show', $item->type, $item->regular, $item->sale, $productid );
?>

<div 
	class="prad-parent prad-block-range prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-ptype="<?php echo esc_attr( $item->type ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-defval="<?php echo esc_attr( isset( $defval ) ? $defval : '' ); ?>"
	data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
>
	<?php if ( ! $hide || $item->type != 'no_cost' ) { ?>
		<div class="prad-d-flex prad-item-center prad-gap-12 prad-range-header">
			<?php if ( ! $hide ) : ?>
				<div class="prad-relative prad-w-fit">
					<div class="prad-block-title"><?php echo wp_kses( $label, $prad_allowed_html_tags ); ?></div>
					<?php if ( $required ) : ?>
						<div class="prad-block-required prad-absolute">
							*
						</div>
					<?php endif; ?> 
				</div>
			<?php endif; ?>
			<?php if ( $item->type != 'no_cost' ) { ?>
				<div class="prad-block-price prad-text-upper">
					<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! $hide && $description ) { ?>
		<div class="prad-block-description prad-mb-12">
			<?php echo wp_kses( $description, $prad_allowed_html_tags ); ?>
		</div>
	<?php } ?>
	<div class="prad-range-input-container">
		<input 
			class='prad-block-range-input prad-range-frontend'
			type="range"
			min="<?php echo esc_attr( $min ? $min : 0 ); ?>"
			max="<?php echo esc_attr( $max ? $max : 100 ); ?>"
			step="<?php echo esc_attr( $step ? $step : 1 ); ?>"
			id="<?php echo esc_attr( $blockid ); ?>-prad-range-field"
			data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
		/>
		<input 
			class='prad-block-input prad-input prad-input'
			type="number"
			min="<?php echo esc_attr( $min ? $min : 0 ); ?>"
			max="<?php echo esc_attr( $max ? $max : 100 ); ?>"
			step="<?php echo esc_attr( $step ? $step : 1 ); ?>"
			id="<?php echo esc_attr( $blockid ); ?>-prad-range-field"
			data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
		/>
	</div>
</div>
