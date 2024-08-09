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
 if(! defined ( 'WPINC' )) {
    die;
 }


 //define constants
 define ( 'TODO_LIST_VERSION', '1.0.0' );


/**
*code runs during plugin activation
*/
 function activate_todo_list() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-todo-list-activator.php';
    Todo_List_Activator::activate();
 }


 
/**
*code runs during plugin deactivation
*/
function deactivate_todo_list() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-todo-list-deactivator.php';
    Todo_List_Deactivator::deactivate();
 }


 register_activation_hook(__FILE__, 'activate_todo_list');
 register_deactivation_hook(__FILE__, 'deactivate_todo_list');


 /**
  * Core plugin class used to define internationalization, 
  * admin specific hooks and public site hooks
  */
  require_once plugin_dir_path(__FILE__) . 'includes/class-todo-list.php';


  /**
   * execution of plugin starts
   */
  function run_todo_list() {
    $plugin = new Todo_List();
    $plugin->run();
  }
  run_todo_list();