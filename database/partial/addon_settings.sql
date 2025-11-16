-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 07, 2023 at 07:05 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demandium_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addon_settings`
--

CREATE TABLE `addon_settings` (
  `id` char(36) NOT NULL,
  `key_name` varchar(191) DEFAULT NULL,
  `live_values` longtext DEFAULT NULL,
  `test_values` longtext DEFAULT NULL,
  `settings_type` varchar(255) DEFAULT NULL,
  `mode` varchar(20) NOT NULL DEFAULT 'live',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addon_settings`
--

INSERT INTO `addon_settings` (`id`, `key_name`, `live_values`, `test_values`, `settings_type`, `mode`, `is_active`, `created_at`, `updated_at`, `additional_data`) VALUES
-- SMS Gateways (3)
('070c6bbd-d777-11ed-96f4-0c7a158e4469', 'twilio', '{"gateway":"twilio","mode":"live","status":"0","sid":"data","messaging_service_sid":"data","token":"data","from":"data","otp_template":"data"}', '{"gateway":"twilio","mode":"live","status":"0","sid":"data","messaging_service_sid":"data","token":"data","from":"data","otp_template":"data"}', 'sms_config', 'live', 0, NULL, '2023-08-12 07:01:29', NULL),
('18210f2b-d776-11ed-96f4-0c7a158e4469', 'nexmo', '{"gateway":"nexmo","mode":"live","status":"0","api_key":"","api_secret":"","token":"","from":"","otp_template":""}', '{"gateway":"nexmo","mode":"live","status":"0","api_key":"","api_secret":"","token":"","from":"","otp_template":""}', 'sms_config', 'live', 0, NULL, '2023-04-10 02:14:44', NULL),
('1821029f-d776-11ed-96f4-0c7a158e4469', 'msg91', '{"gateway":"msg91","mode":"live","status":"0","template_id":"data","auth_key":"data"}', '{"gateway":"msg91","mode":"live","status":"0","template_id":"data","auth_key":"data"}', 'sms_config', 'live', 0, NULL, '2023-08-12 07:01:48', NULL),

-- Payment Gateways (6)
('101befdf-d44b-11ed-8564-0c7a158e4469', 'paypal', '{"gateway":"paypal","mode":"test","status":"0","client_id":"data","client_secret":"data"}', '{"gateway":"paypal","mode":"test","status":"0","client_id":"data","client_secret":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-30 03:41:32', '{"gateway_title":null,"gateway_image":""}'),
('3201d2e6-c937-11ed-a424-0c7a158e4469', 'amazon_pay', '{"gateway":"amazon_pay","mode":"test","status":"0","pass_phrase":"data","access_code":"data","merchant_identifier":"data"}', '{"gateway":"amazon_pay","mode":"test","status":"0","pass_phrase":"data","access_code":"data","merchant_identifier":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-12 06:36:07', '{"gateway_title":null,"gateway_image":""}'),
('544a24a4-c872-11ed-ac7a-0c7a158e4469', 'fatoorah', '{"gateway":"fatoorah","mode":"test","status":"0","api_key":"data"}', '{"gateway":"fatoorah","mode":"test","status":"0","api_key":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-12 06:36:24', '{"gateway_title":null,"gateway_image":""}'),
('5e2d2ef9-d6ab-11ed-962c-0c7a158e4469', 'thawani', '{"gateway":"thawani","mode":"test","status":"0","public_key":"data","private_key":"data"}', '{"gateway":"thawani","mode":"test","status":"0","public_key":"data","private_key":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-30 04:50:40', '{"gateway_title":null,"gateway_image":"2023-04-13-64378f9856f29.png"}'),
('998ccc62-d6a0-11ed-962c-0c7a158e4469', 'stripe', '{"gateway":"stripe","mode":"test","status":"0","api_key":"data","published_key":"data"}', '{"gateway":"stripe","mode":"test","status":"0","api_key":"data","published_key":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-30 04:18:55', '{"gateway_title":null,"gateway_image":"2023-08-10-64d4bc2bb983a.png"}'),
('dc0f5fc9-d6a5-11ed-962c-0c7a158e4469', 'worldpay', '{"gateway":"worldpay","mode":"test","status":"0","OrgUnitId":"data","jwt_issuer":"data","mac":"data","merchantCode":"data","xml_password":"data"}', '{"gateway":"worldpay","mode":"test","status":"0","OrgUnitId":"data","jwt_issuer":"data","mac":"data","merchantCode":"data","xml_password":"data"}', 'payment_config', 'test', 0, NULL, '2023-08-12 06:35:26', '{"gateway_title":null,"gateway_image":""}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addon_settings`
--
ALTER TABLE `addon_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_settings_id_index` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
