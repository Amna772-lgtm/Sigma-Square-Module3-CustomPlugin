# Custom To-Do List Plugin

A comprehensive WordPress plugin for managing personal tasks and to-do lists with user registration and login functionalities.

## Motivation

As an intern at a software house, I was assigned a task to develop a custom WordPress plugin as part of my training in plugin development. This project provided an opportunity to dive deep into WordPress's extensive plugin architecture, enhance my skills in PHP, JavaScript, and MySQL, and contribute to a real-world application. The goal was to create a functional and user-friendly to-do list plugin that allows users to manage tasks efficiently, with the added challenge of implementing secure user registration and login features. This experience not only strengthened my technical capabilities but also gave me valuable insights into the development process within a professional environment.

## Code Style

This plugin follows standard WordPress coding practices to ensure readability, maintainability, and compatibility with other WordPress plugins and themes. Key aspects of the code style include:

- **PHP Coding Standards:** All PHP code is written following the [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/). This includes proper indentation, naming conventions, and documentation.

- **HTML & CSS:** The HTML structure adheres to semantic HTML5 standards, and CSS is organized using a modular approach. CSS naming follows the [BEM (Block, Element, Modifier)](http://getbem.com/introduction/) methodology where appropriate.

- **JavaScript:** JavaScript is written following the [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/). jQuery is used for DOM manipulation, and care is taken to avoid conflicts with other plugins.

- **Security Best Practices:** The plugin code is written with security in mind, including proper data sanitization, validation, and escaping to prevent vulnerabilities such as SQL injection and XSS.

- **Version Control:** The project is maintained under version control using Git, with clear commit messages and branching strategies to ensure a clean development process.

## Video Tutorials

[Todo List Video](https://drive.google.com/file/d/1P1bVNXa4UbQ7JGPU_gidMEkb483_lrnA/view?usp=drive_link)

[Rest API Video](https://drive.google.com/file/d/1P1bVNXa4UbQ7JGPU_gidMEkb483_lrnA/view?usp=drive_link)

[WP CLI Custom Commands Video](https://drive.google.com/file/d/1shjpVzCGhTTG0X0zGVZVM-zCt1eeG3Pc/view?usp=drive_link)

## Tech/Framework Used

This Custom To-Do List Plugin leverages several technologies and frameworks to ensure robust functionality and seamless integration with WordPress. The key technologies used in this project include:

- **WordPress:** The plugin is built for WordPress, utilizing its rich set of APIs and hooks to integrate with the WordPress ecosystem.

- **PHP:** The server-side scripting language used to create the core functionalities of the plugin. It adheres to WordPress PHP Coding Standards for consistency and readability.

- **JavaScript:** Employed to enhance user interactions and manage dynamic content on the client side. JavaScript is used in combination with jQuery to provide a responsive and interactive user experience.

- **jQuery:** A JavaScript library that simplifies HTML document traversal, event handling, and animation, used to streamline DOM manipulations and AJAX requests.

- **AJAX:** Asynchronous JavaScript and XML (AJAX) is used for dynamic content updates and interactions without requiring a full page reload, providing a smoother and more interactive user experience.

- **REST API:** The WordPress REST API is utilized to enable communication between the front-end and back-end, facilitating data retrieval and updates in a structured manner.

- **HTML5:** Used for structuring the plugin’s user interface, ensuring semantic and accessible HTML markup.

- **CSS3:** Applied for styling the plugin’s front-end interface, using modern CSS techniques for a clean and user-friendly design.

- **WordPress Shortcodes API:** Allows for embedding plugin functionality into posts, pages, or widgets via shortcodes.

## Features

### User Registration and Login
- **Registration Form:** Users can register with their name, email, and password. Duplicate users are detected, and appropriate messages are displayed.
- **Login Form:** Users can log in with their credentials. Successful login redirects to the To-Do List page.

### To-Do List Management
- **Add Tasks:** Create new tasks with titles.
- **Mark Tasks as Complete:** Update task status to completed.

### User-Specific Task Management
- **Personalized Lists:** Each user has their own to-do list.
- **Individual Task Views:** Ensure privacy with user-specific tasks.

### Admin Features
- **User Management:** Admins can view and manage all users and their tasks.
- **Task Overview:** Admins get a comprehensive view of tasks across the platform.

### Nonce Verification
- **Security Measures:** Utilizes nonce verification to secure form submissions and AJAX requests.

### AJAX Integration
- **Real-Time Updates:** AJAX enables task operations without page reloads.
- **Seamless User Experience:** Provides instant feedback and improved interaction.

### WP-CLI Commands

This plugin includes custom WP-CLI commands to manage tasks from the command line interface.

#### Available Commands

##### `wp todo add_task`

- **Description:** Adds a new task to the specified user's to-do list. If no user ID is provided, the task will be added to the currently logged-in user's list.
- **Usage:** `wp todo add_task "Task Description" [--user=<user_id>]`
- **Example:**
  ```bash
  wp todo add_task "Complete project report" --user=1

##### `wp todo fetch_task`

- **Description:** Fetches and displays the to-do tasks for the specified user. If no user ID is provided, it fetches tasks for the currently logged-in user.
- **Usage:** `wp todo fetch_tasks [<user_id>]`
- **Example:**
  ```bash
  wp todo fetch_tasks --user=1


##### `wp todo update_task`

- **Description:** Updates the status of a specified task for the currently logged-in user.
- **Usage:** `wp todo update_task <task_id> --status=<status> [<user_id>]`
- **Example:**
  ```bash
  wp todo update_task 1-66d15d7036b5f --status=completed --user=1

#### Requirements

- **WP-CLI:** Ensure WP-CLI is installed on your system. You can find installation instructions and download WP-CLI from [wp-cli.org](https://wp-cli.org/).

#### Usage

1. **Open your terminal or command line interface.**
2. **Navigate to the WordPress root directory.** 
3. **Run the desired WP CLI commands.**


## Installation

1. Download the plugin ZIP file.
2. In your WordPress admin panel, go to **Plugins > Add New**.
3. Click **Upload Plugin** and choose the downloaded ZIP file.
4. Click **Install Now** and then **Activate**.

## Contributing

Feel free to contribute to this project by submitting issues or pull requests. Please follow the code style guidelines and ensure all changes are well-documented.

## Contact

For any questions or feedback, please contact Amna Rani at [amnarani338@gmail.com].