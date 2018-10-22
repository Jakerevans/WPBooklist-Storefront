<?php
/**
 * WPBookList WPBookList_StoreFront_Form Submenu Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_StoreFront_Form', false ) ) :
/**
 * WPBookList_StoreFront_Form Class.
 */
class WPBookList_StoreFront_Form {

	public static function output_storefront_form(){

		global $wpdb;
	
		// For grabbing an image from media library
		wp_enqueue_media();

		$string1 = '';
		
    	return $string1;
	}
}

endif;