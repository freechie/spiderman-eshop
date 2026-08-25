<?php

require_once __DIR__ . '/config.php';
$conn = $db_connection;

// Check if the form is submitted
if (isset($_POST['submit'])) {
	// Retrieve the form data
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$password = $_POST['password'];
	$username = $_POST['username'];
	$sex = $_POST['sex'];

	// Create a prepared statement to prevent SQL injection
	$stmt = $conn->prepare("INSERT INTO CLIENT (FirstName, LastName, password, username, sex) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssss", $firstName, $lastName, $password, $username, $sex);

	// Execute the statement
	if ($stmt->execute()) {
    	echo "Registration successful!";
	} else {
    	echo "Error: " . $stmt->error;
	}

	// Close the statement
	$stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Registration Form</title>
</head>
<body>
	<h2>Registration Form</h2>
	<form method="POST" action="">
    	<label for="firstName">First Name:</label>
    	<input type="text" name="firstName" required><br><br>
   	 
    	<label for="lastName">Last Name:</label>
    	<input type="text" name="lastName" required><br><br>
   	 
    	<label for="password">Password:</label>
    	<input type="password" name="password" required><br><br>
   	 
    	<label for="username">Username:</label>
    	<input type="text" name="username" required><br><br>
   	 
    	<label for="sex">Sex:</label>
    	<input type="text" name="sex" required><br><br>
   	 
    	<input type="submit" name="submit" value="Register">
	</form>
</body>
</html>
