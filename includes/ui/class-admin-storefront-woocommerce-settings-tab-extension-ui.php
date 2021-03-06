<?php
/**
 * WPBookList Storefront Tab
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Storefront', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Storefront {

		/**
		 * Class Constructor
		 */
		public function __construct() {

			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once STOREFRONT_CLASS_DIR . 'class-wpbooklist-storefront-woocommerce-form.php';

			// Get Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->trans = new WPBookList_StoreFront_Translations();
			$this->trans->trans_strings();

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_Storefront_WooCommerce_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}

		/**
		 * Opens the admin container for the tab
		 */
		private function output_open_admin_container() {
			$title    = $this->trans->storefront_trans_41;
			$icon_url = STOREFRONT_ROOT_IMG_URL . 'businessman-working-online-with-a-laptop.svg';
			echo $this->template->output_open_admin_container( $title, $icon_url );
		}

		/**
		 * Outputs actual tab contents
		 */
		private function output_tab_content() {
			echo $this->form->output_storefront_form();
		}

		/**
		 * Closes admin container
		 */
		private function output_close_admin_container() {
			echo $this->template->output_close_admin_container();
		}

		/**
		 * Outputs advertisment area
		 */
		private function output_admin_template_advert() {
			echo $this->template->output_template_advert();
		}


	}
endif;

// Instantiate the class.
$cm = new WPBookList_Storefront();
