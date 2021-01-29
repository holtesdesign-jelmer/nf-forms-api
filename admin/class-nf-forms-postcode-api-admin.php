<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webreact.nl
 * @since      1.0.0
 *
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/admin
 * @author     Webreact <email@example.com>
 */
class nf_forms_postcode_api_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $nf_forms_postcode_api The ID of this plugin.
     */
    private $nf_forms_postcode_api;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $nf_forms_postcode_api The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($nf_forms_postcode_api, $version)
    {
        $this->nf_forms_postcode_api = $nf_forms_postcode_api;
        $this->version = $version;
    }

    /**
     * Check the API-key for correctness.
     *
     * @since    1.0.0
     */
    public function check_nf_key($setting)
    {
        $key = Ninja_Forms()->get_setting('nf_api_key');
        $secret = Ninja_Forms()->get_setting('nf_api_secret');
        // Check for errors...
        if (!empty($key) && !empty($secret)) {
            $url = 'https://api.postcode.nl/rest/addresses/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);
            $jsonResponse = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Translate errors
            $errors = array(
                'Combination does not exist.' => __('Combination does not exist.', 'nf-forms-postcode-api'),
                'Specified postcode is too short.' => __('Specified postcode is too short.', 'nf-forms-postcode-api'),
                'Housenumber must contain numbers only.' => __(
                    'Housenumber must contain numbers only.',
                    'nf-forms-postcode-api'
                ),
                'Postcode does not use format `1234AB`.' => __(
                    'Postcode does not use format `1234AB`.',
                    'nf-forms-postcode-api'
                ),
                'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_NotAuthorizedException' => __(
                    'API-Gegevens incorrect, verbinding mislukt.',
                    'nf-forms-postcode-api'
                ),
                'PostcodeNl_Controller_Address_PostcodeTooShortException' => __(
                    'API-Gegevens correct, verbinding geslaagd.',
                    'nf-forms-postcode-api'
                ),
            );

            foreach ($errors as $string => $translation) {
                $jsonResponse = str_replace($string, $translation, $jsonResponse);
            }

            $ar = json_decode($jsonResponse, true);

            if ($ar['exceptionId'] == "API-Gegevens correct, verbinding geslaagd.") {
                add_action('admin_notices', 'nf_admin_notice_succes');
            } elseif ($ar['exceptionId'] == "API-Gegevens incorrect, verbinding mislukt.") {
                add_action('admin_notices', 'nf_admin_notice_error');
            }
        }
        return $setting;
    }

    /**
     * Check the API-secret for correctness.
     *
     * @since    1.0.0
     */
    public function check_nf_secret($setting)
    {
        // Check for errors...

        return $setting;
    }

    /**
     * Check the Webreact License key for correctness.
     *
     * @since    1.0.0
     */
    public function check_nf_license($setting)
    {
        // Check for errors...

        return $setting;
    }

    /**
     * Init the settings in Ninja Forms backend.
     *
     * @since    1.0.0
     */
    public function plugin_settings($settings)
    {
        $settings['nf-postcode'] = array(
            'nf_api_key' => array(
                'id' => 'nf_api_key',
                'type' => 'textbox',
                'label' => __('Postcode.nl API-Key', 'ninja-forms-example'),
                'desc' => __('This is the Postcode.nl API-Key.', 'ninja-forms-example'),
            ),
            'nf_api_secret' => array(
                'id' => 'nf_api_secret',
                'type' => 'textbox',
                'label' => __('Postcode.nl API-Secret', 'ninja-forms-example'),
                'desc' => __('This is the Postcode.nl API-Secret.', 'ninja-forms-example'),
            ),
            'nf_webreact_license' => array(
                'id' => 'nf_webreact_license',
                'type' => 'textbox',
                'label' => __('Webreact License', 'ninja-forms-example'),
                'desc' => __('This is the Webreact License-ID.', 'ninja-forms-example'),
            ),
        );
        return $settings;
    }

    /**
     * Init the settings field in Ninja forms backend..
     *
     * @since    1.0.0
     */
    public function nf_settings_groups($groups)
    {
        $groups['nf-postcode'] = array(
            'id' => 'nf-postcode',
            'label' => __('Postcode.nl API Connector', 'ninja-forms-example'),
        );
        return $groups;
    }

    /**
     * Request an address from Postcode.nl's API.
     */
    public function request_address()
    {
        if (!check_ajax_referer('nf_forms_postcode_api', 'security', false)) {
            die();
        }

        $key = Ninja_Forms()->get_setting('nf_api_key');
        $secret = Ninja_Forms()->get_setting('nf_api_secret');

        extract($_POST);

        // Clean postcode to 9999XX format
        $postcode = preg_replace('/[^a-zA-Z0-9]/', '', esc_attr($postcode));

        $url = 'https://api.postcode.nl/rest/addresses/' . urlencode($postcode) . '/' . urlencode(
                esc_attr($house_number)
            ) . '/' . urlencode(esc_attr($house_number_suffix));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);
        $jsonResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Translate errors
        $errors = array(
            'Combination does not exist.' => __('Combination does not exist.', 'nf-forms-postcode-api'),
            'Specified postcode is too short.' => __('Specified postcode is too short.', 'nf-forms-postcode-api'),
            'Housenumber must contain numbers only.' => __('Housenumber must contain numbers only.', 'nf-forms-postcode-api'),
            'Postcode does not use format `1234AB`.' => __('Postcode does not use format `1234AB`.', 'nf-forms-postcode-api'),
        );

        foreach ($errors as $string => $translation) {
            $jsonResponse = str_replace($string, $translation, $jsonResponse);
        }

        echo $jsonResponse;
        die();
    }
}
