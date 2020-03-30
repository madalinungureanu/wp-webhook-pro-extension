<?php
/**
 * This class extends WP Webhooks and WP Webhooks Pro
 * with a custom menu item within the Configuration pages of WP Webhooks.
 * 
 * The menu item is called "Demo" and will be available within the WP Webhooks menu
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

/*
 * Please note: This plugin is just one possible usage
 * on how you can extend WP Webhooks and Pro. You can also set up
 * the plugin by just using WordPress hooks. We just use a class
 * here to show you how it should be implemented in case you want
 * to add multiple webhooks for a better readability.
 */

if( !class_exists( 'WP_Webhooks_Custom_Extension_Menu_Items' ) ){

	class WP_Webhooks_Custom_Extension_Menu_Items{

		public function __construct() {

			/*
			 * Add the menu items
			 * 
			 * The first filter call defines the names and slugs of the menu items.
			 * With the add_action() call, we register the actual content for the menu items.
			 */
			add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
			add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );
		}

		/**
		 * ######################
		 * ###
		 * #### MENU ITEMS
		 * ###
		 * ######################
		 */

		/**
		 * Add the settings tabs - to add multiple ones, just duplicate the line.
		 */
		public function add_main_settings_tabs( $tabs ){

			$tabs['demo'] = WPWHPRO()->helpers->translate( 'Demo', 'admin-menu' );

			return $tabs;

		}

		/**
		 * This function takes care of displaying the actual content of our custom extension. 
		 * Within out demo site, we display a simple string. 
		 */
		public function add_main_settings_content( $tab ){

			switch($tab){
				case 'demo':
					echo 'This is some custom text for our very own demo tab.';
					break;
			}

		}

	}

	new WP_Webhooks_Custom_Extension_Menu_Items();

}