<?php
session_start();

// Check if the cart session variable is not set, initialize it as an empty array
if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = array();
}

// Check if the user session variable is set
if (isset($_SESSION['username'])) {
	// Retrieve username from the session
	$username = $_SESSION['username'];
} else {
	// User information not found, display an error message
	echo "User information not found. Please try again later.";
	exit();
}

// Check if the submit_order form is submitted
if (isset($_POST['submit_order'])) {
	// Retrieve the cart items from the session
	$cart_items = $_SESSION['cart'];
	require_once __DIR__ . '/config.php';
	$conn = $db_connection;

	// Prepare and execute the query to insert the order data into the database
	$orderDate = date('Y-m-d'); // Get the current date

	$values = array();
	foreach ($cart_items as $item) {
    	$productName = mysqli_real_escape_string($conn, $item['name']);
    	$productPrice = $item['price'];

    	// Add the current item to the values array
    	$values[] = "('$username', '$productName', $productPrice, '$orderDate')";
	}

	// Join the values with commas to create a single multi-row insert statement
	$valuesString = implode(',', $values);

	$sql = "INSERT INTO ORDER_HISTORY (CLIENT_ID, PRODUCT_NAME, PRODUCT_PRICE, ORDER_DATE)
        	VALUES $valuesString";

	if (mysqli_query($conn, $sql)) {
    	// Clear the cart after processing the order
    	$_SESSION['cart'] = array();

    	// Display the order details and success message
    	echo "<h2>Order submitted successfully!</h2>";
    	echo "<p>Order Details:</p>";
    	echo "<ul>";
    	echo "<li>Username: $username</li>";
    	foreach ($cart_items as $item) {
        	echo "<li>Product Name: " . $item['name'] . "</li>";
        	echo "<li>Product Price: " . $item['price'] . "</li>";
        	echo "<li>Order Date: $orderDate</li>";
    	}
    	echo "</ul>";
	} else {
    	// Display an error message if the query execution fails
    	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	// Close the database connection
	mysqli_close($conn);
} else {
	// If the submit_order form is not submitted, redirect back to the product page
	header("Location: clientProductPage.PHP");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Submit Order</title>
</head>
<body>
	<a href="clientProductPage.PHP">Go back to Product Page</a>
	<!-- Add any additional HTML or styling as needed -->
</body>
</html>