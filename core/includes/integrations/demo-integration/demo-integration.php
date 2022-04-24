<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Webhooks_Integrations_demo_integration
 *
 * This class contains the main definition of the demo
 * integration for WP Webhooks
 *
 * @package		WPWHEXT
 * @subpackage	Integrations/WP_Webhooks_Integrations_demo_integration
 * @author		Jon Doe
 * @since		1.0.0
 */
class WP_Webhooks_Integrations_demo_integration {

	/**
	 * Customize the availability of the integration.
	 * By default, we always load it.
	 *
	 * @access	public
	 *
	 * @return boolean
	 */
	public function is_active(){
		return true;
	}

	/**
	 * The details of the custom integration
	 *
	 * @since	1.0.0
	 * @access	public
	 * 
	 * @return	array	The integration details
	 */
	public function get_details(){
		$integration_url = plugin_dir_url( __FILE__ );

		return array(
			'name' => 'Demo Integration',
			'icon' => $integration_url . '/assets/img/icon-wp-webhooks.svg',
		);
	}

}
