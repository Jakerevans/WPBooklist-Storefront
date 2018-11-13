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
 * Description: The WPBookList StoreFront extension allows you to link each title to where they're being sold by displaying prominent and attractive purchase buttons and text links.
 * Version: 6.0.0
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
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

	// Extension version number.
	define( 'STOREFRONT_VERSION_NUM', '6.0.0' );

	// Root plugin folder directory.
	define( 'STOREFRONT_ROOT_DIR', plugin_dir_path( __FILE__ ) );

	// Root WordPress Plugin Directory.
	define( 'STOREFRONT_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-storefront', '', plugin_dir_path( __FILE__ ) ) );

	// Root plugin folder URL .
	define( 'STOREFRONT_ROOT_URL', plugins_url() . '/wpbooklist-storefront/' );

	// Root Classes Directory.
	define( 'STOREFRONT_CLASS_DIR', STOREFRONT_ROOT_DIR . 'includes/classes/' );

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


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Function that loads up the menu page entry for this Extension.
	add_filter( 'wpbooklist_add_sub_menu', array( $storefront_general_functions, 'wpbooklist_storefront_submenu' ) );

	add_filter( 'wpbooklist_add_to_library_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_library_view_display_options' ) );

	add_filter( 'wpbooklist_add_to_book_display_options', array( $storefront_general_functions, 'wpbooklist_storefront_insert_book_view_display_options' ) );

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

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_storefront_create_tables' ) );

	// Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_storefront_record_extension_version' ) );

	// Function to add purchase images to the media library upon activation.
	register_activation_hook( __FILE__, array( $storefront_general_functions, 'wpbooklist_jre_storefront_add_purchase_images' ) );

	// Function that adds in the HTML into the 'Add a Book' form.
	add_filter( 'wpbooklist_append_to_book_form_storefront_fields', array( $storefront_general_functions, 'wpbooklist_storefront_insert_storefront_fields' ) );

	add_filter( 'wpbooklist_append_to_frontend_library_price_purchase', array( $storefront_general_functions, 'wpbooklist_append_to_frontend_library_price_purchase_func' ) );

	add_filter( 'wpbooklist_append_to_frontend_library_image_purchase', array( $storefront_general_functions, 'wpbooklist_append_to_frontend_library_image_purchase_func' ) );

	add_filter( 'wpbooklist_append_to_colorbox_price', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_price_func' ) );

	add_filter( 'wpbooklist_append_to_colorbox_purchase_text_link', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_purchase_text_link_func' ) );

	add_filter( 'wpbooklist_append_to_colorbox_purchase_image_link', array( $storefront_general_functions, 'wpbooklist_append_to_colorbox_purchase_image_link_func' ) );



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_storefront_exit_results_action', array( $storefront_ajax_functions, 'storefront_exit_results_action_callback' ) );

	add_action( 'wp_ajax_wpbooklist_storefront_settings_action', array( $storefront_ajax_functions, 'wpbooklist_storefront_settings_action_callback' ) );

	add_action( 'wp_ajax_wpbooklist_save_woocommerce_storefront_settings_action', array( $storefront_ajax_functions, 'wpbooklist_save_woocommerce_storefront_settings_action_callback' ) );


	



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */






















