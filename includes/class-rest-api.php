<?php
class Rest_API
{
    
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
     * Rest API that take userid, task, status as parameter
     * and display respective task_id
     */
    public function todo_list_register_task_id_api_endpoint()
    {
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)/(?P<task>[^/]+)/(?P<status>[^/]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_task_id_by_parameters'],
            'args' => [
                'user_id' => [
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'task' => [
                    'required' => true,
                ],
                'status' => [
                    'required' => true,
                ]
            ]
        ]);
    }


    /**
     * Callback function for above rest api
     */

    public function get_task_id_by_parameters($data)
    {
        // Get the parameters from the URL
        $user_id = $data['user_id'];
        $task_param = urldecode($data['task']); // Decode the URL-encoded task name
        $status_param = urldecode($data['status']); // Decode the URL-encoded status

        // Get the user's tasks from the wp_usermeta table
        $user_tasks = get_user_meta($user_id, 'todo_tasks', true);

        // Check if tasks are already an array
        $tasks = is_array($user_tasks) ? $user_tasks : [];

        // Search for the task with the matching task and status
        $task_id = null;
        foreach ($tasks as $task) {
            if ($task['task'] === $task_param && $task['status'] === $status_param) {
                $task_id = $task['id'];
                break;
            }
        }

        if ($task_id !== null) {
            // Return the task_id in the JSON response
            $response = ['task_id' => $task_id];
        } else {
            // Return an error message if no matching task is found
            $response = ['error' => 'Task not found'];
        }

        // Return JSON response
        wp_send_json($response);
        exit;
    }


    /**
     * Rest API that take user_id, task_id, status
     * and display true if the task found otherwise return false
     * with error task not found
     */
    public function todo_list_register_check_task_api_endpoint()
    {
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)/task/(?P<task_id>[^/]+)/status/(?P<status>[^/]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'check_task_by_id_and_status'],
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
     * Callback function for above API
     */
    public function check_task_by_id_and_status($data)
    {
        // Get the parameters from the URL
        $user_id = $data['user_id'];
        $task_id_param = urldecode($data['task_id']); // Decode the URL-encoded task ID
        $status_param = urldecode($data['status']); // Decode the URL-encoded status
    
        // Get the user's tasks from the wp_usermeta table
        $user_tasks = get_user_meta($user_id, 'todo_tasks', true);
    
        // Check if tasks are already an array
        $tasks = is_array($user_tasks) ? $user_tasks : [];
    
        // Search for the task with the matching task_id and status
        $task_found = false;
        foreach ($tasks as $task) {
            if (isset($task['id']) && $task['id'] === $task_id_param && $task['status'] === $status_param) {
                $task_found = true;
                break;
            }
        }
    
        // Return JSON response based on whether the task was found
        if ($task_found) {
            wp_send_json(['task_found' => true]);
        } else {
            wp_send_json(['task_found' => false, 'error' => 'Task not found']);
        }
        exit;
    }
 
}