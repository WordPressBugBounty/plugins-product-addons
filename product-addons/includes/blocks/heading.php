<?php
namespace PRAD;

/**
 * Shortcode block template.
 * This template is used to render the shortcode block.
*/

defined( 'ABSPATH' ) || exit;
?>

<div 
	class="prad-parent prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
>
	<?php
		$heading_tag = esc_html( $heading_tag );
		echo wp_kses( '<' . $heading_tag . ' class="prad-block-heading">' . $heading_text . '</' . $heading_tag . '>', $prad_allowed_html_tags );
	?>
</div>
