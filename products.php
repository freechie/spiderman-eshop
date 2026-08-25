<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$user = require_role('client');
$connection = database();
$selectedCategory = $_GET['category'] ?? 'all';

if ($selectedCategory !== 'all') {
    $selectedCategory = filter_var($selectedCategory, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($selectedCategory === false) {
        $selectedCategory = 'all';
    }
}

$categories = $connection
    ->query('SELECT Category_ID, Category FROM PRODUCT_CATEGORY ORDER BY Category')
    ->fetch_all(MYSQLI_ASSOC);

if ($selectedCategory === 'all') {
    $products = $connection
        ->query(
            'SELECT P.Product_ID, P.Product_Name, P.Product_Stock, P.Product_Price, C.Category
             FROM PRODUCT P
             JOIN PRODUCT_CATEGORY C ON C.Category_ID = P.Category_ID
             ORDER BY P.Product_Name'
        )
        ->fetch_all(MYSQLI_ASSOC);
} else {
    $statement = $connection->prepare(
        'SELECT P.Product_ID, P.Product_Name, P.Product_Stock, P.Product_Price, C.Category
         FROM PRODUCT P
         JOIN PRODUCT_CATEGORY C ON C.Category_ID = P.Category_ID
         WHERE P.Category_ID = ?
         ORDER BY P.Product_Name'
    );
    $statement->bind_param('i', $selectedCategory);
    $statement->execute();
    $products = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
}

$flash = take_flash();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products | Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell">
    <nav class="nav">
        <a class="brand" href="products.php">Multiverse Shop</a>
        <div>
            <span><?= escape($user['username']) ?></span>
            <a href="orders.php">Orders</a>
            <a href="cart.php">Cart (<?= escape($cartCount) ?>)</a>
            <form class="inline" action="logout.php" method="post">
                <?= csrf_field() ?>
                <button class="link-button" type="submit">Sign out</button>
            </form>
        </div>
    </nav>

    <header class="hero compact">
        <p class="eyebrow">Server-priced catalog</p>
        <h1>Products</h1>
    </header>

    <?php if ($flash !== null): ?>
        <p class="notice <?= escape($flash['type'] ?? 'info') ?>"><?= escape($flash['message'] ?? '') ?></p>
    <?php endif; ?>

    <form class="filter" method="get" action="products.php">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="all">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= escape($category['Category_ID']) ?>" <?= (string) $selectedCategory === (string) $category['Category_ID'] ? 'selected' : '' ?>>
                    <?= escape($category['Category']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="button small" type="submit">Filter</button>
    </form>

    <section class="product-grid" aria-label="Product catalog">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <p class="eyebrow"><?= escape($product['Category']) ?></p>
                <h2><?= escape($product['Product_Name']) ?></h2>
                <p class="price">$<?= escape(format_cents(money_to_cents((string) $product['Product_Price']))) ?></p>
                <p><?= escape($product['Product_Stock']) ?> in stock</p>

                <?php if ((int) $product['Product_Stock'] > 0): ?>
                    <form class="add-form" action="cart.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= escape($product['Product_ID']) ?>">
                        <label for="quantity-<?= escape($product['Product_ID']) ?>">Quantity</label>
                        <input id="quantity-<?= escape($product['Product_ID']) ?>" name="quantity" type="number" value="1" min="1" max="<?= escape($product['Product_Stock']) ?>" required>
                        <button class="button" type="submit">Add to cart</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>
