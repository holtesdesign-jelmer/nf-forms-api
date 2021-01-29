<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.webreact.nl
 * @since      1.0.0
 *
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/includes
 * @author     Webreact <email@example.com>
 */
class nf_forms_postcode_api_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'nf-forms-postcode-api',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
