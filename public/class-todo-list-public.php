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
                'todo_list_url' => site_url('/todo-list')
            )
        );
    }

    //registration form
    public function registration_form()
    {

        //start output buffering, PHP will store all output in an internal buffer instead of sending it directly to the browser
        ob_start();

        ?>
        <div class="container">
            <div class="container__form-container" id="register-form">
                <h2 class="form-container__title">Register User</h2>
                <form method="POST" id="register">
                    <input type="hidden" name="action" value="register">
                    <div class="form-container__input-group">
                        <label for="register-name" class="input-group__label">Name</label>
                        <input type="text" class="input-group__input" id="name" name="name" placeholder="Enter your name"
                            required>
                        <span id="name-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-email" class="input-group__label">Email</label>
                        <input type="email" class="input-group__input" id="email" name="email" placeholder="abc@gmail.com"
                            required>
                        <span id="email-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-password" class="input-group__label">Password</label>
                        <input type="password" class="input-group__input" id="password" name="password"
                            placeholder="Enter your password" required>
                        <span id="password-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="register-confpassword" class="input-group__label">Confirm Password</label>
                        <input type="password" class="input-group__input" id="confpassword" name="confpassword"
                            placeholder="Enter password again" required>
                        <span id="confpassword-error" class="input-group__error-message"></span>
                    </div>
                    <button type="submit" class="form-container__button">Register</button>
                    <p class="register-link">Already have an account? <a href="<?php echo site_url('/login'); ?>"
                            class="register-link__a">Login</a></p>
                </form>
            </div>
        </div>

        <?php
        //return the buffer contents and clears the buffer.
        return ob_get_clean();
    }


    //handle registration form submission
    public function handle_registration()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'register') {
            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            $confpassword = $_POST['confpassword'];

            $errors = array();

            // Input validation
            if (empty($name)) {
                $errors['name'] = "Name is required";
            }

            if (!is_email($email)) {
                $errors['email'] = "Please enter a valid email";
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}:;<>,.?~\\/-])[A-Za-z\d!@#$%^&*()_+{}:;<>,.?~\\/-]{8,}$/', $password)) {
                $errors['password'] = "Password must be at least 8 characters long and contain at least one capital letter, one special character, and one number.";
            }

            if ($password !== $confpassword) {
                $errors['confpassword'] = "Passwords do not match";
            }

            // Check if there are no validation errors
            if (empty($errors)) {
                // Check if the email already exists
                if (email_exists($email)) {
                    $errors['email'] = "Email already exists";
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

        ob_start();

        ?>

        <div class="container">
            <div class="container__form-container" id="login-form">
                <h2 class="form-container__title">Login</h2>
                <form method="POST" id="login">
                    <input type="hidden" name="action" value="login">
                    <div class="form-container__input-group">
                        <label for="email" class="input-group__label">Email</label>
                        <input type="email" class="input-group__input" id="email" name="email" placeholder="abc@gmail.com"
                            required>
                        <span id="email-error" class="input-group__error-message"></span>
                    </div>
                    <div class="form-container__input-group">
                        <label for="password" class="input-group__label">Password</label>
                        <input type="password" class="input-group__input" id="login-password" name="password"
                            placeholder="Enter your password" required>
                        <span id="password-error" class="input-group__error-message"></span>
                    </div>
                    <button type="submit" class="form-container__button">Login</button>
                    <p class="register-link">Don't have an account? <a href="<?php echo site_url('/register'); ?>"
                            class="register-link__a">Register</a>
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
    public function handle_login()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'login') {
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];

            $errors = array();

            if (!is_email($email)) {
                $errors['email'] = "Please enter a valid email";
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}:;<>,.?~\\/-])[A-Za-z\d!@#$%^&*()_+{}:;<>,.?~\\/-]{8,}$/', $password)) {
                $errors['password'] = "Password must be at least 8 characters long and contain at least one capital letter, one special character, and one number.";
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
                    $errors['email'] = "User not registered or incorrect password";
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
        ob_start();

        // Fetch the logged-in user's name
        $user_id = $this->get_logged_in_user_id();
        $user_info = get_userdata($user_id);
        $user_name = $user_info->display_name;
        ?>

        <div class="todo-container">
            <h1 class="todo-title"><?php echo $user_name; ?>'s Task List</h1>
            <form id="todo-form" class="todo-form">
                <input type="hidden" name="action" value="add_todo">
                <div class="todo-input-group">
                    <input type="text" class="todo-input" id="task" name="task" placeholder="Enter your task" required>
                    <button type="submit" class="todo-button">Add Task</button>
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
    public function handle_add_todo()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'add_todo') {
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => 'You must be logged in to add a task')));
                wp_die();
            }

            $task = sanitize_text_field($_POST['task']);
            $user_id = $this->get_logged_in_user_id();

            if (empty($task)) {
                echo json_encode(array('success' => false, 'errors' => array('task' => 'Task is required')));
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
    public function fetch_tasks()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'fetch_tasks') {
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => 'You must be logged in to fetch tasks')));
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
    public function handle_update_todo()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'update_todo') {
            if (!$this->is_user_logged_in()) {
                echo json_encode(array('success' => false, 'errors' => array('login' => 'You must be logged in to update a task')));
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
                echo json_encode(array('success' => false, 'errors' => array('task' => 'Task not found')));
            }
            wp_die();
        }
    }


}

