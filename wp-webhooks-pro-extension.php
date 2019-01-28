<?php
/**
 * Plugin Name: WP Webhooks Pro Extension
 * Plugin URI: https://ironikus.com/downloads/wp-webhooks-pro/
 * Description: This is an extension example on how you can extend this plugin
 * Version: 1.0
 * Author: Ironikus
 * Author URI: https://ironikus.com/
 * License: GPL2
 *
 * You should have received a copy of the GNU General Public License
 * along with TMG User Filter. If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * Please note: This plugin is just one possible usage
 * on how you can extend WP Webhooks Pro. You can also set up
 * the plugin by just using WordPress hooks. We just use a class
 * here to show you how it should be implemented in case you want
 * to add multiple webhooks for a better readability.
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'WP_Webhooks_Pro_Extensions' ) ){

	class WP_Webhooks_Pro_Extensions{

		public function __construct() {

			/*
			 * Call the webhook actions
			 *
			 * To register a webhook action, you have to define two basic hooks.
			 * The first one is to hook into all possible and available actions
			 * and the second one is to register the call you want to set up with it.
			 */
			add_action( 'wpwhpro/webhooks/add_webhooks_actions', array( $this, 'add_webhook_actions' ), 20, 3 );
			add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_webhook_actions_content' ), 20 );

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

			$actions[] = $this->action_delete_user_content();

			return $actions;
		}

		/*
		 * Add the callback function for a defined action
		 *
		 * We call the default get_active_webhooks function to grab
		 * all of the currently activated triggers.
		 *
		 * We always send three different properties with the defined wehook.
		 * @param $action - the defined action defined within the action_delete_user_content function
		 * @param $webhook - The webhook itself
		 * @param $api_key - an api_key if defined
		 */
		public function add_webhook_actions( $action, $webhook, $api_key ){

			$active_webhooks = WPWHPRO()->settings->get_active_webhooks();

			$available_triggers = $active_webhooks['actions'];

			switch( $action ){
				case 'delete_user':
					/*
					 * We include this isset test to save performance for the whole site.
					 * It is not a requirement, but we highly suggest it.
					 */
					if( isset( $available_triggers['delete_user'] ) ){
						$this->action_delete_user();
					}
					break;
			}
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
		public function action_delete_user_content(){

			$parameter = array(
				'user_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the numeric id of the user.', 'action-delete-user-content' ) ),
				'user_email'    => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the numeric id of the user.', 'action-delete-user-content' ) ),
				'send_email'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user that the account got deleted.', 'action-delete-user-content' ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'action-delete-user-content' ) )
			);

			ob_start();
			?>
                <p>
                    <?php echo WPWHPRO()->helpers->translate( 'This is the main description. Here you can add everything the end user needs to know.', 'action-delete-user-content' ); ?>
                </p>
			<?php
			$description = ob_get_clean();

			return array(
				'action'            => 'demo_delete_user', //required
				'parameter'         => $parameter,
				'short_description' => WPWHPRO()->helpers->translate( 'This is the short description of your webhook action.', 'action-create-user-content' ),
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
		 * The rest depends on your specific logic. As an example,
		 * I included our whole action_delete_user function.
		 *
		 * Please don't forget to die() at the end of a function
		 */
		function action_delete_user() {

			$response_body = WPWHPRO()->helpers->get_response_body();
			$return_args = array(
				'success' => false
			);

		    //This is how defined parameters look - you can use the exact same structure and catch the data you need
			$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$user_email  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			// DO YOUR ACTION HERE...

            if( 'this wehook succeeds, ' == 'then do that!' ){
	            $return_args['msg'] = WPWHPRO()->helpers->translate("User successfully deleted.", 'action-delete-user-success' );
            } else {
	            $return_args['msg'] = WPWHPRO()->helpers->translate("Error deleting user.", 'action-delete-user-success' );
            }

			WPWHPRO()->webhook->echo_json( $return_args );

			die();
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
				'user_object' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-create-user-content' ) ),
				'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) ),
			);

			ob_start();
			?>
            <p><?php echo WPWHPRO()->helpers->translate( "Please copy your Webhooks Pro webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-create-user-content" ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'You will recieve a full response of the user object, as well as the user meta, so everything you need will be there.', 'trigger-create-user-content' ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-create-user-content' ); ?></p>
            <p><?php echo WPWHPRO()->helpers->translate( 'To check the webhook response on a demo request, just open your browser console and you will see the object.', 'trigger-create-user-content' ); ?></p>
			<?php
			$description = ob_get_clean();

			return array(
				'trigger' => 'demo_create_user',
				'name'  => WPWHPRO()->helpers->translate( 'Demo Send Data On Register', 'trigger-create-user-content' ),
				'parameter' => $parameter,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user registered.', 'trigger-create-user-content' ),
				'description' => $description,
				'callback' => 'test_user_create'
			);

		}

		/*
	 * Register the user register trigger as an element
	 */
		public function ironikus_trigger_user_register( $user_id ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'create_user' );
			$user_data = (array) get_user_by( 'id', $user_id );
			$user_data['user_meta'] = get_user_meta( $user_id );

			foreach( $webhooks as $webhook ){
				$response_data = WPWHPRO()->webhook->post_to_webhook( $webhook['webhook_url'], $user_data );
			}

			do_action( 'wpwhpro/webhooks/trigger_user_register', $user_id, $user_data );
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

	// Make sure we load the extension after main plugin is loaded
	if( defined( 'WPWHPRO_SETUP' ) ){
		wpwhpro_load_post_by_email();
	} else {
		add_action( 'wpwhpro_plugin_loaded', 'wpwhpro_load_extension' );
	}

	function wpwhpro_load_extension(){
		/*
		 * Init the extension by calling it here.
		 * Since we don't output anything by default,
		 * you can call the class that way.
		 */
		new WP_Webhooks_Pro_Extensions();
	}

}