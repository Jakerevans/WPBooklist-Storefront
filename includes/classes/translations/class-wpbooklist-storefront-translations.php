<?php
/**
 * Class WPBookList_StoreFront_Translations - class-wpbooklist-translations.php
 *
 * @author   Jake Evans
 * @category Translations
 * @package  Includes/Classes/Translations
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_StoreFront_Translations', false ) ) :
	/**
	 * WPBookList_StoreFront_Translations class. This class will house all the translations we may ever need...
	 */
	class WPBookList_StoreFront_Translations {

		/**
		 * Class Constructor - Simply calls the one function to return all Translated strings.
		 */
		public function __construct() {
			$this->trans_strings();
		}

		/**
		 * All the Translations.
		 */
		public function trans_strings() {
			$this->storefront_trans_1  = __( 'Search', 'wpbooklist-textdomain' );
			$this->storefront_trans_2  = __( 'StoreFront Extension Fields', 'wpbooklist-textdomain' );
			$this->storefront_trans_3  = __( 'Purchase Price', 'wpbooklist-textdomain' );
			$this->storefront_trans_4  = __( 'Sale Link/URL', 'wpbooklist-textdomain' );
			$this->storefront_trans_5  = __( 'Create WooCommerce Product', 'wpbooklist-textdomain' );
			$this->storefront_trans_6  = __( 'Regular Price', 'wpbooklist-textdomain' );
			$this->storefront_trans_7  = __( 'Sale Price', 'wpbooklist-textdomain' );
			$this->storefront_trans_8  = __( 'Sale Begins On', 'wpbooklist-textdomain' );
			$this->storefront_trans_9  = __( 'Sale Ends On', 'wpbooklist-textdomain' );
			$this->storefront_trans_10 = __( 'Book Width', 'wpbooklist-textdomain' );
			$this->storefront_trans_11 = __( 'Book Height', 'wpbooklist-textdomain' );
			$this->storefront_trans_12 = __( 'Book Weight', 'wpbooklist-textdomain' );
			$this->storefront_trans_13 = __( 'Book Length', 'wpbooklist-textdomain' );
			$this->storefront_trans_14 = __( 'Amount Available', 'wpbooklist-textdomain' );
			$this->storefront_trans_15 = __( 'SKU', 'wpbooklist-textdomain' );
			$this->storefront_trans_16 = __( 'Upsells', 'wpbooklist-textdomain' );
			$this->storefront_trans_17 = __( 'Cross-Sells', 'wpbooklist-textdomain' );
			$this->storefront_trans_18 = __( 'Purchase Note', 'wpbooklist-textdomain' );
			$this->storefront_trans_19 = __( 'Choose a Product Category', 'wpbooklist-textdomain' );
			$this->storefront_trans_20 = __( 'Virtual Product', 'wpbooklist-textdomain' );
			$this->storefront_trans_21 = __( 'Enable Reviews', 'wpbooklist-textdomain' );
			$this->storefront_trans_22 = __( 'Purchase Now', 'wpbooklist-textdomain' );
			$this->storefront_trans_23 = __( 'Select a Category...', 'wpbooklist-textdomain' );
			$this->storefront_trans_24 = __( 'Yes', 'wpbooklist-textdomain' );
			$this->storefront_trans_25 = __( 'No', 'wpbooklist-textdomain' );
			$this->storefront_trans_26 = __( 'StoreFront General Settings', 'wpbooklist-textdomain' );
			$this->storefront_trans_27 = __( 'Purchase Now!', 'wpbooklist-textdomain' );
			$this->storefront_trans_28 = __( 'Call-To-Action Text', 'wpbooklist-textdomain' );
			$this->storefront_trans_29 = __( 'Call-To-Action', 'wpbooklist-textdomain' );
			$this->storefront_trans_30 = __( 'text you\'d like to display for each book. The default is', 'wpbooklist-textdomain' );
			$this->storefront_trans_31 = __( 'Library View Purchase Image', 'wpbooklist-textdomain' );
			$this->storefront_trans_32 = __( 'Choose what image you\'d like to display as the Purchase image for the', 'wpbooklist-textdomain' );
			$this->storefront_trans_33 = __( 'Library View', 'wpbooklist-textdomain' );
			$this->storefront_trans_34 = __( 'If no image is choosen, the', 'wpbooklist-textdomain' );
			$this->storefront_trans_35 = __( 'text from above will be displayed', 'wpbooklist-textdomain' );
			$this->storefront_trans_36 = __( 'Choose Image', 'wpbooklist-textdomain' );
			$this->storefront_trans_37 = __( 'Remove Image', 'wpbooklist-textdomain' );
			$this->storefront_trans_38 = __( 'Book View Purchase Image', 'wpbooklist-textdomain' );
			$this->storefront_trans_39 = __( 'Save StoreFront Settings', 'wpbooklist-textdomain' );
			$this->storefront_trans_40 = __( 'You\'ve saved your StoreFront Settings!', 'wpbooklist-textdomain' );
			$this->storefront_trans_41 = __( 'StoreFront WooCommerce Settings', 'wpbooklist-textdomain' );
			$this->storefront_trans_42 = __( 'Hide Price', 'wpbooklist-textdomain' );
			$this->storefront_trans_43 = __( 'Hide Purchase Image', 'wpbooklist-textdomain' );
			$this->storefront_trans_44 = __( 'Enable the StoreFront Extension Links', 'wpbooklist-textdomain' );
			$this->storefront_trans_45 = __( 'Checking this box will enable the StoreFront Extension Links, but only if you specified a price and an Author/Purchase link when adding your books.', 'wpbooklist-textdomain' );
			$this->storefront_trans_46 = __( 'WPBookList-As-A-Storefront Demo', 'wpbooklist-textdomain' );


			// The array of translation strings.
			$translation_array = array(
				'storefronttrans1'  => $this->storefront_trans_1,
				'storefronttrans2'  => $this->storefront_trans_2,
				'storefronttrans3'  => $this->storefront_trans_3,
				'storefronttrans4'  => $this->storefront_trans_4,
				'storefronttrans5'  => $this->storefront_trans_5,
				'storefronttrans6'  => $this->storefront_trans_6,
				'storefronttrans7'  => $this->storefront_trans_7,
				'storefronttrans8'  => $this->storefront_trans_8,
				'storefronttrans9'  => $this->storefront_trans_9,
				'storefronttrans10' => $this->storefront_trans_10,
				'storefronttrans11' => $this->storefront_trans_11,
				'storefronttrans12' => $this->storefront_trans_12,
				'storefronttrans13' => $this->storefront_trans_13,
				'storefronttrans14' => $this->storefront_trans_14,
				'storefronttrans15' => $this->storefront_trans_15,
				'storefronttrans16' => $this->storefront_trans_16,
				'storefronttrans17' => $this->storefront_trans_17,
				'storefronttrans18' => $this->storefront_trans_18,
				'storefronttrans19' => $this->storefront_trans_19,
				'storefronttrans20' => $this->storefront_trans_20,
				'storefronttrans21' => $this->storefront_trans_21,
				'storefronttrans22' => $this->storefront_trans_22,
				'storefronttrans23' => $this->storefront_trans_23,
				'storefronttrans24' => $this->storefront_trans_24,
				'storefronttrans25' => $this->storefront_trans_25,
				'storefronttrans26' => $this->storefront_trans_26,
				'storefronttrans27' => $this->storefront_trans_27,
				'storefronttrans28' => $this->storefront_trans_28,
				'storefronttrans29' => $this->storefront_trans_29,
				'storefronttrans30' => $this->storefront_trans_30,
				'storefronttrans31' => $this->storefront_trans_31,
				'storefronttrans32' => $this->storefront_trans_32,
				'storefronttrans33' => $this->storefront_trans_33,
				'storefronttrans34' => $this->storefront_trans_34,
				'storefronttrans35' => $this->storefront_trans_35,
				'storefronttrans36' => $this->storefront_trans_36,
				'storefronttrans37' => $this->storefront_trans_37,
				'storefronttrans38' => $this->storefront_trans_38,
				'storefronttrans39' => $this->storefront_trans_39,
				'storefronttrans40' => $this->storefront_trans_40,
			);

			return $translation_array;
		}
	}
endif;
