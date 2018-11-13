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

		/**
		 * Class Constructor - Simply calls the Translations.
		 */
		public function __construct() {

			// Get StoreFront Translations.
			require_once STOREFRONT_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-storefront-translations.php';
			$this->storefront_trans = new WPBookList_Storefront_Translations();
			$this->storefront_trans->trans_strings();

			// Get Core Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

		}

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

			$purchasetext = $this->storefront_trans->storefront_trans_22 . '!';

			// Creating the table.
			$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_jre_storefront_options} 
			(
				ID bigint(190) auto_increment,
				calltoaction varchar(190) NOT NULL DEFAULT '$purchasetext',
				libraryimg varchar(255) NOT NULL DEFAULT '$purchasetext',
				bookimg varchar(255) NOT NULL DEFAULT '$purchasetext',
				defaultregularprice varchar(255),
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

			$string1            = '';
			$product_categories = '<option selected default disabled value="' . $this->storefront_trans->storefront_trans_23 . '">' . $this->storefront_trans->storefront_trans_23 . '</option>';
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
						' . $this->storefront_trans->storefront_trans_2 . '
					</p>
				</div>';

			$body_one = '
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
					<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_3 . '</label>
					<input name="book-price" id="wpbooklist-addbook-price" name="book-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
				</div>
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
					<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_4 . '</label>
					<input name="book-sale-author-link" id="wpbooklist-addbook-url" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
				</div>
				<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception" id="wpbooklist-storefront-create-product-row">
					<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
					<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_5 . '?</label>
					<select class="wpbooklist-addbook-select-default" id="wpbooklist-addbook-storefront-select-woocommerce">
						<option>' . $this->storefront_trans->storefront_trans_24 . '</option>
						<option selected default>' . $this->storefront_trans->storefront_trans_25 . '</option>
					</select>
				</div>';

			$body_two = '
				<div id="wpbooklist-book-form-storefront-initial-hidden-fields">
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_6 . '</label>
						<input name="book-woo-regular-price" id="wpbooklist-addbook-woo-regular-woo-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_7 . '</label>
						<input name="book-woo-sale-price" id="wpbooklist-addbook-woo-sale-price" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_15 . '</label>
						<input name="book-woo-sku" id="wpbooklist-addbook-woo-sku" type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_8 . '</label>
						<input name="book-woo-salebegin" id="wpbooklist-addbook-woo-salebegin" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_9 . '</label>
						<input name="book-woo-saleend" id="wpbooklist-addbook-woo-saleend" type="date" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_10 . '</label>
						<input name="book-woo-width" id="wpbooklist-addbook-woo-width" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_11 . '</label>
						<input name="book-woo-height" id="wpbooklist-addbook-woo-height" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_12 . '</label>
						<input name="book-woo-weight" id="wpbooklist-addbook-woo-weight" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_13 . '</label>
						<input name="book-woo-length" id="wpbooklist-addbook-woo-length" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
						<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_14 . '</label>
						<input name="book-woo-stock" id="wpbooklist-addbook-woo-stock" type="number" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry">
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_16 . '</label>
							<select class="storefront-select2-upsells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
									' . $sells . '
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-storefront-up-cross">
							<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label class="wpbooklist-question-icon-label">' . $this->storefront_trans->storefront_trans_17 . '</label>
							<select class="storefront-select2-crosssells select2-storefront-container wpbooklist-addbook-select-default" name="upsellproducts[]" data-customfield-type="plaintextentry" multiple="multiple">
									' . $sells . '
							</select>
	                    </div>
					</div>';

				$body_three = '
					<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_19 . ':</label>
							<select id="wpbooklist-woocommerce-category-select" class="wpbooklist-addbook-select-default">
								' . $product_categories . '
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_20 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-virtual-select">
								<option>' . $this->trans->trans_131 . '</option>
								<option selected default>' . $this->trans->trans_132 . '</option>
							</select>
						</div>
						<div class="wpbooklist-book-form-indiv-attribute-container">
							<img class="wpbooklist-icon-image-question" data-label="book-form-rating" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
							<label class="wpbooklist-question-icon-label" for="book-rating">' . $this->storefront_trans->storefront_trans_21 . '?</label>
							<select class="wpbooklist-addbook-select-default" id="wpbooklist-woocommerce-enable-reviews-select">
								<option>' . $this->trans->trans_131 . '</option>
								<option selected default>' . $this->trans->trans_132 . '</option>
							</select>
						</div>
					</div>
					<div class="wpbooklist-book-form-indiv-attribute-container">
						<img class="wpbooklist-icon-image-question" data-label="book-form-shortdescription" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/question-black.svg">
						<label class="wpbooklist-question-icon-label" for="book-shortdescription">' . $this->storefront_trans->storefront_trans_18 . '</label>
						<textarea name="book-woo-note" id="wpbooklist-addbook-storefront-purchasenote" name="book-shortdescription"></textarea>
					</div>';

			$closing = '
					</div>
				</div>';

			return $string_book_form . $header . $body_one . $body_two . $body_three . $closing;

		}

		/**
		 *  Function to insert the Display Options on the 'Library View' Display Options Tab.
		 */
		public function wpbooklist_storefront_insert_library_view_display_options() {
			return '<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-finished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->storefront_trans->storefront_trans_42 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-hidefrontendbuyprice"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-finished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->storefront_trans->storefront_trans_43 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-hidefrontendbuyimg"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry wpbooklist-display-options-indiv-entry-exception">
						<div id="wpbooklist-enable-purchase-div">' . $this->storefront_trans->storefront_trans_44 . '</div>
						<p id="wpbooklist-enable-purchase-p">' . $this->storefront_trans->storefront_trans_45 . '</p>
						<div id="wpbooklist-enable-purchase-actual-div"><label>' . $this->storefront_trans->storefront_trans_44 . '&nbsp;&nbsp;</label><input type="checkbox" name="enable-purchase-link"></div>
						<div id="wpbooklist-stylepak-demo-links">
						  <a href="http://wpbooklist.com/index.php/storefront-demo//">' . $this->storefront_trans->storefront_trans_46 . '</a>
						</div>
            		</div>';
		}

		/**
		 *  Function to insert the Display Options on the 'Book View' Display Options Tab.
		 */
		public function wpbooklist_storefront_insert_book_view_display_options() {
			return '<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-finished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->storefront_trans->storefront_trans_42 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-hidecolorboxbuyprice"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-finished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->storefront_trans->storefront_trans_43 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-hidecolorboxbuyimg"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry wpbooklist-display-options-indiv-entry-exception">
						<div id="wpbooklist-enable-purchase-div">' . $this->storefront_trans->storefront_trans_44 . '</div>
						<p id="wpbooklist-enable-purchase-p">' . $this->storefront_trans->storefront_trans_45 . '</p>
						<div id="wpbooklist-enable-purchase-actual-div"><label>' . $this->storefront_trans->storefront_trans_44 . '&nbsp;&nbsp;</label><input type="checkbox" name="enable-purchase-link"></div>
						<div id="wpbooklist-stylepak-demo-links">
						  <a href="http://wpbooklist.com/index.php/storefront-demo//">' . $this->storefront_trans->storefront_trans_46 . '</a>
						</div>
					</div>';
		}

		/** Function to display the Price/Buy Img/Link on front-end Library view.
		 *
		 *  @param string $string - The string that contains price, url, img, etc.
		 */
		public function wpbooklist_append_to_frontend_library_price_purchase_func( $string ) {

			if ( null !== $string[0] ) {
				if ( strpos( $string[0], 'http://' ) === false && strpos( $string[0], 'https://' ) === false ) {
					$string[0] = 'http://' . $string[0];
				} else {
					$string[0] = $string[0];
				}
			}

			$string1 = '<div class="wpbooklist-frontend-library-price">
									<a href="' . $string[0] . '">' . $string[1] . '</a>
						</div>';

			return $string1;
		}




		/** Function to display the Price/Buy Img/Link on front-end Library view.
		 *
		 *  @param string $string - The string that contains price, url, img, etc.
		 */
		public function wpbooklist_append_to_frontend_library_image_purchase_func( $string ) {

			// Get saved purcahse image.
			global $wpdb;
			$row = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options' );

			if ( null !== $string[0] ) {

				if ( false === strpos( $string[0], 'http://' ) && false === strpos( $string[0], 'https://' ) ) {
					$string[0] = 'http://' . $string[0];
				} else {
					$string[0] = $string[0];
				}
			}

			if ( ( false !== strpos( $row->libraryimg, 'http://' ) || false !== strpos( $row->libraryimg, 'https://' ) ) && false === strpos( $row->libraryimg, 'book-placeholder.png' ) ) {
				$string1 = '<div class="wpbooklist-frontend-library-buy-img">
								<a class="wpbooklist-library-purchase-link-styled" href="' . $string[0] . '"><img src="' . $row->libraryimg . '"/></a>
						</div>';
			} else {
				$string1 = '<div class="wpbooklist-frontend-library-buy-img">
								<a class="wpbooklist-library-purchase-link-styled" href="' . $string[0] . '">' . $row->calltoaction . '</a>
						</div>';
			}

			return $string1;
		}

		/**
		 *  Function to add purchase images to the media library upon activation.
		 */
		public function wpbooklist_jre_storefront_add_purchase_images() {
			global $wpdb;

			$file = array(
				STOREFRONT_ROOT_IMG_DIR . 'wpbooklist_purchase_img_1.png',
				STOREFRONT_ROOT_IMG_DIR . 'wpbooklist_purchase_img_2.png',
				STOREFRONT_ROOT_IMG_DIR . 'wpbooklist_purchase_img_3.png',
				STOREFRONT_ROOT_IMG_DIR . 'wpbooklist_purchase_img_4.png',
			);

			foreach ( $file as $f ) {
				$max_id      = $wpdb->get_var( "SELECT MAX(id) FROM $wpdb->posts" );
				$post_id     = $max_id + 1;
				$filename    = basename( $f );
				$upload_file = wp_upload_bits( $filename, null, file_get_contents( $f ) );
				if ( ! $upload_file['error'] ) {

					$wp_filetype   = wp_check_filetype( $filename, null );
					$attachment    = array(
						'post_mime_type' => 'image/png',
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
						'post_content'   => 'my description',
						'post_status'    => 'inherit',
					);
					$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
					if ( ! is_wp_error( $attachment_id ) ) {
						require_once ABSPATH . 'wp-admin/includes/image.php';
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );
					}
				}
			}
		}

		/** Function to display the colorbox price.
		 *
		 *  @param string $string - The string that contains price.
		 */
		public function wpbooklist_append_to_colorbox_price_func( $string ) {

			$string1 = '<tr>
			                <td>
			                    <span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Price:</span><span class="wpbooklist-bold-stats-value">' . $string . '</span>
			                </td>   
			            </tr>';

			return $string1;
		}

		/** Function to display the colorbox price.
		 *
		 *  @param string $string - The string that contains text link.
		 */
		public function wpbooklist_append_to_colorbox_purchase_text_link_func( $string ) {

			// Get saved purcahse image.
			global $wpdb;
			$row = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options');

			$string1 = ' id="wpbooklist-purchase-book-view" href="' . $string . '">' . $row->calltoaction . '</a';

			return $string1;
		}

		/** Function to display the colorbox image link
		 *
		 *  @param string $string - The string that contains the colorbox image link.
		 */
		public function wpbooklist_append_to_colorbox_purchase_image_link_func( $string ) {

			// Get saved purchase image.
			global $wpdb;
			$row = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storefront_options' );

			if ( ( false !== strpos( $row->bookimg, 'http://' ) || false !== strpos( $row->bookimg, 'https://' ) ) && false === strpos( $row->bookimg, 'book-placeholder.png' ) ) {

				$string1 = '<a class="wpbooklist-purchase-img" href="' . $string . '" target="_blank">
			            <img src="' . $row->bookimg . '" id="wpbooklist-author-img">
			        </a>';

			} else {

				$string1 = '<a class="wpbooklist-purchase-img" href="' . $string . '" target="_blank">
			            <img src="' . STOREFRONT_ROOT_IMG_URL . 'author-icon.png" id="wpbooklist-author-img">
			        </a>';
			}

			return $string1;
		}


	}
endif;
