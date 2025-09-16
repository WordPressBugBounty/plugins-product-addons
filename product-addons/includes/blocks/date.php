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
	class="prad-parent prad-block-date prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
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
		<div class="prad-date-picker-container prad-block-input prad-w-full prad-d-flex prad-item-center prad-gap-8">
			<div class="prad-lh-0 prad-input-date-icon prad-cursor-pointer">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="20"
					height="20"
					fill="none"
					viewBox="0 0 20 20">
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.5"
						d="M2.577 7.837H17.43m-3.728 3.254h.008m-3.706 0h.008m-3.714 0h.008m7.396 3.239h.008m-3.706 0h.008m-3.714 0h.008M13.37 1.667v2.742M6.638 1.667v2.742" />
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.5"
						d="M13.532 2.983H6.476C4.029 2.983 2.5 4.346 2.5 6.852v7.541c0 2.546 1.529 3.94 3.976 3.94h7.048c2.455 0 3.976-1.37 3.976-3.877V6.852c.008-2.506-1.513-3.87-3.968-3.87Z"
						clip-rule="evenodd" />
				</svg>
			</div>
			<input
				type="text"
				class="prad-date-input prad-custom-date-input prad-w-95 prad-cursor-pointer prad-input"
				data-max-date="<?php echo esc_attr( $max_date ); ?>"
				data-min-date="<?php echo esc_attr( $min_date ); ?>"
				placeholder="<?php echo esc_attr( $format ); ?>"
				data-format="<?php echo esc_attr( $format ); ?>"
				data-disabled-weekdays="<?php echo esc_attr( $disable_days ); ?>"
				data-disabled-date="<?php echo esc_attr( $disable_dates ); ?>"
				data-val="<?php echo esc_attr( $price_obj['price'] ); ?>" />
		</div>
		<?php if ( $pricePosition !== 'with_title' && $item->type != 'no_cost' ) : ?>
			<div class="prad-block-price prad-text-upper">
				<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
