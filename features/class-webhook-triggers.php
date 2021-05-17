<?php
/**
 * This class extends WP Webhooks and WP Webhooks Pro
 * with a custom webhook trigger endpoint.
 * 
 * The trigger endpoint is called "Demo Send Data On Register" and can be activated 
 * within your WordPress dashboard -> Settings -> WP Webhooks/Pro -> Settings
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

if( !class_exists( 'WP_Webhooks_Custom_Extension_Triggers' ) ){

	class WP_Webhooks_Custom_Extension_Triggers{

		public function __construct() {

			/*
			 * Call the webhook triggers
			 *
			 * To register a webhook trigger, you have to define two basic hooks.
			 * The first one is to create the trigger itself. It will be called after
			 * WP Webhooks Pro is loaded by itself. You can call your hooks easily there.
			 * The second hook is to register the trigger itself, so that it gets recognized as such
			 */
			add_action( 'plugins_loaded', array( $this, 'add_webhook_triggers' ), 10 );
			add_filter( 'wpwhpro/webhooks/get_webhooks_triggers', array( $this, 'add_webhook_triggers_content' ), 10 );
		}

		/**
		 * ######################
		 * ###
		 * #### WEBHOOK TRIGGERS
		 * ###
		 * ######################
		 */

		/*
         * Regsiter all available webhook triggers
         */
		public function add_webhook_triggers_content( $triggers ){

			$triggers[] = $this->trigger_create_user_content();

			return $triggers;
		}

		/*
         * Add the specified webhook triggers logic.
         * We also add the demo functionality here
         */
		public function add_webhook_triggers() {

			$active_webhooks   = WPWHPRO()->settings->get_active_webhooks();
			$availale_triggers = $active_webhooks['triggers'];

			if ( isset( $availale_triggers['create_user'] ) ) {
				add_action( 'user_register', array( $this, 'ironikus_trigger_user_register' ), 10, 1 );
				add_filter( 'ironikus_demo_test_user_create', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
			}

		}

		/*
        * Register the trigger as an element
        */
		public function trigger_create_user_content(){

			$parameter = array(
				'user_object' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-demo_create_user-content' ) ),
				'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-demo_create_user-content' ) ),
			);

			ob_start();
			?>
            <p><?php echo WPWHPRO()->helpers->translate( "Please copy your Webhooks Pro webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-demo_create_user-content" ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'You will recieve a full response of the user object, as well as the user meta, so everything you need will be there.', 'trigger-demo_create_user-content' ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-demo_create_user-content' ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'To check the webhook response on a demo request, just open your browser console and you will see the object.', 'trigger-demo_create_user-content' ); ?></p>
			<?php
			$description = ob_get_clean();

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_post_create_user_on_certain_id_demo' => array(
						'id'          => 'wpwhpro_post_create_user_on_certain_id_demo',
						'type'        => 'select',
						'multiple'    => true,
						'choices'      => array(
                            'name_1' => 'Label 1',
                            'name_2' => 'Label 2',
                        ),
						'label'       => WPWHPRO()->helpers->translate('This is the settings label', 'trigger-demo_create_user-content'),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Include the description for your single settings item here.', 'trigger-demo_create_user-content-tip')
					),
				)
			);

			return array(
				'trigger' => 'demo_create_user',
				'name'  => WPWHPRO()->helpers->translate( 'Demo Send Data On Register', 'trigger-demo_create_user-content' ),
				'parameter' => $parameter,
				'settings'          => $settings,
				'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', '' ) ), //Display some response code within the frontend
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user registered.', 'trigger-demo_create_user-content' ),
				'description' => $description,
				'callback' => 'test_user_create'
			);

		}

		/*
        * Register the user register trigger as an element
        */
		public function ironikus_trigger_user_register( $user_id ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'demo_create_user' );
			$user_data = (array) get_user_by( 'id', $user_id );
			$user_data['user_meta'] = get_user_meta( $user_id );

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_post_create_user_on_certain_id_demo' && ! empty( $settings_data ) ){
							if( ! in_array( 'name_1', $settings_data ) ){ //Test against the custom settings you defined earlier
								$is_valid = false;
							}
						}

					}
				}

				if( $is_valid ) {
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                    if( $webhook_url_name !== null ){
                        $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                    } else {
                        $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                    }
				}
				
			}

			do_action( 'wpwhpro/webhooks/trigger_user_register', $user_id, $user_data, $response_data );
		}

		/*
        * Register the demo data response
        */
		public function ironikus_send_demo_user_create( $data, $webhook, $webhook_group ){

			$data = array (
				'data' =>
					array (
						'ID' => '1',
						'user_login' => 'admin',
						'user_pass' => '$P$BVbptZxEcZV2yeLyYeN.O4ZeG8225d.',
						'user_nicename' => 'admin',
						'user_email' => 'admin@ironikus.dev',
						'user_url' => '',
						'user_registered' => '2018-11-06 14:19:18',
						'user_activation_key' => '',
						'user_status' => '0',
						'display_name' => 'admin',
					),
                );

			return $data;
		}

	}

	new WP_Webhooks_Custom_Extension_Triggers();

}