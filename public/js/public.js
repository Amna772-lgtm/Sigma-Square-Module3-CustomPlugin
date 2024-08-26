function showMessage(type, message) {
  const messageBox = document.getElementById('message-box');
  messageBox.classList.remove("success", "error"); // Remove classes
  messageBox.classList.add(type); // Add the appropriate class
  messageBox.textContent = message; // Set the text content
  messageBox.style.display = 'block'; // Show the message box

  setTimeout(function () {
    messageBox.style.display = 'none'; // Hide the message box after 2 seconds
  }, 2000); // Adjust time as needed
}


jQuery(document).ready(function ($) {
  $("#register").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: todolist_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Failed to parse response JSON:", response);
          showMessage("error", todolist_vars.error_messages.general_error);
          return;
        }

        $(".input-group__error-message").text(""); // Clear previous errors

        if (response.success) {
          showMessage(
            "success",
            todolist_vars.success_messages.registration_success
          );
          setTimeout(function () {
            window.location.href = todolist_vars.login_url;
          }, 2000);
        } else {
          if (
            response.errors.email &&
            response.errors.email.includes(
              todolist_vars.error_messages.email_exists
            )
          ) {
            showMessage("error", todolist_vars.error_messages.email_exists);
            setTimeout(function () {
              window.location.href = todolist_vars.login_url;
            }, 2000);
          }

          // Display errors under respective fields
          for (let key in response.errors) {
            $("#" + key + "-error").text(response.errors[key]);
          }
        }
      },
      //xhr object represents the AJAX request and contains information about the request
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        showMessage("error", todolist_vars.error_messages.registration_error);
      },
    });
  });

  //for login
  $("#login").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: todolist_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Failed to parse response JSON:", response);
          showMessage("error", todolist_vars.error_messages.general_error);
          return;
        }

        if (response.success) {
          showMessage("success", todolist_vars.success_messages.login_success);
          setTimeout(function () {
            window.location.href = todolist_vars.todo_list_url;
          }, 2000);
        } else {
          // Display errors under respective fields
          for (let key in response.errors) {
            $("#" + key + "-error").text(response.errors[key]);
          }

          if (
            response.errors.email &&
            response.errors.email.includes(
              todolist_vars.error_messages.user_not_registered
            )
          ) {
            showMessage(
              "error",
              todolist_vars.error_messages.user_not_registered
            );
            setTimeout(function () {
              window.location.href = todolist_vars.register_url;
            }, 2000);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        showMessage("error", todolist_vars.error_messages.login_error);
      },
    });
  });

  // Handle task addition
  $("#todo-form").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: todolist_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          showMessage("error", todolist_vars.error_messages.general_error);
          return;
        }

        if (response.success) {
          fetchTasks(); // Refresh the task list
          $("#task").val(""); // Clear the task input field
        } else {
          if (response.errors.task) {
            $("#task-error").text(response.errors.task);
          }
        }
      },
      error: function (xhr, status, error) {
        showMessage('error', todolist_vars.error_messages.add_task_error);
      },
    });
  });

  // Fetch tasks for the logged-in user
  function fetchTasks() {
    $.ajax({
      type: "POST",
      url: todolist_vars.ajax_url,
      data: { action: "fetch_tasks" },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          showMessage("error", todolist_vars.error_messages.general_error);
          return;
        }

        if (response.success) {
          const tasks = response.tasks;
          const taskList = $("#todo-list");

          taskList.empty();

          if (tasks.length > 0) {
            tasks.forEach(function (task) {
              const taskHtml = `
                      <div class="todo-item" data-task-id="${task.id}">
                        <input type="checkbox" class="todo-checkbox" ${
                          task.status === "completed" ? "checked" : ""
                        }>
                        <span class="todo-text ${
                          task.status === "completed" ? "todo-completed" : ""
                        }">${task.task}</span>
                        <div class="todo-actions">
                          <select class="todo-status">
                            <option value="pending" ${
                              task.status === "pending" ? "selected" : ""
                            }>${todolist_vars.status_labels.pending}</option>
                            <option value="in progress" ${
                              task.status === "in progress" ? "selected" : ""
                            }>${
                todolist_vars.status_labels.in_progress
              }</option>
                            <option value="completed" ${
                              task.status === "completed" ? "selected" : ""
                            }>${todolist_vars.status_labels.completed}</option>
                          </select>
                          <button class="todo-update-button">${
                            todolist_vars.button_labels.update
                          }</button>
                        </div>
                      </div>
                    `;
              taskList.append(taskHtml);
            });
          } else {
            taskList.append(todolist_vars.no_tasks_message);
          }
        }
      },
      error: function (xhr, status, error) {
        //alert(todolist_vars.error_messages.fetch_tasks_error);
      },
    });
  }

  // Handle task update
  $("#todo-list").on("click", ".todo-update-button", function () {
    const taskItem = $(this).closest(".todo-item");
    const taskId = taskItem.data("task-id");

    if (taskId === undefined || taskId === null) {
      //console.error("Task ID is undefined or null");
      //alert(todolist_vars.error_messages.missing_task_id);
      //return;
    }

    const status = taskItem.find(".todo-status").val();

    $.ajax({
      type: "POST",
      url: todolist_vars.ajax_url,
      data: {
        action: "update_todo",
        task_id: taskId,
        status: status,
      },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          showMessage("error", todolist_vars.error_messages.general_error);
          return;
        }

        if (response.success) {
          fetchTasks(); // Refresh the task list
        } else {
          showMessage('error', todolist_vars.error_messages.update_task_error);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        showMessage('error', todolist_vars.error_messages.update_task_error);
      },
    });
  });

  // Fetch tasks on page load
  fetchTasks();
});
