jQuery(document).ready(function ($) {
  $("#register").on("submit", function (e) {
    e.preventDefault();

    //console.log("Registration form submitted");

    $.ajax({
      type: "POST",
      url: wpforms_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        //console.log("Registration response:", response);
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Failed to parse response JSON:", response);
          alert("An unexpected error occurred. Please try again.");
          return;
        }

        $(".input-group__error-message").text(""); // Clear previous errors

        if (response.success) {
          alert("Registration successful. Redirecting to login page...");
          window.location.href = wpforms_vars.login_url;
        } else {
          if (
            response.errors.email &&
            response.errors.email.includes("Email already exists")
          ) {
            alert("Email already exists. Redirecting to login page...");
            window.location.href = wpforms_vars.login_url;
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
        alert(
          "An error occurred during the registration process. Please try again."
        );
      },
    });
  });

  //for login
  $("#login").on("submit", function (e) {
    e.preventDefault();

    //console.log("Login form submitted");

    $.ajax({
      type: "POST",
      url: wpforms_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        //console.log("Login response:", response);
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Failed to parse response JSON:", response);
          alert("An unexpected error occurred. Please try again.");
          return;
        }

        if (response.success) {
          alert("Login successful. Redirecting to todo list page...");
          window.location.href = wpforms_vars.todo_list_url;
        } else {
          // Display errors under respective fields
          for (let key in response.errors) {
            $("#" + key + "-error").text(response.errors[key]);
          }

          if (
            response.errors.email &&
            response.errors.email.includes("User not registered")
          ) {
            alert("User not registered. Redirecting to register page...");
            window.location.href = wpforms_vars.register_url;
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        alert("An error occurred during the login process. Please try again.");
      },
    });
  });

  // Handle task addition
  $("#todo-form").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: wpforms_vars.ajax_url,
      data: $(this).serialize(),
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          alert("An unexpected error occurred. Please try again.");
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
        alert("An error occurred while adding the task. Please try again.");
      },
    });
  });

  // Fetch tasks for the logged-in user
  function fetchTasks() {
    $.ajax({
      type: "POST",
      url: wpforms_vars.ajax_url,
      data: { action: "fetch_tasks" },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          alert("An unexpected error occurred. Please try again.");
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
                            }>Pending</option>
                            <option value="in progress" ${
                              task.status === "in progress" ? "selected" : ""
                            }>In Progress</option>
                            <option value="completed" ${
                              task.status === "completed" ? "selected" : ""
                            }>Completed</option>
                          </select>
                          <button class="todo-update-button">Update</button>
                        </div>
                      </div>
                    `;
              taskList.append(taskHtml);
            });
          } else {
            taskList.append("<p>No tasks added</p>");
          }
        }
      },
      error: function (xhr, status, error) {
        alert("An error occurred while fetching the tasks. Please try again.");
      },
    });
  }

  // Handle task update
  $("#todo-list").on("click", ".todo-update-button", function () {
    const taskItem = $(this).closest(".todo-item");
    const taskId = taskItem.data("task-id");
    const status = taskItem.find(".todo-status").val();

    $.ajax({
      type: "POST",
      url: wpforms_vars.ajax_url,
      data: {
        action: "update_todo",
        task_id: taskId,
        status: status,
      },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          alert("An unexpected error occurred. Please try again.");
          return;
        }

        if (response.success) {
          fetchTasks(); // Refresh the task list
        } else {
          alert("An error occurred while updating the task. Please try again.");
        }
      },
      error: function (xhr, status, error) {
        alert("An error occurred while updating the task. Please try again.");
      },
    });
  });

  // Fetch tasks on page load
  fetchTasks();
});
