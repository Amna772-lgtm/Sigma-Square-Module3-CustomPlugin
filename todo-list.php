<?php
/*
 * Plugin Name:       Todo List
 * Plugin URI:        https://http://module3.local/wp-admin/plugins/todo-list.php
 * Description:       Custom plugin perform todo list functionality
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Amna
 * Author URI:        https://amna.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       todo-list
 * Domain Path:       /languages
 */


//if this php file is accessed directly using url, stop the execution.
if (!defined('WPINC')) {
  die;
}


//define constants
define('TODO_LIST_VERSION', '1.0.0');


/**
 * Core plugin class used to define internationalization, 
 * admin specific hooks and public site hooks
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-todo-list.php';


//include WP CLI command file
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcli.php';

// Register the WP-CLI command
if ( defined( 'WP_CLI' ) && WP_CLI ) {
  WP_CLI::add_command( 'todo', 'WP_CLI_Todo_Command' );
}


/**
 * execution of plugin starts
 */
function run_todo_list()
{
  $plugin = new Todo_List();
  $plugin->run();
}
run_todo_list();



//include rest api endpoints
include_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-api.php';


// Register REST API routes
function register_rest_api_routes() {
  $rest_api = new Rest_API();
  $rest_api->todo_list_register_api_endpoint();
  $rest_api->todo_list_register_task_id_api_endpoint();
  $rest_api->todo_list_register_check_task_api_endpoint();
}
add_action('rest_api_init', 'register_rest_api_routes');


// Activation hook to schedule the cron event
register_activation_hook(__FILE__, 'todo_list_schedule_task_reminder');


function todo_list_schedule_task_reminder() {
  if (!wp_next_scheduled('todo_list_task_reminder')) {
      wp_schedule_event(time(), 'daily', 'todo_list_task_reminder');
  }
}


// Deactivation hook to clear the scheduled cron event
register_deactivation_hook(__FILE__, 'todo_list_clear_task_reminder');

function todo_list_clear_task_reminder() {
    wp_clear_scheduled_hook('todo_list_task_reminder');
}


