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
		 * Class Constructor - Simply calls the Translations.
		 */
		public function __construct() {

			// Get Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->trans = new WPBookList_StoreFront_Translations();
			$this->trans->trans_strings();

		}

		public function wpbooklist_save_woocommerce_storefront_settings_action_callback() {

			global $wpdb;

			check_ajax_referer( 'wpbooklist_save_woocommerce_storefront_settings_action_callback', 'security' );

			if ( isset( $_POST['defaultregularprice'] ) ) {
				$defaultregularprice = filter_var( wp_unslash( $_POST['defaultregularprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultsaleprice'] ) ) {
				$defaultsaleprice = filter_var( wp_unslash( $_POST['defaultsaleprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultsku'] ) ) {
				$defaultsku = filter_var( wp_unslash( $_POST['defaultsku'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultsalebegin'] ) ) {
				$defaultsalebegin = filter_var( wp_unslash( $_POST['defaultsalebegin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultsaleend'] ) ) {
				$defaultsaleend = filter_var( wp_unslash( $_POST['defaultsaleend'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultwidth'] ) ) {
				$defaultwidth = filter_var( wp_unslash( $_POST['defaultwidth'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultheight'] ) ) {
				$defaultheight = filter_var( wp_unslash( $_POST['defaultheight'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultweight'] ) ) {
				$defaultweight = filter_var( wp_unslash( $_POST['defaultweight'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultlength'] ) ) {
				$defaultlength = filter_var( wp_unslash( $_POST['defaultlength'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultstock'] ) ) {
				$defaultstock = filter_var( wp_unslash( $_POST['defaultstock'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultupsell'] ) ) {
				$defaultupsell = filter_var( wp_unslash( $_POST['defaultupsell'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultcrosssell'] ) ) {
				$defaultcrosssell = filter_var( wp_unslash( $_POST['defaultcrosssell'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultcategory'] ) ) {
				$defaultcategory = filter_var( wp_unslash( $_POST['defaultcategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultvirtual'] ) ) {
				$defaultvirtual = filter_var( wp_unslash( $_POST['defaultvirtual'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultreviews'] ) ) {
				$defaultreviews = filter_var( wp_unslash( $_POST['defaultreviews'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['defaultnote'] ) ) {
				$defaultnote = filter_var( wp_unslash( $_POST['defaultnote'] ), FILTER_SANITIZE_STRING );
			}

			$data = array(
				'defaultregularprice' => $defaultregularprice,
				'defaultsaleprice'    => $defaultsaleprice,
				'defaultsku'          => $defaultsku,
				'defaultsalebegin'    => $defaultsalebegin,
				'defaultsaleend'      => $defaultsaleend,
				'defaultwidth'        => $defaultwidth,
				'defaultheight'       => $defaultheight,
				'defaultweight'       => $defaultweight,
				'defaultlength'       => $defaultlength,
				'defaultstock'        => $defaultstock,
				'defaultupsell'       => $defaultupsell,
				'defaultcrosssell'    => $defaultcrosssell,
				'defaultcategory'     => $defaultcategory,
				'defaultvirtual'      => $defaultvirtual,
				'defaultreviews'      => $defaultreviews,
				'defaultnote'         => $defaultnote,
			);

			$format       = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$result       = $wpdb->update( $wpdb->prefix . 'wpbooklist_jre_storefront_options', $data, $where, $format, $where_format );

			wp_die( $result );
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
