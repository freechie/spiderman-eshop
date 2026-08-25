<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config.php';

require_post();
verify_csrf();

$accountType = $_POST['account_type'] ?? '';
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if (!in_array($accountType, ['client', 'employee'], true) || $username === '' || $password === '') {
    set_flash('error', 'Invalid username or password.');
    redirect('index.php');
}

if ($accountType === 'employee') {
    $selectSql = 'SELECT Employee_ID AS account_id, username, password FROM EMPLOYEE WHERE username = ? LIMIT 1';
    $updateSql = 'UPDATE EMPLOYEE SET password = ? WHERE Employee_ID = ?';
} else {
    $selectSql = 'SELECT Client_ID AS account_id, username, password FROM CLIENT WHERE username = ? LIMIT 1';
    $updateSql = 'UPDATE CLIENT SET password = ? WHERE Client_ID = ?';
}

try {
    $connection = database();
    $statement = $connection->prepare($selectSql);
    $statement->bind_param('s', $username);
    $statement->execute();
    $account = $statement->get_result()->fetch_assoc();

    if (!is_array($account) || !password_verify($password, (string) $account['password'])) {
        set_flash('error', 'Invalid username or password.');
        redirect('index.php');
    }

    if (password_needs_rehash((string) $account['password'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $connection->prepare($updateSql);
        $accountId = (int) $account['account_id'];
        $update->bind_param('si', $newHash, $accountId);
        $update->execute();
    }

    session_regenerate_id(true);
    unset($_SESSION['csrf_token']);
    $_SESSION['user'] = [
        'id' => (int) $account['account_id'],
        'username' => (string) $account['username'],
        'role' => $accountType,
    ];

    redirect($accountType === 'employee' ? 'employee.php' : 'products.php');
} catch (mysqli_sql_exception) {
    error_log('Login query failed.');
    set_flash('error', 'Sign-in is temporarily unavailable.');
    redirect('index.php');
}
