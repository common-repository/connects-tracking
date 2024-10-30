<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.connects.ch
 * @since      1.0.0
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/admin
 * @author     Marc Dätwyler <marc.daetwyler@connects.ch>
 */
class Connects_Tracking_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The settings options
     *
     * @since    1.1.0
     * @access   private
     * @var      array    $version    The current values of the settings options
     */
    private $settings_options;

    private $settings_page_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings_page_name = 'connects-tracking-settings';

        $this->settings_options = get_option('connects_options');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Connects_Tracking_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Connects_Tracking_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/connects-tracking-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Connects_Tracking_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Connects_Tracking_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/connects-tracking-admin.js', array('jquery'), $this->version, false);
    }

    public function create_plugin_settings_page()
    {
        add_submenu_page(
            'options-general.php',
            'Connects Tracking Settings',
            'Connects Tracking Settings',
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'plugin_settings_page_content')
        );
    }

    public function plugin_settings_page_content()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-display.php';
    }

    public function setup_sections()
    {

        if (function_exists('add_settings_section')) {
            add_settings_section(
                'setting_section_connects', // ID
                'Settings', // Title
                array($this, 'print_section_info'), // Callback
                $this->settings_page_name // Page
            );
        }
    }

    public function setup_fields()
    {
        $fields = array(
            array(
                'uid' => 'options_connects_id',
                'label' => 'Connects ID',
                'section' => 'setting_section_connects',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'Your Connects ID here',
                'helper' => '',
                'supplemental' => "Please insert your Connects ID. If you don't know your Connects ID or if you're not a Connects Customer yet, please contact us https://www.connects.ch",
                'default' => '',
                'class' => 'connects-field-wrapper'
            ),
            array(
                'uid' => 'options_connects_ocategory',
                'label' => 'Provision-Category (ocategory)',
                'section' => 'setting_section_connects',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'Default: Sales',
                'helper' => '',
                'supplemental' => "Please insert the your provisioncategory name here. If the field is empty we use the default value 'Sales'.",
                'default' => '',
                'class' => 'connects-field-wrapper'
            ),
            array(
                'uid' => 'options_connects_conversion_tracking',
                'label' => 'Conversion Tracking',
                'section' => 'setting_section_connects',
                'type' => 'checkbox',
                'options' => true,
                'placeholder' => '',
                'helper' => '',
                'supplemental' => 'When activated the Connects Conversion Tracking will fire on the Checkout success page.',
                'default' => '',
                'class' => 'connects-field-wrapper'
            ),
            array(
                'uid' => 'options_connects_profiling',
                'label' => 'Profiling',
                'section' => 'setting_section_connects',
                'type' => 'checkbox',
                'options' => false,
                'placeholder' => '',
                'helper' => '',
                'supplemental' => 'When activated the Connects Profiling Tags will fire on the Category-, Product and Cart-Page.',
                'default' => '',
                'class' => 'connects-field-wrapper'
            )
        );


        foreach ($fields as $field) {
            add_settings_field($field['uid'], $field['label'], array($this, 'field_callback'),  $this->settings_page_name, $field['section'], $field);
            register_setting('connects_option_group', $field['uid'], [$this, 'sanitize' . '_' . $field['type']]);
        }
    }


    public function print_section_info($arguments)
    {
    }

    public function field_callback($arguments)
    {

        $value = get_option($arguments['uid']); // Get the current value, if there is one
        if (!$value) { // If no value exists
            $value = $arguments['default']; // Set to our default
        }

        // Check which type of field we want
        switch ($arguments['type']) {
            case 'text': // If it is a text field
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
                break;
            case 'checkbox':
                printf('<input type="checkbox" id="%1$s" name="%1$s" value="1" ' . checked(1, $value, false) . ' /><label for="%1$s">Toggle</label>', $arguments['uid']);
                break;
        }

        // If there is help text
        if ($helper = $arguments['helper']) {
            printf('<span class="helper"> %s</span>', $helper); // Show it
        }

        // If there is supplemental text
        if ($supplimental = $arguments['supplemental']) {
            printf('<p class="description">%s</p>', $supplimental); // Show it
        }
    }

    public function sanitize_text($input)
    {
        return sanitize_text_field($input);
    }

    public function sanitize_checkbox($input)
    {
        return (bool) $input;
    }

    public function add_action_links($links)
    {
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '-settings">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($links, $settings_link);
    }
}
