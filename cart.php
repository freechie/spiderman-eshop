<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$user = require_role('client');
$connection = database();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        set_flash('success', 'Cart cleared.');
        redirect('cart.php');
    }

    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($action === 'remove' && $productId !== false) {
        unset($_SESSION['cart'][(string) $productId]);
        set_flash('success', 'Item removed.');
        redirect('cart.php');
    }

    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 99],
    ]);

    if ($action !== 'add' || $productId === false || $quantity === false) {
        set_flash('error', 'Invalid cart request.');
        redirect('products.php');
    }

    $statement = $connection->prepare(
        'SELECT Product_ID, Product_Stock FROM PRODUCT WHERE Product_ID = ? LIMIT 1'
    );
    $statement->bind_param('i', $productId);
    $statement->execute();
    $product = $statement->get_result()->fetch_assoc();

    if (!is_array($product) || $quantity > (int) $product['Product_Stock']) {
        set_flash('error', 'The requested quantity is unavailable.');
        redirect('products.php');
    }

    $_SESSION['cart'][(string) $productId] = $quantity;
    set_flash('success', 'Cart updated.');
    redirect('cart.php');
}

$cartRows = [];
$totalInCents = 0;
$productStatement = $connection->prepare(
    'SELECT Product_ID, Product_Name, Product_Price, Product_Stock FROM PRODUCT WHERE Product_ID = ? LIMIT 1'
);

foreach ($_SESSION['cart'] as $storedId => $storedQuantity) {
    $productId = filter_var($storedId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $quantity = filter_var($storedQuantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);

    if ($productId === false || $quantity === false) {
        unset($_SESSION['cart'][(string) $storedId]);
        continue;
    }

    $productStatement->bind_param('i', $productId);
    $productStatement->execute();
    $product = $productStatement->get_result()->fetch_assoc();

    if (!is_array($product) || $quantity > (int) $product['Product_Stock']) {
        unset($_SESSION['cart'][(string) $storedId]);
        continue;
    }

    $priceInCents = money_to_cents((string) $product['Product_Price']);
    $subtotalInCents = $priceInCents * $quantity;
    $totalInCents += $subtotalInCents;
    $cartRows[] = [
        'id' => (int) $product['Product_ID'],
        'name' => (string) $product['Product_Name'],
        'price_in_cents' => $priceInCents,
        'quantity' => $quantity,
        'subtotal_in_cents' => $subtotalInCents,
    ];
}

$flash = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart | Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell">
    <nav class="nav">
        <a class="brand" href="products.php">Multiverse Shop</a>
        <div><span><?= escape($user['username']) ?></span> <a href="products.php">Products</a> <a href="orders.php">Orders</a></div>
    </nav>

    <header class="hero compact"><h1>Your cart</h1></header>

    <?php if ($flash !== null): ?>
        <p class="notice <?= escape($flash['type'] ?? 'info') ?>"><?= escape($flash['message'] ?? '') ?></p>
    <?php endif; ?>

    <?php if ($cartRows === []): ?>
        <section class="panel"><p>Your cart is empty.</p><a class="button" href="products.php">Browse products</a></section>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($cartRows as $row): ?>
                    <tr>
                        <td><?= escape($row['name']) ?></td>
                        <td>$<?= escape(format_cents($row['price_in_cents'])) ?></td>
                        <td><?= escape($row['quantity']) ?></td>
                        <td>$<?= escape(format_cents($row['subtotal_in_cents'])) ?></td>
                        <td>
                            <form action="cart.php" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= escape($row['id']) ?>">
                                <button class="link-button danger" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot><tr><th colspan="3">Total</th><th>$<?= escape(format_cents($totalInCents)) ?></th><th></th></tr></tfoot>
            </table>
        </div>

        <div class="actions">
            <form action="cart.php" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="clear">
                <button class="button secondary" type="submit">Clear cart</button>
            </form>
            <form action="checkout.php" method="post">
                <?= csrf_field() ?>
                <button class="button" type="submit">Submit order</button>
            </form>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
