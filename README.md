# Spider-Man E-Shop database project

This repository contains a hardened version of a Database Systems final project. The original Spider-Man-themed coursework is now presented as a small fictional Multiverse Shop so the public repository does not redistribute third-party artwork or sample personal data.

The project focuses on the relational database layer: normalized order data, foreign keys, prepared queries, transactional checkout, inventory updates, and aggregate employee reports.

## Database design

[`spiderman-shop.sql`](spiderman-shop.sql) defines six InnoDB tables:

| Table | Purpose |
| --- | --- |
| `CLIENT` | Client identity, unique username, and password hash |
| `EMPLOYEE` | Employee identity, unique username, and password hash |
| `PRODUCT_CATEGORY` | Normalized category lookup data |
| `PRODUCT` | Catalog items, inventory, category, and price |
| `ORDERS` | Order header with client, timestamp, and total |
| `ORDER_ITEM` | Order lines connecting orders and products |

Foreign keys enforce the client-to-order, order-to-item, product-to-item, and category-to-product relationships. Composite and secondary indexes support order history and reporting queries. Checkout locks product rows and commits the order, order lines, and stock updates in one transaction.

The checked-in fixtures are fictional. Account password columns contain obvious unusable dummy values. Use the local password tool or client registration page to create working local credentials.

## Security controls

- Database settings come only from environment variables.
- MySQLi prepared statements bind every request or session value used in a query.
- PHP's password API hashes and verifies account passwords.
- Session IDs rotate after login, cookies are HTTP-only and SameSite, and HTTPS deployments can require secure cookies.
- Every state-changing form requires a CSRF token.
- Client and employee pages enforce role-based authorization.
- Cart pricing and stock come from the database, not hidden form fields.
- HTML output is context-encoded.
- Database and SQL errors are not returned to the browser.

This remains a compact educational application, not a production commerce platform.

## Run locally

Requirements:

- PHP 8.1 or newer with MySQLi
- MySQL 8 or a compatible MariaDB release

1. Create a local database.

2. Import the schema and fictional fixtures:

   ```sh
   mysql -u your_local_user -p your_local_database < spiderman-shop.sql
   ```

3. Copy the environment template and replace every placeholder with local-only database settings:

   ```sh
   cp .env.example .env
   ```

4. Export the settings into the current shell:

   ```sh
   set -a
   source .env
   set +a
   ```

5. Give the fictional employee account a local password. The tool reads the password without printing it:

   ```sh
   php scripts/set-local-password.php employee employee_example
   ```

6. Start the development server:

   ```sh
   php -S 127.0.0.1:8000
   ```

7. Open `http://127.0.0.1:8000`. Register a client account through the browser or set a local password for `client_example` with the same CLI tool.

For an HTTPS deployment, set `SESSION_SECURE=1`. Keep it at `0` for local HTTP development.

## Verification

Run the helper checks and PHP syntax checks:

```sh
php tests/helpers_test.php
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n 1 php -l
```

Security scans use the audit configuration stored outside this repository. Phase 1 also verifies current files and complete Git history from a fresh clone.

## Repository policy

See [`SECURITY.md`](SECURITY.md) for private vulnerability reporting. See [`LICENSE.md`](LICENSE.md) for the source-availability terms. No third-party artwork is included.
