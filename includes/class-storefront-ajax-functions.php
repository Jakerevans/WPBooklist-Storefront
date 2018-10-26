<?php
/**
 * Class StoreFront_Ajax_Functions - class-wpbooklist-ajax-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'StoreFront_Ajax_Functions', false ) ) :
	/**
	 * StoreFront_Ajax_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class StoreFront_Ajax_Functions {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			// Get Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->trans = new WPBookList_StoreFront_Translations();
			$this->trans->trans_strings();

		}

		/**
		 * Function to save the Call-to-action text and the two images.
		 */
		public function wpbooklist_storefront_settings_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_storefront_settings_action_callback', 'security' );

			if ( isset( $_POST['calltoaction'] ) ) {
				$call_to_action = filter_var( wp_unslash( $_POST['calltoaction'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['libimg'] ) ) {
				$lib_img = filter_var( wp_unslash( $_POST['libimg'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bookimg'] ) ) {
				$book_img = filter_var( wp_unslash( $_POST['bookimg'] ), FILTER_SANITIZE_URL );
			}

			if ( '' === $lib_img || null === $lib_img || false !== strpos( $lib_img, 'placeholder.svg' ) ) {
				$lib_img = $this->trans->storefront_trans_27;
			}

			if ( '' === $book_img || null === $book_img || false !== strpos( $book_img, 'placeholder.svg' ) ) {
				$book_img = $this->trans->storefront_trans_27;
			}

			$data = array(
				'calltoaction' => $call_to_action,
				'libraryimg'   => $lib_img,
				'bookimg'      => $book_img,
			);

			$format       = array( '%s', '%s', '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$response     =  $wpdb->update( $wpdb->prefix . 'wpbooklist_jre_storefront_options', $data, $where, $format, $where_format );

			wp_die( $response );
		}

	}
endif;
