<?php
/**
 * Admin specific functionality of plugin
 */

class Todo_List_Admin{

    private $version = '1.0.0';
    public function __construct(){
        //constructor code
    }

    public function enqueue_styles(){
        wp_enqueue_style('todo-list-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts(){
        wp_enqueue_script('todo-list-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, false);
    }
}