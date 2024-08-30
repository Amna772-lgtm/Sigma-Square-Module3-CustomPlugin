<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_CLI')) {
    return;
}

class WP_CLI_Todo_Command
{

    /**
     * Custom CLI Command to add tasks by specifying userid
     */
    public function add_task($args, $assoc_args)
    {
        $task = sanitize_text_field($args[0]);

        if (empty($task)) {
            WP_CLI::error(__('Task is required', 'todo-list'));
        }

        // Get the logged-in user ID (you might need to change this based on your context)
        $user_id = get_current_user_id();

        if (!$user_id) {
            WP_CLI::error(__('You must be logged in to add a task', 'todo-list'));
        }

        // Store task in user meta
        $tasks = get_user_meta($user_id, 'todo_tasks', true);
        if (!$tasks) {
            $tasks = array();
        }

        // Generate a unique task ID
        $task_id = $user_id . '-' . uniqid();
        $tasks[] = array('id' => $task_id, 'task' => $task, 'status' => 'pending');

        update_user_meta($user_id, 'todo_tasks', $tasks);

        WP_CLI::success(__('Task added successfully', 'todo-list'));
    }

    /**
     * Custom CLI command to fetch tasks on basis of userid
     */
    public function fetch_tasks($args, $assoc_args)
    {
        // Get the user ID from the arguments
        $user_id = isset($args[0]) ? intval($args[0]) : get_current_user_id();

        if (!$user_id) {
            WP_CLI::error(__('You must provide a user ID or be logged in to fetch tasks', 'todo-list'));
        }

        // Get user data
        $user_info = get_userdata($user_id);

        if (!$user_info) {
            WP_CLI::error(__('User not found', 'todo-list'));
        }

        // Get tasks
        $tasks = get_user_meta($user_id, 'todo_tasks', true);
        if (!is_array($tasks)) {
            $tasks = [];
        }

        // Prepare the response
        $response = [
            'user_name' => $user_info->display_name,
            'tasks'     => $tasks
        ];

        // Print the response in JSON format
        WP_CLI::line(json_encode($response, JSON_PRETTY_PRINT));
    }

    /**
     * Custom CLI command to update a task status
     */

    public function update_task($args, $assoc_args)
    {
        // Check required arguments
        if (empty($args[0]) || empty($assoc_args['status'])) {
            WP_CLI::error(__('Task ID and status are required', 'todo-list'));
        }

        $task_id = sanitize_text_field($args[0]);
        $status = sanitize_text_field($assoc_args['status']);

        // Get the logged-in user ID (you might need to change this based on your context)
        $user_id = get_current_user_id();

        if (!$user_id) {
            WP_CLI::error(__('You must be logged in to update a task', 'todo-list'));
        }

        // Fetch existing tasks
        $tasks = get_user_meta($user_id, 'todo_tasks', true);
        if (!is_array($tasks)) {
            WP_CLI::error(__('No tasks found', 'todo-list'));
        }

        // Find and update the task
        $task_found = false;
        foreach ($tasks as &$task) {
            if (isset($task['id']) && $task['id'] === $task_id) {
                $task['status'] = $status;
                $task_found = true;
                break;
            }
        }

        if ($task_found) {
            // Update the user meta with the updated tasks
            update_user_meta($user_id, 'todo_tasks', $tasks);
            WP_CLI::success(__('Task status updated successfully', 'todo-list'));
        } else {
            WP_CLI::error(__('Task not found', 'todo-list'));
        }
    }
}

