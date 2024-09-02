<?php
/**
 * Public site functionality of plugin
 */

class Todo_List_Public
{
    private $version = '1.0.0';


    public function is_user_logged_in()
    {
        return is_user_logged_in();
    }

    public function get_logged_in_user_id()
    {
        return get_current_user_id();
    }


    //including css file
    public function enqueue_styles()
    {
        wp_enqueue_style('todo-list-public', plugin_dir_url(__FILE__) . 'css/public.css', array(), $this->version, 'all');
    }


    //including js file
    public function enqueue_scripts()
    {
        wp_enqueue_script('todo-list-public', plugin_dir_url(__FILE__) . 'js/public.js', array('jquery'), $this->version, false);

        //used to pass data from PHP to JavaScript files. 
        wp_localize_script(
            'todo-list-public',
            'todolist_vars',
            array(
                'ajax_url' => admin_url('admin-ajax.php'), //URL to handle ajex request
                'login_url' => site_url('/login'),
                'register_url' => site_url('/register'),
                'todo_list_url' => site_url('/todo-list'),
                'register_nonce' => wp_create_nonce('register_action'),
                'login_nonce' => wp_create_nonce('login_action'),
                'add_todo_nonce' => wp_create_nonce('add_todo_action'),
                'success_messages' => array(
                    'registration_success' => __('Registration successful. Redirecting to login page...', 'todo-list'),
                    'login_success' => __('Login successful. Redirecting to todo list page...', 'todo-list')
                ),
                'error_messages' => array(
                    'general_error' => __('An unexpected error occurred. Please try again.', 'todo-list'),
                    'email_exists' => __('Email already exists. Redirecting to login page...', 'todo-list'),
                    'registration_error' => __('An error occurred during the registration process. Please try again.', 'todo-list'),
                    'login_error' => __('An error occurred during the login process. Please try again.', 'todo-list'),
                    'add_task_error' => __('An error occurred while adding the task. Please try again.', 'todo-list'),
                    'fetch_tasks_error' => __('An error occurred while fetching the tasks. Please try again.', 'todo-list'),
                    'update_task_error' => __('An error occurred while updating the task. Please try again.', 'todo-list'),
                    'missing_task_id' => __('Task ID is missing. Please try again.', 'todo-list'),
                    'user_not_registered' => __('User not registered. Redirecting to register page...', 'todo-list')
                ),
                'status_labels' => array(
                    'pending' => __('Pending', 'todo-list'),
                    'in_progress' => __('In Progress', 'todo-list'),
                    'completed' => __('Completed', 'todo-list')
                ),
                'button_labels' => array(
                    'update' => __('Update', 'todo-list')
                ),
                'no_tasks_message' => __('No tasks added', 'todo-list')
            )
        );
    }


    //handle registration form submission
    public function todo_list_handle_registration()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'register') {
            if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'register_action')) {
                echo json_encode(array('success' => false, 'errors' => array('nonce' => __('Nonce verification failed', 'todo-list'))));
                wp_die();
            }
            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            $confpassword = $_POST['confpassword'];

            $errors = array();

            // Input validation
            if (empty($name)) {
                $errors['name'] = __('Name is required', 'todo-list');
            }

            if (!is_email($email)) {
                $errors['email'] = __('Please enter a valid email', 'todo-list');
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}:;<>,.?~\\/-])[A-Za-z\d!@#$%^&*()_+{}:;<>,.?~\\/-]{8,}$/', $password)) {
                $errors['password'] = __('Password must be at least 8 characters long and contain at least one capital letter, one special character, and one number.', 'todo-list');
            }

            if ($password !== $confpassword) {
                $errors['confpassword'] = __('Passwords do not match', 'todo-list');
            }

            // Check if there are no validation errors
            if (empty($errors)) {
                // Check if the email already exists
                if (email_exists($email)) {
                    $errors['email'] = __('Email already exists', 'todo-list');
                } else {
                    // Register the user
                    $user_id = wp_create_user($email, $password, $email);

                    if (is_wp_error($user_id)) {
                        $errors['db'] = $user_id->get_error_message();
                    } else {
                        // Update the user's display name
                        wp_update_user(
                            array(
                                'ID' => $user_id,
                                'display_name' => $name
                            )
                        );

                        // Successfully registered
                        echo json_encode(array('success' => true));
                        wp_die();
                    }
                }
            }

            // Return errors if validation failed or user exists
            echo json_encode(array('success' => false, 'errors' => $errors));
            wp_die();
        }
    }


    /**
     * Function to handle login functionality
     */
    public function todo_list_handle_login()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'login') {
            if (!isset($_POST['login_nonce']) || !wp_verify_nonce($_POST['login_nonce'], 'login_action')) {
                echo json_encode(array('success' => false, 'errors' => array('nonce' => __('Nonce verification failed', 'todo-list'))));
                wp_die();
            }
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];

            $errors = array();

            if (!is_email($email)) {
                $errors['email'] = __('Please enter a valid email', 'todo-list');
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}:;<>,.?~\\/-])[A-Za-z\d!@#$%^&*()_+{}:;<>,.?~\\/-]{8,}$/', $password)) {
                $errors['password'] = __('Password must be at least 8 characters long and contain at least one capital letter, one special character, and one number.', 'todo-list');
            }

            if (empty($errors)) {
                $user = wp_signon(
                    array(
                        'user_login' => $email,
                        'user_password' => $password,
                        'remember' => true
                    )
                );

                if (is_wp_error($user)) {
                    $errors['email'] = __('User not registered or incorrect password', 'todo-list');
                } else {
                    // Respond with success
                    echo json_encode(array('success' => true));
                    wp_die();
                }
            }

            // Return errors if validation failed
            echo json_encode(array('success' => false, 'errors' => $errors));
            wp_die();
        }
    }


    /**
     * Handle adding a task
     */
    public function todo_list_handle_add_todo()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'add_todo') {
            if (!isset($_POST['add_todo_nonce']) || !wp_verify_nonce($_POST['add_todo_nonce'], 'add_todo_action')) {
                echo json_encode(array('success' => false, 'errors' => array('nonce' => __('Nonce verification failed', 'todo-list'))));
                wp_die();
            }
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => __('You must be logged in to add a task', 'todo-list'))));
                wp_die();
            }

            $task = sanitize_text_field($_POST['task']);
            $user_id = $this->get_logged_in_user_id();

            if (empty($task)) {
                echo json_encode(array('success' => false, 'errors' => array('task' => __('Task is required', 'todo-list'))));
                wp_die();
            }

            // Store task in user meta
            $tasks = get_user_meta($user_id, 'todo_tasks', true);
            if (!$tasks) {
                $tasks = array();
            }

            // Use user ID as part of the task ID
            $task_id = $user_id . '-' . uniqid();
            $tasks[] = array('id' => $task_id, 'task' => $task, 'status' => 'pending');

            update_user_meta($user_id, 'todo_tasks', $tasks);

            echo json_encode(array('success' => true));
            wp_die();
        }
    }


    /**
     * Fetch tasks for the logged-in user
     */
    public function todo_list_fetch_tasks()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'fetch_tasks') {
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => __('You must be logged in to fetch tasks', 'todo-list'))));
                wp_die();
            }

            $user_id = $this->get_logged_in_user_id();
            $tasks = get_user_meta($user_id, 'todo_tasks', true);

            if (!$tasks) {
                $tasks = array();
            }

            echo json_encode(array('success' => true, 'tasks' => $tasks));
            wp_die();
        }
    }


    /**
     * Handle updating a task
     */
    public function todo_list_handle_update_todo()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'update_todo') {
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => __('You must be logged in to update a task', 'todo-list'))));
                wp_die();
            }

            $task_id = sanitize_text_field($_POST['task_id']);
            $status = sanitize_text_field($_POST['status']);
            $user_id = $this->get_logged_in_user_id();

            // Fetch existing tasks
            $tasks = get_user_meta($user_id, 'todo_tasks', true);
            if (!$tasks) {
                $tasks = array();
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
                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('success' => false, 'errors' => array('task' => __('Task not found', 'todo-list'))));
            }
            wp_die();
        }
    }

    /**
     * Function for pending tasks reminder through email
     */

    function todo_list_send_task_reminder_email()
    {
        $users = get_users(); // Get all registered users

        foreach ($users as $user) {
            $user_id = $user->ID;
            $tasks = get_user_meta($user_id, 'todo_tasks', true);

            if ($tasks) {
                $pending_tasks = array_filter($tasks, function ($task) {
                    return $task['status'] === 'pending';
                });

                if (!empty($pending_tasks)) {
                    // Create email content
                    $task_list = "";
                    foreach ($pending_tasks as $task) {
                        $task_list .= "- " . esc_html($task['task']) . "\n";
                    }

                    $subject = __('Your Pending Tasks Reminder', 'todo-list');
                    $message = sprintf(
                        __("Hello %s,\n\nHere are your pending tasks:\n\n%s\n\nBest regards,\nYour To-Do List Team", 'todo-list'),
                        $user->display_name,
                        $task_list
                    );

                    // Send the email
                    wp_mail($user->user_email, $subject, $message);
                }
            }
        }
    }
}