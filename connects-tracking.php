<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.connects.ch
 * @since             1.0.0
 * @package           Connects_Tracking
 *
 * @wordpress-plugin
 * Plugin Name:       Connects Tracking
 * Plugin URI:        https://docs.connects.ch
 * Description:       This Plugin can be used to integrate the Connects Conversion Tracking and the Profiling Tags
 * Version:           1.1.7
 * Author:            Connects GmbH
 * Author URI:        https://www.connects.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connects-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CONNECTS_TRACKING_VERSION', '1.1.7');

// Define Logo URL
define('LOGO_URL', plugin_dir_url(__FILE__) . '/admin/img/connects_logo.png');

define('CONNECTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-connects-tracking-activator.php
 */
function activate_connects_tracking()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-connects-tracking-activator.php';
    Connects_Tracking_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-connects-tracking-deactivator.php
 */
function deactivate_connects_tracking()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-connects-tracking-deactivator.php';
    Connects_Tracking_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_connects_tracking');
register_deactivation_hook(__FILE__, 'deactivate_connects_tracking');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-connects-tracking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_connects_tracking()
{

    $plugin = new Connects_Tracking();
    $plugin->run();

}
run_connects_tracking();
