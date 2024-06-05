<?php
session_start();

$employee_username = $_POST['employee_user'];
$employee_password = $_POST['employee_password'];
$client_username = $_POST['client_user'];
$client_password = $_POST['client_password'];

$connection = new mysqli("REDACTED", "REDACTED", "REDACTED", "REDACTED");
if ($connection->connect_error) {
	die("Unable to connect to the database: " . $connection->connect_error);
}

if (isset($_POST["employee_login"])) {
	$check = $connection->prepare("SELECT * FROM EMPLOYEE WHERE username = ?");
	$check->bind_param("s", $employee_username);
	$check->execute();

	$check_result = $check->get_result();
	if ($check_result->num_rows > 0) {
    	$data = $check_result->fetch_assoc();
    	if ($data['password'] === $employee_password) {
        	// Set the session variable for the logged-in user
        	$_SESSION['username'] = $employee_username;

        	header("Location: clientProductPage.PHP");
        	exit();
    	}
	}
	echo "<body style=background-color:Red; border:2px solid White;>
    	<h1 style=background-color:Yellow; border:2px solid Yellow>Username or Password is invalid.<br>Please try again<br>
    	<td style='color:Yellow; text-align:right; font-size: 20px;'><a style=color:red; font-size: 20px; href=loginCheck.php>Click me to go back to the login page</a></td></h1>
    	</body>";
}

if (isset($_POST["client_login"])) {
	$check = $connection->prepare("SELECT * FROM CLIENT WHERE username = ?");
	$check->bind_param("s", $client_username);
	$check->execute();

	$check_result = $check->get_result();
	if ($check_result->num_rows > 0) {
    	$data = $check_result->fetch_assoc();
    	if ($data['password'] === $client_password) {
        	// Set the session variable for the logged-in user
        	$_SESSION['username'] = $client_username;

        	header("Location: clientProductPage.PHP");
        	exit();
    	}
	}
	echo "<body style=background-color:Red; border:2px solid White;>
    	<h1 style=background-color:Yellow; border:2px solid Yellow>Username or Password is invalid.<br>Please try again<br>
    	<td style='color:Yellow; text-align:right; font-size: 20px;'><a style=color:red; font-size: 20px; href=loginCheck.php>Click me to go back to the login page</a></td></h1>
    	</body>";
}

$connection->close();
?>