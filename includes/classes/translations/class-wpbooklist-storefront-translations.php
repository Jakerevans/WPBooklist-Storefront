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
			$this->trans_1  = __( 'Search', 'wpbooklist-textdomain' );
			$this->trans_2  = __( 'StoreFront Extension Fields', 'wpbooklist-textdomain' );
			$this->trans_3  = __( 'Purchase Price', 'wpbooklist-textdomain' );
			$this->trans_4  = __( 'Sale Link/URL', 'wpbooklist-textdomain' );
			$this->trans_5  = __( 'Create WooCommerce Product', 'wpbooklist-textdomain' );
			$this->trans_6  = __( 'Regular Price', 'wpbooklist-textdomain' );
			$this->trans_7  = __( 'Sale Price', 'wpbooklist-textdomain' );
			$this->trans_8  = __( 'Sale Begins On', 'wpbooklist-textdomain' );
			$this->trans_9  = __( 'Sale Ends On', 'wpbooklist-textdomain' );
			$this->trans_10 = __( 'Book Width', 'wpbooklist-textdomain' );
			$this->trans_11 = __( 'Book Height', 'wpbooklist-textdomain' );
			$this->trans_12 = __( 'Book Weight', 'wpbooklist-textdomain' );
			$this->trans_13 = __( 'Book Length', 'wpbooklist-textdomain' );
			$this->trans_14 = __( 'Amount Available', 'wpbooklist-textdomain' );
			$this->trans_15 = __( 'SKU', 'wpbooklist-textdomain' );
			$this->trans_16 = __( 'Upsells', 'wpbooklist-textdomain' );
			$this->trans_17 = __( 'Cross-Sells', 'wpbooklist-textdomain' );
			$this->trans_18 = __( 'Purchase Note', 'wpbooklist-textdomain' );
			$this->trans_19 = __( 'Choose a Product Category', 'wpbooklist-textdomain' );
			$this->trans_20 = __( 'Virtual Product', 'wpbooklist-textdomain' );
			$this->trans_21 = __( 'Enable Reviews', 'wpbooklist-textdomain' );
			$this->trans_22 = __( 'Purchase Now', 'wpbooklist-textdomain' );
			$this->trans_23 = __( 'Select a Category...', 'wpbooklist-textdomain' );
			$this->trans_24 = __( 'Yes', 'wpbooklist-textdomain' );
			$this->trans_25 = __( 'No', 'wpbooklist-textdomain' );

			// The array of translation strings.
			$translation_array = array(
				'trans1'  => $this->trans_1,
				'trans2'  => $this->trans_2,
				'trans3'  => $this->trans_3,
				'trans4'  => $this->trans_4,
				'trans5'  => $this->trans_5,
				'trans6'  => $this->trans_6,
				'trans7'  => $this->trans_7,
				'trans8'  => $this->trans_8,
				'trans9'  => $this->trans_9,
				'trans10' => $this->trans_10,
				'trans11' => $this->trans_11,
				'trans12' => $this->trans_12,
				'trans13' => $this->trans_13,
				'trans14' => $this->trans_14,
				'trans15' => $this->trans_15,
				'trans16' => $this->trans_16,
				'trans17' => $this->trans_17,
				'trans18' => $this->trans_18,
				'trans19' => $this->trans_19,
				'trans20' => $this->trans_20,
				'trans21' => $this->trans_21,
				'trans22' => $this->trans_22,
				'trans23' => $this->trans_23,
				'trans24' => $this->trans_24,
				'trans25' => $this->trans_25,
			);

			return $translation_array;
		}
	}
endif;
