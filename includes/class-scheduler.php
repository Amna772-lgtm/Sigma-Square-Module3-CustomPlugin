<?php

class Scheduler
{

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