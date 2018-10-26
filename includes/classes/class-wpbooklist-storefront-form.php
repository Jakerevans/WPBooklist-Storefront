<?php
/**
 * WPBookList WPBookList_StoreFront_Form Submenu Class - class-wpbooklist-storefront-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_StoreFront_Form', false ) ) :
	/**
	 * WPBookList_StoreFront_Form Class.
	 */
	class WPBookList_StoreFront_Form {

		/**
		 * Function to actually output the HTML.
		 */
		public function output_storefront_form() {

			// Get Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->trans = new WPBookList_StoreFront_Translations();
			$this->trans->trans_strings();

			global $wpdb;
			$row            = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options' );
			$call_to_action = $row->calltoaction;

			if ( $this->trans->storefront_trans_27 === $row->libraryimg || false !== strpos( $row->libraryimg, 'placeholder.svg' ) ) {
				$row->libraryimg = ROOT_IMG_ICONS_URL . 'book-placeholder.svg';
			}

			if ( $this->trans->storefront_trans_27 === $row->bookimg || false !== strpos( $row->bookimg, 'placeholder.svg' ) ) {
				$row->bookimg = ROOT_IMG_ICONS_URL . 'book-placeholder.svg';
			}

			// For grabbing an image from media library.
			wp_enqueue_media();

			$string1 = '<div id="wpbooklist-storefront-container-div">
							<div id="wpbooklist-storefront-text-div">
								<p class="wpbooklist-tab-intro-para" id="wpbooklist-storefront-text-label">
									<span class="wpbooklist-color-orange-italic">' . $this->trans->storefront_trans_28  . '</span>
									Input the <strong><em>\'' . $this->trans->storefront_trans_29  . '\'</em></strong> ' . $this->trans->storefront_trans_30  . ' <strong><em>\'' . $this->trans->storefront_trans_27  . '\'</em></strong>
								</p>
								<input id="wpbooklist-storefront-call-to-action-input" value="' . $call_to_action . '" />
							</div>
							<div id="wpbooklist-storefront-library-buy-img-div">
								<p class="wpbooklist-tab-intro-para" id="wpbooklist-storefront-buy-img-label">
									<span class="wpbooklist-color-orange-italic">' . $this->trans->storefront_trans_31  . '</span>
									' . $this->trans->storefront_trans_32  . ' <strong><em>' . $this->trans->storefront_trans_33  . '</em></strong>. ' . $this->trans->storefront_trans_34  . ' <strong><em>\'' . $this->trans->storefront_trans_29  . '\'</em></strong> ' . $this->trans->storefront_trans_35  . '.
								</p>
								<div id="wpbooklist-storefront-image-div">
									<img id="wpbooklist-storefront-preview-img-1" class="wpbooklist-storefront-preview-img" src="'.$row->libraryimg.'">
									<input id="wpbooklist-storefront-img-button-1" class="wpbooklist-storefront-upload_image_button" type="button" value="' . $this->trans->storefront_trans_36  . '"/>
									<input id="wpbooklist-storefront-img-remove-1" class="wpbooklist-storefront-upload_image_button" type="button" value="' . $this->trans->storefront_trans_37  . '"/>
								</div>
								<p class="wpbooklist-tab-intro-para" id="wpbooklist-storefront-colorbox-buy-img-label">
									<span class="wpbooklist-color-orange-italic">' . $this->trans->storefront_trans_38  . '</span>
									' . $this->trans->storefront_trans_32  . ' <strong><em>Book View</em></strong>. ' . $this->trans->storefront_trans_34  . ' <strong><em>\'' . $this->trans->storefront_trans_29  . '\'</em></strong> ' . $this->trans->storefront_trans_35  . '.
								</p>
								<div id="wpbooklist-storefront-colorbox-image-div">
									<img id="wpbooklist-storefront-preview-img-2" class="wpbooklist-storefront-preview-img" src="'.$row->bookimg.'">
									<input id="wpbooklist-storefront-img-button-2" class="wpbooklist-storefront-upload_image_button" type="button" value="' . $this->trans->storefront_trans_36  . '"/>
									<input id="wpbooklist-storefront-img-remove-2" class="wpbooklist-storefront-upload_image_button" type="button" value="' . $this->trans->storefront_trans_37  . '"/>
								</div>
							</div>
							<button class="wpbooklist-response-success-fail-button" id="wpbooklist-storefront-save-settings">' . $this->trans->storefront_trans_39  . '</button>
							<div class="wpbooklist-spinner" id="wpbooklist-spinner-storefront-lib"></div>
							<div id="wpbooklist-storefront-success-div"></div>
						</div>';
	    	return $string1;
		}
	}

endif;