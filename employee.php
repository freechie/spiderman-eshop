<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$user = require_role('employee');
$connection = database();

$transactions = $connection->query(
    'SELECT O.Order_ID, O.Order_Date, O.Total_Amount, C.username,
            P.Product_Name, I.Quantity, I.Unit_Price
     FROM ORDERS O
     JOIN CLIENT C ON C.Client_ID = O.Client_ID
     JOIN ORDER_ITEM I ON I.Order_ID = O.Order_ID
     JOIN PRODUCT P ON P.Product_ID = I.Product_ID
     ORDER BY O.Order_Date DESC, O.Order_ID DESC'
)->fetch_all(MYSQLI_ASSOC);

$clients = $connection->query(
    'SELECT Client_ID, FirstName, LastName, username FROM CLIENT ORDER BY LastName, FirstName'
)->fetch_all(MYSQLI_ASSOC);

$topProducts = $connection->query(
    'SELECT P.Product_Name, COALESCE(SUM(I.Quantity), 0) AS Quantity_Sold
     FROM PRODUCT P
     LEFT JOIN ORDER_ITEM I ON I.Product_ID = P.Product_ID
     GROUP BY P.Product_ID, P.Product_Name
     ORDER BY Quantity_Sold DESC, P.Product_Name
     LIMIT 3'
)->fetch_all(MYSQLI_ASSOC);

$revenueRow = $connection->query(
    'SELECT COALESCE(SUM(Total_Amount), 0) AS Total_Revenue FROM ORDERS'
)->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee report | Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell">
    <nav class="nav">
        <span class="brand">Multiverse Shop reporting</span>
        <div>
            <span><?= escape($user['username']) ?></span>
            <form class="inline" action="logout.php" method="post">
                <?= csrf_field() ?>
                <button class="link-button" type="submit">Sign out</button>
            </form>
        </div>
    </nav>

    <header class="hero compact">
        <p class="eyebrow">Employee-only view</p>
        <h1>Database report</h1>
        <p class="metric">Revenue: $<?= escape(format_cents(money_to_cents((string) ($revenueRow['Total_Revenue'] ?? '0.00')))) ?></p>
    </header>

    <section class="panel">
        <h2>Top products</h2>
        <div class="table-wrap">
            <table><thead><tr><th>Product</th><th>Units sold</th></tr></thead><tbody>
            <?php foreach ($topProducts as $product): ?>
                <tr><td><?= escape($product['Product_Name']) ?></td><td><?= escape($product['Quantity_Sold']) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </section>

    <section class="panel">
        <h2>Clients</h2>
        <div class="table-wrap">
            <table><thead><tr><th>ID</th><th>Name</th><th>Username</th></tr></thead><tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= escape($client['Client_ID']) ?></td>
                    <td><?= escape($client['FirstName'] . ' ' . $client['LastName']) ?></td>
                    <td><?= escape($client['username']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </section>

    <section class="panel">
        <h2>Order lines</h2>
        <div class="table-wrap">
            <table><thead><tr><th>Order</th><th>Date</th><th>Client</th><th>Product</th><th>Quantity</th><th>Unit price</th><th>Order total</th></tr></thead><tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= escape($transaction['Order_ID']) ?></td>
                    <td><?= escape($transaction['Order_Date']) ?></td>
                    <td><?= escape($transaction['username']) ?></td>
                    <td><?= escape($transaction['Product_Name']) ?></td>
                    <td><?= escape($transaction['Quantity']) ?></td>
                    <td>$<?= escape(format_cents(money_to_cents((string) $transaction['Unit_Price']))) ?></td>
                    <td>$<?= escape(format_cents(money_to_cents((string) $transaction['Total_Amount']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </section>
</main>
</body>
</html>
