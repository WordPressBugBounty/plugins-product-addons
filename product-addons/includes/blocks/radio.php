<?php

namespace PRAD;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;
$column_class = '1';
if ( 2 == $columns ) {
	$column_class = '2';
} elseif ( 3 == $columns ) {
	$column_class = '3';
}

?>
<div
	class="prad-parent prad-block-radio prad-type-radio-input prad-switcher-count prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-defval="<?php echo esc_attr( isset( $defval ) ? $defval : '' ); ?>">
	<?php if ( ! $hide ) : ?>
		<div class="prad-relative prad-w-fit">
			<div class="prad-mb-12 prad-block-title"><?php echo wp_kses( $label, $prad_allowed_html_tags ); ?></div>
			<?php if ( $required ) : ?>
				<div class="prad-block-required prad-absolute">
					*
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $description ) { ?>
			<div class="prad-block-description prad-mb-12">
				<?php echo wp_kses( $description, $prad_allowed_html_tags ); ?>
			</div>
		<?php } ?>
	<?php endif; ?>
	<div class="prad-input-container prad-column-<?php echo esc_attr( $column_class ); ?>">
		<?php
		foreach ( $_options as $index => $item ) :
			$price_obj = apply_filters( 'prad_blocks_price_both_show', $item->type, $item->regular, $item->sale, $productid );
			?>
			<div class="prad-radio-item-wrapper prad-d-flex prad-item-center prad-gap-8 prad-justify-<?php echo esc_attr( $pricePosition === 'with_option' ? 'left' : 'between' ); ?>">
				<div class="prad-radio-item prad-d-flex prad-item-center prad-gap-10">
					<input
						class='prad-input-hidden'
						type="radio"
						id="<?php echo esc_attr( $blockid . $index ); ?>"
						name="prad-radio-<?php echo esc_attr( $blockid ); ?>"
						value="<?php echo esc_attr( $price_obj['price'] ); ?>"
						data-ptype="<?php echo esc_attr( $item->type ); ?>"
						data-index="<?php echo esc_attr( $index ); ?>"
						data-label="<?php echo esc_attr( $item->value ); ?>"
						data-count="<?php echo esc_attr( $enableCount ? 'yes' : 'no' ); ?>"
						data-counter="<?php echo esc_attr( $blockid . $index ) . '-switcher-count'; ?>" />
					<label for="<?php echo esc_attr( $blockid . $index ); ?>" class="prad-d-flex prad-item-center prad-gap-10">
						<div class="prad-radio-mark prad-realtive prad-br-round prad-selection-none">
						</div>
						<div class="prad-block-content prad-d-flex prad-item-center">
							<?php if ( isset( $item->img ) && $item->img && product_addons()->is_pro_feature_available() ) : ?>
								<img
									class="prad-block-item-img"
									src= "<?php echo esc_url( $item->img ); ?>"
									alt="Item"
								/>
							<?php endif ?>
							<div title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>" class="prad-ellipsis-2"><?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?></div>
						</div>
					</label>
				</div>
				<?php if ( $item->type != 'no_cost' || $enableCount ) { ?>
					<div class="prad-d-flex prad-item-center prad-gap-12">
						<?php if ( $item->type != 'no_cost' ) { ?>
							<div class="prad-block-price prad-text-upper">
								<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
							</div>
						<?php } ?>
						<?php if ( $enableCount && ( 1 == $columns ) ) : ?>
							<input
								id="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
								name="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
								type="number"
								placeholder="<?php echo esc_attr( $min ? $min : 1 ); ?>"
								value="<?php echo esc_attr( $min ? $min : 1 ); ?>"
								min="<?php echo esc_attr( $min ? $min : 1 ); ?>"
								max="<?php echo esc_attr( $max ); ?>"
								class="prad-block-input prad-quantity-input switcher-count prad-input"
								data-counter="<?php echo esc_attr( $blockid . $index ); ?>-switcher-count" />
						<?php endif ?>
					</div>
				<?php } ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
