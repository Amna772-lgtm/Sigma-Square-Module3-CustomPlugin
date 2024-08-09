<?php
/**
 * execute during plugin deactivation
 */

class Todo_List_Deactivator
{
    public static function deactivate()
    {
        self::drop_custom_tables();
    }

    private static function drop_custom_tables()
    {
        global $wpdb;

        $table_name_users = $wpdb->prefix . 'register_users';
        $table_name_todos = $wpdb->prefix . 'todo_list';

        // Just drop the tables directly
        $sql_todos = "DROP TABLE IF EXISTS $table_name_todos;";
        $wpdb->query($sql_todos);

        $sql_users = "DROP TABLE IF EXISTS $table_name_users;";
        $wpdb->query($sql_users);
    }
}


