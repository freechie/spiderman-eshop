<!DOCTYPE html>
<html>
<head>
    <title>Employee View of the SpiderMan Multiverse Shop</title>
    <style>
   	 .top-right {
   		 position: absolute;
   		 top: 10px;
   		 right: 10px;
   	 }
    </style>
</head>
<body>
    <h1>Employee View of the SpiderMan Multiverse Shop</h1>

    <?php
    // Database credentials
    $servername = "REDACTED";
    $database = "REDACTED";
    $username = "REDACTED";
    $password = "REDACTED";

    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

    // Fetch transactions
    $transactionQuery = $conn->query("SELECT * FROM ORDER_HISTORY");
    $transactions = $transactionQuery->fetchAll(PDO::FETCH_ASSOC);

    // Display transactions
    if ($transactionQuery->rowCount() > 0) {
   	 echo "<h2>Transactions</h2>";
   	 echo "<table>";
   	 echo "<tr><th>Client ID</th><th>ID</th><th>Order Date</th><th>Product Name</th><th>Product Price</th></tr>";
   	 foreach ($transactions as $transaction) {
   		 echo "<tr>";
   		 echo "<td>" . $transaction['CLIENT_ID'] . "</td>";
   		 echo "<td>" . $transaction['ID'] . "</td>";
   		 echo "<td>" . $transaction['ORDER_DATE'] . "</td>";
   		 echo "<td>" . $transaction['PRODUCT_NAME'] . "</td>";
   		 echo "<td>" . $transaction['PRODUCT_PRICE'] . "</td>";
   		 echo "</tr>";
   	 }
   	 echo "</table>";
    } else {
   	 echo "<p>No transactions found.</p>";
    }

    // Fetch client information
    $clientQuery = $conn->query("SELECT CLIENT_ID, FirstName, LastName, Sex, username FROM CLIENT");
    $clients = $clientQuery->fetchAll(PDO::FETCH_ASSOC);

    // Display client information
    if ($clientQuery->rowCount() > 0) {
   	 echo "<h2>Clients</h2>";
   	 echo "<table>";
   	 echo "<tr><th>Client ID</th><th>First Name</th><th>Last Name</th><th>Sex</th><th>Username</th></tr>";
   	 foreach ($clients as $client) {
   		 echo "<tr>";
   		 echo "<td>" . $client['CLIENT_ID'] . "</td>";
   		 echo "<td>" . $client['FirstName'] . "</td>";
   		 echo "<td>" . $client['LastName'] . "</td>";
   		 echo "<td>" . $client['Sex'] . "</td>";
   		 echo "<td>" . $client['username'] . "</td>";
   		 echo "</tr>";
   	 }
   	 echo "</table>";
    } else {
   	 echo "<p>No clients found.</p>";
    }

    // Fetch top 3 items sold
    $topItemsQuery = $conn->query("SELECT PRODUCT_NAME, COUNT(*) as Quantity FROM ORDER_HISTORY GROUP BY PRODUCT_NAME ORDER BY Quantity DESC LIMIT 3");
    $topItems = $topItemsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Display top 3 items sold
    if ($topItemsQuery->rowCount() > 0) {
   	 echo "<h2>Top 3 Items Sold</h2>";
   	 echo "<table>";
   	 echo "<tr><th>Product Name</th><th>Quantity Sold</th></tr>";
   	 foreach ($topItems as $item) {
   		 echo "<tr>";
   		 echo "<td>" . $item['PRODUCT_NAME'] . "</td>";
   		 echo "<td>" . $item['Quantity'] . "</td>";
   		 echo "</tr>";
   	 }
   	 echo "</table>";
    } else {
   	 echo "<p>No top items found.</p>";
    }

    // Calculate total revenue
    $totalRevenueQuery = $conn->query("SELECT SUM(PRODUCT_PRICE) as TotalRevenue FROM ORDER_HISTORY");
    $totalRevenue = $totalRevenueQuery->fetch(PDO::FETCH_ASSOC)['TotalRevenue'];

    // Display total revenue
    echo "<div class='top-right'>";
    echo "<h2>Total Revenue</h2>";
    echo "<p>$" . number_format($totalRevenue, 2) . "</p>";

    // Log Out button
    echo "<form action='loginCheck.php' method='post'>";
    echo "<input type='submit' value='Log Out'>";
    echo "</form>";
    echo "</div>";

    // Close the database connection
    $conn = null;
    ?>
</body>
</html>






