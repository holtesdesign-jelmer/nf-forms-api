<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://www.holtesdesign.nl
 * @since      1.0.0
 *
 * @package    NF_Postcode_Api
 * @subpackage NF_Postcode_Api/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    NF_Postcode_Api
 * @subpackage NF_Postcode_Api/includes
 * @author     Holtes Design <email@example.com>
 */
class NF_Postcode_Api_Loader {




    /**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/nf-postcode-api-public.php';

		// Register AJAX function hook
        add_action( 'wp_ajax_nf_postcode_api_request', array($this, 'request_address' ));
        add_action( 'wp_ajax_nopriv_nf_postcode_api_request', array($this, 'request_address' ));

	}


    public function request_address () {
		
		// Check nonce
        if ( !check_ajax_referer( 'nf_postcode_api', 'security', false ) ) {
        	die();
        }
		
		// Define Postcode.nl API credentials
        $key = "huB90MvEPtQPFiviVMgaEAV628QwYOpDTGZLTSg4Dqm";
        $secret = "meJKlgGH8YilTiAqQh5ShtfRZNofjVUAgRwCCk70jI4DNpyBli";

		// Extract POST data from Ninja Forms fields, to validate address data
        extract($_POST);
                
        // Clean postcode to 9999XX format
        $postcode = preg_replace('/[^a-zA-Z0-9]/', '', esc_attr($postcode));

        $url = 'https://api.postcode.nl/rest/addresses/' . urlencode($postcode). '/'. urlencode(esc_attr($house_number)) . '/'. urlencode(esc_attr($house_number_suffix));

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
        );

        foreach ($errors as $string => $translation) {
            $jsonResponse = str_replace($string, $translation, $jsonResponse);
        }

        echo $jsonResponse;

        die();
    }














    /**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

}
