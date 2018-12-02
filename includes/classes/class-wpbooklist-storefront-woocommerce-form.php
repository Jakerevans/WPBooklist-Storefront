<?php
/**
 * WPBookList WPBookList_Storefront_WooCommerce_Form Submenu Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Storefront_WooCommerce_Form', false ) ) :
	/**
	 * WPBookList_Storefront_WooCommerce_Form Class.
	 */
	class WPBookList_Storefront_WooCommerce_Form {

		/**
		 * Class Constructor - Simply calls the one function to return all Translated strings.
		 */
		public function __construct() {

			// Get StoreFront Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->storefront_trans = new WPBookList_Storefront_Translations();
			$this->storefront_trans->trans_strings();

		}


		/**
		 * Opens HTML for the tab.
		 */
		public function output_storefront_form() {

			global $wpdb;
			$string1 = '';
			$string2 = '';
			$string3 = '';
			$string4 = '';

			// Get saved settings.
			$settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options' );

			// Getting all WooCommerce Published Products.
			$all_product_data = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'" );

			// Setting up the Crossells.
			$crosssell_options = '';
			$saved_crosssells  = '';
			if ( false !== stripos( $settings->defaultcrosssell, ',' ) ) {
				$saved_crosssells = explode( ',', $settings->defaultcrosssell );
			}

			foreach ( $all_product_data as $key => $value ) {
				if ( '' !== $saved_crosssells ) {
					foreach ( $saved_crosssells as $key => $indiv_crosssell ) {
						if ( $indiv_crosssell === $value->ID ) {
							$crosssell_options = $crosssell_options . '<option selected value="' . $value->ID . '">' . $value->post_title . '</option>';
						} else {
							$crosssell_options = $crosssell_options . '<option value="' . $value->ID . '">' . $value->post_title . '</option>';
						}
					}
				} else {
					$crosssell_options = $crosssell_options . '<option value="' . $value->ID . '">' . $value->post_title . '</option>';
				}
			}

			// Setting up the upells.
			$upsell_options = '';
			$saved_upsells  = '';
			if ( false !== stripos( $settings->defaultupsell, ',' ) ) {
				$saved_upsells = explode( ',', $settings->defaultupsell );
			}

			foreach ( $all_product_data as $key => $value ) {
				if ( '' !== $saved_upsells ) {
					foreach ( $saved_upsells as $key => $indiv_upsell ) {
						if ( $indiv_upsell === $value->ID ) {
							$upsell_options = $upsell_options . '<option selected value="' . $value->ID . '">' . $value->post_title . '</option>';
						} else {
							$upsell_options = $upsell_options . '<option value="' . $value->ID . '">' . $value->post_title . '</option>';
						}
					}
				} else {
					$upsell_options = $upsell_options . '<option value="' . $value->ID . '">' . $value->post_title . '</option>';
				}
			}

			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$show_count   = 0;
			$pad_counts   = 0;
			$hierarchical = 1;
			$title        = '';
			$empty        = 0;

			$args = array(
				'taxonomy'     => $taxonomy,
				'orderby'      => $orderby,
				'show_count'   => $show_count,
				'pad_counts'   => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li'     => $title,
				'hide_empty'   => $empty,
			);

			$all_categories = get_categories( $args );

			$cat_array = array();

			foreach ( $all_categories as $cat ) {
				if ( 0 === $cat->category_parent ) {
					$category_id = $cat->term_id;
					array_push( $cat_array, $cat->name );

					$args2 = array(
						'taxonomy'     => $taxonomy,
						'child_of'     => 0,
						'parent'       => $category_id,
						'orderby'      => $orderby,
						'show_count'   => $show_count,
						'pad_counts'   => $pad_counts,
						'hierarchical' => $hierarchical,
						'title_li'     => $title,
						'hide_empty'   => $empty,
					);

					$sub_cats = get_categories( $args2 );
					if ( $sub_cats ) {
						foreach ( $sub_cats as $sub_category ) {
							array_push( $cat_array, $sub_category->name );
						}
					}
				}
			}

			if ( 'Yes' === $settings->defaultvirtual ) {
				$virtual_options = '<option selected>' . $this->storefront_trans->storefront_trans_47 . '</option>
								<option>' . $this->storefront_trans->storefront_trans_48 . '</option>';
			} else {
				$virtual_options = '<option>' . $this->storefront_trans->storefront_trans_47 . '</option>
								<option selected>' . $this->storefront_trans->storefront_trans_48 . '</option>';
			}

			if ( 'Yes' === $settings->defaultreviews ) {
				$reviews_options = '<option selected>' . $this->storefront_trans->storefront_trans_47 . '</option>
								<option>' . $this->storefront_trans->storefront_trans_48 . '</option>';
			} else {
				$reviews_options = '<option>' . $this->storefront_trans->storefront_trans_47 . '</option>
								<option selected>' . $this->storefront_trans->storefront_trans_48 . '</option>';
			}

			$product_categories = '<option selected default disabled value="' . $this->storefront_trans->storefront_trans_23 . '">' . $this->storefront_trans->storefront_trans_23 . '</option>';

			// Build the WooCommerce Product Categories Drop-Down string.
			foreach ( $cat_array as $key => $value ) {
				if ( 'WPBookList WooCommerce Product' !== $value ) {
					if ( $value === $settings->defaultcategory ) {
						$product_categories = $product_categories . '<option selected value="' . $value . '">' . $value . '</option>';
					} else {
						$product_categories = $product_categories . '<option value="' . $value . '">' . $value . '</option>';
					}
				}
			}

			$string1 = '<div class="wpbooklist-book-form-container"><div id="wpbooklist-storefront-fields-wrapper"><p class="wpbooklist-tab-intro-para">Here you can set default WooCommerce options that will be applied when adding a book, editing a book, and when importing titles via the <a href="https://wpbooklist.com/index.php/downloads/bulk-upload-extension/">Bulk-Upload Extension</a> and the <a href="https://wpbooklist.com/index.php/downloads/goodreads-extension/">Goodreads Extension</a>.</p><div id="wpbooklist-book-form-storefront-woo-settings-fields">
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_6 . '</label>
						<input value="' . $settings->defaultregularprice . '" name="book-woo-regular-price" id="wpbooklist-addbook-woo-regular-woo-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_7 . '</label>
						<input  value="' . $settings->defaultsaleprice . '" name="book-woo-sale-price" id="wpbooklist-addbook-woo-sale-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_15 . '</label>
						<input value="' . $settings->defaultsku . '" name="book-woo-sku" id="wpbooklist-addbook-woo-sku" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_8 . '</label>
						<input value="' . $settings->defaultsalebegin . '" name="book-woo-salebegin" id="wpbooklist-addbook-woo-salebegin" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_9 . '</label>
						<input value="' . $settings->defaultsaleend . '" name="book-woo-saleend" id="wpbooklist-addbook-woo-saleend" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_10 . '</label>
						<input value="' . $settings->defaultwidth . '" name="book-woo-width" id="wpbooklist-addbook-woo-width" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_11 . '</label>
						<input value="' . $settings->defaultheight . '" name="book-woo-height" id="wpbooklist-addbook-woo-height" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_12 . '</label>
						<input value="' . $settings->defaultweight . '" name="book-woo-weight" id="wpbooklist-addbook-woo-weight" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_13 . '</label>
						<input value="' . $settings->defaultlength . '" name="book-woo-length" id="wpbooklist-addbook-woo-length" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_14 . '</label>
						<input value="' . $settings->defaultstock . '" name="book-woo-stock" id="wpbooklist-addbook-woo-stock" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_16 . '</label>
							<select id="wpbooklist-addbook-woo-upsells" class="storefront-select2-upsells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
		                        ' . $upsell_options . '
		                    </select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_17 . '</label>
							<select id="wpbooklist-addbook-woo-crosssells" class="storefront-select2-crosssells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
		                        ' . $crosssell_options . '
		                    </select>
	                    </div>
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_19 . ':</label>
							<select id="wpbooklist-woocommerce-category-select" class="wpbooklist-addbook-select-default">
								' . $product_categories . '
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_20 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-virtual-select">
								' . $virtual_options . '
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_21 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-enable-reviews-select">
								' . $reviews_options . '
							</select>
						</div>
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container">
						<img class="wpbooklist-icon-image-question" data-label="book-form-shortdescription" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label" for="book-shortdescription">' . $this->storefront_trans->storefront_trans_18 . '</label>
						<textarea name="book-woo-note" id="wpbooklist-addbook-storefront-purchasenote" name="book-shortdescription">' . $settings->defaultnote . '</textarea>
					</div>
					<div class="wpbooklist-response-success-fail-container">
			    		<button class="wpbooklist-response-success-fail-button wpbooklist-admin-editbook-edit-button" type="button" id="wpbooklist-storefront-save-woocommerce-settings">' . $this->storefront_trans->storefront_trans_39 . '</button>
			    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
			    		<div class="wpbooklist-response-success-fail-response-actual-container" id="wpbooklist-admin-addbook-response-actual-container"></div>
		    		</div>
				</div>
			</div>
		</div>';

			return $string1;
		}
	}

endif;
