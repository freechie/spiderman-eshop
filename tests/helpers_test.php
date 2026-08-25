<?php

declare(strict_types=1);

$_SERVER['REQUEST_METHOD'] = 'POST';
require_once dirname(__DIR__) . '/bootstrap.php';

function assert_same(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

assert_same(
    '&lt;script&gt;&quot;x&quot; &amp; &#039;y&#039;&lt;/script&gt;',
    escape('<script>"x" & \'y\'</script>'),
    'HTML escaping failed.'
);

$token = csrf_token();
assert_same(64, strlen($token), 'CSRF token length is invalid.');
assert_same($token, csrf_token(), 'CSRF token did not persist in the session.');
assert_same(4800, money_to_cents('48.00'), 'Money conversion failed.');
assert_same(1605, money_to_cents('16.05'), 'Money fraction conversion failed.');
assert_same('1,234.56', format_cents(123456), 'Money formatting failed.');

$_POST['csrf_token'] = $token;
verify_csrf();

set_flash('success', 'Saved.');
assert_same(['type' => 'success', 'message' => 'Saved.'], take_flash(), 'Flash message round trip failed.');
assert_same(null, take_flash(), 'Flash message was not cleared.');

fwrite(STDOUT, "Helper checks passed.\n");
