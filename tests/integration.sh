#!/usr/bin/env bash

set -euo pipefail

repository_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
test_root=$(mktemp -d /private/tmp/multiverse-shop-test.XXXXXX)
mysql_pid=''
php_pid=''

cleanup() {
    if [[ -n "$php_pid" ]]; then
        kill "$php_pid" >/dev/null 2>&1 || true
        wait "$php_pid" >/dev/null 2>&1 || true
    fi

    if [[ -n "$mysql_pid" ]]; then
        mysqladmin --socket="$test_root/mysql.sock" --user=root shutdown >/dev/null 2>&1 || true
        wait "$mysql_pid" >/dev/null 2>&1 || true
    fi

    case "$test_root" in
        /private/tmp/multiverse-shop-test.*) rm -rf "$test_root" ;;
    esac
}
trap cleanup EXIT

pick_port() {
    php -r '$server = stream_socket_server("tcp://127.0.0.1:0", $errorNumber, $errorMessage); if ($server === false) { exit(1); } $address = stream_socket_get_name($server, false); echo substr(strrchr($address, ":"), 1); fclose($server);'
}

extract_csrf() {
    sed -nE 's/.*name="csrf_token" value="([^"]+)".*/\1/p' | head -n 1
}

mysql_port=$(pick_port)
php_port=$(pick_port)
mysql_data="$test_root/mysql-data"
mkdir -p "$mysql_data"

mysqld --no-defaults --initialize-insecure --datadir="$mysql_data" --log-error="$test_root/mysql-init.log"
mysqld --no-defaults \
    --datadir="$mysql_data" \
    --bind-address=127.0.0.1 \
    --port="$mysql_port" \
    --socket="$test_root/mysql.sock" \
    --pid-file="$test_root/mysql.pid" \
    --log-error="$test_root/mysql.log" \
    --mysqlx=0 \
    --skip-name-resolve &
mysql_pid=$!

for _ in {1..80}; do
    if mysqladmin --socket="$test_root/mysql.sock" --user=root ping >/dev/null 2>&1; then
        break
    fi
    sleep 0.25
done
mysqladmin --socket="$test_root/mysql.sock" --user=root ping >/dev/null

admin_mysql=(mysql --socket="$test_root/mysql.sock" --user=root)
test_db_password=$(php -r 'echo bin2hex(random_bytes(16));')
"${admin_mysql[@]}" --execute="CREATE DATABASE multiverse_shop_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER 'shop_test'@'127.0.0.1' IDENTIFIED BY '$test_db_password'; GRANT ALL ON multiverse_shop_test.* TO 'shop_test'@'127.0.0.1';"

test_mysql() {
    MYSQL_PWD="$test_db_password" mysql --protocol=TCP --host=127.0.0.1 --port="$mysql_port" --user=shop_test "$@"
}

test_mysql multiverse_shop_test < "$repository_root/spiderman-shop.sql"

client_password='local-client-password'
employee_password='local-employee-password'
client_hash=$(php -r 'echo password_hash("local-client-password", PASSWORD_DEFAULT);')
employee_hash=$(php -r 'echo password_hash("local-employee-password", PASSWORD_DEFAULT);')
test_mysql multiverse_shop_test --execute="UPDATE CLIENT SET password = '$client_hash' WHERE username = 'client_example'; UPDATE EMPLOYEE SET password = '$employee_hash' WHERE username = 'employee_example';"

DB_HOST=127.0.0.1 \
DB_PORT="$mysql_port" \
DB_USER=shop_test \
DB_PASSWORD="$test_db_password" \
DB_NAME=multiverse_shop_test \
SESSION_SECURE=0 \
php -S "127.0.0.1:$php_port" -t "$repository_root" > "$test_root/php.log" 2>&1 &
php_pid=$!

base_url="http://127.0.0.1:$php_port"
for _ in {1..40}; do
    if curl -fsS "$base_url/index.php" >/dev/null 2>&1; then
        break
    fi
    sleep 0.25
done

security_headers=$(curl -fsSI "$base_url/index.php")
printf '%s' "$security_headers" | rg -i '^Content-Security-Policy:' >/dev/null
printf '%s' "$security_headers" | rg -i '^X-Content-Type-Options: nosniff' >/dev/null

registration_cookie="$test_root/registration.cookies"
registration_html=$(curl -fsS --cookie-jar "$registration_cookie" "$base_url/register.php")
registration_csrf=$(printf '%s' "$registration_html" | extract_csrf)
registration_result=$(curl -sS --cookie "$registration_cookie" --cookie-jar "$registration_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' \
    --data-urlencode "csrf_token=$registration_csrf" \
    --data-urlencode 'first_name=Taylor' \
    --data-urlencode 'last_name=Example' \
    --data-urlencode 'username=registered_client' \
    --data-urlencode 'password=registered-client-password' \
    "$base_url/register.php")
[[ "$registration_result" == "303:$base_url/index.php" ]]
registered_hash=$(test_mysql --skip-column-names multiverse_shop_test --execute="SELECT password FROM CLIENT WHERE username = 'registered_client'")
[[ "$registered_hash" != 'registered-client-password' ]]
php -r 'exit(password_verify($argv[1], $argv[2]) ? 0 : 1);' 'registered-client-password' "$registered_hash"

client_cookie="$test_root/client.cookies"
index_html=$(curl -fsS --cookie-jar "$client_cookie" "$base_url/index.php")
csrf=$(printf '%s' "$index_html" | extract_csrf)
[[ ${#csrf} -eq 64 ]]

login_result=$(curl -sS --cookie "$client_cookie" --cookie-jar "$client_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' \
    --data-urlencode "csrf_token=$csrf" \
    --data-urlencode 'account_type=client' \
    --data-urlencode 'username=client_example' \
    --data-urlencode "password=$client_password" \
    "$base_url/login.php")
[[ "$login_result" == "303:$base_url/products.php" ]]

products_html=$(curl -fsS --cookie "$client_cookie" "$base_url/products.php")
printf '%s' "$products_html" | rg -F 'Portal Hoodie' >/dev/null
csrf=$(printf '%s' "$products_html" | extract_csrf)

add_result=$(curl -sS --cookie "$client_cookie" --cookie-jar "$client_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' \
    --data-urlencode "csrf_token=$csrf" \
    --data-urlencode 'action=add' \
    --data-urlencode 'product_id=1' \
    --data-urlencode 'quantity=2' \
    --data-urlencode 'product_price=0.01' \
    "$base_url/cart.php")
[[ "$add_result" == "303:$base_url/cart.php" ]]

cart_html=$(curl -fsS --cookie "$client_cookie" "$base_url/cart.php")
printf '%s' "$cart_html" | rg -F '$96.00' >/dev/null
csrf=$(printf '%s' "$cart_html" | extract_csrf)

checkout_result=$(curl -sS --cookie "$client_cookie" --cookie-jar "$client_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' \
    --data-urlencode "csrf_token=$csrf" \
    "$base_url/checkout.php")
[[ "$checkout_result" == "303:$base_url/orders.php" ]]

orders_html=$(curl -fsS --cookie "$client_cookie" "$base_url/orders.php")
printf '%s' "$orders_html" | rg -F 'Order submitted successfully.' >/dev/null
printf '%s' "$orders_html" | rg -F 'Portal Hoodie × 2' >/dev/null

order_count=$(test_mysql --skip-column-names multiverse_shop_test --execute='SELECT COUNT(*) FROM ORDERS')
stock_count=$(test_mysql --skip-column-names multiverse_shop_test --execute='SELECT Product_Stock FROM PRODUCT WHERE Product_ID = 1')
[[ "$order_count" == '2' ]]
[[ "$stock_count" == '18' ]]

employee_redirect=$(curl -sS --cookie "$client_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' "$base_url/employee.php")
[[ "$employee_redirect" == "303:$base_url/index.php" ]]

injection_status=$(curl -sS --cookie "$client_cookie" --output /dev/null --write-out '%{http_code}' --get \
    --data-urlencode "category=1' OR 1=1 --" \
    "$base_url/products.php")
[[ "$injection_status" == '200' ]]

test_mysql multiverse_shop_test --execute="UPDATE PRODUCT SET Product_Name = '<script>alert(1)</script>' WHERE Product_ID = 1"
escaped_html=$(curl -fsS --cookie "$client_cookie" "$base_url/products.php")
printf '%s' "$escaped_html" | rg -F '&lt;script&gt;alert(1)&lt;/script&gt;' >/dev/null
if printf '%s' "$escaped_html" | rg -F '<script>alert(1)</script>' >/dev/null; then
    exit 1
fi

invalid_csrf_status=$(curl -sS --output /dev/null --write-out '%{http_code}' \
    --data-urlencode 'csrf_token=invalid' \
    --data-urlencode 'account_type=client' \
    --data-urlencode 'username=client_example' \
    --data-urlencode "password=$client_password" \
    "$base_url/login.php")
[[ "$invalid_csrf_status" == '403' ]]

employee_cookie="$test_root/employee.cookies"
index_html=$(curl -fsS --cookie-jar "$employee_cookie" "$base_url/index.php")
csrf=$(printf '%s' "$index_html" | extract_csrf)
employee_login=$(curl -sS --cookie "$employee_cookie" --cookie-jar "$employee_cookie" --output /dev/null --write-out '%{http_code}:%{redirect_url}' \
    --data-urlencode "csrf_token=$csrf" \
    --data-urlencode 'account_type=employee' \
    --data-urlencode 'username=employee_example' \
    --data-urlencode "password=$employee_password" \
    "$base_url/login.php")
[[ "$employee_login" == "303:$base_url/employee.php" ]]

employee_html=$(curl -fsS --cookie "$employee_cookie" "$base_url/employee.php")
printf '%s' "$employee_html" | rg -F 'Database report' >/dev/null
printf '%s' "$employee_html" | rg -F 'client_example' >/dev/null

printf 'Integration checks passed.\n'
