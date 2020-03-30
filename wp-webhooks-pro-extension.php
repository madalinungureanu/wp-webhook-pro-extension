<?php
/**
 * Plugin Name: WP Webhooks and Pro Extension
 * Plugin URI: https://ironikus.com/downloads/wp-webhooks-pro/
 * Description: This is an extension example on how you can customize this plugin
 * Version: 1.0.0
 * Author: Ironikus
 * Author URI: https://ironikus.com/
 * License: GPL2
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

// Plugin Root File.
define( 'WPWH_EXTENSION_PLUGIN_FILE',    __FILE__ );

// Plugin base.
define( 'WPWH_EXTENSION_PLUGIN_BASE',    plugin_basename( WPWH_EXTENSION_PLUGIN_FILE ) );

// Plugin Folder Path.
define( 'WPWH_EXTENSION_PLUGIN_DIR',     plugin_dir_path( WPWH_EXTENSION_PLUGIN_FILE ) );

// Plugin Folder URL.
define( 'WPWH_EXTENSION_PLUGIN_URL',     plugin_dir_url( WPWH_EXTENSION_PLUGIN_FILE ) );

/*
* This plugin demonstrates you how to create certain featues of WP Webhooks and WP Webhooks Pro
* 
* For a better readybility, all of the features are separated into single files 
* to make reading as easy as possible.
* In case you don't want a feature, simply comment it out down below.
*/
function wpwh_extension_load(){

	//Extens the plugin with custom webhook actions
	require_once WPWH_EXTENSION_PLUGIN_DIR . 'features/class-webhook-actions.php';

	//Extens the plugin with custom webhook triggers
	require_once WPWH_EXTENSION_PLUGIN_DIR . 'features/class-webhook-triggers.php';

	//Extens the plugin with custom webhook menu items
	require_once WPWH_EXTENSION_PLUGIN_DIR . 'features/class-webhook-menu-items.php';

}

// Make sure we load the extension after main plugin is loaded
if( defined( 'WPWHPRO_SETUP' ) || defined( 'WPWH_SETUP' ) ){
	wpwh_extension_load();
} else {
	add_action( 'wpwhpro_plugin_loaded', 'wpwh_extension_load' );
}