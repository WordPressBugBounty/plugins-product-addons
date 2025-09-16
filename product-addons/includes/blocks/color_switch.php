<?php

namespace PRAD;

use PRAD\Includes\RenderBlocks;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;

$input_type  = $multiple ? 'checkbox' : 'radio';
$hover_class = 'always';
if ( $layoutVisibility == 'hover_show' ) {
	$hover_class = 'show';
} elseif ( $layoutVisibility == 'hover_hide' ) {
	$hover_class = 'hide';
} else {
	$hover_class = 'always';
}


?>

<div
	class="prad-parent prad-block-color-switcher prad-switcher-count prad-switcher-count-<?php echo esc_attr( $input_type ); ?> prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?> prad-swatch-layout<?php echo esc_attr( $layout ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-defval="<?php echo esc_attr( isset( $defval ) ? $defval : '' ); ?>"
	<?php if ( $multiple ) : ?>
		data-minselect="<?php echo esc_attr( $minSelect ); ?>"
		data-maxselect="<?php echo esc_attr( $maxSelect ); ?>"
	<?php endif; ?>
>
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
	<div class="prad-swatch-wrapper">
		<?php
		foreach ( $_options as $index => $item ) :
			$price_obj = apply_filters( 'prad_blocks_price_both_show', $item->type, $item->regular, $item->sale, $productid );
			?>
			<div class="prad-swatch-item-wrapper prad-relative prad-d-flex prad-flex-column prad-h-full">
				<div class="prad-swatch-container prad-p-2 prad-w-fit prad-relative prad-hover-<?php echo esc_attr( $hover_class ); ?>-bottom">
					<input
						class="prad-input-hidden"
						type=<?php echo esc_attr( $input_type ); ?>
						data-index="<?php echo esc_attr( $index ); ?>"
						id="<?php echo esc_attr( $blockid . $index ); ?>"
						name="<?php echo esc_attr( $blockid ); ?>"
						value="<?php echo esc_attr( $price_obj['price'] ); ?>"
						data-ptype="<?php echo esc_attr( $item->type ); ?>"
						data-label="<?php echo esc_attr( $item->value ); ?>"
						data-count="<?php echo esc_attr( $enableCount ? 'yes' : 'no' ); ?>"
						data-counter="<?php echo esc_attr( $blockid . $index ); ?>-switcher-count" />
					<label class="prad-lh-0 prad-mb-4" for="<?php echo esc_attr( $blockid . $index ); ?>">
						<div
							class="prad-swatch-item"
							aria-label="Color swatch for <?php echo esc_attr( $item->value ); ?>"
							style="background-color: <?php echo esc_attr( $item->color ); ?>;"></div>
					</label>
					<div class="prad-swatch-mark-image" style="border: 1px solid #ffffff;padding: 1px !important;border-radius: 2px;">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							fill="none"
							width="16"
							height="16"
							viewBox="0 0 16 16">
							<rect width="16" height="16" fill="currentColor" rx="2" />
							<path
								stroke="#fff"
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="1.5"
								d="m12.125 5.375-5.25 5.25L4.25 8" />
						</svg>
					</div>
					<?php
					if ( $layout == '_overlay' ) {
						echo RenderBlocks::prad_render_block_content( $item, $index, $blockid, $price_obj, $enableCount, $min, $max, $prad_allowed_html_tags );
					}
					?>
				</div>
				<?php
				if ( $layout == '_default' ) {
					echo RenderBlocks::prad_render_block_content( $item, $index, $blockid, $price_obj, $enableCount, $min, $max, $prad_allowed_html_tags );
				}
				if ( $enableCount && $layout == '_img' ) :
					?>
					<input
						id="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
						name="prad_quantity_<?php echo esc_attr( $blockid . $index ); ?>"
						type="number"
						placeholder="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						value="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						min="<?php echo esc_attr( $min ? $min : 1 ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						class="prad-block-input prad-quantity-input switcher-count prad-input prad-w-full prad-mt-6"
						data-counter="<?php echo esc_attr( $blockid . $index ); ?>-switcher-count" 
					/>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
