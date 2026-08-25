SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ORDER_ITEM`;
DROP TABLE IF EXISTS `ORDERS`;
DROP TABLE IF EXISTS `PRODUCT`;
DROP TABLE IF EXISTS `PRODUCT_CATEGORY`;
DROP TABLE IF EXISTS `EMPLOYEE`;
DROP TABLE IF EXISTS `CLIENT`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `CLIENT` (
  `Client_ID` int unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`Client_ID`),
  UNIQUE KEY `client_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `EMPLOYEE` (
  `Employee_ID` int unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`Employee_ID`),
  UNIQUE KEY `employee_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `PRODUCT_CATEGORY` (
  `Category_ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Category` varchar(80) NOT NULL,
  PRIMARY KEY (`Category_ID`),
  UNIQUE KEY `product_category_name_unique` (`Category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `PRODUCT` (
  `Product_ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Product_Stock` int unsigned NOT NULL DEFAULT 0,
  `Product_Name` varchar(120) NOT NULL,
  `Category_ID` int unsigned NOT NULL,
  `Product_Price` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`Product_ID`),
  KEY `product_category_index` (`Category_ID`),
  CONSTRAINT `product_category_fk`
    FOREIGN KEY (`Category_ID`) REFERENCES `PRODUCT_CATEGORY` (`Category_ID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ORDERS` (
  `Order_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `Client_ID` int unsigned NOT NULL,
  `Order_Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Total_Amount` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `orders_client_date_index` (`Client_ID`, `Order_Date`),
  CONSTRAINT `orders_client_fk`
    FOREIGN KEY (`Client_ID`) REFERENCES `CLIENT` (`Client_ID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ORDER_ITEM` (
  `Order_ID` bigint unsigned NOT NULL,
  `Product_ID` int unsigned NOT NULL,
  `Quantity` int unsigned NOT NULL,
  `Unit_Price` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`Order_ID`, `Product_ID`),
  KEY `order_item_product_index` (`Product_ID`),
  CONSTRAINT `order_item_order_fk`
    FOREIGN KEY (`Order_ID`) REFERENCES `ORDERS` (`Order_ID`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `order_item_product_fk`
    FOREIGN KEY (`Product_ID`) REFERENCES `PRODUCT` (`Product_ID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fictional records only. Account passwords are intentionally unusable dummy values.
INSERT INTO `CLIENT` (`Client_ID`, `FirstName`, `LastName`, `username`, `password`) VALUES
  (1, 'Alex', 'Example', 'client_example', 'DUMMY_NOT_A_LOGIN');

INSERT INTO `EMPLOYEE` (`Employee_ID`, `FirstName`, `LastName`, `username`, `password`) VALUES
  (1, 'Casey', 'Example', 'employee_example', 'DUMMY_NOT_A_LOGIN');

INSERT INTO `PRODUCT_CATEGORY` (`Category_ID`, `Category`) VALUES
  (1, 'Apparel'),
  (2, 'Accessories'),
  (3, 'Prints');

INSERT INTO `PRODUCT` (`Product_ID`, `Product_Stock`, `Product_Name`, `Category_ID`, `Product_Price`) VALUES
  (1, 20, 'Portal Hoodie', 1, 48.00),
  (2, 35, 'Orbit Mug', 2, 16.00),
  (3, 15, 'Nebula Poster', 3, 22.00);

INSERT INTO `ORDERS` (`Order_ID`, `Client_ID`, `Order_Date`, `Total_Amount`) VALUES
  (1, 1, '2026-01-15 12:00:00', 48.00);

INSERT INTO `ORDER_ITEM` (`Order_ID`, `Product_ID`, `Quantity`, `Unit_Price`) VALUES
  (1, 1, 1, 48.00);
