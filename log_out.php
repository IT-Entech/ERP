<?php
session_start(); // Starts the session, allowing you to access and modify session variables.
session_destroy(); // Destroys all session data, effectively logging the user out.
header("Location: ./pages-login.html"); // Redirects the user to the login page.
exit(); // It's a good practice to include exit() after a header redirect to stop further execution of the script.
?>
