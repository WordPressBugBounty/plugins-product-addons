<?php
namespace PRAD;

/**
 *
 *
 * This template is used to render the checkbox container
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $formula_data->valid ) || ! $formula_data->valid || ! isset( $formula_data->expression ) || empty( $formula_data->expression ) ) {
	return;
}
?>

<div 
	class="prad-parent prad-block-custom-formula prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-formula-data="<?php echo esc_attr( wp_json_encode( ! empty( $formula_data->expression ) ? $formula_data->expression : '' ) ); ?>"
>
	<div class="prad-d-flex prad-item-center prad-gap-16 prad-mb-12">
		<div class="prad-block-title"><?php echo wp_kses( $label, $prad_allowed_html_tags ); ?></div>
		<div class="prad-block-price prad-text-upper prad-formula-price-container">
			<?php echo wp_kses( wc_price( 0 ), $prad_allowed_html_tags ); ?>
		</div>
	</div>
	<?php if ( $description ) { ?>
			<div class="prad-block-description prad-mb-12">
				<?php echo wp_kses( $description, $prad_allowed_html_tags ); ?>
			</div>
		<?php } ?>
	
</div>
