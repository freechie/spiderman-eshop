<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

$errors = [];
$firstName = '';
$lastName = '';
$username = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();

    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($firstName === '' || strlen($firstName) > 60) {
        $errors[] = 'First name must be between 1 and 60 characters.';
    }

    if ($lastName === '' || strlen($lastName) > 60) {
        $errors[] = 'Last name must be between 1 and 60 characters.';
    }

    if (!preg_match('/\A[A-Za-z][A-Za-z0-9_]{2,29}\z/', $username)) {
        $errors[] = 'Username must be 3-30 characters and use letters, numbers, or underscores.';
    }

    if (strlen($password) < 12 || strlen($password) > 128) {
        $errors[] = 'Password must be between 12 and 128 characters.';
    }

    if ($errors === []) {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $statement = database()->prepare(
                'INSERT INTO CLIENT (FirstName, LastName, username, password) VALUES (?, ?, ?, ?)'
            );
            $statement->bind_param('ssss', $firstName, $lastName, $username, $passwordHash);
            $statement->execute();

            set_flash('success', 'Account created. You can now sign in.');
            redirect('index.php');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $errors[] = 'That username is unavailable.';
            } else {
                error_log('Client registration failed.');
                $errors[] = 'Registration is temporarily unavailable.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Multiverse Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="shell narrow">
    <section class="panel">
        <p class="eyebrow">Client registration</p>
        <h1>Create an account</h1>

        <?php if ($errors !== []): ?>
            <div class="notice error" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="stack" method="post" action="register.php">
            <?= csrf_field() ?>

            <label for="first_name">First name</label>
            <input id="first_name" name="first_name" value="<?= escape($firstName) ?>" maxlength="60" autocomplete="given-name" required>

            <label for="last_name">Last name</label>
            <input id="last_name" name="last_name" value="<?= escape($lastName) ?>" maxlength="60" autocomplete="family-name" required>

            <label for="username">Username</label>
            <input id="username" name="username" value="<?= escape($username) ?>" maxlength="30" autocomplete="username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" minlength="12" maxlength="128" autocomplete="new-password" required>

            <button class="button" type="submit">Create account</button>
        </form>

        <p class="panel-footer"><a href="index.php">Back to sign in</a></p>
    </section>
</main>
</body>
</html>
