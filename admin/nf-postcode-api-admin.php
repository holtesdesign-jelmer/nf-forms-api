<?php

/**
 * Call the Ninja Form filters.
 *
 * @since    1.0.0
 */
add_filter( 'ninja_forms_plugin_settings_groups', 'nf_settings_groups', 10, 1 );
add_filter( 'ninja_forms_plugin_settings', 'plugin_settings', 10, 1 );
add_filter( 'ninja_forms_check_setting_nf_api_key',  'check_nf_key', 10, 1 );
add_filter( 'ninja_forms_check_setting_nf_api_secret',  'check_nf_secret', 10, 1 );
add_filter( 'ninja_forms_check_setting_nf_webreact_license',  'check_nf_license', 10, 1 );


/**
 * Check the API-key for correctness.
 *
 * @since    1.0.0
 */
function check_nf_key( $setting )
{
    $key = Ninja_Forms()->get_setting( 'nf_api_key' );
    $secret = Ninja_Forms()->get_setting( 'nf_api_secret' );
    // Check for errors...
    if (!empty($key) && !empty($secret)) {

        $url = 'https://api.postcode.nl/rest/addresses/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $key .':'. $secret);
        $jsonResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Translate errors
        $errors = array(
            'Combination does not exist.'            => __('Combination does not exist.', 'nf-postcode-api'),
            'Specified postcode is too short.'       => __('Specified postcode is too short.', 'nf-postcode-api'),
            'Housenumber must contain numbers only.' => __('Housenumber must contain numbers only.', 'nf-postcode-api'),
            'Postcode does not use format `1234AB`.' => __('Postcode does not use format `1234AB`.', 'nf-postcode-api'),
            'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_NotAuthorizedException' => __('API-Gegevens incorrect, verbinding mislukt.', 'nf-postcode-api'),
            'PostcodeNl_Controller_Address_PostcodeTooShortException' => __('API-Gegevens correct, verbinding geslaagd.', 'nf-postcode-api'),
        );

        foreach ($errors as $string => $translation) {
            $jsonResponse = str_replace($string, $translation, $jsonResponse);
        }

        $ar          = json_decode($jsonResponse, true);

            if($ar['exceptionId'] == "API-Gegevens correct, verbinding geslaagd."){
                add_action( 'admin_notices', 'nf_admin_notice_succes' );
            }
            elseif ($ar['exceptionId'] == "API-Gegevens incorrect, verbinding mislukt."){
                add_action( 'admin_notices', 'nf_admin_notice_error' );
            }

    }
    return $setting;
}

/**
 * Check the API-secret for correctness.
 *
 * @since    1.0.0
 */
function check_nf_secret( $setting )
{
    // Check for errors...

    return $setting;
}

/**
 * Check the Webreact License key for correctness.
 *
 * @since    1.0.0
 */
function check_nf_license( $setting )
{
    // Check for errors...

    return $setting;
}

/**
 * Init the settings in Ninja Forms backend.
 *
 * @since    1.0.0
 */
function plugin_settings( $settings )
{
    $settings[ 'nf-postcode' ] = array(
        'nf_api_key' => array(
            'id'    => 'nf_api_key',
            'type'  => 'textbox',
            'label'  => __( 'Postcode.nl API-Key', 'ninja-forms-example' ),
            'desc'  => __( 'This is the Postcode.nl API-Key.', 'ninja-forms-example' ),
        ),
        'nf_api_secret' => array(
            'id'    => 'nf_api_secret',
            'type'  => 'textbox',
            'label'  => __( 'Postcode.nl API-Secret', 'ninja-forms-example' ),
            'desc'  => __( 'This is the Postcode.nl API-Secret.', 'ninja-forms-example' ),
        ),
        'nf_webreact_license' => array(
            'id'    => 'nf_webreact_license',
            'type'  => 'textbox',
            'label'  => __( 'Webreact License', 'ninja-forms-example' ),
            'desc'  => __( 'This is the Webreact License-ID.', 'ninja-forms-example' ),
        ),
    );
    return $settings;
}

/**
 * Init the settings field in Ninja forms backend..
 *
 * @since    1.0.0
 */
function nf_settings_groups( $groups )
{
    $groups[ 'nf-postcode' ] = array(
        'id' => 'nf-postcode',
        'label' => __( 'Postcode.nl API Connector', 'ninja-forms-example' ),
    );
    return $groups;
}

