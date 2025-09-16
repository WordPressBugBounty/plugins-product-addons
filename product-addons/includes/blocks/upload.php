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

if ( empty( $allowed_file_types ) || ! is_array( $allowed_file_types ) ) {
	$accept_types = '';
} else {
	$accept_types = implode( ',', array_map( fn( $ext ) => '.' . trim( $ext ), $allowed_file_types ) );
}


?>

<div 
	class="prad-parent prad-block-upload prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-ptype="<?php echo esc_attr( $item->type ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-btype="<?php echo esc_attr( $btype ); ?>"
	data-max_size="<?php echo esc_attr( $max_size ); ?>"
	data-size_prefix="<?php echo esc_attr( $size_prefix ); ?>"
	data-size_error="<?php echo esc_attr( $size_error ); ?>"
	data-number_prefix="<?php echo esc_attr( $number_prefix ); ?>"
	data-max_number="<?php echo esc_attr( $max_number ); ?>"
	data-number_error="<?php echo esc_attr( $number_error ); ?>"
	data-allowed="<?php echo esc_attr( wp_json_encode( $allowed_file_types ) ); ?>"
	data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
>
	<?php if ( ! $hide || $item->type != 'no_cost' ) { ?>
		<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">
			<?php if ( ! $hide ) : ?>
				<div class="prad-relative prad-w-fit">
					<div class="prad-block-title"><?php echo wp_kses( $label, $prad_allowed_html_tags ); ?></div>
					<?php if ( $required ) : ?>
						<div class="prad-block-required prad-absolute">*</div>
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
	<div class="prad-upload-wrapper">
		<label for="prad_block_upload_<?php echo esc_attr( $blockid ); ?>" class=" prad-drop-zone prad-upload-container prad-border-none prad-bg-transparent prad-m-0">
			<div class="prad-block-upload-icon prad-lh-0 prad-mb-8">
				<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none">
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="3"
						d="M17.625 27.65C12.488 28.87 8.667 33.49 8.667 39c0 6.444 5.223 11.667 11.667 11.667 1.105 0 2.174-.154 3.188-.441M46.065 27.65C51.2 28.87 55.02 33.49 55.02 39c0 6.444-5.223 11.667-11.667 11.667-1.105 0-2.174-.154-3.187-.441M46 27.333c0-7.731-6.268-14-14-14-7.731 0-14 6.269-14 14m5.91 9.195L32 28.41l8.321 8.256m-8.32 11.666V31.54"
					/>
				</svg>
			</div>
			<div class="prad-block-upload-title prad-d-flex prad-item-center prad-gap-4 prad-w-fit prad-center-horizontal prad-mb-12">
				<input
					id="prad_block_upload_<?php echo esc_attr( $blockid ); ?>"
					class="prad-input-hidden prad-upload-input"
					type="file"
					hidden
					id="<?php echo esc_attr( $blockid ); ?>-prad-file-field"
					data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
					multiple
					accept="<?php echo esc_attr( $accept_types ); ?>"
				/>
				<div class="prad-block-upload-link prad-cursor-pointer prad-color-text-dark prad-text-underline">
					<?php echo esc_html__( 'Click to upload', 'product-addons' ); ?>
				</div>
				<div>
					<?php echo esc_html__( 'or drag and drop', 'product-addons' ); ?>
				</div>
			</div>
			<?php if ( isset( $size_prefix ) && $size_prefix ) : ?>
				<div class="prad-block-upload-content prad-font-12 prad-color-text-medium prad-mb-4">
					<?php echo esc_html( str_replace( '[max_size]', $max_size . 'MB', $size_prefix ) ); ?> 
				</div>
			<?php endif; ?>
			<?php if ( isset( $number_prefix ) && $number_prefix ) : ?>
				<div class="prad-block-upload-content prad-font-12 prad-color-text-medium">
					<?php echo esc_html( str_replace( '[max_files]', $max_number, $number_prefix ) ); ?> 
				</div>
			<?php endif; ?>
			<?php if ( isset( $allowed_file_types ) && $allowed_file_types && ! empty( $allowed_prefix ) ) : ?>
				<div class="prad-block-upload-type prad-font-12 prad-color-text-body prad-mt-8">
					<span><?php echo esc_html( str_replace( '[allowed_types]', implode( ', ', $allowed_file_types ), $allowed_prefix ) ); ?></span>
				</div>
			<?php endif; ?>
		</label>
		<div class="prad-upload-result"></div>
	</div>
</div>
