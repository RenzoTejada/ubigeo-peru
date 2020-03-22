<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://renzotejada.com/
 * @since      2.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
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
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 * @author     Renzo Tejada <info@renzotejada.com>
 */
class Ubigeo_Peru {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Ubigeo_Peru_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $ubigeo_peru    The string used to uniquely identify this plugin.
	 */
	protected $ubigeo_peru;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
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
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'UBIGEO_PERU_VERSION' ) ) {
			$this->version = UBIGEO_PERU_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->ubigeo_peru = 'ubigeo-peru';

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
	 * - Ubigeo_Peru_Loader. Orchestrates the hooks of the plugin.
	 * - Ubigeo_Peru_i18n. Defines internationalization functionality.
	 * - Ubigeo_Peru_Admin. Defines all hooks for the admin area.
	 * - Ubigeo_Peru_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ubigeo-peru-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ubigeo-peru-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ubigeo-peru-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ubigeo-peru-public.php';

		$this->loader = new Ubigeo_Peru_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ubigeo_Peru_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ubigeo_Peru_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ubigeo_Peru_Admin( $this->get_ubigeo_peru(), $this->get_version() );
                $this->loader->add_action( 'admin_menu', $plugin_admin, 'ubigeo_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ubigeo_Peru_Public( $this->get_ubigeo_peru(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_after_checkout_form', $plugin_public, 'add_checkout_script' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'pluginname_ajaxurl' );
		$this->loader->add_action( 'wp_ajax_get_states_call', $plugin_public, 'get_states_call' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_states_call', $plugin_public, 'get_states_call' );
		$this->loader->add_action( 'wp_ajax_get_cities_call', $plugin_public, 'get_cities_call' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_cities_call', $plugin_public, 'get_cities_call' );
		
                $this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'rt_select_field_deptoCode' );
                $this->loader->add_action( 'woocommerce_after_checkout_shipping_form', $plugin_public, 'rt_select_field_shippingDeptoCode' );
                $this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'rt_select_field_billing_city' );
                $this->loader->add_action( 'woocommerce_after_checkout_shipping_form', $plugin_public, 'rt_select_field_shipping_city' );
                $this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'rt_select_field_distCode' );
                $this->loader->add_action( 'woocommerce_after_checkout_shipping_form', $plugin_public, 'rt_select_field_shippingDistCode' );
                $this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'save_ubigeo_peru' );
                $this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'check_if_selected' );
                $this->loader->add_action( 'woocommerce_admin_order_data_after_billing_address', $plugin_public, 'show_custom_fields_order_billing' );
                $this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address', $plugin_public, 'show_custom_fields_order_shipping' );
                $this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'show_custom_fields_thankyou', 20 );
                $this->loader->add_action( 'woocommerce_view_order', $plugin_public, 'show_custom_fields_thankyou', 20 );
                $this->loader->add_action( 'woocommerce_email_order_meta_fields', $plugin_public, 'show_custom_fields_emails', 10 , 3 );
		
                
                $this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'rt_remove_fields', 9999 );
                $this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'rearrange_checkout_fields' );
                $this->loader->add_filter( 'woocommerce_default_address_fields', $plugin_public, 'rename_city', 9999 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run()
        {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_ubigeo_peru() {
		return $this->ubigeo_peru;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Ubigeo_Peru_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
