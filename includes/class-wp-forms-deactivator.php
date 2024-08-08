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

      $sql_users = "DROP TABLE IF EXISTS $table_name_users;";
      $sql_todos = "DROP TABLE IF EXISTS $table_name_todos;";

      $wpdb->query($sql_users);
      $wpdb->query($sql_todos);
   }
}
