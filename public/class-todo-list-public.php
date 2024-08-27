<?php
/**
 * Public site functionality of plugin
 */

class Todo_List_Public
{
    private $version = '1.0.0';
    public function __construct()
    {

    }


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

    //registration form
    public function registration_form()
    {
        if ($this->is_user_logged_in()) {
            wp_redirect(site_url('/todo-list'));
            exit;
        }
        //start output buffering, PHP will store all output in an internal buffer instead of sending it directly to the browser
        ob_start();

        ?>
        <div class="container">
            <div class="container__form-container" id="register-form">
                <div id="message-box" class="message-box"></div>
                <h2 class="form-container__title"><?php _e('Register User', 'todo-list'); ?></h2>
                <form method="POST" id="register">
                    <?php wp_nonce_field('register_action', 'register_nonce'); ?>
                    <input type="hidden" name="action" value="register">
                    <div class="form-container__input-group">
                        <label for="register-name" class="input-group__label"><?php _e('Name', 'todo-list'); ?></label>
                        <input type="text" class="input-group__input" id="name" name="name"
                            placeholder="<?php _e('Enter your name', 'todo-list'); ?>" required>
                        <span id="name-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-email" class="input-group__label"><?php _e('Email', 'todo-list'); ?></label>
                        <input type="email" class="input-group__input" id="email" name="email"
                            placeholder="<?php _e('abc@gmail.com', 'todo-list'); ?>" required>
                        <span id="email-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-password" class="input-group__label"><?php _e('Password', 'todo-list'); ?></label>
                        <input type="password" class="input-group__input" id="password" name="password"
                            placeholder="<?php _e('Enter your password', 'todo-list'); ?>" required>
                        <span id="password-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-confpassword"
                            class="input-group__label"><?php _e('Confirm Password', 'todo-list'); ?></label>
                        <input type="password" class="input-group__input" id="confpassword" name="confpassword"
                            placeholder="<?php _e('Enter password again', 'todo-list'); ?>" required>
                        <span id="confpassword-error" class="input-group__error-message"></span>
                    </div>
                    <button type="submit" class="form-container__button"><?php _e('Register', 'todo-list'); ?></button>
                    <p class="register-link"><?php _e('Already have an account?', 'todo-list'); ?> <a
                            href="<?php echo site_url('/login'); ?>"
                            class="register-link__a"><?php _e('Login', 'todo-list'); ?></a></p>
                </form>
            </div>
        </div>

        <?php
        //return the buffer contents and clears the buffer.
        return ob_get_clean();
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
     * Login form shortcode
     */
    public function login_form()
    {
        if ($this->is_user_logged_in()) {
            wp_redirect(site_url('/todo-list'));
            exit;
        }

        ob_start();

        ?>

        <div class="container">
            <div class="container__form-container" id="login-form">
                <div id="message-box" class="message-box"></div>
                <h2 class="form-container__title"><?php _e('Login', 'todo-list'); ?></h2>
                <form method="POST" id="login">
                    <?php wp_nonce_field('login_action', 'login_nonce'); ?>
                    <input type="hidden" name="action" value="login">
                    <div class="form-container__input-group">
                        <label for="email" class="input-group__label"><?php _e('Email', 'todo-list'); ?></label>
                        <input type="email" class="input-group__input" id="email" name="email"
                            placeholder="<?php _e('abc@gmail.com', 'todo-list'); ?>" required>
                        <span id="email-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="password" class="input-group__label"><?php _e('Password', 'todo-list'); ?></label>
                        <input type="password" class="input-group__input" id="login-password" name="password"
                            placeholder="<?php _e('Enter your password', 'todo-list'); ?>" required>
                        <span id="password-error" class="input-group__error-message"></span>
                    </div>
                    <button type="submit" class="form-container__button"><?php _e('Login', 'todo-list'); ?></button>
                    <p class="register-link"><?php _e("Don't have an account?", 'todo-list'); ?> <a
                            href="<?php echo site_url('/register'); ?>"
                            class="register-link__a"><?php _e('Register', 'todo-list'); ?></a>
                    </p>
                </form>
            </div>
        </div>

        <?php
        return ob_get_clean();

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
     * Function to create a todo list form
     */
    public function todo_list_form()
    {
        if (!$this->is_user_logged_in()) {
            wp_redirect(site_url('/login'));
            exit;
        }
        ob_start();

        // Fetch the logged-in user's name
        $user_id = $this->get_logged_in_user_id();
        $user_info = get_userdata($user_id);
        $user_name = $user_info->display_name;
        ?>

        <div class="todo-container">
            <div id="message-box" class="message-box"></div>
            <h1 class="todo-title"><?php echo esc_html($user_name); ?><?php _e("'s Task List", 'todo-list'); ?></h1>
            <form id="todo-form" class="todo-form">
                <?php wp_nonce_field('add_todo_action', 'add_todo_nonce'); ?>
                <input type="hidden" name="action" value="add_todo">
                <div class="todo-input-group">
                    <input type="text" class="todo-input" id="task" name="task"
                        placeholder="<?php _e('Enter your task', 'todo-list'); ?>" required>
                    <button type="submit" class="todo-button"><?php _e('Add Task', 'todo-list'); ?></button>
                </div>
                <span id="task-error" class="todo-error-message"></span>
            </form>
            <div id="todo-list" class="todo-list">
                <!-- To-Do items will be appended here -->
            </div>
        </div>
        <?php
        return ob_get_clean();
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


}

