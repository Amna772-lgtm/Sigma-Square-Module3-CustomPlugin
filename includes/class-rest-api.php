<?php
class Rest_API
{

    /**
     * JWT Authentication function for adding task
     * and updating task status
     */
    public function validate_jwt_authentication($request)
    {
        $auth = $request->get_header('Authorization');

        if (empty($auth)) {
            return new WP_Error('rest_forbidden', 'JWT Token is missing.', array('status' => 403));
        }

        $auth = str_replace('Bearer ', '', $auth);
        $user = apply_filters('jwt_auth_token_before_dispatch', null, $auth);

        if (is_wp_error($user)) {
            return $user;
        }

        return true;
    }
    /**
     * Rest API that take user id as parameter and 
     * display list of his/her tasks that he/she added
     */
    public function todo_list_register_api_endpoint()
    {
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_user_todo_list'],
            'args' => [
                'user_id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
            ],
        ]);
    }


    /**
     * Callback function for above API
     */
    public function get_user_todo_list($data)
    {
        // Get the user ID from the URL parameter
        $user_id = $data['user_id'];

        // Get the user data
        $user_info = get_userdata($user_id);

        if ($user_info) {
            // Get the user's display name
            $user_name = $user_info->display_name;

            // Get the user's tasks from the wp_usermeta table
            $user_tasks = get_user_meta($user_id, 'todo_tasks', true);

            // Check if tasks are already an array
            $tasks = is_array($user_tasks) ? $user_tasks : [];

            // Prepare the response
            $response = [
                'user_name' => $user_name,
                'tasks' => $tasks
            ];
        } else {
            // Return an error message if the user doesn't exist
            $response = ['error' => 'User not found'];
        }

        // Return JSON response
        wp_send_json($response);
        exit;
    }


    /**
     * Rest API that takes user_id, task_id, status,
     * updates the task status, and returns true if updated, otherwise false.
     */
    public function todo_list_register_update_task_status_api_endpoint()
    {
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)/task/(?P<task_id>[^/]+)/status/(?P<status>[^/]+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_task_status_by_id'],
            'permission_callback' => [$this, 'validate_jwt_authentication'],
            'args' => [
                'user_id' => [
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'task_id' => [
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return preg_match('/^[\d\w-]+$/', $param);
                    }
                ],
                'status' => [
                    'required' => true,
                ]
            ]
        ]);
    }


    /**
     * Callback function to update task status.
     */
    public function update_task_status_by_id($data)
    {
        // Get the parameters from the URL
        $user_id = $data['user_id'];
        $task_id_param = urldecode($data['task_id']); // Decode the URL-encoded task ID
        $new_status = urldecode($data['status']);     // Decode the URL-encoded new status

        // Get the user's tasks from the wp_usermeta table
        $user_tasks = get_user_meta($user_id, 'todo_tasks', true);

        // Check if tasks are already an array
        $tasks = is_array($user_tasks) ? $user_tasks : [];

        // Flag to check if task is found and updated
        $task_found = false;

        // Search for the task by ID and update its status
        foreach ($tasks as &$task) {
            if (isset($task['id']) && $task['id'] === $task_id_param) {
                $task['status'] = $new_status; // Update the task status
                $task_found = true;
                break;
            }
        }

        if ($task_found) {
            // Update the tasks back into the user meta
            $update_result = update_user_meta($user_id, 'todo_tasks', $tasks);

            // Debugging step: Log whether update_user_meta was successful
            error_log('Update user meta result: ' . ($update_result ? 'Success' : 'Failure'));

            // Return success response
            wp_send_json(['success' => true, 'message' => 'Task status updated successfully']);
        } else {
            // Return error response if task was not found
            wp_send_json(['success' => false, 'error' => 'Task not found']);
        }

        exit;
    }

    public function todo_list_register_add_task_api_endpoint()
    {
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)/add-task', [
            'methods' => 'POST',
            'callback' => [$this, 'add_task_to_user_todo_list'],
            'permission_callback' => [$this, 'validate_jwt_authentication'],
            'args' => [
                'user_id' => [
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'task' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'status' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);
    }

    /**
     * Callback function for adding a task to the user's to-do list
     */
    public function add_task_to_user_todo_list($data)
    {
        // Get the parameters from the request
        $user_id = $data['user_id'];
        $task_name = sanitize_text_field($data['task']);
        $status = sanitize_text_field($data['status']);

        // Get the user's existing tasks from the wp_usermeta table
        $user_tasks = get_user_meta($user_id, 'todo_tasks', true);

        // Check if tasks are already an array, otherwise initialize as an array
        if (!is_array($user_tasks)) {
            $user_tasks = [];
        }

        // Generate a new task ID in the format user_id-uniqid
        $new_task_id = $user_id . '-' . uniqid();

        // Create a new task array
        $new_task = [
            'id' => $new_task_id,
            'task' => $task_name,
            'status' => $status
        ];

        // Add the new task to the user's tasks
        $user_tasks[] = $new_task;

        // Update the user's tasks in the wp_usermeta table
        update_user_meta($user_id, 'todo_tasks', $user_tasks);

        // Return a success response with the new task ID
        $response = [
            'success' => true,
            'task_id' => $new_task_id,
            'message' => 'Task added successfully'
        ];

        // Return JSON response
        wp_send_json($response);
        exit;
    }
}


