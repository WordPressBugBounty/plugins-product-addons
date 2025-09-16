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
	class="prad-parent prad-block-shortcode prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
>
	<?php if ( ! $hide ) { ?>
		<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">
			<div class="prad-relative prad-w-fit">
				<div class="prad-block-title"><?php echo wp_kses( $label, $prad_allowed_html_tags ); ?></div>
			</div>
		</div>
	<?php } ?>
	<?php
		echo do_shortcode( $value );
	?>
	
</div>
