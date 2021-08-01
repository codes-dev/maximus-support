<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.xuro.com
 * @since             1.0.0
 * @package           Maximus_support
 *
 * @wordpress-plugin
 * Plugin Name:       Maximus Support
 * Plugin URI:        www.westernpaceglobal.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Codes
 * Author URI:        www.xuro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       maximus_support
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MAXIMUS_SUPPORT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-maximus_support-activator.php
 */
function activate_maximus_support($network_wide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maximus_support-activator.php';
	Maximus_support_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-maximus_support-deactivator.php
 */
function deactivate_maximus_support() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maximus_support-deactivator.php';
	Maximus_support_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_maximus_support' );
register_deactivation_hook( __FILE__, 'deactivate_maximus_support' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-maximus_support.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_maximus_support() {

	$plugin = new Maximus_support();
	$plugin->run();

}
run_maximus_support();
