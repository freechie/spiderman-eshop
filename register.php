<?php

// Display errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your config.php file to use the existing database connection
include_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the user inputs
    $username = mysqli_real_escape_string($db_connection, $_POST["username"]);
    $email = mysqli_real_escape_string($db_connection, $_POST["email"]);
    $password = mysqli_real_escape_string($db_connection, $_POST["password"]);
    $user_type = mysqli_real_escape_string($db_connection, $_POST["user_type"]); // Get the user type from the form

    // Validate the user inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registration.php?error=Invalid email format");
        exit;
    }

    // Check if a user with the same email or username already exists
    $sql = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $result = $db_connection->query($sql);

    if ($result->num_rows > 0) {
        header("Location: registration.php?error=A user with this email or username already exists");
        exit;
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

        if ($db_connection->query($sql) === TRUE) {
            // Redirect the user to user_dashboard.php
            header("Location: user_dashboard.php");
            exit;
        } else {
            header("Location: registration.php?error=" . urlencode("Error: " . $sql . "<br>" . $db_connection->error));
            exit;
        }
    }
}

// Close the connection
$db_connection->close();