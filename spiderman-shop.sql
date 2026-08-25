-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: REDACTED:3306
-- Generation Time: Jun 22, 2023 at 06:40 PM
-- Server version: 5.7.42
-- PHP Version: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `REDACTEDman_shop_example`
--

-- --------------------------------------------------------

--
-- Table structure for table `CART`
--

CREATE TABLE `CART` (
  `Cart_ID` int(11) NOT NULL,
  `Client_ID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `CART_INFO`
--

CREATE TABLE `CART_INFO` (
  `CART_INFO_ID` int(11) NOT NULL,
  `CART_ID` int(11) DEFAULT NULL,
  `PRODUCT_ID` int(11) DEFAULT NULL,
  `QUANTITY` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `CLIENT`
--

CREATE TABLE `CLIENT` (
  `Client_ID` int(11) NOT NULL,
  `FirstName` varchar(15) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `password` varchar(30) NOT NULL,
  `username` varchar(20) NOT NULL,
  `Sex` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CLIENT`
--

INSERT INTO `CLIENT` (`Client_ID`, `FirstName`, `LastName`, `password`, `username`, `Sex`) VALUES
(1, 'Miles', 'Morales', 'DUMMY_NOT_FOR_LOGIN', 'miles.demo', 'M'),
(2, 'Gwen', 'Stacy', 'DUMMY_NOT_FOR_LOGIN', 'gwen.demo', 'F');

-- --------------------------------------------------------

--
-- Table structure for table `EMPLOYEE`
--

CREATE TABLE `EMPLOYEE` (
  `Employee_id` int(11) UNSIGNED NOT NULL,
  `Fname` varchar(20) NOT NULL,
  `Minit` char(1) DEFAULT NULL,
  `Lname` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `Ssn` char(9) NOT NULL,
  `Bdate` date DEFAULT NULL,
  `Address` varchar(30) DEFAULT NULL,
  `Sex` char(1) DEFAULT NULL,
  `Salary` decimal(6,0) DEFAULT NULL,
  `Super_ssn` char(9) DEFAULT NULL,
  `Dno` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `EMPLOYEE`
--

INSERT INTO `EMPLOYEE` (`Employee_id`, `Fname`, `Minit`, `Lname`, `username`, `password`, `Ssn`, `Bdate`, `Address`, `Sex`, `Salary`, `Super_ssn`, `Dno`) VALUES
(1, 'Peter', 'B', 'Parker', 'employee.demo', 'DUMMY_NOT_FOR_LOGIN', '000000000', '1900-01-01', 'Fictional Address', 'M', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ORDER_HISTORY`
--

CREATE TABLE `ORDER_HISTORY` (
  `ID` int(11) NOT NULL,
  `CLIENT_ID` varchar(50) NOT NULL,
  `PRODUCT_NAME` varchar(100) NOT NULL,
  `PRODUCT_PRICE` decimal(10,2) NOT NULL,
  `ORDER_DATE` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ORDER_HISTORY`
--

INSERT INTO `ORDER_HISTORY` (`ID`, `CLIENT_ID`, `PRODUCT_NAME`, `PRODUCT_PRICE`, `ORDER_DATE`) VALUES
(1, 'miles.demo', 'Demo Web Cartridge', 25.00, '2000-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `PRODUCT`
--

CREATE TABLE `PRODUCT` (
  `Product_ID` int(11) NOT NULL,
  `Product_Stock` int(11) DEFAULT NULL,
  `Product_Name` varchar(255) NOT NULL,
  `Category_ID` int(11) DEFAULT NULL,
  `Product_Price` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PRODUCT`
--

INSERT INTO `PRODUCT` (`Product_ID`, `Product_Stock`, `Product_Name`, `Category_ID`, `Product_Price`) VALUES
(1, 10, 'Demo T-Shirt', 1, 20),
(2, 10, 'Demo Web Cartridge', 2, 25),
(3, 10, 'Demo Poster', 3, 15);

-- --------------------------------------------------------

--
-- Table structure for table `PRODUCT_CATEGORY`
--

CREATE TABLE `PRODUCT_CATEGORY` (
  `Category_ID` int(11) NOT NULL,
  `Category` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PRODUCT_CATEGORY`
--

INSERT INTO `PRODUCT_CATEGORY` (`Category_ID`, `Category`) VALUES
(1, 'Apparel'),
(2, 'Accessories'),
(3, 'Collectibles');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CART`
--
ALTER TABLE `CART`
  ADD PRIMARY KEY (`Cart_ID`);

--
-- Indexes for table `CART_INFO`
--
ALTER TABLE `CART_INFO`
  ADD PRIMARY KEY (`CART_INFO_ID`),
  ADD KEY `CART_ID` (`CART_ID`),
  ADD KEY `PRODUCT_ID` (`PRODUCT_ID`);

--
-- Indexes for table `CLIENT`
--
ALTER TABLE `CLIENT`
  ADD PRIMARY KEY (`Client_ID`);

--
-- Indexes for table `EMPLOYEE`
--
ALTER TABLE `EMPLOYEE`
  ADD PRIMARY KEY (`Employee_id`);

--
-- Indexes for table `ORDER_HISTORY`
--
ALTER TABLE `ORDER_HISTORY`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `PRODUCT`
--
ALTER TABLE `PRODUCT`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `PRODUCT_CATEGORY`
--
ALTER TABLE `PRODUCT_CATEGORY`
  ADD PRIMARY KEY (`Category_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CART`
--
ALTER TABLE `CART`
  MODIFY `Cart_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CART_INFO`
--
ALTER TABLE `CART_INFO`
  MODIFY `CART_INFO_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CLIENT`
--
ALTER TABLE `CLIENT`
  MODIFY `Client_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `EMPLOYEE`
--
ALTER TABLE `EMPLOYEE`
  MODIFY `Employee_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ORDER_HISTORY`
--
ALTER TABLE `ORDER_HISTORY`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `PRODUCT`
--
ALTER TABLE `PRODUCT`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `PRODUCT_CATEGORY`
--
ALTER TABLE `PRODUCT_CATEGORY`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;