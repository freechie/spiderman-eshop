<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config.php';

$role = $argv[1] ?? '';
$username = $argv[2] ?? '';

if (!in_array($role, ['client', 'employee'], true) || !preg_match('/\A[A-Za-z][A-Za-z0-9_]{2,59}\z/', $username)) {
    fwrite(STDERR, "Usage: php scripts/set-local-password.php <client|employee> <username>\n");
    exit(1);
}

fwrite(STDOUT, 'New local password: ');
$sttyAvailable = function_exists('shell_exec') && trim((string) shell_exec('command -v stty')) !== '';
if ($sttyAvailable) {
    shell_exec('stty -echo');
}
$password = rtrim((string) fgets(STDIN), "\r\n");
if ($sttyAvailable) {
    shell_exec('stty echo');
    fwrite(STDOUT, PHP_EOL);
}

if (strlen($password) < 12 || strlen($password) > 128) {
    fwrite(STDERR, "Password must be between 12 and 128 characters.\n");
    exit(1);
}

if ($role === 'employee') {
    $sql = 'UPDATE EMPLOYEE SET password = ? WHERE username = ?';
} else {
    $sql = 'UPDATE CLIENT SET password = ? WHERE username = ?';
}

try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement = database()->prepare($sql);
    $statement->bind_param('ss', $hash, $username);
    $statement->execute();

    if ($statement->affected_rows !== 1) {
        fwrite(STDERR, "Account not found.\n");
        exit(1);
    }

    fwrite(STDOUT, "Local password updated.\n");
} catch (mysqli_sql_exception) {
    fwrite(STDERR, "Password update failed.\n");
    exit(1);
}
