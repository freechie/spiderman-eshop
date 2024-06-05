<?php
session_start();

// Check if the cart session variable is not set, initialize it as an empty array
if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = array();
}

// Check if the add_to_cart form is submitted
if (isset($_POST['add_to_cart'])) {
	// Retrieve the product information from the form
	$product_ids = $_POST['product_id'];
	$product_names = $_POST['product_name'];
	$product_prices = $_POST['product_price'];
	$quantities = $_POST['quantity'];

	// Loop through the submitted products
	for ($i = 0; $i < count($product_ids); $i++) {
    	$product_id = $product_ids[$i];
    	$product_name = $product_names[$i];
    	$product_price = $product_prices[$i];
    	$quantity = $quantities[$i];

    	// Check if the quantity is greater than 0
    	if ($quantity > 0) {
        	// Create an associative array to represent the product
        	$product = array(
            	'id' => $product_id,
            	'name' => $product_name,
            	'price' => $product_price,
            	'quantity' => $quantity
        	);

        	// Add the product to the cart session
        	$_SESSION['cart'][] = $product;
    	}
	}

	// Store the cart data in the database
	if (isset($_SESSION['user_id'])) {
    	$userId = $_SESSION['user_id'];
    	$cartData = serialize($_SESSION['cart']);
    	// Replace "client" with the actual table name for clients
    	$query = "UPDATE client SET cart_data = '$cartData' WHERE client_ID = $userId";
    	// Execute the query to update the cart_data column in the CLIENT table
    	
	}
}

// Clear the cart if the clear_cart form is submitted
if (isset($_POST['clear_cart'])) {
	$_SESSION['cart'] = array();

	// Clear the cart data in the database
	if (isset($_SESSION['user_id'])) {
    	$userId = $_SESSION['user_id'];
    	// Replace "client" with the actual table name for clients
    	$query = "UPDATE client SET cart_data = '' WHERE client_ID = $userId";
    	// Execute the query to clear the cart_data column in the CLIENT table
    	// Code to execute the query...
	}
}

// Calculate the total of all items in the cart
$total = 0;
foreach ($_SESSION['cart'] as $item) {
	$product_price = $item['price'];
	$quantity = $item['quantity'];
	$subtotal = $product_price * $quantity;
	$total += $subtotal;
}

// Display the cart contents and the total
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
	echo "<h1>Cart Items:</h1>";

	foreach ($_SESSION['cart'] as $item) {
    	$product_name = $item['name'];
    	$product_price = $item['price'];
    	$quantity = $item['quantity'];

    	echo "<p>$product_name - $$product_price - Quantity: $quantity</p>";
	}

	echo "<h2>Total: $$total</h2>";

	// Add a form to clear the cart
	echo "<form action='' method='POST'>";
	echo "<input type='submit' name='clear_cart' value='Clear Cart'>";
	echo "</form>";

	// Add a form to submit the order
	echo "<form action='SubmitOrder.php' method='POST'>";
	echo "<input type='submit' name='submit_order' value='Submit Order'>";
	echo "</form>";
} else {
	echo "<h1>Your cart is empty.</h1>";
}

// Add a button to go back to the products page
echo "<a href='clientProductPage.PHP'>Go Back to Products</a>";

// Add a logout button
echo "<form action='logout.php' method='POST'>";
echo "<input type='submit' name='logout' value='Logout'>";
echo "</form>";
?>

<!DOCTYPE html>
<html>
<head>
	<title>Cart</title>
</head>
<body>
	<!-- Add any additional HTML or styling as needed -->
</body>
</html>

`

This next one is logout.php 

`<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Redirect the user to the login page or any other desired page
header("Location: loginCheck.php");
exit();
?>