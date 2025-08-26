<?php	// phpcs:ignore
/**
 * RequestAPI Action.
 *
 * @package PRAD\Options
 * @since 1.0.0
 */

namespace PRAD;

use WP_REST_Response;
use WC_Data_Store;

defined( 'ABSPATH' ) || exit;
/**
 * RequestAPI class to handle API requests.
 *
 * @since 1.0.0
 */
class RequestAPI {

	/**
	 * Initialize the RequestAPI class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Register hook
	 *
	 * @since 1.0.0
	 */
	public function register_route() {
		$routes = array(
			// Single Product Page file upload.
			array(
				'endpoint'            => 'upload-file',
				'methods'             => 'POST',
				'callback'            => array( $this, 'upload_files_callback' ),
				'permission_callback' => '__return_true',
			),
			array(
				'endpoint'            => 'set_analytics',
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_analytics_data_callback' ),
				'permission_callback' => '__return_true',
			),

			// Get Analytics Data
			array(
				'endpoint'            => 'get_analytics',
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_analytics_data_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),

			// Backend Option Listing.
			array(
				'endpoint'            => 'option_list',
				'methods'             => 'POST',
				'callback'            => array( $this, 'option_listing_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			// Duplicate List.
			array(
				'endpoint'            => 'list_duplicate',
				'methods'             => 'POST',
				'callback'            => array( $this, 'list_duplicate_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Import List.
			array(
				'endpoint'            => 'list_import',
				'methods'             => 'POST',
				'callback'            => array( $this, 'list_import_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Delete List.
			array(
				'endpoint'            => 'list_delete',
				'methods'             => 'POST',
				'callback'            => array( $this, 'list_delete_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Update List.
			array(
				'endpoint'            => 'list_update',
				'methods'             => 'POST',
				'callback'            => array( $this, 'list_update_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Get Option Edit Page Data.
			array(
				'endpoint'            => 'get_option',
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_option_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			// Save Option Edit Page Data.
			array(
				'endpoint'            => 'set_option',
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_option_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Option Assign Search Products.
			array(
				'endpoint'            => 'assign_search',
				'methods'             => 'POST',
				'callback'            => array( $this, 'assign_search_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			array(
				'endpoint'            => 'product_search',
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_product_search_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			array(
				'endpoint'            => 'products_details',
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_products_details_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			// Get Assign Data.
			array(
				'endpoint'            => 'get_assign',
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_assign_product_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			// Set Assign Data.
			array(
				'endpoint'            => 'set_assign',
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_assign_product_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Get Global Settings.
			array(
				'endpoint'            => 'get_global',
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_global_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			// Set Global Settings.
			array(
				'endpoint'            => 'set_global',
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_global_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Set Global Settings.
			array(
				'endpoint'            => 'install_plugin',
				'methods'             => 'POST',
				'callback'            => array( $this, 'install_plugin_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Product Image Compability.
			array(
				'endpoint'            => 'product_image',
				'methods'             => 'POST',
				'callback'            => array( $this, 'product_image_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			// Hello Bar.
			array(
				'endpoint'            => 'hello_bar',
				'methods'             => 'POST',
				'callback'            => array( $this, 'hello_bar_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
		);

		foreach ( $routes as $route ) {
			register_rest_route(
				'prad',
				$route['endpoint'],
				array(
					array(
						'methods'             => $route['methods'],
						'callback'            => $route['callback'],
						'permission_callback' => $route['permission_callback'],
					),
				)
			);
		}
	}

	/**
	 * Check permissions for endpoint.
	 *
	 * @return bool
	 */
	public function get_endpoint_permissions() {
		return current_user_can( apply_filters( 'prad_demo_capability_check', 'manage_options' ) );
	}

	/**
	 * Retrieves option data for a given post ID.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing option data or an error message.
	 */
	public function get_option_callback( \WP_REST_Request $request ) {
		$params = $request->get_params();
		$id     = isset( $params['id'] ) ? sanitize_text_field( $params['id'] ) : 0;
		$post   = get_post( $id );

		if ( ! $post ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Post not found.', 'product-addons' ),
				),
				404
			);
		}

		// Prepare the response data.
		$data = array(
			'id'      => $post->ID,
			'title'   => $post->post_title,
			'status'  => $post->post_status,
			'content' => get_post_meta( $id, 'prad_addons_blocks', true ),
			'error'   => json_last_error_msg(),
		);

		// Return the success response with the post data.
		return new WP_REST_Response(
			array(
				'success' => true,
				'post'    => $data,
			),
			200
		);
	}

	/**
	 * Updates or creates option data.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response indicating success or failure.
	 */
	public function set_option_callback( \WP_REST_Request $request ) {
		// Retrieve and sanitize request parameters.
		$params  = $request->get_params();
		$id      = isset( $params['id'] ) ? sanitize_text_field( $params['id'] ) : '';
		$title   = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : 'Untitled';
		$status  = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : 'draft';
		$content = isset( $params['content'] ) && is_array( $params['content'] ) ? product_addons()->sanitize_rest_params( $params['content'] ) : '';
		$css     = isset( $params['css'] ) ? product_addons()->sanitize_rest_params( $params['css'] ) : '';

		// Prepare the attributes for the post.
		$attr = array(
			'post_title'   => $title,
			'post_status'  => $status,
			'post_content' => $title,
			'post_type'    => 'prad_option',
		);

		if ( 'new' === $id ) {
			$id = wp_insert_post( $attr );
			if ( is_wp_error( $id ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => __( 'Failed to create a new option.', 'product-addons' ),
					),
					400
				);
			}

			update_post_meta( $id, 'prad_addons_blocks', $content );
			if ( $css ) {
				update_post_meta( $id, 'prad_addons_css', $css );
			}
			do_action( 'prad_wp_rocket_clear_cache' );

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'New option added.', 'product-addons' ),
					'id'      => $id,
				),
				200
			);
		} else {
			$attr['ID'] = $id;
			$update     = wp_update_post( $attr, true );

			if ( is_wp_error( $update ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => __( 'Failed to update the option.', 'product-addons' ),
					),
					400
				);
			}

			if ( $css ) {
				update_post_meta( $id, 'prad_addons_css', $css );
			}

			update_post_meta( $id, 'prad_addons_blocks', $content );

			do_action( 'prad_wp_rocket_clear_cache' );

			return new WP_REST_Response(
				array(
					'success' => true,
					'content' => $content,
					'message' => __( 'Option updated.', 'product-addons' ),
				),
				200
			);
		}
	}

	/**
	 * Updates the status of multiple options based on provided IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response indicating success or failure of the update.
	 */
	public function list_update_callback( \WP_REST_Request $request ) {
		$params = $request->get_params();
		$ids    = isset( $params['ids'] ) ? sanitize_text_field( $params['ids'] ) : '';
		$status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';

		if ( empty( $ids ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'No IDs provided.', 'product-addons' ),
				),
				400
			);
		}

		// Convert IDs to an array and update each post.
		$ids_array = explode( ',', $ids );
		foreach ( $ids_array as $id ) {
			$attr = array(
				'ID'          => (int) $id,
				'post_status' => ( 'active' === $status ) ? 'publish' : 'draft',
			);

			$update = wp_update_post( $attr, true );

			if ( is_wp_error( $update ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => sprintf(
							/* translators: %1s - Post Id, %2s - Error Message */
							__( 'Failed to update post ID: %1$s. Error: %2$s', 'product-addons' ),
							$id,
							$update->get_error_message()
						),
					),
					400
				);
			}
		}

		do_action( 'prad_wp_rocket_clear_cache' );
		// Return success response.
		return new WP_REST_Response(
			array(
				'success' => true,
				'status'  => $status,
				'message' => __( 'Items updated successfully.', 'product-addons' ),
			),
			200
		);
	}

	/**
	 * Deletes multiple options based on provided IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response indicating success or failure of the deletion.
	 */
	public function list_delete_callback( \WP_REST_Request $request ) {
		// Retrieve and sanitize request parameters.
		$params = $request->get_params();
		$ids    = isset( $params['ids'] ) ? sanitize_text_field( $params['ids'] ) : '';

		if ( empty( $ids ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'No IDs provided.', 'product-addons' ),
				),
				400
			);
		}

		// Convert IDs to an array and delete each post.
		$gallery_image_data = get_option( 'prad_product_image_update_data', array() );
		$ids_array          = explode( ',', $ids );
		foreach ( $ids_array as $id ) {
			$id = (int) $id; // Ensure ID is an integer.

			if ( isset( $gallery_image_data[ $id ] ) ) {
				unset( $gallery_image_data[ $id ] );
			}

			/**
			 * Fires before deleting a post with a specific ID.
			 *
			 * @param int $id The ID of the post to be deleted.
			 */
			do_action( 'prad_delete_option_product_meta', $id );

			wp_delete_post( $id, true );
		}

		update_option( 'prad_product_image_update_data', $gallery_image_data );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Items deleted successfully.', 'product-addons' ),
			),
			200
		);
	}


	/**
	 * Duplicates an option based on the provided ID.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response indicating success or failure of the duplication.
	 */
	public function list_duplicate_callback( \WP_REST_Request $request ) {
		// Retrieve and sanitize the request parameter.
		$params  = $request->get_params();
		$id      = isset( $params['id'] ) ? sanitize_text_field( $params['id'] ) : '';
		$content = isset( $params['content'] ) && is_array( $params['content'] ) ? product_addons()->sanitize_rest_params( $params['content'] ) : '';

		if ( empty( $id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'No ID provided.', 'product-addons' ),
				),
				400
			);
		}

		// Retrieve the post object and proceed with duplication.
		$post = get_post( $id );
		if ( ! $post ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Post not found.', 'product-addons' ),
				),
				404
			);
		}

		// Set up the arguments for duplicating the post.
		$args = array(
			'post_author'  => $post->post_author,
			'post_content' => $post->post_content,
			'post_name'    => $post->post_name,
			'post_status'  => 'draft',
			'post_title'   => $post->post_title . ' Copy',
			'post_type'    => $post->post_type,
		);

		// Insert the new post (duplicate).
		$new_id = wp_insert_post( $args );

		if ( is_wp_error( $new_id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => sprintf(
						/* translators: %1s - Post Id */
						__( 'Failed to duplicate post ID: %s.', 'product-addons' ),
						$id
					),
				),
				400
			);
		}

		// Copy the custom meta data.
		$blocks = $content ? $content : get_post_meta( $id, 'prad_addons_blocks', true );
		update_post_meta( $new_id, 'prad_addons_blocks', $blocks );

		// Return success response.
		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Item duplicated successfully.', 'product-addons' ),
				'new_id'  => $new_id,
			),
			200
		);
	}
	/**
	 * Import List
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response indicating success or failure of the duplication.
	 */
	public function list_import_callback( \WP_REST_Request $request ) {
		$params  = $request->get_params();
		$title   = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : '';
		$content = isset( $params['content'] ) && is_array( $params['content'] ) ? product_addons()->sanitize_rest_params( $params['content'] ) : array();

		$args = array(
			'post_status' => 'draft',
			'post_title'  => $title . ' Imported',
			'post_type'   => 'prad_option',
		);

		$new_id = wp_insert_post( $args );
		update_post_meta( $new_id, 'prad_addons_blocks', $content );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Imported successfully.', 'product-addons' ),
				'new_id'  => $new_id,
			),
			200
		);
	}

	/**
	 * Retrieves a list of options with search and pagination functionality.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing the list of options and pagination info.
	 */
	public function option_listing_callback( \WP_REST_Request $request ) {
		$params   = $request->get_params();
		$search   = isset( $params['search'] ) ? sanitize_text_field( $params['search'] ) : '';
		$paged    = isset( $params['page'] ) ? sanitize_text_field( $params['page'] ) : 1;
		$per_page = isset( $params['per_page'] ) ? sanitize_text_field( $params['per_page'] ) : 3;
		$order    = isset( $params['order'] ) ? sanitize_text_field( $params['order'] ) : 'DESC';

		$args = array(
			'post_type'      => 'prad_option',
			'posts_per_page' => $per_page,
			'order'          => $order,
			'orderby'        => 'ID',
			'post_status'    => array( 'publish', 'draft' ),
			'paged'          => $paged,
		);

		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		$query      = new \WP_Query( $args );
		$data       = array();
		$all_blocks = array();
		$page_num   = 0;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id                = get_the_ID();
				$blocks            = get_post_meta( $id, 'prad_addons_blocks', true );
				$all_blocks[ $id ] = $blocks;
				$data[]            = array(
					'id'       => $id,
					'title'    => get_the_title(),
					'status'   => get_post_status() === 'publish',
					'options'  => is_string( $blocks ) ? substr_count( $blocks, 'blockid' ) : substr_count( wp_json_encode( $blocks ), 'blockid' ),
					'assigned' => product_addons()->get_assigned_product_data( $id ),
				);
			}
			$page_num = $query->max_num_pages;

			wp_reset_postdata();
		}

		return new WP_REST_Response(
			array(
				'success'    => true,
				'page'       => $page_num,
				'posts'      => $data,
				'all_blocks' => $all_blocks,
			),
			200
		);
	}

	/**
	 * Searches for products or categories based on the provided keyword and trigger type.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing the search results.
	 */
	public function assign_search_callback( \WP_REST_Request $request ) {
		$params         = $request->get_params();
		$trigger_type   = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : 'products';
		$search_keyword = isset( $params['term'] ) ? sanitize_text_field( $params['term'] ) : '';
		$limit          = isset( $params['limit'] ) ? absint( $params['limit'] ) : 25;

		$response_data = array();
		switch ( $trigger_type ) {
			case 'products':
				$response_data = product_addons()->get_searched_products( $search_keyword, false, $limit );
				break;
			case 'cat':
			case 'tag':
			case 'brand':
				$response_data = product_addons()->get_searched_categories(
					array(
						'term'         => $search_keyword,
						'limit'        => $limit,
						'includes'     => '',
						'trigger_type' => $trigger_type,
					)
				);
				break;
			default:
				break;
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $response_data,
			),
			200
		);
	}

	/**
	 * Searches for products or categories based on the provided keyword and trigger type.
	 *
	 * @since 1.0.3
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing the search results.
	 */
	public function get_product_search_callback( \WP_REST_Request $request ) {
		$params         = $request->get_params();
		$search_keyword = isset( $params['term'] ) ? sanitize_text_field( $params['term'] ) : '';
		$limit          = isset( $params['limit'] ) ? absint( $params['limit'] ) : 5;
		$exclude_ids    = isset( $params['excludes'] ) ? $params['excludes'] : array();

		// Load the product data store.
		$data_store = WC_Data_Store::load( 'product' );

		$include_ids = array();
		$limit       = '5';
		$ids         = $data_store->search_products( $search_keyword, '', false, false, $limit, $include_ids, $exclude_ids );
		$products    = array();

		foreach ( $ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( $product ) {
				$datas = array(
					'id'         => $product_id,
					'url'        => get_permalink( $product_id ),
					'value'      => rawurldecode( wp_strip_all_tags( $product->get_name() ) ),
					'img'        => wp_get_attachment_url( $product->get_image_id() ),
					'isVariable' => $product->is_type( 'variable' ),
				);

				if ( $product->is_type( 'variable' ) ) {
					$available_variations = $product->get_available_variations();
					$variations_data      = array();

					foreach ( $available_variations as $variation_data ) {
						$variation_id = $variation_data['variation_id'];
						$variation    = wc_get_product( $variation_id );

						if ( $variation && $variation->is_purchasable() && $variation->is_in_stock() ) {
							$variations_data[] = array(
								'id'         => $variation_id,
								'url'        => get_permalink( $variation_id ),
								'value'      => rawurldecode( wp_strip_all_tags( $variation->get_name() ) ),
								'img'        => wp_get_attachment_url( $variation->get_image_id() ),
								'attributes' => wc_get_product_variation_attributes( $variation_id ),
								'regular'    => $variation->get_regular_price( 'edit' ),
								'sale'       => $variation->get_sale_price( 'edit' ),
							);
						}
					}
					$datas['variation'] = $variations_data;
				}
				$products[] = $datas;
			}
		}

		return $products;
	}
	/**
	 * Searches for products or categories based on the provided keyword and trigger type.
	 *
	 * @since 1.0.3
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing the search results.
	 */
	public function get_products_details_callback( \WP_REST_Request $request ) {
		$params = $request->get_params();
		$items  = isset( $params['items'] ) && is_array( $params['items'] ) ? $params['items'] : array();
		$output = array();

		foreach ( $items as $item ) {
			$product_id = isset( $item['id'] ) ? absint( $item['id'] ) : 0;

			if ( ! $product_id ) {
				continue;
			}

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				continue;
			}

			$data = array(
				'id'      => $product_id,
				'url'     => get_permalink( $product_id ),
				'value'   => rawurldecode( wp_strip_all_tags( $product->get_name() ) ),
				'img'     => wp_get_attachment_url( $product->get_image_id() ),
				'regular' => $product->get_regular_price( 'edit' ),
				'sale'    => $product->get_sale_price( 'edit' ),
			);

			if ( $product->is_type( 'variable' ) ) {
				$variation_ids_input  = is_array( $item['variation'] ) ? array_map( 'absint', $item['variation'] ) : array();
				$available_variations = $product->get_available_variations();
				$variations_data      = array();

				foreach ( $available_variations as $variation_data ) {
					$variation_id = $variation_data['variation_id'];
					$variation    = wc_get_product( $variation_id );

					if ( $variation && $variation->is_purchasable() && $variation->is_in_stock() ) {
						$variations_data[] = array(
							'id'         => $variation_id,
							'url'        => get_permalink( $variation_id ),
							'value'      => rawurldecode( wp_strip_all_tags( $variation->get_name() ) ),
							'img'        => wp_get_attachment_url( $variation->get_image_id() ),
							'attributes' => wc_get_product_variation_attributes( $variation_id ),
							'regular'    => $variation->get_regular_price( 'edit' ),
							'sale'       => $variation->get_sale_price( 'edit' ),
							'enable'     => in_array( $variation_id, $variation_ids_input ),
						);
					}
				}
				$data['variation'] = $variations_data;
			}

			$output[] = $data;
		}

		return $output;
	}

	/**
	 * Retrieves assigned product data based on the provided option ID.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST request object containing the option ID.
	 *
	 * @return \WP_REST_Response The REST response containing the assigned data or an error message.
	 */
	public function get_assign_product_callback( \WP_REST_Request $request ) {
		$request_params = $request->get_params();
		$option_id      = ! empty( $request_params['option_id'] ) ? sanitize_text_field( $request_params['option_id'] ) : '';

		if ( $option_id ) {
			return new WP_REST_Response(
				array(
					'success'  => true,
					'assigned' => product_addons()->get_assigned_product_data( $option_id ),
				),
				200
			);
		}

		// If option_id is missing, return a message indicating the issue.
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => 'Option ID Missing',
			),
			400
		);
	}


	/**
	 * Set assigned product data for a given option.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST request object containing the option ID and assignment data.
	 *
	 * @return \WP_REST_Response The REST response containing the success message or an error response.
	 */
	public function set_assign_product_callback( \WP_REST_Request $request ) {
		$params        = $request->get_params();
		$option_id     = ! empty( $params['option_id'] ) ? sanitize_text_field( $params['option_id'] ) : '';
		$product_image = ! empty( $params['product_image'] ) ? product_addons()->sanitize_rest_params( $params['product_image'] ) : array();

		if ( empty( $option_id ) ) {
			return new WP_REST_Response(
				array(
					'success'  => false,
					'response' => array(
						'message' => __( 'No ID found', 'product-addons' ),
					),
				),
				400
			);
		}

		$new_image_data               = get_option( 'prad_product_image_update_data', array() );
		$new_image_data[ $option_id ] = $product_image;
		update_option( 'prad_product_image_update_data', $new_image_data );

		$raw_data = isset( $params['raw_data'] ) ? product_addons()->sanitize_rest_params( $params['raw_data'] ) : array();

		/* First Remove existing assign include / excludes meta */
		$this->handle_existing_assign_meta( $option_id );

		// Update Option Data Both Option Data and Product/Term Data.
		if ( 'specific_product' === $raw_data['aType'] ) {  // Update meta for Specific Product.
			if ( is_array( $raw_data['includes'] ) && ! empty( $raw_data['includes'] ) ) {
				foreach ( $raw_data['includes'] as $include ) {
					$meta_inc = json_decode( stripslashes( get_post_meta( $include, 'prad_product_assigned_meta_inc', true ) ), true );
					$meta_inc = is_array( $meta_inc ) ? $meta_inc : array();

					if ( ! in_array( $option_id, $meta_inc, false ) ) {
						$meta_inc[] = $option_id;
					}
					update_post_meta( $include, 'prad_product_assigned_meta_inc', wp_json_encode( $meta_inc ) );
				}
			}
		} elseif ( 'specific_category' === $raw_data['aType'] || 'specific_tag' === $raw_data['aType'] || 'specific_brand' === $raw_data['aType'] ) {  /* Update meta for Terms */
			if ( is_array( $raw_data['includes'] ) && ! empty( $raw_data['includes'] ) ) {
				foreach ( $raw_data['includes'] as $include ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $include, 'prad_term_assigned_meta_inc', true ) ), true );
					$meta_inc = is_array( $meta_inc ) ? $meta_inc : array();

					if ( ! in_array( $option_id, $meta_inc, false ) ) {
						$meta_inc[] = $option_id;
					}
					update_term_meta( $include, 'prad_term_assigned_meta_inc', wp_json_encode( $meta_inc ) );
				}
			}
		} elseif ( 'all_product' === $raw_data['aType'] ) {        // Update meta for All Products.
			$option_settings = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
			$option_settings = is_array( $option_settings ) ? $option_settings : array();

			if ( ! in_array( $option_id, $option_settings, false ) ) {
				$option_settings[] = $option_id;
			}
			update_option( 'prad_option_assign_all', wp_json_encode( $option_settings ) );
		}

		// Update Meta for Excludes Products.
		if ( is_array( $raw_data['excludes'] ) && count( $raw_data['excludes'] ) > 0 ) {
			foreach ( $raw_data['excludes'] as $exclude ) {
				$meta_exc = json_decode( stripslashes( get_post_meta( $exclude, 'prad_product_assigned_meta_exc', true ) ), true );
				$meta_exc = is_array( $meta_exc ) ? $meta_exc : array();

				if ( ! in_array( $option_id, $meta_exc, false ) ) {
					$meta_exc[] = $option_id;
				}
				update_post_meta( $exclude, 'prad_product_assigned_meta_exc', wp_json_encode( $meta_exc ) );
			}
		}

		// Update the option meta with the assigned data.
		update_post_meta( $option_id, 'prad_base_assigned_data', wp_json_encode( $raw_data ) );

		return new WP_REST_Response(
			array(
				'success'  => true,
				'response' => array(
					'product_image' => $product_image,
					'option_id'     => $option_id,
					'message'       => __( 'Option Assigned Updated successfully', 'product-addons' ),
					'newData'       => json_decode( stripslashes( get_post_meta( $option_id, 'prad_base_assigned_data', true ) ), true ),
				),
			),
			200
		);
	}

	/**
	 * Handle Existing Assign Data
	 *
	 * @since 1.0.0
	 * @param int $option_id The ID of the option to retrieve assigned data for.
	 *
	 * @return void
	 */
	public function handle_existing_assign_meta( $option_id ) {
		$assigned_data = json_decode( stripslashes( get_post_meta( $option_id, 'prad_base_assigned_data', true ) ), true );
		if ( isset( $assigned_data['aType'] ) && 'all_product' === $assigned_data['aType'] ) {
			$option_settings = json_decode( stripslashes( get_option( 'prad_option_assign_all', '[]' ) ), true );
			$option_settings = is_array( $option_settings ) ? $option_settings : array();

			if ( in_array( $option_id, $option_settings, false ) ) {
				$option_settings = array_diff( $option_settings, array( $option_id ) );
			}
			update_option( 'prad_option_assign_all', wp_json_encode( $option_settings ) );
		} elseif ( isset( $assigned_data['aType'] ) && 'specific_product' === $assigned_data['aType'] ) {
			if ( is_array( $assigned_data['includes'] ) && ! empty( $assigned_data['includes'] ) ) {
				foreach ( $assigned_data['includes'] as $include ) {
					$meta_inc = json_decode( stripslashes( get_post_meta( $include, 'prad_product_assigned_meta_inc', true ) ), true );
					$meta_inc = is_array( $meta_inc ) ? $meta_inc : array();

					if ( in_array( $option_id, $meta_inc, false ) ) {
						$meta_inc = array_diff( $meta_inc, array( $option_id ) );
					}
					update_post_meta( $include, 'prad_product_assigned_meta_inc', wp_json_encode( $meta_inc ) );
				}
			}
		} elseif ( isset( $assigned_data['aType'] ) && ( 'specific_category' === $assigned_data['aType'] || 'specific_tag' === $assigned_data['aType'] || 'specific_brand' === $assigned_data['aType'] ) ) {
			if ( is_array( $assigned_data['includes'] ) && ! empty( $assigned_data['includes'] ) ) {
				foreach ( $assigned_data['includes'] as $include ) {
					$meta_inc = json_decode( stripslashes( get_term_meta( $include, 'prad_term_assigned_meta_inc', true ) ), true );
					$meta_inc = is_array( $meta_inc ) ? $meta_inc : array();

					if ( in_array( $option_id, $meta_inc, false ) ) {
						$meta_inc = array_diff( $meta_inc, array( $option_id ) );
					}
					update_term_meta( $include, 'prad_term_assigned_meta_inc', wp_json_encode( $meta_inc ) );
				}
			}
		}
		if ( isset( $assigned_data['excludes'] ) && is_array( $assigned_data['excludes'] ) && count( $assigned_data['excludes'] ) > 0 ) {
			foreach ( $assigned_data['excludes'] as $exclude ) {
				$meta_exc = json_decode( stripslashes( get_post_meta( $exclude, 'prad_product_assigned_meta_exc', true ) ), true );
				$meta_exc = is_array( $meta_exc ) ? $meta_exc : array();

				if ( in_array( $option_id, $meta_exc, false ) ) {
					$meta_exc = array_diff( $meta_exc, array( $option_id ) );
				}
				update_post_meta( $exclude, 'prad_product_assigned_meta_exc', wp_json_encode( $meta_exc ) );
			}
		}
	}

	/**
	 * Get global data.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response The REST response containing the global data.
	 */
	public function get_global_callback() {
		return new WP_REST_Response(
			array(
				'success'  => true,
				'response' => get_option( 'prad_global_style', '' ),
			),
			200
		);
	}
	/**
	 * Set global data.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The request object containing the data.
	 *
	 * @return \WP_REST_Response The REST response with success or error message.
	 */
	public function set_global_callback( \WP_REST_Request $request ) {
		$request_params = $request->get_params();
		$style          = isset( $request_params['style'] ) ? $request_params['style'] : '';
		$css            = isset( $request_params['css'] ) ? $request_params['css'] : '';

		if ( $style ) {
			update_option( 'prad_global_style', $style );
		}
		if ( $css ) {
			update_option( 'prad_global_style_css', $css );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Style saved successfully.', 'product-addons' ),
			),
			200
		);
	}



	/**
	 * Product Image Compability
	 *
	 * @since 1.0.5
	 *
	 * @param \WP_REST_Request $request The request object containing the data.
	 *
	 * @return \WP_REST_Response The REST response with success or error message.
	 */
	public function product_image_callback( \WP_REST_Request $request ) {
		$request_params = $request->get_params();
		$productData    = isset( $request_params['productData'] ) ? $request_params['productData'] : '';

		$to_return = array();
		if ( ! empty( $productData ) && is_array( $productData ) ) {
			foreach ( $productData as $key => $value ) {
				$id                = function_exists( 'attachment_url_to_postid' ) && $value['src'] ? attachment_url_to_postid( $value['src'] ) : '';
				$to_return[ $key ] = $id;
			}
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'message'   => $productData,
				'to_return' => $to_return,
			),
			200
		);
	}


	/**
	 * Customize the upload directory path for PRAD files.
	 *
	 * @param array $upload The existing upload directory data.
	 * @return array The modified upload directory data.
	 */
	public function prad_handle_upload_dir( $upload ) {
		$directory        = 'prad_option_files';
		$upload['subdir'] = '/' . $directory;
		$upload['path']   = $upload['basedir'] . $upload['subdir'];
		$upload['url']    = $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}
	/**
	 * Handles file uploads for PRAD.
	 *
	 * @since check_version
	 *
	 * @return WP_REST_Response Response indicating success or failure.
	 */
	public function upload_files_callback() {
		if (
		empty( $_FILES['prad_file'] ) ||
		! isset( $_FILES['prad_file']['name'] ) ||
		( ! ( isset( $_POST['pradnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['pradnonce'] ) ), 'prad-nonce' ) ) )
		) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'No file found or invalid nonce.', 'product-addons' ),
				),
				400
			);
		}

		$uploaded_file = $_FILES['prad_file']; // phpcs:ignore
		// Check if the wp_handle_upload function exists.
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		// Allowed file types and size.
		$allowed_extensions = array( 'jpg', 'jpeg', 'png', 'txt', 'pdf', 'csv', 'doc', 'ppt' );
		$allowed_mime_types = array( 'image/jpeg', 'image/png', 'text/plain', 'application/pdf', 'text/csv', 'application/msword', 'application/vnd.ms-powerpoint' );
		$max_file_size      = 5 * 1024 * 1024; // 5MB
		$file_error         = '';
		// Validate file size.
		if ( $uploaded_file['size'] > $max_file_size ) {
			$file_error = __( 'File size exceeds the maximum allowed limit.', 'product-addons' );
		}
		// Validate file extension.
		$file_extension = strtolower( pathinfo( $uploaded_file['name'], PATHINFO_EXTENSION ) );
		if ( ! in_array( $file_extension, $allowed_extensions, true ) ) {
			$file_error = __( 'Invalid file extension. Allowed types are: jpg, jpeg, png, txt, pdf, csv, doc, ppt.', 'product-addons' );
		}
		// Validate MIME type.
		$file_mime_type = mime_content_type( $uploaded_file['tmp_name'] ); // Use tmp_name for accuracy.
		if ( ! in_array( $file_mime_type, $allowed_mime_types, true ) ) {
			$file_error = __( 'Invalid MIME type. Allowed types are: image/jpeg, image/png, text/plain, application/pdf.', 'product-addons' );
		}
		// Return error response if validation fails.
		if ( $file_error ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $file_error,
				),
				400
			);
		}
		// Upload settings.
		$upload_overrides = array( 'test_form' => false );
		// Add custom upload directory filter.
		add_filter( 'upload_dir', array( $this, 'prad_handle_upload_dir' ) );
		// Handle the file upload.
		$uploaded = wp_handle_upload( $uploaded_file, $upload_overrides );
		// Remove the custom upload directory filter after processing.
		remove_filter( 'upload_dir', array( $this, 'prad_handle_upload_dir' ) );
		if ( isset( $uploaded['error'] ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $uploaded['error'],
				),
				400
			);
		}
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'file' => $uploaded,
				),
			),
			200
		);
	}

	public function set_analytics_data_callback( \WP_REST_Request $request ) {
		$request_params = $request->get_params();
		$option_id      = isset( $request_params['optionId'] ) ? sanitize_text_field( $request_params['optionId'] ) : '';
		$type           = isset( $request_params['type'] ) ? sanitize_text_field( $request_params['type'] ) : '';

		if ( $option_id && $type ) {
			do_action( 'prad_update_stats_table_data', $option_id, $type, '' );
			return new WP_REST_Response(
				array(
					'success'   => true,
					'option_id' => $option_id,
					'type'      => $type,
					'message'   => __( 'Analytics data updated.', 'product-addons' ),
				),
				200
			);
		}
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Invalid Analytics data.', 'product-addons' ),
			),
			400
		);
	}

	/**
	 * Retrieves a list of options with search and pagination functionality.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response Response containing the list of options and pagination info.
	 */
	public function get_analytics_data_callback( \WP_REST_Request $request ) {
		$params = $request->get_params();
		$search = isset( $params['search'] ) ? sanitize_text_field( $params['search'] ) : '';

		global $wpdb;

		$table_name   = $wpdb->prefix . 'prad_stats_graph';
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if ( $table_exists ) {
			$stats_graph = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}prad_stats_graph ORDER BY id ASC" );
		} else {
			$wpdb->hide_errors();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$analytics = new \PRAD\PRAD_Analytics();
			$analytics->create_stats_graph_table();
			$stats_graph = array();
		}

		return new WP_REST_Response(
			array(
				'success'     => true,
				'stats_table' => $this->stats_table_data(),
				'stats_graph' => ! empty( $stats_graph ) ? $stats_graph : array(),
			),
			200
		);
	}

	public function stats_table_data() {
		global $wpdb;

		$table_name   = $wpdb->prefix . 'prad_stats_table';
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if ( ! $table_exists ) {
			$wpdb->hide_errors();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$analytics = new \PRAD\PRAD_Analytics();
			$analytics->create_stats_table();
			return array();
		}

		$paged    = 1;
		$per_page = -1;
		$order    = 'DESC';

		$args = array(
			'post_type'      => 'prad_option',
			'posts_per_page' => $per_page,
			'order'          => $order,
			'orderby'        => 'ID',
			'post_status'    => array( 'publish', 'draft' ),
			'paged'          => $paged,
		);

		$query = new \WP_Query( $args );
		$data  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id           = get_the_ID();
				$option_stats = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}prad_stats_table WHERE option_id = %d",
						$id
					)
				);

				if ( ! empty( $option_stats ) ) {
					$option_stats = $option_stats[0];
				} else {
					$option_stats = (object) array();
				}

				$click_rate = ( isset( $option_stats->impression_count ) && isset( $option_stats->click_count ) && $option_stats->impression_count > 0 ) ? ( $option_stats->click_count / $option_stats->impression_count ) * 100 : 0;
				$cart_rate  = ( isset( $option_stats->impression_count ) && isset( $option_stats->add_to_cart_count ) && $option_stats->impression_count > 0 ) ? ( $option_stats->add_to_cart_count / $option_stats->impression_count ) * 100 : 0;

				$data[] = array(
					'id'           => $id,
					'title'        => get_the_title(),
					'click'        => round( $click_rate ),
					'cart'         => round( $cart_rate ),
					'sales'        => isset( $option_stats->sales ) ? $option_stats->sales : 0,
					'option_stats' => $option_stats,
					'assigned'     => product_addons()->get_assigned_product_data( $id ),
				);
			}

			wp_reset_postdata();
		}

		return $data;
	}

	/**
	 * Hello Bar CallBack
	 *
	 * @since 1.0.7
	 *
	 * @param \WP_REST_Request $request The request object containing the data.
	 *
	 * @return \WP_REST_Response The REST response with success or error message.
	 */
	public function hello_bar_callback( \WP_REST_Request $request ) {
		$request_params = $request->get_params();
		$type           = isset( $request_params['type'] ) ? $request_params['type'] : '';
		$duration       = isset( $request_params['duration'] ) ? $request_params['duration'] : 1296000;

		if ( 'hello_bar' === $type ) {
			product_addons()->set_transient_without_cache( 'prad_helloBar', 'hide', $duration );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Hello Bar Action performed', 'product-addons' ),
			),
			200
		);
	}
}
