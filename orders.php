<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$user = require_role('client');
$statement = database()->prepare(
    'SELECT O.Order_ID, O.Order_Date, O.Total_Amount,
            P.Product_Name, I.Quantity, I.Unit_Price
     FROM ORDERS O
     JOIN ORDER_ITEM I ON I.Order_ID = O.Order_ID
     JOIN PRODUCT P ON P.Product_ID = I.Product_ID
     WHERE O.Client_ID = ?
     ORDER BY O.Order_Date DESC, O.Order_ID DESC, P.Product_Name'
);
$clientId = (int) $user['id'];
$statement->bind_param('i', $clientId);
$statement->execute();
$rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

$orders = [];
foreach ($rows as $row) {
    $orderId = (int) $row['Order_ID'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'date' => $row['Order_Date'],
            'total' => $row['Total_Amount'],
            'items' => [],
        ];
    }
    $orders[$orderId]['items'][] = $row;
}

$flash = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders | Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell">
    <nav class="nav">
        <a class="brand" href="products.php">Multiverse Shop</a>
        <div><span><?= escape($user['username']) ?></span> <a href="products.php">Products</a> <a href="cart.php">Cart</a></div>
    </nav>
    <header class="hero compact"><h1>Order history</h1></header>

    <?php if ($flash !== null): ?>
        <p class="notice <?= escape($flash['type'] ?? 'info') ?>"><?= escape($flash['message'] ?? '') ?></p>
    <?php endif; ?>

    <?php if ($orders === []): ?>
        <section class="panel"><p>No orders yet.</p></section>
    <?php endif; ?>

    <?php foreach ($orders as $orderId => $order): ?>
        <section class="panel order-card">
            <div class="order-heading">
                <h2>Order <?= escape($orderId) ?></h2>
                <p><?= escape($order['date']) ?> · $<?= escape(format_cents(money_to_cents((string) $order['total']))) ?></p>
            </div>
            <ul>
                <?php foreach ($order['items'] as $item): ?>
                    <li><?= escape($item['Product_Name']) ?> × <?= escape($item['Quantity']) ?> at $<?= escape(format_cents(money_to_cents((string) $item['Unit_Price']))) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
