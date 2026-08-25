<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$user = current_user();
$flash = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell narrow">
    <header class="hero">
        <p class="eyebrow">Database Systems final project</p>
        <h1>Multiverse Shop</h1>
        <p>A small PHP and MySQL storefront demonstrating relational data, transactions, and reporting.</p>
    </header>

    <?php if ($flash !== null): ?>
        <p class="notice <?= escape($flash['type'] ?? 'info') ?>"><?= escape($flash['message'] ?? '') ?></p>
    <?php endif; ?>

    <?php if ($user !== null): ?>
        <section class="panel stack">
            <h2>Signed in</h2>
            <p>Welcome, <?= escape($user['username']) ?>.</p>
            <a class="button" href="<?= $user['role'] === 'employee' ? 'employee.php' : 'products.php' ?>">Continue</a>
            <form action="logout.php" method="post">
                <?= csrf_field() ?>
                <button class="button secondary" type="submit">Sign out</button>
            </form>
        </section>
    <?php else: ?>
        <section class="panel">
            <h2>Sign in</h2>
            <form class="stack" action="login.php" method="post">
                <?= csrf_field() ?>

                <label for="account_type">Account type</label>
                <select id="account_type" name="account_type" required>
                    <option value="client">Client</option>
                    <option value="employee">Employee</option>
                </select>

                <label for="username">Username</label>
                <input id="username" name="username" type="text" maxlength="60" autocomplete="username" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" maxlength="128" autocomplete="current-password" required>

                <button class="button" type="submit">Sign in</button>
            </form>
            <p class="panel-footer">Need a client account? <a href="register.php">Register locally</a>.</p>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
