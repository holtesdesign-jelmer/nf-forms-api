<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.webreact.nl
 * @since      1.0.0
 *
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    nf_forms_postcode_api
 * @subpackage nf_forms_postcode_api/includes
 * @author     Webreact <email@example.com>
 */
class nf_forms_postcode_api {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      nf_forms_postcode_api_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $nf_forms_postcode_api    The string used to uniquely identify this plugin.
	 */
	protected $nf_forms_postcode_api;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->nf_forms_postcode_api = 'nf-forms-postcode-api';
		$this->version = '5.1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - nf_forms_postcode_api_Loader. Orchestrates the hooks of the plugin.
	 * - nf_forms_postcode_api_i18n. Defines internationalization functionality.
	 * - nf_forms_postcode_api_Admin. Defines all hooks for the admin area.
	 * - nf_forms_postcode_api_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nf-forms-postcode-api-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nf-forms-postcode-api-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nf-forms-postcode-api-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nf-forms-postcode-api-public.php';

		$this->loader = new nf_forms_postcode_api_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the nf_forms_postcode_api_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new nf_forms_postcode_api_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new nf_forms_postcode_api_Admin( $this->get_nf_forms_postcode_api(), $this->get_version() );

		$this->loader->add_filter( 'ninja_forms_plugin_settings_groups', $plugin_admin, 'nf_settings_groups', 10, 1 );
        $this->loader->add_action( 'wp_ajax_nf_forms_postcode_api_request', $plugin_admin, 'request_address' );
        $this->loader->add_action( 'wp_ajax_nopriv_nf_forms_postcode_api_request', $plugin_admin, 'request_address' );
        $this->loader->add_filter( 'ninja_forms_check_setting_nf_api_key', $plugin_admin,  'check_nf_key', 10, 1 );
        $this->loader->add_filter( 'ninja_forms_check_setting_nf_api_secret', $plugin_admin,  'check_nf_secret', 10, 1 );
        $this->loader->add_filter( 'ninja_forms_check_setting_nf_webreact_license', $plugin_admin,  'check_nf_license', 10, 1 );
        $this->loader->add_filter( 'ninja_forms_plugin_settings', $plugin_admin, 'plugin_settings', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new nf_forms_postcode_api_Public( $this->get_nf_forms_postcode_api(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 1000 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_nf_forms_postcode_api() {
		return $this->nf_forms_postcode_api;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    nf_forms_postcode_api_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
