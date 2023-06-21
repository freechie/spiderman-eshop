<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Include your config.php file to use the existing database connection
include 'config.php';

// Check if the user is logged in and has the correct user_type
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "user") {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Spiderman: No Way Home Merch Store Dashboard</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <h1>Welcome to the Spiderman: No Way Home Merch Store! <?php echo $_SESSION["username"]; ?>!</h1>
        <br>

        <form action="dashboard.php" method="post">
            <input type="text" name="search" placeholder="Search for merchandise">
            <input type="submit" value="Search">
        </form>
        <br><br>
        
        <?php
            // Include your database config file
            include 'config.php';

            // Create SQL query based on whether user has submitted a search
            $sql = "SELECT * FROM items";
            if (!empty($_POST['search'])) {
                $search = $db_connection->real_escape_string($_POST['search']);
                $sql .= " WHERE item_name LIKE '%$search%'";
            }

            $result = $db_connection->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<div class='item'>";
                    echo "<h2>" . htmlspecialchars($row["item_name"]) . "</h2>";
                    echo "<p>Category: " . htmlspecialchars($row["category"]) . "</p>";
                    echo "<p>Price: $" . htmlspecialchars($row["price"]) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "No results found";
            }
            $db_connection->close();
        ?>
    </div>
</body>
</html>
