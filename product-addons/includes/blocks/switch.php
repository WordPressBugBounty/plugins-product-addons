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
	class="prad-parent prad-block-switch prad-switcher-count _switchCount prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-defval="<?php echo esc_attr( isset( $defval ) ? $defval : '' ); ?>">
	<?php if ( ! $hide || ( $pricePosition === 'with_title' && $item->type != 'no_cost' ) ) { ?>
		<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">
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
			<?php if ( $pricePosition === 'with_title' && $item->type != 'no_cost' ) : ?>
				<div class="prad-block-price prad-text-upper">
					<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php } ?>
	<?php if ( ! $hide && $description ) { ?>
		<div class="prad-block-description prad-mb-12">
			<?php echo wp_kses( $description, $prad_allowed_html_tags ); ?>
		</div>
	<?php } ?>
	<div class="prad-switch-item-wrapper prad-d-flex prad-item-center prad-justify-<?php echo esc_attr( $pricePosition === 'with_option' ? 'left' : 'between' ); ?>  prad-gap-12 prad-w-full">
		<div class="prad-switch-item prad-d-flex prad-item-center prad-gap-10">
			<input
				class='prad-input-hidden'
				type="checkbox"
				id="<?php echo esc_attr( $blockid ); ?>"
				name="prad-checkbox-<?php echo esc_attr( $blockid ); ?>"
				value="<?php echo esc_attr( $price_obj['price'] ); ?>"
				data-ptype="<?php echo esc_attr( $item->type ); ?>"
				data-index="0"
				data-label="<?php echo esc_attr( $item->value ); ?>"
				data-count="<?php echo esc_attr( $enableCount ? 'yes' : 'no' ); ?>"
				data-counter="<?php echo esc_attr( $blockid ) . '-switcher-count'; ?>" />
			<label for="<?php echo esc_attr( $blockid ); ?>" class="prad-d-flex prad-item-center prad-gap-10">
				<div class="prad-switch-body prad-shrink-0 prad-selection-none">
					<div class="prad-switch-thumb"></div>
				</div>
				<div class="prad-block-content prad-d-flex prad-item-center">
					<?php if ( isset( $item->img ) && $item->img && product_addons()->is_pro_feature_available() ) : ?>
						<img
							class="prad-block-item-img"
							src= "<?php echo esc_url( $item->img ); ?>"
							alt="Item"
						/>
					<?php endif ?>
					<div class="prad-ellipsis-2" title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>"><?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?></div>
				</div>
			</label>
		</div>
		<?php if ( $enableCount || ( $pricePosition !== 'with_title' && $item->type != 'no_cost' ) ) { ?>
			<div class="prad-d-flex prad-item-center prad-gap-12">
				<?php if ( $pricePosition !== 'with_title' && $item->type != 'no_cost' ) : ?>
					<div class="prad-block-price prad-text-upper">
						<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $enableCount ) : ?>
					<input
						id="prad_quantity_<?php echo esc_attr( $blockid ); ?>"
						name="prad_quantity_<?php echo esc_attr( $blockid ); ?>"
						type="number"
						placeholder="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						value="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						min="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						class="prad-block-input prad-quantity-input switcher-count prad-input"
						data-counter="<?php echo esc_attr( $blockid ); ?>-switcher-count" />
				<?php endif ?>
			</div>
		<?php } ?>
	</div>
</div>
