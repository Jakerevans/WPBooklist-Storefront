<?php
/**
 * WPBookList WPBookList_Storefront_Update Class
 *
 * @author   Jake Evans
 * @category admin
 * @package  classes/update
 * @version  1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Storefront_Update', false ) ) :
	/**
	 * WPBookList_Storefront_Update Class.
	 */
	class WPBookList_Storefront_Update {

		/**
		 * Class Constructor
		 */
		public function __construct() {

			$this->wpbooklist_storefront_update_kickoff();

		}


		/**
		 * Outputs the actual HTML for the tab.
		 */
		public function wpbooklist_storefront_update_kickoff() {

			if ( ! class_exists( 'WPBookList_Storefront_Update_Actual' ) ) {

				// Load our custom updater if it doesn't already exist.
				require_once( STOREFRONT_UPDATE_DIR . 'class-wpbooklist-storefront-update-actual.php' );
			}

			global $wpdb;

			// Get license key from plugin options, if it's already been saved. If it has, don't display anything.
			$extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_storefront_settings' );
			$extension_settings = explode( '---', $extension_settings->treh);

			// Retrieve our license key from the DB.
			$license_key = $extension_settings[0];

			// Setup the updater.
			$edd_updater = new WPBookList_Storefront_Update_Actual( EDD_SL_STORE_URL_STOREFRONT, STOREFRONT_ROOT_DIR . 'wpbooklist-storefront.php', array(
				'version' => WPBOOKLIST_STOREFRONT_VERSION_NUM,
				'license' => $license_key,
				'item_id' => EDD_SL_ITEM_ID_STOREFRONT,
				'author'  => 'Jake Evans',
				'url'     => home_url(),
				'beta'    => false,
			) );

			/*
			$to_send = array(
				'slug'   => $edd_updater->slug,
				'is_ssl' => is_ssl(),
				'fields' => array(
					'banners' => array(),
					'reviews' => false,
					'icons'   => array(),
				),
			);

			$edd_updater->api_request( 'plugin_information', $to_send );
			*/

		}
	}

endif;
