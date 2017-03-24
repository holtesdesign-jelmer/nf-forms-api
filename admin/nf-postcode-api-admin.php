<?php
/**
 * Created by PhpStorm.
 * User: nilsringersma
 * Date: 23-03-17
 * Time: 10:12
 */

add_action( 'admin_init', 'nf_plugin_settings' );
add_action( 'admin_menu', 'my_admin_menu' );

function nf_plugin_settings(){
    register_setting( 'nf-plugin-settings-group', 'nf_api_key' );
    register_setting( 'nf-plugin-settings-group', 'nf_api_secret' );
    register_setting( 'nf-plugin-settings-group', 'nf_webreact_license' );
}

function my_admin_menu() {

    // Add top level Page.
    add_menu_page( 'Postcode.nl', 'Postcode.nl', 'manage_options', 'nf-postcode-api/postcode_settings.php', 'postcode_menu_init', 'dashicons-tickets', 6  );

}

function postcode_menu_init() {
    require_once plugin_dir_path( __FILE__ ). 'partials/nf-postcode-setting-page.php';
}























	 function load_admin_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/nf-postcode-api-admin-display.php';
	}

