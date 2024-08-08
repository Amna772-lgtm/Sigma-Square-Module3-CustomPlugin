<?php
/**
 * Execute during plugin activation
 */

class Wp_Forms_Activator
{
    public static function activate()
    {
        self::wp_forms_create_table();
    }

    /**
     * Create custom tables for storing data of registered users and their tasks
     */
    private static function wp_forms_create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for registered users
        $table_name_users = $wpdb->prefix . 'register_users';
        $sql_users = "CREATE TABLE $table_name_users (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL UNIQUE,
            password varchar(255) NOT NULL,
            reg_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Table for todo list
        $table_name_todos = $wpdb->prefix . 'todo_list';
        $sql_todos = "CREATE TABLE $table_name_todos (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            task varchar(255) NOT NULL,
            status varchar(50) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES $table_name_users(id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_users);
        dbDelta($sql_todos);
    }
}
