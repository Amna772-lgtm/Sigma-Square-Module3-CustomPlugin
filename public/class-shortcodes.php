<?php
class Shortcodes
{
    private $todo_list_public;

    public function __construct($todo_list_public)
    {
        $this->todo_list_public = $todo_list_public;
    }

    //registration form
    public function registration_form()
    {
        if ($this->todo_list_public->is_user_logged_in()) {
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



    /**
     * Login form shortcode
     */
    public function login_form()
    {
        if ($this->todo_list_public->is_user_logged_in()) {
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
     * Function to create a todo list form
     */
    public function todo_list_form()
    {
        if (!$this->todo_list_public->is_user_logged_in()) {
            wp_redirect(site_url('/login'));
            exit;
        }
        ob_start();

        // Fetch the logged-in user's name
        $user_id = $this->todo_list_public->get_logged_in_user_id();
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
}
?>