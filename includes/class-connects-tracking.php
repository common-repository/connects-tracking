<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.connects.ch
 * @since      1.0.0
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/includes
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
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/includes
 * @author     Marc Dätwyler <marc.daetwyler@connects.ch>
 */
class Connects_Tracking
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Connects_Tracking_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

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
    public function __construct()
    {
        if (defined('CONNECTS_TRACKING_VERSION')) {
            $this->version = CONNECTS_TRACKING_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'connects-tracking';

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
     * - Connects_Tracking_Loader. Orchestrates the hooks of the plugin.
     * - Connects_Tracking_i18n. Defines internationalization functionality.
     * - Connects_Tracking_Admin. Defines all hooks for the admin area.
     * - Connects_Tracking_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-connects-tracking-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-connects-tracking-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-connects-tracking-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-connects-tracking-public.php';

        /**
         * Load Redux Config
         */

        // require_once (plugin_dir_path( dirname( __FILE__ ) ) . '/includes/redux-config.php');

        $this->loader = new Connects_Tracking_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Connects_Tracking_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Connects_Tracking_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Connects_Tracking_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'create_plugin_settings_page');
        $this->loader->add_action('admin_menu', $plugin_admin, 'setup_sections');
        $this->loader->add_action('admin_menu', $plugin_admin, 'setup_fields');

        $this->loader->add_filter('plugin_action_links_' . CONNECTS_PLUGIN_BASENAME, $plugin_admin, 'add_action_links');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Connects_Tracking_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_head', $plugin_public, 'add_connects_profiling');
        $this->loader->add_action('woocommerce_thankyou', $plugin_public, 'add_connects_conversion');

        $this->loader->add_action('init', $plugin_public, 'create_first_party_cookie');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Connects_Tracking_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the value of the options_connects_profiling option.
     *
     * @since     1.1.2
     * @return    boolean    The value of the options_connects_profiling option.
     */
    public static function get_is_profiling_enabled()
    {
        return get_option('options_connects_profiling');
    }

       /**
     * Retrieve the value of the options_connects_conversion_tracking option.
     *
     * @since     1.1.2
     * @return    boolean    The value of the options_connects_conversion_tracking option.
     */
    public static function get_is_conversion_tracking_enabled()
    {
        return get_option('options_connects_conversion_tracking');
    }

    /**
     * Check if Woocommerce Plugin is active
     *
     * @since     1.1.2
     * @return    boolean    Is Woocommerce Plugin active.
     */
    public static function is_woocommerce_active(){
        return class_exists('WooCommerce') ? true : false;
    }
    

}
