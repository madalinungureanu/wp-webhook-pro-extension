<?php
/**
 * WP Webhooks - Custom Integration
 *
 * @package       WPWHEXT
 * @author        Jon Doe
 * @license       gplv3-or-later
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   WP Webhooks - Custom Integration
 * Plugin URI:    https://yourdomain.test
 * Description:   This is a demo integration for WP Webhooks
 * Version:       1.0.0
 * Author:        Jon Doe
 * Author URI:    http://jondoe.test
 * Text Domain:   wpwh-custom-integration
 * Domain Path:   /languages
 * License:       GPLv3 or later
 * License URI:   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Webhooks - Custom Integration. If not, see <https://www.gnu.org/licenses/gpl-3.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'WPWHEXT_NAME',			'WP Webhooks - Custom Integration' );

// Plugin version
define( 'WPWHEXT_VERSION',		'1.0.0' );

// Plugin Root File
define( 'WPWHEXT_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'WPWHEXT_PLUGIN_BASE',	plugin_basename( WPWHEXT_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'WPWHEXT_PLUGIN_DIR',	plugin_dir_path( WPWHEXT_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'WPWHEXT_PLUGIN_URL',	plugin_dir_url( WPWHEXT_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once WPWHEXT_PLUGIN_DIR . 'core/class-wpwh-custom-integration.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Jon Doe
 * @since   1.0.0
 * @return  object|Wpwh_Custom_Integration
 */
function WPWHEXT() {
	return Wpwh_Custom_Integration::instance();
}

WPWHEXT();
