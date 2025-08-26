<?php	// phpcs:ignore
/**
 * Cart_Page.
 *
 * @package PRAD
 * @since v.1.0.0
 */
namespace PRAD;

defined( 'ABSPATH' ) || exit;

/**
 * Render_Blocks class.
 */
class Checkout_Page {
	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'woocommerce_checkout_create_order' ), 10 );
		add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'woocommerce_checkout_create_order' ), 10 );
		add_action( 'woocommerce_view_order', array( $this, 'prad_custom_view_order_fields' ), 10, 1 );
		add_action( 'woocommerce_thankyou', array( $this, 'prad_custom_view_order_fields' ), 10, 1 );
	}

	/**
	 * Display and enqueue custom assets for the "View Order" page in My Account.
	 *
	 * Retrieves the WooCommerce order by ID and checks for the custom meta field
	 * `_prad_option_ids`. If the meta exists and is not empty, enqueue the
	 * required CSS and JavaScript files for displaying custom order details.
	 *
	 * @param int $order_id The ID of the WooCommerce order being viewed.
	 *
	 * @return void
	 */
	public function prad_custom_view_order_fields( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$custom_note = $order->get_meta( '_prad_option_ids' );

		if ( ! empty( $custom_note ) ) {
			wp_enqueue_style( 'prad-cart-style', PRAD_URL . 'assets/css/wowcart.css', array(), PRAD_VER );
			wp_enqueue_script( 'prad-cart-script', PRAD_URL . 'assets/js/wowcart.js', array( 'jquery' ), PRAD_VER, true );
		}
	}

	/**
	 * WooCommerce create order line item
	 *
	 * @param object   $item Item Data.
	 * @param string   $cart_item_key Cart Item Key.
	 * @param array    $cart_item Cart Item.
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $cart_item, $order ) {

		// Item order.
		if ( ! empty( $cart_item['prad_option_published_ids'] ) ) {
			$item->add_meta_data( '_prad_option_ids', $cart_item['prad_option_published_ids'] );
		}

		if ( ! empty( $cart_item['prad_selection']['extra_data'] ) ) {
			foreach ( $cart_item['prad_selection']['extra_data'] as $val ) {
				$item->add_meta_data( $val['name'], $val['value'] );
			}
		}
		if ( isset( $cart_item['prad_selection']['price_data'] ) ) {
			$item->add_meta_data( '_prad_option_price_data', $cart_item['prad_selection']['price_data'] );
		}
	}


	/**
	 * Perform action after create order on WooCommerce
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function woocommerce_checkout_create_order( $order ) {
		$order = wc_get_order( $order );

		if ( ! $order ) {
			return;
		}

		// Get all items from the order.
		$items = $order->get_items();

		$data = array();

		// Loop through each item in the order.
		foreach ( $items as $item ) {
			// Get the campaign ID from the item's meta data.
			$option_ids             = $item->get_meta( '_prad_option_ids' );
			$prad_option_price_data = $item->get_meta( '_prad_option_price_data' );

			if ( $option_ids ) {
				$order->update_meta_data( '_prad_option_ids', $option_ids );
				$option_ids = (array) $option_ids;
				$data       = array_unique( array_merge( $data, $option_ids ) );
				if ( $prad_option_price_data ) {
					foreach ( $option_ids as $opt_id ) {
						if ( isset( $prad_option_price_data[ $opt_id ] ) ) {
							do_action( 'prad_update_stats_table_data', $opt_id, 'sales', $prad_option_price_data[ $opt_id ] );
						}
					}
				}
			}
		}

		if ( ! empty( $data ) ) {
			$order->save();
			foreach ( $data as $campaign_id ) {
				do_action( 'prad_update_stats_table_data', $campaign_id, 'order_count', '' );
				// do_action( 'prad_update_stats_table_data', $campaign_id, 'sales', $order->get_total() );
			}
		}
	}
}
