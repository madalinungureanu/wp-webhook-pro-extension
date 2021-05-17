<?php
/**
 * This class extends WP Webhooks and WP Webhooks Pro
 * with a custom webhook action endpoint.
 * 
 * The action endpoint is called demo_action and can be activated 
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

if( !class_exists( 'WP_Webhooks_Custom_Extension_Actions' ) ){

	class WP_Webhooks_Custom_Extension_Actions{

		private $wpwh_use_new_filter = null;

		public function __construct() {

			/*
			 * Call the webhook actions
			 *
			 * To register a webhook action, you have to define two basic hooks.
			 * The first one is to hook into all possible and available actions
			 * and the second one is to register the call you want to set up with it.
			 */
			if( $this->wpwh_use_new_action_filter() ){
				add_filter( 'wpwhpro/webhooks/add_webhook_actions', array( $this, 'add_webhook_actions' ), 20, 4 );
			} else {
				add_action( 'wpwhpro/webhooks/add_webhooks_actions', array( $this, 'add_webhook_actions' ), 20, 3 );
			}
			add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_webhook_actions_content' ), 20 );

		}

		/**
		 * ######################
		 * ###
		 * #### HELPERS
		 * ###
		 * ######################
		 */

		public function wpwh_use_new_action_filter(){

			if( $this->wpwh_use_new_filter !== null ){
				return $this->wpwh_use_new_filter;
			}

			$return = false;
			$version_current = '0';
			$version_needed = '0';
	
			if( defined( 'WPWHPRO_VERSION' ) ){
				$version_current = WPWHPRO_VERSION;
				$version_needed = '4.1.0';
			}
	
			if( defined( 'WPWH_VERSION' ) ){
				$version_current = WPWH_VERSION;
				$version_needed = '3.1.0';
			}
	
			if( version_compare( (string) $version_current, (string) $version_needed, '>=') ){
				$return = true;
			}

			$this->wpwh_use_new_filter = $return;

			return $return;
		}

		/**
		 * ######################
		 * ###
		 * #### WEBHOOK ACTIONS
		 * ###
		 * ######################
		 */

		/*
		 * Register all available action webhooks here
		 *
		 * This function will add your webhook to our globally registered actions array
		 * You can add a webhook by just adding a new line item here.
		 */
		public function add_webhook_actions_content( $actions ){

			$actions[] = $this->action_demo_action_content();

			return $actions;
		}

		/*
		 * Add the callback function for a defined action
		 *
		 * We call the default get_active_webhooks function to grab
		 * all of the currently activated triggers.
		 *
		 * We always send three different properties with the defined wehook.
		 * @param $action - the defined action defined within the action_demo_action function
		 * @param $webhook - The webhook itself
		 * @param $api_key - an api_key if defined
		 */
		public function add_webhook_actions( $response, $action, $webhook, $api_key = '' ){

			//Backwards compatibility prior 4.1.0 (wpwhpro) or 3.1.0 (wpwh)
			if( ! $this->wpwh_use_new_action_filter() ){
				$api_key = $webhook;
				$webhook = $action;
				$action = $response;

				$active_webhooks = WPWHPRO()->settings->get_active_webhooks();
				$available_actions = $active_webhooks['actions'];

				if( ! isset( $available_actions[ $action ] ) ){
					return $response;
				}
			}

			$return_data = null;

			switch( $action ){
				case 'demo_action':
					$return_data = $this->action_demo_action();
					break;
			}

			//Make sure we only fire the response in case the old logic is used
			if( $return_data !== null && ! $this->wpwh_use_new_action_filter() ){
				WPWHPRO()->webhook->echo_action_data( $return_data );
				die();
			}

			if( $return_data !== null ){
				$response = $return_data;
			}

			return $response;
		}

		/*
		 * The core logic to delete a specified user
		 *
		 * This function gets loaded within the add_webhook_actions_content
		 * function and it is responsible for registering the webhook itself
		 * with all its data.
		 *
		 * The parameter array is an array of all the available parameter items
		 * we parse to the action itself. The key is the parameter name and the
		 * value is another array that can contain a short description, as well
		 * as a marker for expired users.
		 *
		 * The $description contains the main description of the action itself. In it,
		 * you can define all the information for this webhook.
		 *
		 * We always return an array with all the values.
		 * It is important to always define the action key/value, since this is the
		 * main identifier that will be used for all of our internal logic.
		 *
		 * You can also use our translation function to make your own webhook
		 * multilingual with ease. The first parameter is the translateable string and
		 * the second parameter is in identifier wher ethe string comes from (you can use
		 * for example your plugin slug.
		 */
		public function action_demo_action_content(){

			//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'demo_value_1'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'This value only allows integers. If a string is given, it gets validated within the incoming data.', 'action-demo_action-content' ) ),
				'demo_value_2'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'This field can contain a string.', 'action-demo_action-content' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-demo_action-content' ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-post-content' ) ),
			);

			//This area will be displayed within the "return" area of the webhook action
			ob_start();
			?>
            <pre>
$return_args = array(
    'success' => false,
    'msg' => 'This is a test message'
);
        </pre>
			<?php
			$returns_code = ob_get_clean();

			ob_start();
			?>
                <p>
                    <?php echo WPWHPRO()->helpers->translate( 'This is the main description. Here you can add everything the end user needs to know.', 'action-demo_action-content' ); ?>
                </p>
			<?php
			$description = ob_get_clean();

			return array(
				'action'            => 'demo_action', //required
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This is the short description of your webhook action.', 'action-demo_action-content' ),
				'description'       => $description
			);

		}

		/**
		 * The core logic of the delete function
		 *
		 * This function is responsible from catching the delete request
		 * and firing the logic itself. You can take the request by
		 * taking the values defined inside of your $parameters variale
		 * in the upper function.
		 *
		 * The rest depends on your specific logic.
		 *
		 * Please don't forget to die() at the end of a function
		 */
		public function action_demo_action() {

			$response_body = WPWHPRO()->helpers->get_response_body();
			$return_args = array(
				'success' => false
			);

		    //This is how defined parameters look - you can use the exact same structure and catch the data you need
			$demo_value_1     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'demo_value_1' ) );
			$demo_value_2     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'demo_value_2' );

			//Validate required fields
			if( empty( $demo_value_1 ) && $demo_value_1 !== 0 && $demo_value_1 !== '0' ){

				$return_args['msg'] = WPWHPRO()->helpers->translate("Please set the demo_value_1 to continue.", 'action-demo_action-failure' );

				return $return_args;
			}

			ob_start();
			echo 'First value: ';
			echo( $demo_value_1 );
			echo ' Second value: ';
			echo( $demo_value_2 );
			$text = ob_get_clean();

			// DO YOUR ACTION HERE...
			$return_args['msg'] = WPWHPRO()->helpers->translate("You set the following demo values ( encoded with htmlspecialchars() ): ", 'action-demo_action-success' ) . htmlspecialchars( $text );
			$return_args['success'] = true;

			return $return_args;
		}

	}

	new WP_Webhooks_Custom_Extension_Actions();

}