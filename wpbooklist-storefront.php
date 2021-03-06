<?php
/**
 * WordPress Book List StoreFront Extension
 *
 * @package     WordPress Book List StoreFront Extension
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList StoreFront Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: The WPBookList StoreFront extension allows the automatic creation of WooCommerce products when adding a book, as well as various other sales options - linking to where books are sold, displaying pricing info, etc.
 * Version: 1.0.1
 * Author: Jake Evans
 * Text Domain: wpbooklist
 * Author URI: https://www.jakerevans.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



global $wpdb;

/* REQUIRE STATEMENTS */
	require_once 'includes/class-storefront-general-functions.php';
	require_once 'includes/class-storefront-ajax-functions.php';
	require_once 'includes/classes/update/class-wpbooklist-storefront-update.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

	// Root plugin folder directory.
	if ( ! defined('WPBOOKLIST_VERSION_NUM' ) ) {
		define( 'WPBOOKLIST_VERSION_NUM', '6.1.5' );
	}

	// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed.
	define( 'EDD_SL_STORE_URL_STOREFRONT', 'https://wpbooklist.com' );

	// The id of your product in EDD.
	define( 'EDD_SL_ITEM_ID_STOREFRONT', 713 );

	// This Extension's Version Number.
	define( 'WPBOOKLIST_STOREFRONT_VERSION_NUM', '1.0.1' );

	// Root plugin folder directory.
	define( 'STOREFRONT_ROOT_DIR', plugin_dir_path( __FILE__ ) );

	// Root WordPress Plugin Directory. The If is for taking into account the update process - a temp folder gets created when updating, which temporarily replaces the 'wpbooklist-bulkbookupload' folder.
	if ( false !== stripos( plugin_dir_path( __FILE__ ) , '/wpbooklist-storefront' ) ) { 
		define( 'STOREFRONT_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-storefront', '', plugin_dir_path( __FILE__ ) ) );
	} else {
		$temp = explode( 'plugins/', plugin_dir_path( __FILE__ ) );
		define( 'STOREFRONT_ROOT_WP_PLUGINS_DIR', $temp[0] . 'plugins/' );
	}

	// Root WPBL Dir.
	if ( ! defined('ROOT_WPBL_DIR' ) ) {
		define( 'ROOT_WPBL_DIR', STOREFRONT_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
	}

	// Root WPBL Url.
	if ( ! defined('ROOT_WPBL_URL' ) ) {
		define( 'ROOT_WPBL_URL', plugins_url() . '/wpbooklist/' );
	}

	// Root WPBL Classes Dir.
	if ( ! defined('ROOT_WPBL_CLASSES_DIR' ) ) {
		define( 'ROOT_WPBL_CLASSES_DIR', ROOT_WPBL_DIR . 'includes/classes/' );
	}

	// Root WPBL Transients Dir.
	if ( ! defined('ROOT_WPBL_TRANSIENTS_DIR' ) ) {
		define( 'ROOT_WPBL_TRANSIENTS_DIR', ROOT_WPBL_CLASSES_DIR . 'transients/' );
	}

	// Root WPBL Translations Dir.
	if ( ! defined('ROOT_WPBL_TRANSLATIONS_DIR' ) ) {
		define( 'ROOT_WPBL_TRANSLATIONS_DIR', ROOT_WPBL_CLASSES_DIR . 'translations/' );
	}

	// Root WPBL Root Img Icons Dir.
	if ( ! defined('ROOT_WPBL_IMG_ICONS_URL' ) ) {
		define( 'ROOT_WPBL_IMG_ICONS_URL', ROOT_WPBL_URL . 'assets/img/icons/' );
	}

	// Root WPBL Root Utilities Dir.
	if ( ! defined('ROOT_WPBL_UTILITIES_DIR' ) ) {
		define( 'ROOT_WPBL_UTILITIES_DIR', ROOT_WPBL_CLASSES_DIR . 'utilities/' );
	}

	// Root plugin folder URL .
	define( 'STOREFRONT_ROOT_URL', plugins_url() . '/wpbooklist-storefront/' );

	// Root Classes Directory.
	define( 'STOREFRONT_CLASS_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/' );

	// Root Update Directory.
	define( 'STOREFRONT_UPDATE_DIR', STOREFRONT_CLASS_DIR . 'update/' );

	// Root Image Icons DIR of this extension.
	define('STOREFRONT_ROOT_IMG_DIR', STOREFRONT_ROOT_DIR . 'assets/img/');

	// Root REST Classes Directory.
	define( 'STOREFRONT_CLASS_REST_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/rest/' );

	// Root Compatability Classes Directory.
	define( 'STOREFRONT_CLASS_COMPAT_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/compat/' );

	// Root Translations Directory.
	define( 'STOREFRONT_CLASS_TRANSLATIONS_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/translations/' );

	// Root Transients Directory.
	define( 'STOREFRONT_CLASS_TRANSIENTS_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/transients/' );

	// Root Image URL.
	define( 'STOREFRONT_ROOT_IMG_URL', STOREFRONT_ROOT_URL . 'assets/img/' );

	// Root Image Icons URL.
	define( 'STOREFRONT_ROOT_IMG_ICONS_URL', STOREFRONT_ROOT_URL . 'assets/img/icons/' );

	// Root CSS URL.
	define( 'STOREFRONT_CSS_URL', STOREFRONT_ROOT_URL . 'assets/css/' );

	// Root JS URL.
	define( 'STOREFRONT_JS_URL', STOREFRONT_ROOT_URL . 'assets/js/' );

	// Root UI directory.
	define( 'STOREFRONT_ROOT_INCLUDES_UI', STOREFRONT_ROOT_DIR . 'includes/ui/' );

	// Root UI Admin directory.
	define( 'STOREFRONT_ROOT_INCLUDES_UI_ADMIN_DIR', STOREFRONT_ROOT_DIR . 'includes/ui/admin/' );

	// Define the Uploads base directory.
	$uploads     = wp_upload_dir();
	$upload_path = $uploads['basedir'];
	define( 'STOREFRONT_UPLOADS_BASE_DIR', $upload_path . '/' );

	// Define the Uploads base URL.
	$upload_url = $uploads['baseurl'];
	define( 'STOREFRONT_UPLOADS_BASE_URL', $upload_url . '/' );

	// Nonces array.
	define( 'STOREFRONT_NONCES_ARRAY',
		wp_json_encode(array(
			'adminnonce1' => 'wpbooklist_storefront_settings_action_callback',
			'adminnonce2' => 'wpbooklist_save_woocommerce_storefront_settings_action_callback',
			'adminnonce3' => 'wpbooklist_storefront_save_license_key_action_callback',
		))
	);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, STOREFRONT_ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */

/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$storefront_general_functions = new StoreFront_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$storefront_ajax_functions = new StoreFront_Ajax_Functions();

	// Include the Update Class.
	$storefront_update_functions = new WPBookList_Storefront_Update();


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Function that adds in the License Key Submission form on this Extension's entry on the plugins page.
	add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $storefront_general_functions, 'wpbooklist_storefront_pluginspage_nonce_entry' ) );



global $wpdb;
$test_name = $wpdb->prefix . 'wpbooklist_storefront_settings';
if ( $test_name === $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
	$extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_storefront_settings' );
	if ( false !== stripos( $extension_settings->treh, 'aod' ) ) {
		// Function that loads up the menu page entry for this Extension.
		add_filter( 'wpbooklist_add_sub_menu', array( $storefront_general_functions, 'wpbooklist_storefront_submenu' ) );

		add_filter( 'wpbooklist_add_to_library_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_library_view_display_options' ) );

		add_filter( 'wpbooklist_add_to_book_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_book_view_display_options' ) );

		add_filter( 'wpbooklist_add_to_posts_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_posts_display_options' ) );

		add_filter( 'wpbooklist_add_to_pages_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_pages_display_options' ) );

		add_filter( 'wpbooklist_append_to_book_form_storefront_fields', array( $storefront_general_functions, 'wpbooklist_storefront_insert_storefront_fields' ) );

		add_filter( 'wpbooklist_append_to_colorbox_price', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_price_func' ) );

		add_filter( 'wpbooklist_append_to_colorbox_purchase_text_link', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_purchase_text_link_func' ) );

		add_filter( 'wpbooklist_append_to_colorbox_purchase_image_link', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_purchase_image_link_func' ) );

		add_filter( 'wpbooklist_append_to_frontend_library_price_purchase', array( $storefront_general_functions, 'wpbooklist_append_to_frontend_library_price_purchase_func' ) );

		add_filter( 'wpbooklist_append_to_frontend_library_image_purchase', array( $storefront_general_functions, 'wpbooklist_append_to_frontend_library_image_purchase_func' ) );

		add_filter( 'wpbooklist_add_storefront_calltoaction_page', array( $storefront_general_functions, 'wpbooklist_add_storefront_calltoaction_page_func' ) );

		add_filter( 'wpbooklist_add_storefront_bookimg_page', array( $storefront_general_functions, 'wpbooklist_add_storefront_bookimg_page_func' ) );

		add_filter( 'wpbooklist_add_storefront_calltoaction_post', array( $storefront_general_functions, 'wpbooklist_add_storefront_calltoaction_post_func' ) );

		add_filter( 'wpbooklist_add_storefront_bookimg_post', array( $storefront_general_functions, 'wpbooklist_add_storefront_bookimg_post_func' ) );
	}
}





	// Adding the function that will take our STOREFRONT_NONCES_ARRAY Constant from above and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $storefront_general_functions, 'wpbooklist_storefront_create_nonces' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $storefront_general_functions, 'wpbooklist_storefront_update_upgrade_function' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $storefront_general_functions, 'wpbooklist_storefront_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $storefront_general_functions, 'wpbooklist_storefront_frontend_js' ) );

	// Adding the admin css file for this extension.
	add_action( 'admin_enqueue_scripts', array( $storefront_general_functions, 'wpbooklist_storefront_admin_style' ) );

	// Adding the Front-End css file for this extension.
	add_action( 'wp_enqueue_scripts', array( $storefront_general_functions, 'wpbooklist_storefront_frontend_style' ) );

	// Function to add table names to the global $wpdb.
	add_action( 'admin_footer', array( $storefront_general_functions, 'wpbooklist_storefront_register_table_name' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'admin_footer', array( $storefront_general_functions, 'wpbooklist_storefront_admin_pointers_javascript' ) );

	// Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_storefront_core_plugin_required' ) );

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_storefront_create_tables' ) );

	// Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_storefront_record_extension_version' ) );

	// Function to add purchase images to the media library upon activation.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_jre_storefront_add_purchase_images' ) );

	// And in the darkness bind them.
	add_filter( 'admin_footer', array( $storefront_general_functions, 'wpbooklist_storefront_smell_rose' ) );

	// Displays the 'Enter Your License Key' message at the top of the dashboard if the user hasn't done so already.
	add_action( 'admin_notices', array( $storefront_general_functions, 'wpbooklist_storefront_top_dashboard_license_notification' ) );

	// Function that adds in the License Key Submission form on this Extension's entry on the plugins page.
	add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $storefront_general_functions, 'wpbooklist_storefront_pluginspage_nonce_entry' ) );



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_storefront_exit_results_action', array( $storefront_ajax_functions, 'storefront_exit_results_action_callback' ) );

	add_action( 'wp_ajax_wpbooklist_storefront_settings_action', array( $storefront_ajax_functions, 'wpbooklist_storefront_settings_action_callback' ) );

	add_action( 'wp_ajax_wpbooklist_save_woocommerce_storefront_settings_action', array( $storefront_ajax_functions, 'wpbooklist_save_woocommerce_storefront_settings_action_callback' ) );

	// Callback function for handling the saving of the user's License Key.
	add_action( 'wp_ajax_wpbooklist_storefront_save_license_key_action', array( $storefront_ajax_functions, 'wpbooklist_storefront_save_license_key_action_callback' ) );
	



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */






















