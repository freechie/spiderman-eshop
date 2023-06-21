<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include your database connection file
include_once 'config.php';

$db_connection = new mysqli($host, $user, $password, $database);

// Check the connection
if ($db_connection->connect_error) {
    die("Connection failed: " . $db_connection->connect_error);
}


// Store email and password from the submitted form
$email = $_POST['email'];
$password = $_POST['password'];

// Sanitize email and password
$email = mysqli_real_escape_string($db_connection, $email);
$password = mysqli_real_escape_string($db_connection, $password);

// Prepare and execute the query to get the user information
$query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($db_connection, $query);

// Check if a user with the given email exists
if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    // Verify the submitted password with the stored hashed password
    if (password_verify($password, $user['password'])) {

        // Store user information in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];



        // Redirect based on user type
        if ($user['user_type'] == "user") {
            header("Location: user_dashboard.php");
            exit();
        } else if ($user['user_type'] == "driver") { #perhaps needs changing to modify ecommerce users/admin/etc
            header("Location: driver_dashboard.php");
            exit();
        } else {
            header("Location: admin_dashboard.php");
            exit();
        }
    } else {
        // Password is incorrect
        header("Location: /login.php?error=Invalid email or password.");
        exit();
    }
} else {
    // Email doesn't exist
    header("Location: /login.php?error=Invalid email or password.");
    exit();
}

// Close the database connection
mysqli_close($db_connection);
?>