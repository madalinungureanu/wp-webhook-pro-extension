<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Wpwh_Custom_Integration' ) ) :

	/**
	 * Main Wpwh_Custom_Integration Class.
	 *
	 * @package		WPWHEXT
	 * @subpackage	Classes/Wpwh_Custom_Integration
	 * @since		1.0.0
	 * @author		Jon Doe
	 */
	final class Wpwh_Custom_Integration {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Wpwh_Custom_Integration
		 */
		private static $instance;

		/**
		 * WPWHEXT helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Wpwh_Custom_Integration_Helpers
		 */
		public $helpers;

		/**
		 * WPWHEXT settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Wpwh_Custom_Integration_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'wpwh-custom-integration' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'wpwh-custom-integration' ), '1.0.0' );
		}

		/**
		 * Main Wpwh_Custom_Integration Instance.
		 *
		 * Insures that only one instance of Wpwh_Custom_Integration exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Wpwh_Custom_Integration	The one true Wpwh_Custom_Integration
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wpwh_Custom_Integration ) ) {
				self::$instance					= new Wpwh_Custom_Integration;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Wpwh_Custom_Integration_Helpers();
				self::$instance->settings		= new Wpwh_Custom_Integration_Settings();

				//Fire the plugin logic
				new Wpwh_Custom_Integration_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'WPWHEXT/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once WPWHEXT_PLUGIN_DIR . 'core/includes/classes/class-wpwh-custom-integration-helpers.php';
			require_once WPWHEXT_PLUGIN_DIR . 'core/includes/classes/class-wpwh-custom-integration-settings.php';

			require_once WPWHEXT_PLUGIN_DIR . 'core/includes/classes/class-wpwh-custom-integration-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpwh-custom-integration', FALSE, dirname( plugin_basename( WPWHEXT_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.