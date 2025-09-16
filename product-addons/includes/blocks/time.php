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
	class="prad-parent prad-block-time prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
	data-bid="<?php echo esc_attr( $blockid ); ?>"
	id="prad-bid-<?php echo esc_attr( $blockid ); ?>"
	data-sectionid="<?php echo esc_attr( $sectionid ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-ptype="<?php echo esc_attr( $item->type ); ?>"
	data-enlogic="<?php echo esc_attr( $en_logic ? 'yes' : 'no' ); ?>"
	data-required="<?php echo esc_attr( $required ? 'yes' : 'no' ); ?>"
	data-fieldconditions="<?php echo esc_attr( wp_json_encode( $fieldConditions ) ); ?>"
	data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
>
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
	<div class="prad-custom-datetime-picker-container prad-d-flex prad-item-center prad-gap-12 prad-w-full">
		<div class="prad-time-picker-container prad-block-input prad-w-full prad-d-flex prad-item-center prad-gap-8">
			<div class="prad-lh-0 prad-input-time-icon prad-cursor-pointer">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					width="20"
					height="20"
					viewBox="0 0 20 20">
					<g
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.5"
						clip-path="url(#clock_20)">
						<path d="M10 18.333a8.333 8.333 0 1 0 0-16.666 8.333 8.333 0 0 0 0 16.666Z" />
						<path d="M10 5v5l3.333 1.667" />
					</g>
					<defs>
						<clipPath id="clock_20">
							<path fill="#fff" d="M0 0h20v20H0z" />
						</clipPath>
					</defs>
				</svg>
			</div>
			<input
				type="text"
				class="prad-time-input prad-custom-time-input prad-w-95 prad-cursor-pointer prad-input"
				readonly
				placeholder="HH:MM AM"
				data-val="<?php echo esc_attr( $price_obj['price'] ); ?>" 
				data-min-time="<?php echo esc_attr( $min_time ); ?>"
				data-max-time="<?php echo esc_attr( $max_time ); ?>"
			/>
				
		</div>
		<?php if ( $pricePosition !== 'with_title' && $item->type != 'no_cost' ) : ?>
			<div class="prad-block-price prad-text-upper">
				<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
