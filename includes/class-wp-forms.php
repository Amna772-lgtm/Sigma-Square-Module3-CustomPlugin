<?php
/*
 * Plugin Name:       WP Forms
 * Plugin URI:        https://http://module3.local/wp-admin/plugins/wp-forms.php
 * Description:       Custom plugin containing forms of login and registration functionalities 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Amna
 * Author URI:        https://amna.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       wp-forms
 * Domain Path:       /languages
 */


class Wp_Forms
{

    protected $plugin_name;
    protected $version;

    public function __construct()
    {

        $this->plugin_name = 'wp-forms';
        $this->version = '1.0.0';

        //load dependencies
        $this->load_dependencies();

        //register admin and plugin hooks
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }


    private function load_dependencies()
    {
        //include all files
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-forms-activator.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-forms-deactivator.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-forms-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-forms-public.php';
    }


    /**
     * Register all hooks related to admin area functionality 
     * of plugin
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Wp_Forms_Admin();
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
    }

    /**
     * Register all hooks related to public side functionality 
     * of plugin
     */
    private function define_public_hooks()
    {
        $plugin_public = new Wp_Forms_Public();

        //for session
        add_action('init', array($plugin_public, 'start_session'), 1);

        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));

        // Add shortcodes of login, registration and todo-list forms
        add_shortcode('wp_forms_registration', array($plugin_public, 'registration_form'));
        add_shortcode('wp_forms_login', array($plugin_public, 'login_form'));
        add_shortcode('wp_forms_todo_list', array($plugin_public, 'todo_list_form'));

        // Register AJAX actions 
        add_action('wp_ajax_register', array($plugin_public, 'handle_registration'));
        add_action('wp_ajax_nopriv_register', array($plugin_public, 'handle_registration'));

        //Login AJAX actions
        add_action('wp_ajax_login', array($plugin_public, 'handle_login'));
        add_action('wp_ajax_nopriv_login', array($plugin_public, 'handle_login'));

        //Add Todo list AJAX actions
        add_action('wp_ajax_add_todo', array($plugin_public, 'handle_add_todo'));

        //Fetch Todo list AJAX actions
        add_action('wp_ajax_fetch_tasks', array($plugin_public, 'fetch_tasks'));

        //Update Todo list AJAX actions
        add_action('wp_ajax_update_todo', array($plugin_public, 'handle_update_todo'));

    }


    public function run()
    {
        //run the plugin
    }
}


// Register activation hook
register_activation_hook(__FILE__, array('Wp_Forms_Activator', 'activate'));

// Register deactivation hook
register_deactivation_hook(__FILE__, array('Wp_Forms_Deactivator', 'deactivate_plugin'));
