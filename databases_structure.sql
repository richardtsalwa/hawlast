-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 28, 2026 at 08:46 AM
-- Server version: 10.11.19-MariaDB
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `n2a33d5_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

CREATE TABLE `affiliates` (
  `affid` mediumint(4) UNSIGNED NOT NULL,
  `fname` varchar(35) NOT NULL,
  `surname` varchar(35) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `access_level` int(2) NOT NULL,
  `user_status` varchar(15) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `affiliates_sales`
--

CREATE TABLE `affiliates_sales` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `affid` mediumint(5) UNSIGNED NOT NULL,
  `product` varchar(60) NOT NULL,
  `date` date NOT NULL,
  `price` mediumint(5) NOT NULL,
  `verify` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airtime_commission`
--

CREATE TABLE `airtime_commission` (
  `idac` int(10) UNSIGNED NOT NULL,
  `mpesaId` int(10) UNSIGNED NOT NULL,
  `affiliate` varchar(4) NOT NULL,
  `amount` mediumint(6) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airtime_saas`
--

CREATE TABLE `airtime_saas` (
  `saasid` int(10) UNSIGNED NOT NULL,
  `saas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airtime_transactions`
--

CREATE TABLE `airtime_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `airtimeid` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `phone` char(13) NOT NULL,
  `amount` smallint(6) NOT NULL,
  `discount` decimal(6,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `requestid` varchar(100) NOT NULL,
  `TransID` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airtime_user`
--

CREATE TABLE `airtime_user` (
  `airtimeid` int(11) UNSIGNED NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `idno` int(12) UNSIGNED NOT NULL,
  `phonea` varchar(15) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `credit` char(4) NOT NULL,
  `password` char(80) NOT NULL,
  `refererid` int(11) NOT NULL,
  `creditlimit` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `signup` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dservices`
--

CREATE TABLE `dservices` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `service_name` varchar(30) NOT NULL,
  `service_cost` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesaapi`
--

CREATE TABLE `mpesaapi` (
  `Auto` mediumint(9) NOT NULL,
  `TransactionType` varchar(40) NOT NULL,
  `TransID` varchar(40) NOT NULL,
  `TransTime` varchar(40) NOT NULL,
  `TransAmount` double NOT NULL,
  `BusinessShortCode` varchar(15) NOT NULL,
  `BillRefNumber` varchar(40) NOT NULL,
  `CheckoutRequestID` varchar(100) DEFAULT NULL,
  `MerchantRequestID` varchar(100) DEFAULT NULL,
  `ResultCode` int(11) DEFAULT NULL,
  `ResultDesc` varchar(255) DEFAULT NULL,
  `InvoiceNumber` varchar(40) NOT NULL,
  `ThirdPartyTransID` varchar(40) NOT NULL,
  `MSISDN` varchar(20) NOT NULL,
  `FirstName` varchar(60) NOT NULL,
  `MiddleName` varchar(60) NOT NULL,
  `LastName` varchar(60) NOT NULL,
  `OrgAccountBalance` mediumint(7) UNSIGNED NOT NULL,
  `done` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `confirmation_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesaapisafi`
--

CREATE TABLE `mpesaapisafi` (
  `Auto` mediumint(9) NOT NULL,
  `TransactionType` varchar(40) NOT NULL,
  `TransID` varchar(40) NOT NULL,
  `TransTime` varchar(40) NOT NULL,
  `TransAmount` double NOT NULL,
  `BusinessShortCode` varchar(15) NOT NULL,
  `BillRefNumber` varchar(40) NOT NULL,
  `InvoiceNumber` varchar(40) NOT NULL,
  `ThirdPartyTransID` varchar(40) NOT NULL,
  `MSISDN` varchar(20) NOT NULL,
  `FirstName` varchar(60) NOT NULL,
  `MiddleName` varchar(60) NOT NULL,
  `LastName` varchar(60) NOT NULL,
  `OrgAccountBalance` mediumint(7) UNSIGNED NOT NULL,
  `done` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `renewals`
--

CREATE TABLE `renewals` (
  `id` smallint(4) UNSIGNED NOT NULL,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `service` varchar(50) NOT NULL,
  `cost` mediumint(14) UNSIGNED NOT NULL,
  `url` varchar(100) NOT NULL,
  `signup_date` date NOT NULL,
  `renewal_date` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `emailalt` varchar(100) DEFAULT NULL,
  `phone` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `renewalsprepaid`
--

CREATE TABLE `renewalsprepaid` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `renewalsid` smallint(5) UNSIGNED NOT NULL,
  `prepaid` smallint(5) UNSIGNED NOT NULL,
  `TransID` varchar(12) NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `sms_logsID` int(12) NOT NULL,
  `receiver` char(13) NOT NULL,
  `smsuserID` int(7) NOT NULL,
  `message` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `messageID` char(38) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cost` tinyint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_settings`
--

CREATE TABLE `sms_settings` (
  `settid` int(11) UNSIGNED NOT NULL,
  `as_username` varchar(50) NOT NULL,
  `as_key` varchar(250) NOT NULL,
  `airtimeid` int(11) UNSIGNED DEFAULT 0,
  `system_name` varchar(100) NOT NULL,
  `security_2_factor` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_users`
--

CREATE TABLE `sms_users` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `access_level` int(2) NOT NULL,
  `user_status` varchar(15) NOT NULL,
  `bal` smallint(6) NOT NULL,
  `airtimeid` mediumint(9) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` smallint(4) UNSIGNED NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `lastname` varchar(25) DEFAULT NULL,
  `gross` mediumint(14) UNSIGNED NOT NULL,
  `loan` mediumint(5) UNSIGNED NOT NULL,
  `pin` varchar(13) NOT NULL,
  `idnumber` mediumint(13) UNSIGNED NOT NULL,
  `title` varchar(70) NOT NULL,
  `signup_date` date NOT NULL,
  `email` varchar(35) NOT NULL,
  `emailalt` varchar(35) DEFAULT '',
  `phone` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `user_access_level` int(1) NOT NULL DEFAULT 0,
  `user_status` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD PRIMARY KEY (`affid`);

--
-- Indexes for table `affiliates_sales`
--
ALTER TABLE `affiliates_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `airtime_commission`
--
ALTER TABLE `airtime_commission`
  ADD PRIMARY KEY (`idac`);

--
-- Indexes for table `airtime_saas`
--
ALTER TABLE `airtime_saas`
  ADD PRIMARY KEY (`saasid`);

--
-- Indexes for table `airtime_transactions`
--
ALTER TABLE `airtime_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `airtime_user`
--
ALTER TABLE `airtime_user`
  ADD PRIMARY KEY (`airtimeid`),
  ADD UNIQUE KEY `id` (`airtimeid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `dservices`
--
ALTER TABLE `dservices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mpesaapi`
--
ALTER TABLE `mpesaapi`
  ADD PRIMARY KEY (`Auto`),
  ADD UNIQUE KEY `TransID` (`TransID`),
  ADD KEY `idx_checkout_id` (`CheckoutRequestID`);

--
-- Indexes for table `mpesaapisafi`
--
ALTER TABLE `mpesaapisafi`
  ADD PRIMARY KEY (`Auto`);

--
-- Indexes for table `renewals`
--
ALTER TABLE `renewals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `renewalsprepaid`
--
ALTER TABLE `renewalsprepaid`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `TransID` (`TransID`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`sms_logsID`);

--
-- Indexes for table `sms_settings`
--
ALTER TABLE `sms_settings`
  ADD PRIMARY KEY (`settid`),
  ADD KEY `user_id` (`airtimeid`);

--
-- Indexes for table `sms_users`
--
ALTER TABLE `sms_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliates`
--
ALTER TABLE `affiliates`
  MODIFY `affid` mediumint(4) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `affiliates_sales`
--
ALTER TABLE `affiliates_sales`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airtime_commission`
--
ALTER TABLE `airtime_commission`
  MODIFY `idac` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airtime_saas`
--
ALTER TABLE `airtime_saas`
  MODIFY `saasid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airtime_transactions`
--
ALTER TABLE `airtime_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airtime_user`
--
ALTER TABLE `airtime_user`
  MODIFY `airtimeid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dservices`
--
ALTER TABLE `dservices`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesaapi`
--
ALTER TABLE `mpesaapi`
  MODIFY `Auto` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesaapisafi`
--
ALTER TABLE `mpesaapisafi`
  MODIFY `Auto` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `renewals`
--
ALTER TABLE `renewals`
  MODIFY `id` smallint(4) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `renewalsprepaid`
--
ALTER TABLE `renewalsprepaid`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `sms_logsID` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_settings`
--
ALTER TABLE `sms_settings`
  MODIFY `settid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_users`
--
ALTER TABLE `sms_users`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` smallint(4) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
