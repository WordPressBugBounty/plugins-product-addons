<?php
namespace PRAD;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;
?>
<div 
	class="prad-parent prad-block-select prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-defval="<?php echo esc_attr( isset( $defval ) ? $defval : '' ); ?>"
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
	<div class="prad-custom-select prad-w-full">
		<div class="prad-select-box prad-block-input prad-block-content" readonly="readonly"><div class="prad-select-box-item"><?php esc_html_e( 'Select an option', 'product-addons' ); ?></div> <div class="prad-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6"></path></svg></div></div>
		<div class="prad-select-options">
			<?php
			foreach ( $_options as $i => $item ) :
				$price_obj = apply_filters( 'prad_blocks_price_both_show', $item->type, $item->regular, $item->sale, $productid );
				?>
				<div
					class="prad-select-option"
					data-value="<?php echo esc_attr( $price_obj['price'] ); ?>" 
					data-label="<?php echo esc_attr( $item->value ); ?>"
					data-index="<?php echo esc_attr( $i ); ?>"
					data-ptype="<?php echo esc_attr( $item->type ); ?>"
				>
					
					<div class="prad-d-flex prad-item-center prad-gap-8">
						<div class="prad-block-content prad-d-flex prad-item-center">
							<?php if ( isset( $item->img ) && $item->img && product_addons()->handle_all_pro_block() ) : ?>
								<img
									class="prad-block-item-img"
									src= "<?php echo esc_url( $item->img ); ?>"
									alt="Item"
								/>
							<?php endif ?>
							<div class="prad-ellipsis-2" title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>"><?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?></div>
						</div>
						<?php if ( $item->type != 'no_cost' ) { ?>
							<div class="prad-block-price prad-text-upper">
								<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
