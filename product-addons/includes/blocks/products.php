<?php

namespace PRAD;

use PRAD\Includes\RenderBlocks;

/**
 * Unified template for checkbox, radio, and swatches blocks
 */

defined( 'ABSPATH' ) || exit;

$input_type   = 'checkbox';
$hover_class  = 'always';
$column_class = '1';

if ( '_swatches' === $block_type ) {
	$input_type = $multiple ? 'checkbox' : 'radio';
	if ( 'hover_show' === $layout_visibility ) {
		$hover_class = 'show';
	} elseif ( 'hover_hide' === $layout_visibility ) {
		$hover_class = 'hide';
	}
} else {
	$input_type = ( '_radios' === $block_type ) ? 'radio' : 'checkbox';
	if ( 2 == $columns ) {
		$column_class = '2';
	} elseif ( 3 == $columns ) {
		$column_class = '3';
	}
}

foreach ( $manual_products as $item ) {
	if ( isset( $item->variation ) ) {
		if ( $merge_variation ) {
			$product_data = product_addons()->get_product_block_product_attr( $item->id, true );
			if ( $product_data ) {
				$_options[] = $product_data;
			}
		} elseif ( is_array( $item->variation ) ) {
			foreach ( $item->variation as $v_id ) {
				$product_data = product_addons()->get_product_block_product_attr( $v_id, false );
				if ( $product_data ) {
					$_options[] = $product_data;
				}
			}
		}
	} else {
		$product_data = product_addons()->get_product_block_product_attr( $item->id, false );
		if ( $product_data ) {
			$_options[] = $product_data;
		}
	}
}

if ( ! product_addons()->is_pro_feature_available() && is_array( $_options ) && count( $_options ) > 2 ) {
	$_options = array_slice( $_options, 0, 2 );
}

?>

<div
	class="prad-parent prad-type<?php echo esc_attr( '_swatches' === $block_type ? $block_type : '-' . $input_type ); ?>-input prad-block-products prad-switcher-count prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?> prad-swatch-layout<?php echo esc_attr( $layout ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-input-type="<?php echo esc_attr( $input_type ); ?>"
	<?php if ( 'checkbox' === $input_type ) : ?>
		data-minselect="<?php echo esc_attr( $minSelect ); ?>"
		data-maxselect="<?php echo esc_attr( $maxSelect ); ?>"
	<?php endif; ?>
>
	<?php if ( ! $hide ) : ?>
		<div class="prad-relative prad-w-fit">
			<div class="prad-mb-12 prad-block-title">
				<?php echo wp_kses( $label, $prad_allowed_html_tags ); ?>
			</div>
			<?php if ( $required ) : ?>
				<div class="prad-block-required prad-absolute">*</div>
			<?php endif; ?>
		</div>
		<?php if ( $description ) { ?>
			<div class="prad-block-description prad-mb-12">
				<?php echo wp_kses( $description, $prad_allowed_html_tags ); ?>
			</div>
		<?php } ?>
	<?php endif; ?>

	<div class="<?php echo '_swatches' === $block_type ? 'prad-swatch-wrapper' : 'prad-input-container prad-column-' . esc_attr( $column_class ); ?>">
		<?php
		foreach ( $_options as $index => $item ) :
			$price_obj             = product_addons()->get_price_object( $item->regular, $item->sale );
			$item_id               = $blockid . $index;
			$img_url               = $item->img ?? '';
			$variation_select_html = product_addons()->generate_products_block_variation_section_html(
				array(
					'item'  => $item,
					'index' => $index,
				)
			);
			?>
			<div 
				class="prad-products-item-wrapper 
				<?php
				echo ( '_checkbox' === $block_type || '_radios' === $block_type ) ?
				'prad-d-flex prad-item-center prad-gap-8 prad-column-' . esc_attr( $column_class )
				: 'prad-swatch-item-wrapper prad-relative prad-d-flex prad-flex-column prad-h-full';
				?>
				"
			>

				<?php if ( '_swatches' === $block_type ) : ?>
					<div class="prad-swatch-container prad-p-2 prad-w-fit prad-relative prad-hover-<?php echo esc_attr( $hover_class ); ?>-bottom">
						<input
							class="prad-input-hidden"
							type="<?php echo esc_attr( $input_type ); ?>"
							data-index="<?php echo esc_attr( $index ); ?>"
							id="<?php echo esc_attr( $item_id ); ?>"
							name="<?php echo esc_attr( $blockid ); ?>"
							value="<?php echo esc_attr( $price_obj['price'] ); ?>"
							data-ptype="<?php echo esc_attr( $item->type ); ?>"
							data-product-id="<?php echo esc_attr( $item->id ); ?>"
							data-label="<?php echo esc_attr( $item->value ); ?>"
							data-count="<?php echo esc_attr( $enableCount ? 'yes' : 'no' ); ?>"
							data-counter="<?php echo esc_attr( $item_id ); ?>-switcher-count"
						/>
						<label class="prad-lh-0 prad-mb-0" for="<?php echo esc_attr( $item_id ); ?>">
							<img
								class="prad-swatch-item"
								title="<?php echo esc_attr( $price_obj['price'] ); ?>"
								src="<?php echo esc_url( $img_url ?: PRAD_URL . 'assets/img/default-product.svg' ); ?>"
								alt="swatch item"
							/>
						</label>
						<div class="prad-swatch-mark-image" style="border: 1px solid #fff; padding: 1px !important; border-radius: 2px;">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16" height="16" viewBox="0 0 16 16">
								<rect width="16" height="16" fill="currentColor" rx="2" />
								<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m12.125 5.375-5.25 5.25L4.25 8" />
							</svg>
						</div>
						<?php
						if ( $layout === '_overlay' ) {
							echo RenderBlocks::prad_render_block_content( $item, $index, $blockid, $price_obj, $enableCount, $min, $max, $prad_allowed_html_tags );
						}
						?>
					</div>
					<?php
					if ( $layout === '_default' ) {
						echo RenderBlocks::prad_render_block_content( $item, $index, $blockid, $price_obj, $enableCount, $min, $max, $prad_allowed_html_tags, $variation_select_html );
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
						<?php
					endif;
					if ( '_overlay' === $layout || '_img' === $layout ) {
						echo $variation_select_html;
					}
					?>
					

				<?php else : ?>
					<div class="prad-d-flex <?php echo ( $variation_select_html ? 'prad-item-start' : 'prad-item-center' ); ?> prad-gap-8">
						<div class="prad-<?php echo esc_attr( $input_type ); ?>-item prad-d-flex prad-item-center prad-gap-10">
							<input
								class="prad-input-hidden"
								type="<?php echo esc_attr( $input_type ); ?>"
								id="<?php echo esc_attr( $blockid . $index ); ?>"
								name="prad-<?php echo esc_attr( $input_type ); ?>-<?php echo esc_attr( $blockid ); ?>"
								value="<?php echo esc_attr( $price_obj['price'] ); ?>"
								data-ptype="<?php echo esc_attr( $item->type ); ?>"
								data-product-id="<?php echo esc_attr( $item->id ); ?>"
								data-index="<?php echo esc_attr( $index ); ?>"
								data-label="<?php echo esc_attr( $item->value ); ?>"
								data-count="<?php echo esc_attr( $enableCount ? 'yes' : 'no' ); ?>"
								data-counter="<?php echo esc_attr( $item_id ); ?>-switcher-count"
							/>
							<label for="<?php echo esc_attr( $blockid . $index ); ?>" class="prad-d-flex prad-item-center prad-gap-10">
								<?php if ( $input_type === 'radio' ) : ?>
									<div class="prad-radio-mark prad-realtive prad-br-round prad-selection-none"></div>
								<?php else : ?>
									<div class="prad-checkbox-mark prad-selection-none">
										<svg
											width="12"
											height="12"
											viewBox="0 0 12 12"
											fill="none"
											xmlns="http://www.w3.org/2000/svg"
										>
											<path
												d="m10.125 3.375-5.25 5.25L2.25 6"
												stroke="currentColor"
												stroke-width="1.5"
												stroke-linecap="round"
												stroke-linejoin="round"
											/>
										</svg>
									</div>
								<?php endif; ?>
								<div class="prad-block-content prad-d-flex prad-item-center">
									<?php if ( isset( $item->img ) && $item->img && product_addons()->is_pro_feature_available() ) : ?>
										<img class="prad-block-item-img" src="<?php echo esc_url( $item->img ); ?>" alt="Item" />
									<?php endif; ?>
									<?php if ( $variation_select_html ) : ?>
										<div>
											<div title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>" class="prad-ellipsis-2"><?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?></div>
											<?php echo $variation_select_html; ?>
										</div>
									<?php else : ?>
										<div title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>" class="prad-ellipsis-2"><?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?></div>
									<?php endif; ?>
									
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
								<?php if ( $enableCount ) : ?>
									<input
										id="prad_quantity_<?php echo esc_attr( $item_id ); ?>"
										name="prad_quantity_<?php echo esc_attr( $item_id ); ?>"
										type="number"
										placeholder="1"
										min="<?php echo esc_attr( $min ?? 0 ); ?>"
										max="<?php echo esc_attr( $max ?? 0 ); ?>"
										class="prad-block-input prad-quantity-input switcher-count prad-input"
										data-counter="<?php echo esc_attr( $item_id ); ?>-switcher-count"
									/>
								<?php endif; ?>
							</div>
						<?php } ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
