<?php
session_start();

// Check if the user session variable is set
if (isset($_SESSION['username'])) {
	// Retrieve the username from the session
	$username = $_SESSION['username'];

	// Connect to the database
	$servername = "REDACTED";
	$db_username = "REDACTED";
	$password = "REDACTED";
	$database = "REDACTED";

	$conn = mysqli_connect($servername, $db_username, $password, $database);

	// Check connection
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// Prepare and execute the query to retrieve the order history for the client
	$sql = "SELECT * FROM ORDER_HISTORY WHERE CLIENT_ID = '$username'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
    	// Display the order history for the client
    	echo "<h2>Order History for $username</h2>";
    	echo "<table>";
    	echo "<tr><th>Order ID</th><th>Product Name</th><th>Product Price</th><th>Order Date</th></tr>";

    	while ($row = mysqli_fetch_assoc($result)) {
        	$orderId = $row['ORDER_ID'];
        	$productName = $row['PRODUCT_NAME'];
        	$productPrice = $row['PRODUCT_PRICE'];
        	$orderDate = $row['ORDER_DATE'];

        	echo "<tr><td>$orderId</td><td>$productName</td><td>$productPrice</td><td>$orderDate</td></tr>";
    	}

    	echo "</table>";
	} else {
    	echo "No order history found for $username.";
	}

	// Close the database connection
	mysqli_close($conn);
} else {
	// User information not found, display an error message
	echo "User information not found. Please try again later.";
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Order History</title>
</head>
<body>
	<a href="clientProductPage.PHP">Go back to Product Page</a>
	<!-- Add any additional HTML or styling as needed -->
</body>
</html>