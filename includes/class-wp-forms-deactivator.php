<?php
/**
 * execute during plugin deactivation
 */

class Wp_Forms_Deactivator
{
   public static function deactivate()
   {
      self::drop_custom_tables();
   }

   /**
    * Drop custom tables for book details and to-do list.
    */
    private static function drop_custom_tables()
    {
        global $wpdb;
    
        $table_name_users = $wpdb->prefix . 'register_users';
        $table_name_todos = $wpdb->prefix . 'todo_list';
    
        // Drop foreign key constraint
        $drop_foreign_key_sql = "ALTER TABLE $table_name_todos DROP FOREIGN KEY wp_todo_list_ibfk_1;";
        $wpdb->query($drop_foreign_key_sql);
    
        // Now drop the tables
        $sql_todos = "DROP TABLE IF EXISTS $table_name_todos;";
        $wpdb->query($sql_todos);
    
        $sql_users = "DROP TABLE IF EXISTS $table_name_users;";
        $wpdb->query($sql_users);
    }
    
}
