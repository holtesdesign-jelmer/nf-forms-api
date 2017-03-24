<?php
/**
 * Created by PhpStorm.
 * User: nilsringersma
 * Date: 23-03-17
 * Time: 10:12
 */



add_action( 'wp_enqueue_scripts', 'nf_postcode_enqueue', 1000 );


function nf_postcode_enqueue() {

    wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/nf-postcode-api-public.js', array('nf-front-end'), "1.0" , true );
    wp_localize_script(
        'ajax-script',
        'nf_ajax_url',
        array(
            'ajaxurl'          => admin_url( 'admin-ajax.php' ), // URL to WordPress ajax handling page
            'nonce'                  => wp_create_nonce('nf_postcode_api'),
        )
    );


}
