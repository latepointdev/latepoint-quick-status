<?php
/**
 * Plugin Name: LatePoint Addon - Quick Status
 * Description: Adds a toggle button to enable/disable services directly from the list.
 * Version: 1.1.0
 * Author: KNYN.DEV
 * Author URI: https://knyn.dev
 * Plugin URI: https://knyn.dev
 * Text Domain: latepoint-quick-status
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LatePointAddonQuickStatus' ) ):

	/**
	 * Main Addon Class.
	 *
	 */

	class LatePointAddonQuickStatus {

		/**
		 * Addon version.
		 *
		 */
		public $version = '1.1.0';
		public $db_version = '1.0.0';
		public $addon_name = 'latepoint-quick-status';


		/**
		 * LatePoint Constructor.
		 */
		public function __construct() {
			$this->define_constants();
            $this->init_hooks();

			add_action( 'latepoint_includes', [ $this, 'core_includes' ] );
			add_action( 'latepoint_init_hooks', [ $this, 'core_init_hooks' ] );
		}

        public function init_hooks(){
			add_action( 'latepoint_on_addon_deactivate', [ $this, 'on_addon_deactivate' ], 10, 2 );

			register_activation_hook( __FILE__, [ $this, 'on_activate' ] );
			register_deactivation_hook( __FILE__, [ $this, 'on_deactivate' ] );
        }

        public function on_addon_deactivate( $addon_name, $addon_version ) {
            if(class_exists('LatePoint\Cerber\RouterPro')){
                LatePoint\Cerber\RouterPro::wipe($addon_name, $addon_version);
            }
            if(class_exists('OsAddonsHelper')){
                OsAddonsHelper::remove_routed_addon($addon_name);
            }
        }


		/**
		 * Define LatePoint Constants.
		 */
		public function define_constants() {
			$upload_dir = wp_upload_dir();

			global $wpdb;

		if ( ! defined( 'LATEPOINT_ADDON_QUICK_STATUS_ABSPATH' ) ) {
			define( 'LATEPOINT_ADDON_QUICK_STATUS_ABSPATH', dirname( __FILE__ ) . '/' );
		}
		if ( ! defined( 'LATEPOINT_ADDON_QUICK_STATUS_LIB_ABSPATH' ) ) {
			define( 'LATEPOINT_ADDON_QUICK_STATUS_LIB_ABSPATH', LATEPOINT_ADDON_QUICK_STATUS_ABSPATH . 'lib/' );
		}
		if ( ! defined( 'LATEPOINT_ADDON_QUICK_STATUS_VIEWS_ABSPATH' ) ) {
			define( 'LATEPOINT_ADDON_QUICK_STATUS_VIEWS_ABSPATH', LATEPOINT_ADDON_QUICK_STATUS_LIB_ABSPATH . 'views/' );
		}
		}


		public static function public_stylesheets() {
			return plugin_dir_url( __FILE__ ) . 'assets/css/';
		}

		public static function public_javascripts() {
			return plugin_dir_url( __FILE__ ) . 'assets/js/';
		}


		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function core_includes() {
			// HELPERS
			include_once( dirname( __FILE__ ) . '/lib/helpers/quick_status_helper.php' );
		}

		public function core_init_hooks() {
			add_filter( 'latepoint_installed_addons', [ $this, 'register_addon' ] );
			add_filter( 'latepoint_addons_sqls', [ $this, 'db_sqls' ] );

			add_action( 'latepoint_admin_enqueue_scripts', [ $this, 'load_admin_scripts_and_styles' ] );

			add_filter( 'latepoint_localized_vars_admin', [ $this, 'localized_vars_for_admin' ] );
			
			// Initialize the quick service toggle functionality
			add_action( 'latepoint_init', [ $this, 'init_quick_service_toggle' ] );
		}
		
		public function init_quick_service_toggle() {
			// This will be handled by the OsQuickStatusHelper class
		}

		/**
		 * Init LatePoint when WordPress Initialises.
		 */
		public function init() {
			// Set up localisation.
			$this->load_plugin_textdomain();
		}

		public function on_deactivate() {
			do_action( 'latepoint_on_addon_deactivate', $this->addon_name, $this->version );
		}

		public function on_activate() {
			do_action( 'latepoint_on_addon_activate', $this->addon_name, $this->version );
		}

		public function register_addon( $installed_addons ) {
			$installed_addons[] = [
				'name'       => $this->addon_name,
				'db_version' => $this->db_version,
				'version'    => $this->version
			];

			return $installed_addons;
		}

		public function db_sqls( $sqls ) {
			// No database tables needed for this addon
			return $sqls;
		}

		public function localized_vars_for_admin( $localized_vars ) {
			// Add any admin-specific localized variables here
			return $localized_vars;
		}

		public function load_admin_scripts_and_styles() {
			// Stylesheets
			wp_enqueue_style( 'latepoint-quick-status-admin', $this->public_stylesheets() . 'style.css', false, $this->version );

			// Javascripts
			wp_enqueue_script( 'latepoint-quick-status-admin', $this->public_javascripts() . 'toggle.js', array( 'jquery' ), $this->version );

			wp_localize_script('latepoint-quick-status-admin', 'LPServiceToggle', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('lp_toggle_service_nonce')
			]);
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'latepoint-quick-status', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

	}

endif;

// Check if LatePoint is active before initializing
function latepoint_quick_status_is_latepoint_active() {
	return in_array('latepoint/latepoint.php', get_option('active_plugins', array())) || 
		   array_key_exists('latepoint/latepoint.php', get_site_option('active_sitewide_plugins', array()));
}

if ( latepoint_quick_status_is_latepoint_active() ) {
	$LATEPOINT_ADDON_QUICK_STATUS = new LatePointAddonQuickStatus();
} else {
	function latepoint_not_installed_activated() {
		if ( ! ( current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' ) ) ) {
			return;
		}
		
		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}
		
		$latepoint_plugin_path = 'latepoint/latepoint.php';
		if ( file_exists( WP_PLUGIN_DIR . '/latepoint/latepoint.php' ) ) {
			$action_url = wp_nonce_url( 'plugins.php?action=activate&plugin=' . $latepoint_plugin_path . '&plugin_status=all&paged=1&s', 'activate-plugin_' . $latepoint_plugin_path );
			$button_label = __( 'Activate LatePoint', 'latepoint-quick-status' );
		} else {
			$action_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=latepoint' ), 'install-plugin_latepoint' );
			$button_label = __( 'Install LatePoint', 'latepoint-quick-status' );
		}
		
		$button = '<p><a href="' . esc_url( $action_url ) . '" class="button-primary">' . esc_html( $button_label ) . '</a></p>';
		$message = sprintf( __( '%1$sQuick Status Addon%2$s requires %1$sLatePoint%2$s core plugin installed & activated.', 'latepoint-quick-status' ), '<strong>', '</strong>' );
		$class = 'notice notice-error';
		
		printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
	}
	add_action( 'admin_notices', 'latepoint_not_installed_activated' );
	add_action( 'network_admin_notices', 'latepoint_not_installed_activated' );
}
