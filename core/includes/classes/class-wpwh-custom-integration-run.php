<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Wpwh_Custom_Integration_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		WPWHEXT
 * @subpackage	Classes/Wpwh_Custom_Integration_Run
 * @author		Jon Doe
 * @since		1.0.0
 */
class Wpwh_Custom_Integration_Run{

	/**
	 * Our Wpwh_Custom_Integration_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugins_loaded', array( $this, 'add_wp_webhooks_integrations' ), 9 );
		add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 20 );
		add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 20 );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * ####################
	 * ### WP Webhooks 
	 * ####################
	 */

	/*
	 * Register dynamically all integrations
	 * The integrations are available within core/includes/integrations.
	 * A new folder is considered a new integration.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function add_wp_webhooks_integrations(){

		// Abort if WP Webhooks is not active
		if( ! function_exists('WPWHPRO') ){
			return;
		}

		$custom_integrations = array();
		$folder = WPWHEXT_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';

		try {
			$custom_integrations = WPWHPRO()->helpers->get_folders( $folder );
		} catch ( Exception $e ) {
			WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
		}

		if( ! empty( $custom_integrations ) ){
			foreach( $custom_integrations as $integration ){
				$file_path = $folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
				WPWHPRO()->integrations->register_integration( array(
					'slug' => $integration,
					'path' => $file_path,
				) );
			}
		}
	}

	/*
	 * Add the setting tabs
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	mixed	$tabs	All available tabs
	 *
	 * @return	array	$data
	 */
	public function add_main_settings_tabs( $tabs ){

		$tabs['demo'] = WPWHPRO()->helpers->translate( 'Demo', 'admin-menu' );

		return $tabs;

	}

	/*
	 * Output the content of the tab
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	mixed	$tab	The current tab
	 *
	 * @return	void
	 */
	public function add_main_settings_content( $tab ){

		switch($tab){
			case 'demo':
				echo '<div class="wpwh-container">This is some custom text for our very own demo tab.</div>';
				break;
		}

	}

}
