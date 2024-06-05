<?php
session_start();

// Check if the user session variable is set
if (!isset($_SESSION['username'])) {
	// User information not found, display an error message
	echo "User information not found. Please try again later.";
	exit();
}

$employee_username = $_POST['employee_user'];
$employee_password = $_POST['employee_password'];
$client_username = $_POST['client_user'];
$client_password = $_POST['client_password'];

$connection = new mysqli("REDACTED", "REDACTED", "REDACTED", "REDACTED");
if ($connection->connect_error) {
	die("Unable to connect to the database " . $connection->connect_error);
}

$sql = "SELECT * FROM PRODUCT";

if (isset($_GET['filter'])) {
	$category_id = $_GET['filter'];
	if ($category_id != 'all') {
    	$sql .= " WHERE Category_ID = '$category_id'";
	}
}

$result = mysqli_query($connection, $sql);

if (!$result) {
	die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Spiderman Movie Store</title>
	<style>
    	/* Add your custom styles here */
	</style>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
      	integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
      	crossorigin="anonymous">
</head>
<body>
<h1>Welcome to the Spiderman Movie Store!</h1>

<!-- Add the button to go to orderHistory.php -->
<a href="orderHistory.php" style="position: absolute; top: 10px; right: 10px;">Order History</a>

<!-- Add the logout button -->
<form action="logout.php" method="POST">
	<input type="submit" name="logout" value="Logout">
</form>

<h2>Available Products</h2>
<form action="AddtoCart.php" method="POST">
	<table>
    	<tr>
        	<th>Product Name</th>
        	<th>Product Price</th>
        	<th>Quantity</th>
        	<th>Stock</th>
    	</tr>
    	<?php
    	while ($row = mysqli_fetch_assoc($result)) {
        	$product_id = $row['Product_ID'];
        	$product_name = $row['Product_Name'];
        	$product_price = $row['Product_Price'];
        	$product_stock = $row['Product_Stock'];
        	?>
        	<tr>
            	<td><?php echo $product_name; ?></td>
            	<td><?php echo "$" . $product_price; ?></td>
            	<td>
                	<input type="hidden" name="product_id[]" value="<?php echo $product_id; ?>">
                	<input type="hidden" name="product_name[]" value="<?php echo $product_name; ?>">
                	<input type="hidden" name="product_price[]" value="<?php echo $product_price; ?>">
                	<input type="number" name="quantity[]" value="0" min="0">
            	</td>
            	<td><?php echo $product_stock; ?></td>
        	</tr>
        	<?php
    	}
    	?>
	</table>

	<input type="submit" name="add_to_cart" value="Add to Cart">
</form>

<!-- Add a View Cart button -->
<form action="AddtoCart.php" method="GET">
	<input type="hidden" name="view_cart" value="1">
	<input type="submit" value="View Cart">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    	integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

</body>
</html>