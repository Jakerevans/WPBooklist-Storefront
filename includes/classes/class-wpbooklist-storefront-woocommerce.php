<?php
/**
 * WPBookList WPBookList_StoreFront_WooCommerce Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_StoreFront_WooCommerce', false ) ) :
	/**
	 * WPBookList_StoreFront_WooCommerce Class.
	 */
	class WPBookList_StoreFront_WooCommerce {

		public $post_id;

		/** Class Constructor - Simply calls the Translations
		 *
		 *  @param array $book_array - The array that holds all the book info.
		 *  @param int   $id - The book's id.
		 */
		public function __construct( $book_array, $id, $title, $description, $image, $upsells, $crosssells ) {

			$this->title       = $title;
			$this->description = $description;
			$this->image       = $image;
			$this->upsells     = $upsells;
			$this->crosssells  = $crosssells;

			// Setting up $book_array values, wrapped in isset() to prevent php error_log notices.
			if ( isset( $book_array['download'] ) ) {
				$this->download = $book_array['download'];
			}

			if ( isset( $book_array['height'] ) ) {
				$this->height = $book_array['height'];
			}

			if ( isset( $book_array['length'] ) ) {
				$this->length = $book_array['length'];
			}

			if ( isset( $book_array['price'] ) ) {
				$this->price = $book_array['price'];
			}

			if ( isset( $book_array['productcategory'] ) ) {
				$this->productcategory = $book_array['productcategory'];
			}

			if ( isset( $book_array['purchasenote'] ) ) {
				$this->purchasenote = $book_array['purchasenote'];
			}

			if ( isset( $book_array['regularprice'] ) ) {
				$this->regularprice = $book_array['regularprice'];
			}

			if ( isset( $book_array['reviews'] ) ) {
				$this->reviews = $book_array['reviews'];
			}

			if ( isset( $book_array['salebegin'] ) ) {
				$this->salebegin = $book_array['salebegin'];
			}

			if ( isset( $book_array['saleend'] ) ) {
				$this->saleend = $book_array['saleend'];
			}

			if ( isset( $book_array['saleprice'] ) ) {
				$this->saleprice = $book_array['saleprice'];
			}

			if ( isset( $book_array['sku'] ) ) {
				$this->sku = $book_array['sku'];
			}

			if ( isset( $book_array['stock'] ) ) {
				$this->stock = $book_array['stock'];
			}

			if ( isset( $book_array['virtual'] ) ) {
				$this->virtual = $book_array['virtual'];
			}

			if ( isset( $book_array['weight'] ) ) {
				$this->weight = $book_array['weight'];
			}

			if ( isset( $book_array['width'] ) ) {
				$this->width = $book_array['width'];
			}

			if ( isset( $book_array['woocommerce'] ) ) {
				$this->woocommerce = $book_array['woocommerce'];
			}

			if ( isset( $book_array['woofile'] ) ) {
				$this->woofile = $book_array['woofile'];
			}

			global $wpdb;
			$table = $wpdb->prefix . 'wpbooklist_jre_storefront_options';
			$row   = $wpdb->get_row( "SELECT * FROM $table" );

			// If we're creating a new product - otherwise, we're editing an existing one.
			if ( '' === $id || null === $id ) {
				$post_id = wp_insert_post(
					array(
						'post_author'  => get_current_user_id(),
						'post_name'    => wp_strip_all_tags( $this->title ),
						'post_title'   => wp_strip_all_tags( $this->title ),
						'post_status'  => 'publish',
						'post_type'    => 'product',
						'post_content' => wp_strip_all_tags( $this->description ),
						'post_excerpt' => wp_strip_all_tags( $this->description ),
					)
				);
			} else {
				$post_id = $id;
			}

			// Create image for product.
			$this->wpbooklist_woocommerce_create_image( $this->image, $post_id );

			// Create Upsells and Crosssells arrays.
			$upsell_array    = array();
			$upsells         = ltrim( $this->upsells, ',' );
			$upsell_array    = explode( ',', $this->upsells );
			$crosssell_array = array();
			$crosssells      = ltrim( $this->crosssells, ',' );
			$crosssell_array = explode( ',', $this->crosssells );

			// Converting the Virtual value from 'Yes' to 'true', so that WooCommerce will place a checkbox in the 'Virtual' box.
			if ( 'Yes' === $this->virtual ) {
				$this->virtual = 'true';
			}

			// Set all object terms and post meta for the product.
			wp_set_object_terms( $post_id, $this->productcategory, 'product_cat' );
			wp_set_object_terms( $post_id, 'simple', 'product_type' );
			wp_set_object_terms( $post_id, $upsells, '_upsell_ids' );
			update_post_meta( $post_id, '_upsell_ids', $upsell_array );
			wp_set_object_terms( $post_id, $crosssells, '_crosssell_ids' );
			update_post_meta( $post_id, '_crosssell_ids', $crosssell_array );
			update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status', 'instock' );
			update_post_meta( $post_id, 'total_sales', '0' );
			update_post_meta( $post_id, '_downloadable', $this->download );
			update_post_meta( $post_id, '_virtual', $this->virtual );
			update_post_meta( $post_id, '_regular_price', $this->price );
			update_post_meta( $post_id, '_sale_price', $this->saleprice );
			update_post_meta( $post_id, '_purchase_note', $this->purchasenote );
			update_post_meta( $post_id, '_featured', 'no' );
			update_post_meta( $post_id, '_weight', $this->weight );
			update_post_meta( $post_id, '_length', $this->length );
			update_post_meta( $post_id, '_width', $this->width );
			update_post_meta( $post_id, '_height', $this->height );
			update_post_meta( $post_id, '_sku', $this->sku );
			update_post_meta( $post_id, '_product_attributes', array() );
			update_post_meta( $post_id, '_sale_price_dates_from', $this->salebegin );
			update_post_meta( $post_id, '_sale_price_dates_to', $this->saleend );
			update_post_meta( $post_id, '_price', $this->price );
			update_post_meta( $post_id, '_sold_individually', '' );
			update_post_meta( $post_id, '_manage_stock', 'no' );
			update_post_meta( $post_id, '_backorders', 'no' );
			update_post_meta( $post_id, '_stock', $this->stock );

			if ( '' !== $this->woofile ) {
				$file_url = wp_get_attachment_url( $this->woofile );
				$md5_num  = md5( $file_url );

				// Inserting new file in the exiting array of downloadable files.
				$woo_file[0][ $md5_num ] = array(
					'name' => wp_strip_all_tags( $this->title ),
					'file' => $file_url,
				);

				// Updating database with the new array.
				update_post_meta( $post_id, '_downloadable_files', $woo_file[0] );
			}

			$this->post_id = $post_id;
		}

		/** Creates the image for the Product.
		 *
		 *  @param string $image_url - The URL of the image for the book.
		 *  @param int    $post_id - The id for the product.
		 */
		private function wpbooklist_woocommerce_create_image( $image_url, $post_id ) {

			$upload_dir = wp_upload_dir();

			// Get the Image data from the URL.
			$image_data = '';
			$response   = wp_remote_get( $image_url );
			// Check the response code.
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );

			if ( 200 !== $response_code && ! empty( $response_message ) ) {
				return new WP_Error( $response_code, $response_message );
			} elseif ( 200 !== $response_code ) {
				return new WP_Error( $response_code, 'Unknown error occurred' );
			} else {
				$image_data = wp_remote_retrieve_body( $response );
			}

			$image_url = str_replace( '%', '', $image_url );
			$filename  = basename( $image_url );

			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			// Initialize the WP filesystem.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$result      = $wp_filesystem->put_contents( $file, $image_data );
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2        = set_post_thumbnail( $post_id, $attach_id );
		}
	}

endif;
