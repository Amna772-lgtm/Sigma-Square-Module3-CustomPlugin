<?php
/*
 * Plugin Name:       WP Custom Forms
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


//if this php file is accessed directly using url, stop the execution.
 if(! defined ( 'WPINC' )) {
    die;
 }


 //define constants
 define ( 'WP_FORMS_VERSION', '1.0.0' );


/**
*code runs during plugin activation
*/
 function activate_wp_forms() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-forms-activator.php';
    Wp_Forms_Activator::activate();
 }


 
/**
*code runs during plugin deactivation
*/
function deactivate_wp_forms() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-forms-deactivator.php';
    Wp_Forms_Deactivator::deactivate();
 }


 register_activation_hook(__FILE__, 'activate_wp_forms');
 register_deactivation_hook(__FILE__, 'deactivate_wp_forms');


 /**
  * Core plugin class used to define internationalization, 
  * admin specific hooks and public site hooks
  */
  require_once plugin_dir_path(__FILE__) . 'includes/class-wp-forms.php';


  /**
   * execution of plugin starts
   */
  function run_wp_forms() {
    $plugin = new Wp_Forms();
    $plugin->run();
  }
  run_wp_forms();