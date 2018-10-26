<?php
/**
 * Class StoreFront_General_Functions - class-storefront-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'StoreFront_General_Functions', false ) ) :
	/**
	 * StoreFront_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class StoreFront_General_Functions {

		/** Functions that loads up the menu page entry for this Extension.
		 *
		 *  @param array $submenu_array - The array that contains submenu entries to add to.
		 */
		public function wpbooklist_storefront_submenu( $submenu_array ) {
			$extra_submenu = array(
				'StoreFront',
			);

			// Combine the two arrays.
			$submenu_array = array_merge( $submenu_array, $extra_submenu );
			return $submenu_array;
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_storefront_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( STOREFRONT_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'STOREFRONT_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 *  Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
		 */
		public function wpbooklist_storefront_record_extension_version() {
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered.
			if ( false !== strpos( $existing_string->extensionversions, 'storefront' ) ) {
				$split_string = explode( 'storefront', $existing_string->extensionversions );
				$first_part   = $split_string[0];
				$last_part    = substr( $split_string[1], 5 );
				$new_string   = $first_part . 'storefront' . STOREFRONT_VERSION_NUM . $last_part;
			} else {
				$new_string = $existing_string->extensionversions . 'storefront' . STOREFRONT_VERSION_NUM;
			}

			$data         = array(
				'extensionversions' => $new_string,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist-storefront.php
		 */
		public function wpbooklist_storefront_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered and matches this version.
			if ( false !== strpos( $existing_string->extensionversions, 'storefront' ) ) {
				$split_string = explode( 'storefront', $existing_string->extensionversions );
				$version      = substr( $split_string[1], 0, 5 );

				// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
				if ( STOREFRONT_VERSION_NUM !== $version ) {
					require_once STOREFRONT_CLASS_COMPAT_DIR . 'class-storefront-compat-functions.php';
					$compat_class = new StoreFront_Compat_Functions();
				}
			}
		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_storefront_admin_js() {

			wp_register_script( 'wpbooklist_storefront_adminjs', STOREFRONT_JS_URL . 'wpbooklist_storefront_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$trans = new WPBookList_StoreFront_Translations();

			// Get Core translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$coretrans = new WPBookList_Translations();

			// Localize the script with the appropriate translation array from this Extension's Translations class.
			$translation_array1 = $trans->trans_strings();

			// Localize the script with the appropriate translation array from the Core Translations class.
			$translation_array2 = $coretrans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( STOREFRONT_FINAL_NONCES_ARRAY, true ) );

			// Now merge in the Core translations.
			//$final_array_of_php_values = array_merge( $final_array_of_php_values, $translation_array2 );

			// Adding some other individual values we may need.
			$final_array_of_php_values['STOREFRONT_ROOT_IMG_ICONS_URL'] = STOREFRONT_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['STOREFRONT_ROOT_IMG_URL']       = STOREFRONT_ROOT_IMG_URL;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']                         = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID']                      = get_option( 'media_selector_attachment_id', 0 );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_storefront_adminjs', 'wpbooklistStoreFrontPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_storefront_adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_storefront_frontend_js() {

			wp_register_script( 'wpbooklist_storefront_frontendjs', STOREFRONT_JS_URL . 'wpbooklist_storefront_frontend.min.js', array( 'jquery' ), STOREFRONT_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$trans = new WPBookList_StoreFront_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( STOREFRONT_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['STOREFRONT_ROOT_IMG_ICONS_URL'] = STOREFRONT_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['STOREFRONT_ROOT_IMG_URL']       = STOREFRONT_ROOT_IMG_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_storefront_frontendjs', 'wpbooklistStoreFrontPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_storefront_frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_storefront_admin_style() {

			wp_register_style( 'wpbooklist_storefront_adminui', STOREFRONT_CSS_URL . 'wpbooklist-storefront-main-admin.css', null, STOREFRONT_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_storefront_adminui' );

		}

		/**
		 * Adding the frontend css file
		 */
		public function wpbooklist_storefront_frontend_style() {

			wp_register_style( 'wpbooklist_storefront_frontendui', STOREFRONT_CSS_URL . 'wpbooklist-storefront-main-frontend.css', null, STOREFRONT_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_storefront_frontendui' );

		}

		/**
		 *  Function to add table names to the global $wpdb.
		 */
		public function wpbooklist_storefront_register_table_name() {
			global $wpdb;
			$wpdb->wpbooklist_jre_storefront_options = "{$wpdb->prefix}wpbooklist_jre_storefront_options";
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_storefront_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_storefront_create_tables() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_storefront_register_table_name();

			// Get StoreFront Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->storefront_trans = new WPBookList_Storefront_Translations();
			$this->storefront_trans->trans_strings();

			$purchasetext = $this->storefront_trans->trans_22 . '!';

			// Creating the table.
			$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_jre_storefront_options} 
		    (
		        ID bigint(190) auto_increment,
		        calltoaction varchar(190) NOT NULL DEFAULT '$purchasetext',
		        libraryimg varchar(255) NOT NULL DEFAULT '$purchasetext',
		        bookimg varchar(255) NOT NULL DEFAULT '$purchasetext',
		        defaultsaleprice varchar(255),
		        defaultsalebegin varchar(255),
		        defaultsaleend varchar(255),
		        defaultwidth varchar(255),
		        defaultheight varchar(255),
		        defaultweight varchar(255),
		        defaultlength varchar(255),
		        defaultstock varchar(255),
		        defaultsku varchar(255),
		        defaultnote varchar(255),
		        defaultupsell varchar(255),
		        defaultcrosssell varchar(255),
		        defaultcategory varchar(255),
		        defaultvirtual varchar(255),
		        defaultdownload varchar(255),
		        defaultreviews varchar(255),
		        PRIMARY KEY  (ID),
		          KEY title (calltoaction)
		    ) $charset_collate; ";
			dbDelta( $sql_create_table );

			$table_name = $wpdb->prefix . 'wpbooklist_jre_storefront_options';
			$wpdb->insert( $table_name, array( 'ID' => 1 ) );
		}

		/** Function to output HTML into the 'Add A Book' form.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_storefront_insert_storefront_fields( $string_book_form ) {

			global $wpdb;

			// Get StoreFront Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->storefront_trans = new WPBookList_Storefront_Translations();
			$this->storefront_trans->trans_strings();

			// Get Core Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			$string1            = '';
			$product_categories = '<option selected default disabled value="' . $this->storefront_trans->trans_23 . '">' . $this->storefront_trans->trans_23 . '</option>';
			$string3            = '';
			$string4            = '';

			// Get saved settings.
			$settings_table = $wpdb->prefix . 'wpbooklist_jre_storefront_options';
			$settings       = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options' );

			// Getting all WooCommerce Published Products.
			$all_product_data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'" );

			$sells = '';
			foreach ( $all_product_data as $key => $value ) {
				$sells = $sells . '<option value="' . $value->ID . '">' . $value->post_title . '</option>';
			}

			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$show_count   = 0;      // 1 for yes, 0 for no.
			$pad_counts   = 0;      // 1 for yes, 0 for no.
			$hierarchical = 1;      // 1 for yes, 0 for no.
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

			$header = '<div id="wpbooklist-storefront-fields-wrapper">
				<div id="wpbooklist-addbook-select-library-label">
					<p>
						<img class="wpbooklist-icon-image-question-with-link" data-label="book-storefront-fields-heading" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg"/>
						' . $this->storefront_trans->trans_2 . '
					</p>
				</div>';

			$body_one = '
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
					<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_3 . '</label>
					<input name="book-price" id="wpbooklist-addbook-price" name="book-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
				</div>
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
					<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_4 . '</label>
					<input name="book-sale-author-link" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
				</div>
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
					<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
					<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->trans_5 . '?</label>
					<select class="wpbooklist-addbook-select-default" id="wpbooklist-addbook-storefront-select-woocommerce">
						<option>' . $this->storefront_trans->trans_24 . '</option>
						<option selected default>' . $this->storefront_trans->trans_25 . '</option>
					</select>
				</div>';

			$body_two = '
				<div id="wpbooklist-book-form-storefront-initial-hidden-fields">
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_6 . '</label>
						<input name="book-woo-regular-price" id="wpbooklist-addbook-woo-regular-woo-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_7 . '</label>
						<input name="book-woo-sale-price" id="wpbooklist-addbook-woo-sale-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_15 . '</label>
						<input name="book-woo-sku" id="wpbooklist-addbook-woo-sku" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_8 . '</label>
						<input name="book-woo-salebegin" id="wpbooklist-addbook-woo-salebegin" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_9 . '</label>
						<input name="book-woo-saleend" id="wpbooklist-addbook-woo-saleend" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_10 . '</label>
						<input name="book-woo-width" id="wpbooklist-addbook-woo-width" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_11 . '</label>
						<input name="book-woo-height" id="wpbooklist-addbook-woo-height" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_12 . '</label>
						<input name="book-woo-weight" id="wpbooklist-addbook-woo-weight" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_13 . '</label>
						<input name="book-woo-length" id="wpbooklist-addbook-woo-length" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_14 . '</label>
						<input name="book-woo-stock" id="wpbooklist-addbook-woo-stock" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_16 . '</label>
							<select class="storefront-select2-upsells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
		                        ' . $sells . '
		                    </select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->trans_17 . '</label>
							<select class="storefront-select2-crosssells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
		                        ' . $sells . '
		                    </select>
	                    </div>
					</div>';

				$body_three = '
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->trans_19 . ':</label>
							<select id="wpbooklist-woocommerce-category-select" class="wpbooklist-addbook-select-default">
								' . $product_categories . '
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->trans_20 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-virtual-select">
								<option>' . $this->trans->trans_131 . '</option>
								<option selected default>' . $this->trans->trans_132 . '</option>
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->trans_21 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-enable-reviews-select">
								<option>' . $this->trans->trans_131 . '</option>
								<option selected default>' . $this->trans->trans_132 . '</option>
							</select>
						</div>
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container">
						<img class="wpbooklist-icon-image-question" data-label="book-form-shortdescription" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
						<label class="wpbooklist-question-icon-label" for="book-shortdescription">' . $this->storefront_trans->trans_18 . '</label>
						<textarea name="book-woo-note" id="wpbooklist-addbook-storefront-purchasenote" name="book-shortdescription"></textarea>
					</div>';

			$closing = '
					</div>
				</div>';

			return $string_book_form . $header . $body_one . $body_two . $body_three . $closing;

		}


	}
endif;
