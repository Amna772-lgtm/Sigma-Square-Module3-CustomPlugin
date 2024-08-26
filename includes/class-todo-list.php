<?php
class Todo_List
{
    protected $plugin_name;
    protected $version;

    public function __construct()
    {
        $this->plugin_name = 'todo-list';
        $this->version = '1.0.0';

        // Load dependencies
        $this->load_dependencies();

        // Register admin and plugin hooks
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    private function load_dependencies()
    {
        // Include all files
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-todo-list-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-todo-list-public.php';
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new Todo_List_Admin();

        add_action('show_user_profile', array($plugin_admin, 'todo_list_display_user'));
        add_action('edit_user_profile', array($plugin_admin, 'todo_list_display_user'));
    }

    private function define_public_hooks()
    {
        $plugin_public = new Todo_List_Public();

        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));

        // Add shortcodes of login, registration, and todo-list forms
        add_shortcode('todo_list_registration_form', array($plugin_public, 'registration_form'));
        add_shortcode('todo_list_login_form', array($plugin_public, 'login_form'));
        add_shortcode('todo_list_form', array($plugin_public, 'todo_list_form'));

        // Register AJAX actions 
        add_action('wp_ajax_register', array($plugin_public, 'todo_list_handle_registration'));
        add_action('wp_ajax_nopriv_register', array($plugin_public, 'todo_list_handle_registration'));

        // Login AJAX actions
        add_action('wp_ajax_login', array($plugin_public, 'todo_list_handle_login'));
        add_action('wp_ajax_nopriv_login', array($plugin_public, 'todo_list_handle_login'));

        // Add Todo list AJAX actions
        add_action('wp_ajax_add_todo', array($plugin_public, 'todo_list_handle_add_todo'));

        // Fetch Todo list AJAX actions
        add_action('wp_ajax_fetch_tasks', array($plugin_public, 'todo_list_fetch_tasks'));

        // Update Todo list AJAX actions
        add_action('wp_ajax_update_todo', array($plugin_public, 'todo_list_handle_update_todo'));

        //rest api endpoint
        add_action('rest_api_init', array($plugin_public, 'todo_list_register_api_endpoint'));

    }
    
    public function run()
    {
        // Run the plugin
    }
}
