# Spider-Man E-Shop Database System

This e-commerce prototype was built for a Database Systems final project. Its main purpose is to demonstrate a relational design for customers, employees, products, carts, and order history through a PHP web interface.

## Database design

The schema and fictional fixtures are in [`spiderman-shop.sql`](spiderman-shop.sql).

| Table | Purpose |
| --- | --- |
| `CLIENT` | Customer profile and login fields |
| `EMPLOYEE` | Employee profile, reporting hierarchy fields, and login fields |
| `PRODUCT_CATEGORY` | Product category lookup data |
| `PRODUCT` | Product catalog, stock, category, and price |
| `CART` | A customer's shopping cart |
| `CART_INFO` | Cart line items connecting carts and products |
| `ORDER_HISTORY` | Completed-order snapshots used for customer history and employee reporting |

The ID columns express these logical relationships:

- a client owns a cart;
- a cart contains multiple cart-info rows;
- each cart-info row identifies a product;
- a product belongs to a category; and
- order-history rows identify the client who placed each order.

The original project schema uses MyISAM. It defines primary keys and lookup indexes, but MyISAM does not enforce foreign-key constraints. The relationships above are therefore maintained by the application rather than by the database engine.

## Database operations

The PHP application demonstrates several database workloads:

- prepared lookups for client and employee authentication;
- client registration;
- product catalog reads;
- order-history inserts and customer order-history views; and
- employee reporting queries for transactions, customers, popular products, and revenue.

Database access uses MySQLi. [`config.php`](config.php) reads connection settings from environment variables and returns generic connection errors, so working credentials are not stored in tracked files.

## Data safety

The public SQL fixture contains fictional records only. Names, addresses, identifiers, usernames, and order data are invented for demonstration. Password fields contain obvious unusable dummy values, not live credentials.

The repository intentionally excludes `.env`. Never commit a working database password or a production data export.

## Run locally

Requirements:

- PHP with the MySQLi extension
- MySQL or MariaDB
- a web server such as PHP's development server or Apache in XAMPP

1. Create a local database.

2. Import the schema and fictional fixtures into that database:

   ```sh
   mysql -u your_local_user -p your_local_database < spiderman-shop.sql
   ```

3. Copy the environment template and replace every placeholder with local-only settings:

   ```sh
   cp .env.example .env
   ```

4. Export the settings and start the development server:

   ```sh
   set -a
   source .env
   set +a
   php -S 127.0.0.1:8000
   ```

5. Open `http://127.0.0.1:8000`.

The fixture password values intentionally cannot be used to sign in. Replace them only in your local database if you need to exercise an authenticated flow.

## Technology

- PHP and MySQLi
- MySQL or MariaDB
- HTML, CSS, and JavaScript
