#!/usr/bin/env bash

set -euo pipefail

repository_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
cd "$repository_root"

fail_if_found() {
    local pattern=$1
    shift
    if rg -n "$pattern" --glob '!tests/static_security.sh' "$@" >/dev/null; then
        printf 'Forbidden pattern found: %s\n' "$pattern" >&2
        exit 1
    fi
}

fail_if_found '/~[[:alnum:]_-]+|\.edu\b|CSIT[[:space:]_-]*[0-9]+' -g '!.git/**'
fail_if_found "ini_set\(['\"]display_(errors|startup_errors)['\"], *1\)" --glob '*.php'
fail_if_found 'mysqli_real_escape_string|mysqli_query' --glob '*.php'
fail_if_found 'echo.*(mysqli_error|->error)|exit.*(mysqli_error|->error)|die.*(mysqli_error|->error)' --glob '*.php'
fail_if_found "\\\$[A-Za-z_]+\\[['\"]password['\"]\\][[:space:]]*===" --glob '*.php'
fail_if_found 'https?://' --glob '*.php' --glob '*.css'

[[ -z "$(git ls-files '.env')" ]]
git check-ignore -q .env
[[ -z "$(git ls-files '*.jpg' '*.jpeg' '*.png' '*.PNG')" ]]
[[ -f LICENSE.md ]]
[[ -f SECURITY.md ]]

[[ $(rg -c '^CREATE TABLE' spiderman-shop.sql) -eq 6 ]]
[[ $(rg -c 'ENGINE=InnoDB' spiderman-shop.sql) -eq 6 ]]
rg -q '`password` varchar\(255\) NOT NULL' spiderman-shop.sql
rg -q 'DUMMY_NOT_A_LOGIN' spiderman-shop.sql
fail_if_found 'email|ssn|address|birth|salary' spiderman-shop.sql -i

post_form_count=$(rg -c '<form[^>]+method="post"' --glob '*.php' | awk -F: '{ total += $2 } END { print total + 0 }')
csrf_field_count=$(rg -c 'csrf_field\(\)' --glob '*.php' | awk -F: '{ total += $2 } END { print total + 0 }')
(( csrf_field_count >= post_form_count ))

printf 'Static security checks passed.\n'
