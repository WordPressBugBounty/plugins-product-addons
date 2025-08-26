<?php //phpcs:ignore
namespace PRAD;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;

$input_type = $multiple ? 'checkbox' : 'radio';
?>

<div 
	class="prad-parent prad-block-button prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
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
	<div class="prad-d-flex prad-flex-wrap prad-gap-<?php echo esc_attr( true === $vertical ? '8' : '12' ); ?> prad-flex-<?php echo esc_attr( true === $vertical ? 'column' : 'row' ); ?>">
		<?php
		foreach ( $_options as $index => $item ) :
			$price_obj = apply_filters( 'prad_blocks_price_both_show', $item->type, $item->regular, $item->sale, $productid );
			?>
			<div class="prad-button-container">
				<input 
					class="prad-input-hidden"
					type=<?php echo esc_attr( $input_type ); ?>
					data-index="<?php echo esc_attr( $index ); ?>"
					id="<?php echo esc_attr( $blockid . $index ); ?>" 
					name="<?php echo esc_attr( $blockid ); ?>" 
					value="<?php echo esc_attr( $price_obj['price'] ); ?>"
					data-ptype="<?php echo esc_attr( $item->type ); ?>"
					data-label="<?php echo esc_attr( $item->value ); ?>"
				/>
				<label class="prad-mb-0" for="<?php echo esc_attr( $blockid . $index ); ?>">
					<div class="prad-button-item prad-w-fit prad-d-flex prad-item-center prad-gap-8">
						<div title="<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>" class="prad-ellipsis-2 prad-text-<?php echo $item->type != 'no_cost' ? 'start' : 'center'; ?>" style="min-width: <?php echo $item->type != 'no_cost' ? 'unset' : '2rem'; ?>;">
							<?php echo wp_kses( $item->value, $prad_allowed_html_tags ); ?>
						</div>
						<?php if ( $item->type != 'no_cost' ) { ?>
							<div class="prad-block-price prad-text-upper">
								<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
							</div>
						<?php } ?>
					</div>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
</div>
