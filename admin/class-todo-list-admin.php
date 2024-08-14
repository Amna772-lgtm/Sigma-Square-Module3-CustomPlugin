<?php
/**
 * Admin specific functionality of plugin
 */

class Todo_List_Admin{

    private $version = '1.0.0';
    public function __construct(){
        //constructor code
    }

    public function todo_list_display_user($user)
    {
        // Fetch user ID
        $user_id = $user->ID;

        // Query to fetch the tasks for the user
        global $wpdb;
        $tasks = get_user_meta($user_id, 'todo_tasks', true);

        echo '<h2>' . __('To-Do List', 'todo-list') . '</h2>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th>' . __('Task', 'todo-list') . '</th>';
        echo '<th>' . __('Status', 'todo-list') . '</th>';
        echo '</tr>';

        if ($tasks && is_array($tasks)) {
            foreach ($tasks as $task) {
                // Extract task details
                $task_name = isset($task['task']) ? $task['task'] : '';
                $task_status = isset($task['status']) ? $task['status'] : '';

                echo '<tr>';
                echo '<td>' . esc_html($task_name) . '</td>'; 
                echo '<td>' . esc_html($task_status) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr>';
            echo '<td colspan="2">' . __('No tasks found', 'todo-list') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }

}