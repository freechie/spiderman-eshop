<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$user = require_role('client');
require_post();
verify_csrf();

$cart = $_SESSION['cart'] ?? [];
if (!is_array($cart) || $cart === []) {
    set_flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$connection = database();

try {
    $connection->begin_transaction();

    $createOrder = $connection->prepare(
        'INSERT INTO ORDERS (Client_ID, Order_Date, Total_Amount) VALUES (?, CURRENT_TIMESTAMP, 0.00)'
    );
    $clientId = (int) $user['id'];
    $createOrder->bind_param('i', $clientId);
    $createOrder->execute();
    $orderId = $connection->insert_id;

    $selectProduct = $connection->prepare(
        'SELECT Product_ID, Product_Price, Product_Stock FROM PRODUCT WHERE Product_ID = ? FOR UPDATE'
    );
    $insertItem = $connection->prepare(
        'INSERT INTO ORDER_ITEM (Order_ID, Product_ID, Quantity, Unit_Price) VALUES (?, ?, ?, ?)'
    );
    $reduceStock = $connection->prepare(
        'UPDATE PRODUCT SET Product_Stock = Product_Stock - ? WHERE Product_ID = ? AND Product_Stock >= ?'
    );

    $totalInCents = 0;

    foreach ($cart as $storedId => $storedQuantity) {
        $productId = filter_var($storedId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $quantity = filter_var($storedQuantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);

        if ($productId === false || $quantity === false) {
            throw new RuntimeException('Invalid cart state.');
        }

        $selectProduct->bind_param('i', $productId);
        $selectProduct->execute();
        $product = $selectProduct->get_result()->fetch_assoc();

        if (!is_array($product) || $quantity > (int) $product['Product_Stock']) {
            throw new RuntimeException('Insufficient stock.');
        }

        $unitPriceInCents = money_to_cents((string) $product['Product_Price']);
        $unitPrice = number_format($unitPriceInCents / 100, 2, '.', '');
        $totalInCents += $unitPriceInCents * $quantity;

        $insertItem->bind_param('iiis', $orderId, $productId, $quantity, $unitPrice);
        $insertItem->execute();

        $reduceStock->bind_param('iii', $quantity, $productId, $quantity);
        $reduceStock->execute();

        if ($reduceStock->affected_rows !== 1) {
            throw new RuntimeException('Stock update failed.');
        }
    }

    $totalAmount = number_format($totalInCents / 100, 2, '.', '');
    $updateOrder = $connection->prepare('UPDATE ORDERS SET Total_Amount = ? WHERE Order_ID = ?');
    $updateOrder->bind_param('si', $totalAmount, $orderId);
    $updateOrder->execute();

    $connection->commit();
    $_SESSION['cart'] = [];
    set_flash('success', 'Order submitted successfully.');
    redirect('orders.php');
} catch (Throwable) {
    $connection->rollback();
    error_log('Checkout transaction failed.');
    set_flash('error', 'The order could not be submitted. Please review your cart and try again.');
    redirect('cart.php');
}
