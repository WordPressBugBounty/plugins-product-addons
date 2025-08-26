<?php //phpcs:ignore
namespace PRAD;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Notice
 */
class Product_Edit {

	/**
	 * Notice Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'prad_product_custom_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'prad_tab_data' ) );
	}

	/**
	 * WholesaleX Tab in Single Product Edit Page
	 *
	 * @param array $tabs Single Product Page Tabs.
	 * @return array Updated Tabs.
	 */
	public function prad_product_custom_tab( $tabs ) {
		$tabs['prad_tab'] = array(
			'label'    => 'WowAddons',
			'priority' => 15,
			'target'   => 'prad_tab_data',
			'class'    => array( 'hide_if_grouped' ),
		);

		return $tabs;
	}

	/**
	 * WholesaleX Custom Tab Data.
	 *
	 * @return void
	 */
	public function prad_tab_data() {
		global $post;
		$product_id = $post->ID;

		$option_all = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
		$option_all = is_array( $option_all ) ? $option_all : array();

		$option_product = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_inc', true ) ), true );
		$option_product = is_array( $option_product ) ? $option_product : array();

		$option_exclude = json_decode( stripslashes( get_post_meta( $product_id, 'prad_product_assigned_meta_exc', true ) ), true );
		$option_exclude = is_array( $option_exclude ) ? $option_exclude : array();

		$option_term = array();
		// Merge option IDs from product_cat, product_tag, and product_brand taxonomies.
		$taxonomies = array( 'product_cat', 'product_tag', 'product_brand' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $product_id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $term->term_id, 'prad_term_assigned_meta_inc', true ) ), true );
					if ( is_array( $meta_inc ) ) {
						$option_term = array_unique( array_merge( $option_term, $meta_inc ) );
					}
				}
			}
		}

		$merged     = array_unique( array_merge( $option_all, $option_term, $option_product ) );
		$option_ids = array_diff( $merged, $option_exclude );
		$counter    = 1;

		?>
		<div class="panel woocommerce_options_panel" id="prad_tab_data" style="padding: 20px !important; max-width: 560px;">
			<div style="font-size: 14px;font-weight: 500;margin-bottom: 16px !important;color:#0b0e04">Option Lists:</div>
			<div style="display: flex;flex-direction:column;gap: 16px;">
				<?php foreach ( $option_ids as $id ) : ?>
					<?php if ( 'publish' === get_post_status( $id ) ) : ?>
						<div style="display: flex;align-items:center;justify-content: space-between;gap: 16px;padding: 10px 20px !important;background-color:#fafdef;border-radius: 4px;box-shadow: 0px 2px 4px 0px rgba(92, 95, 88, 0.16)"><div style="font-size: 13px;font-weight: 500;color:0b0e04"><?php echo esc_html( get_the_title( $id ) ); ?></div><a target="_blank" href="<?php echo esc_attr( admin_url( 'admin.php?page=prad-dashboard#lists/' . $id ) ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none">
								<path
									stroke="currentColor"
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="1.2"
									d="M9.333 1.333 12 4l-7.333 7.333H2V8.667l7.333-7.334ZM2 14.667h12"
								/>
							</svg>
						</a></div>
						<?php
						++$counter;
					endif;
				endforeach;
				?>
			</div>
		</div>
		<?php
	}
}
