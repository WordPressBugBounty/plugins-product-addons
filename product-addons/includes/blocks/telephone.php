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
	class="prad-parent prad-block-telephone prad-block-<?php echo esc_attr( $blockid . ' ' . $class ); ?>"
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
	<div class="prad-d-flex prad-item-center prad-gap-12 prad-mb-12">
		<div class="prad-tel-container prad-w-full prad-d-flex prad-item-center <?php echo esc_attr( $show_flag ? 'prad-tel-flag-active' : '' ); ?>">
			<?php if ( $show_flag ) : ?>
				<div class="prad-tel-country-wrapper prad-relative">
					<div class="prad-tel-flag-handler prad-d-flex prad-item-center prad-gap-8">
						<div class="prad-tel-flag prad-flag-selected"></div>
						<div class="prad-flag-arrow">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none">
								<path
									stroke="currentColor"
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="1.5"
									d="m1 1 6 6 6-6" />
							</svg>
						</div>
					</div>
					<div class="prad-tel-country-list-container prad-absolute prad-bg-base2">
						<div class="prad-country-search">
							<input type="text" class="prad-country-search-input" placeholder="<?php esc_html_e( 'Search', 'product-addons' ); ?>" />
						</div>
						<div class="prad-country-list prad-scrollbar"></div>
					</div>
				</div>
			<?php endif; ?>
			<div class="prad-tel-input-wrapper prad-d-flex prad-item-center prad-w-full">
				<?php if ( $show_flag ) : ?>
					<div class="prad-dial-code-show" data-selected="bd">+880</div>
				<?php endif; ?>
				<input 
					class='prad-w-full prad-block-input prad-input'
					type="tel"
					placeholder="<?php echo esc_attr( $placeholder ); ?>" 
					id="<?php echo esc_attr( $blockid ) . '-prad-telephone-field'; ?>"
					value="<?php echo esc_attr( $value ); ?>"
					data-val="<?php echo esc_attr( $price_obj['price'] ); ?>"
				/>
			</div>
		</div>
		<?php if ( $pricePosition !== 'with_title' && $item->type != 'no_cost' ) : ?>
			<div class="prad-block-price prad-text-upper">
				<?php echo wp_kses( $price_obj['html'], $prad_allowed_html_tags ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
