<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.webreact.nl
 * @since             1.0.0
 * @package           nf_forms_postcode_api
 *
 * @wordpress-plugin
 * Plugin Name:       Ninja Forms - Postcode.nl API
 * Plugin URI:        https://www.webreact.nl/nf-forms-postcode-api-uri/
 * Description:       This plug-in combines Ninja Forms with Postcode.nl API to auto-fill and validate address data.
 * Version:           5.1.0
 * Author:            Webreact
 * Author URI:        https://www.webreact.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nf-forms-postcode-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nf-forms-postcode-api-activator.php
 */
function activate_nf_forms_postcode_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nf-forms-postcode-api-activator.php';
	nf_forms_postcode_api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nf-forms-postcode-api-deactivator.php
 */
function deactivate_nf_forms_postcode_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nf-forms-postcode-api-deactivator.php';
	nf_forms_postcode_api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nf_forms_postcode_api' );
register_deactivation_hook( __FILE__, 'deactivate_nf_forms_postcode_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nf-forms-postcode-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_nf_forms_postcode_api() {

	$plugin = new nf_forms_postcode_api();
	$plugin->run();

}
run_nf_forms_postcode_api();

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/webreact/nf-forms-postcode-api',
    __FILE__,
    'nf-forms-postcode-api'
);