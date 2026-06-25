-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manufacturingerp`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `total_hours` decimal(4,2) DEFAULT 0.00,
  `status` enum('present','absent','half_day','leave') DEFAULT 'present',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bom`
--

CREATE TABLE `bom` (
  `id` int(11) NOT NULL,
  `bom_number` varchar(20) NOT NULL,
  `finished_product_id` int(11) NOT NULL,
  `version` varchar(10) DEFAULT '1.0',
  `effective_date` date DEFAULT NULL,
  `status` enum('draft','active','inactive') DEFAULT 'draft',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` text DEFAULT NULL,
  `total_cost` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bom_items`
--

CREATE TABLE `bom_items` (
  `id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(20) DEFAULT 'PCS',
  `waste_percentage` decimal(5,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_type` enum('raw_material','packaging','finished_goods','waste') DEFAULT 'raw_material',
  `created_by` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `description`, `parent_id`, `status`, `created_at`, `updated_at`, `category_type`, `created_by`) VALUES
(1, 'Raw Materials', 'Raw materials used in manufacturing', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'raw_material', 1),
(2, 'Finished Goods', 'Completed products ready for sale', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'raw_material', 1),
(3, 'Semi-Finished', 'Partially completed products', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'raw_material', 1),
(4, 'Packaging', 'Packaging materials', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'raw_material', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_code` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pan_number` varchar(20) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `return_policy` text DEFAULT NULL,
  `debit_note_config` text DEFAULT NULL,
  `sales_zone` varchar(50) DEFAULT NULL,
  `sales_region` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `customer_name`, `contact_person`, `email`, `website`, `phone`, `address`, `city`, `state`, `pincode`, `gst_number`, `credit_limit`, `status`, `created_at`, `updated_at`, `pan_number`, `payment_terms`, `return_policy`, `debit_note_config`, `sales_zone`, `sales_region`, `created_by`) VALUES
(5, '0001', 'rupesh sahu', 'rupesh sahu', 'sahu.rupesh54@gmail.com', '', '0895917644', 'Ranjhi', 'Jabalpur', 'Madhya Pradesh', '482011', '8877AA55440025', 0.00, 'active', '2025-08-17 05:08:40', '2025-08-17 05:08:40', 'ABCDE1234F', '', '', 'allowed', '', '', 1),
(6, '0002', 'rupesh sahu', 'rupesh sahu', 'sahu.rupesh54@gmail.com', '', '0895917644', 'Ranjhi', 'Jabalpur', 'Madhya Pradesh', '482011', '', 1000000.00, 'active', '2025-08-17 05:27:07', '2025-08-17 05:27:07', '', '', '', 'allowed', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` int(11) NOT NULL,
  `payment_number` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','bank_transfer','cheque','credit_card','online') NOT NULL DEFAULT 'cash',
  `reference_number` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `customer_payments`
--

INSERT INTO `customer_payments` (`id`, `payment_number`, `customer_id`, `invoice_id`, `payment_date`, `payment_amount`, `payment_method`, `reference_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PAY2026020001', 5, 2, '2026-02-06', 214760.00, 'bank_transfer', '745896', '', 1, '2026-02-06 08:09:14', '2026-02-06 08:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Production', 'Manufacturing and production operations', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(2, 'Quality Control', 'Quality assurance and control operations', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(3, 'Sales', 'Sales and customer relations', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(4, 'Finance', 'Financial management and accounting', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(5, 'Human Resources', 'HR management and employee relations', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(6, 'IT', 'Information technology and systems', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13'),
(7, 'Logistics', 'Supply chain and logistics management', 'active', 1, NULL, '2025-08-23 10:51:13', '2025-08-23 10:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_items`
--

CREATE TABLE `dispatch_items` (
  `id` int(11) NOT NULL,
  `dn_id` int(11) NOT NULL,
  `so_item_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_notes`
--

CREATE TABLE `dispatch_notes` (
  `id` int(11) NOT NULL,
  `dn_number` varchar(20) NOT NULL,
  `so_id` int(11) NOT NULL,
  `dispatch_date` date NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `transporter` varchar(100) DEFAULT NULL,
  `eway_bill_number` varchar(20) DEFAULT NULL,
  `lr_number` varchar(20) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('draft','dispatched','delivered') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_notes`
--

CREATE TABLE `goods_receipt_notes` (
  `id` int(11) NOT NULL,
  `grn_number` varchar(20) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `receipt_date` date NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `transporter` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('draft','received','verified','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn_items`
--

CREATE TABLE `grn_items` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `po_item_id` int(11) DEFAULT NULL,
  `received_quantity` decimal(10,2) NOT NULL,
  `accepted_quantity` decimal(10,2) NOT NULL,
  `rejected_quantity` decimal(10,2) DEFAULT 0.00,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `so_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `so_id`, `customer_id`, `invoice_date`, `due_date`, `subtotal`, `gst_amount`, `total_amount`, `paid_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'INV2025120001', NULL, 5, '2025-12-21', '2026-01-27', 182000.00, 32760.00, 214760.00, 214760.00, 'paid', 1, '2025-12-21 12:34:24', '2026-02-06 02:39:14'),
(3, 'INV2025120002', NULL, 5, '2025-12-26', '2026-01-25', 88540.00, 15937.20, 104477.20, 0.00, 'paid', 1, '2025-12-26 18:27:41', '2025-12-26 18:27:41');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_id`, `quantity`, `unit_price`, `gst_rate`, `gst_amount`, `total_amount`) VALUES
(1, 2, 14, 52.00, 3500.00, 18.00, 32760.00, 214760.00),
(2, 3, 14, 25.00, 3500.00, 18.00, 15750.00, 103250.00),
(3, 3, 25, 52.00, 20.00, 18.00, 187.20, 1227.20);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `cost_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `min_stock` decimal(10,2) DEFAULT 0.00,
  `max_stock` decimal(10,2) DEFAULT 0.00,
  `reorder_level` decimal(10,2) DEFAULT 0.00,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `material_type` enum('raw_material','packaging','finished_goods','waste') NOT NULL,
  `waste_percentage` decimal(5,2) DEFAULT 0.00,
  `is_recyclable` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `preferred_supplier_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `unit_of_measure` varchar(20) DEFAULT 'PCS',
  `weight` decimal(10,3) DEFAULT 0.000,
  `dimensions` varchar(100) DEFAULT NULL,
  `shelf_life_days` int(11) DEFAULT NULL,
  `storage_conditions` text DEFAULT NULL,
  `hazardous` tinyint(1) DEFAULT 0,
  `barcode` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_code`, `item_name`, `category_id`, `description`, `unit`, `cost_price`, `selling_price`, `min_stock`, `max_stock`, `reorder_level`, `gst_rate`, `material_type`, `waste_percentage`, `is_recyclable`, `status`, `created_at`, `updated_at`, `preferred_supplier_id`, `supplier_id`, `unit_of_measure`, `weight`, `dimensions`, `shelf_life_days`, `storage_conditions`, `hazardous`, `barcode`, `sku`) VALUES
(1, 'ITM001', 'Steel Rod 12mm', 1, 'Raw material for production', 'KG', 50.00, 60.00, 100.00, 500.00, 150.00, 18.00, 'raw_material', 5.00, 1, 'active', '2025-09-02 05:31:07', '2025-09-02 05:31:07', NULL, NULL, 'PCS', 0.000, NULL, NULL, NULL, 0, NULL, NULL),
(2, 'ITM002', 'Plastic Container 1L', 2, 'Packaging material', 'PCS', 25.00, 30.00, 50.00, 200.00, 75.00, 18.00, 'packaging', 2.00, 1, 'active', '2025-09-02 05:31:07', '2025-09-02 05:31:07', NULL, NULL, 'PCS', 0.000, NULL, NULL, NULL, 0, NULL, NULL),
(3, 'ITM003', 'Finished Product A', 3, 'Final product ready for sale', 'PCS', 100.00, 150.00, 20.00, 100.00, 30.00, 18.00, 'finished_goods', 0.00, 0, 'active', '2025-09-02 05:31:07', '2025-09-02 05:31:07', NULL, NULL, 'PCS', 0.000, NULL, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-08-23-051950', 'App\\Database\\Migrations\\CreateDepartmentsTable', 'default', 'App', 1755926417, 1),
(2, '2025-08-23-052025', 'App\\Database\\Migrations\\UpdateEmployeesTableDepartmentField', 'default', 'App', 1755926444, 2),
(3, '2025-08-24-092100', 'App\\Database\\Migrations\\AddIsUrgentToPurchaseOrders', 'default', 'App', 1756007502, 3),
(4, '2025-08-24-092200', 'App\\Database\\Migrations\\UpdatePurchaseOrdersTableStructure', 'default', 'App', 1756007554, 4),
(5, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateSalesReturnsTable', 'default', 'App', 1756271057, 5),
(6, '2024-01-01-000002', 'App\\Database\\Migrations\\CreateSalesReturnItemsTable', 'default', 'App', 1756271057, 5),
(7, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateCustomerPaymentsTable', 'default', 'App', 1756271058, 5),
(8, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateQuotationsTable', 'default', 'App', 1756271058, 5),
(9, '2024-01-01-000005', 'App\\Database\\Migrations\\CreateQuotationItemsTable', 'default', 'App', 1756271059, 5);

-- --------------------------------------------------------

--
-- Table structure for table `production_tracking`
--

CREATE TABLE `production_tracking` (
  `id` int(11) NOT NULL,
  `wo_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `shift` enum('morning','afternoon','night') DEFAULT 'morning',
  `quantity_produced` decimal(10,2) DEFAULT 0.00,
  `quantity_rejected` decimal(10,2) DEFAULT 0.00,
  `machine_hours` decimal(5,2) DEFAULT 0.00,
  `labor_hours` decimal(5,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'PCS',
  `hsn_code` varchar(20) DEFAULT NULL,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `cgst_rate` decimal(5,2) DEFAULT 9.00,
  `sgst_rate` decimal(5,2) DEFAULT 9.00,
  `igst_rate` decimal(5,2) DEFAULT 18.00,
  `cost_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `min_stock` decimal(10,2) DEFAULT 0.00,
  `max_stock` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reorder_level` decimal(10,2) DEFAULT 0.00,
  `material_type` enum('raw_material','packaging','finished_goods','waste') DEFAULT 'raw_material',
  `waste_percentage` decimal(5,2) DEFAULT 0.00,
  `is_recyclable` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT 1,
  `unit_price` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_name`, `description`, `category_id`, `unit`, `hsn_code`, `gst_rate`, `cgst_rate`, `sgst_rate`, `igst_rate`, `cost_price`, `selling_price`, `min_stock`, `max_stock`, `status`, `created_at`, `updated_at`, `reorder_level`, `material_type`, `waste_percentage`, `is_recyclable`, `created_by`, `unit_price`) VALUES
(1, 'P2025010001', 'Raw Steel Sheets', 'High-quality steel sheets for manufacturing', NULL, 'KG', '7208', 18.00, 9.00, 9.00, 18.00, 85.50, 95.00, 1000.00, 5000.00, 'active', '2025-08-02 14:14:16', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 85.50),
(2, 'P2025010002', 'Aluminum Alloy', 'Premium aluminum alloy for casting', NULL, 'KG', '7601', 18.00, 9.00, 9.00, 18.00, 120.00, 135.00, 500.00, 2500.00, 'active', '2025-08-02 14:14:17', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 120.00),
(3, 'P2025010003', 'Copper Wire', 'Electrical grade copper wire', NULL, 'MTR', '7408', 18.00, 9.00, 9.00, 18.00, 45.00, 52.00, 2000.00, 10000.00, 'active', '2025-08-02 14:14:17', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 45.00),
(4, 'P2025010004', 'Plastic Granules', 'High-density polyethylene granules', NULL, 'KG', '3901', 18.00, 9.00, 9.00, 18.00, 65.00, 75.00, 800.00, 4000.00, 'active', '2025-08-02 14:14:17', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 65.00),
(5, 'P2025010005', 'Electronic Components', 'Assorted electronic components kit', NULL, 'PCS', '8544', 18.00, 9.00, 9.00, 18.00, 25.00, 30.00, 500.00, 2500.00, 'active', '2025-08-02 14:14:17', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 25.00),
(6, 'P2025010006', 'Industrial Bearings', 'High-precision industrial bearings', NULL, 'PCS', '8482', 18.00, 9.00, 9.00, 18.00, 150.00, 180.00, 100.00, 500.00, 'active', '2025-08-02 14:14:17', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 150.00),
(7, 'P2025010007', 'Safety Equipment', 'Industrial safety equipment set', NULL, 'SET', '9020', 18.00, 9.00, 9.00, 18.00, 250.00, 300.00, 50.00, 200.00, 'active', '2025-08-02 14:14:18', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 250.00),
(8, 'P2025010008', 'Lubricating Oil', 'Industrial grade lubricating oil', NULL, 'LTR', '2710', 18.00, 9.00, 9.00, 18.00, 85.00, 95.00, 200.00, 1000.00, 'active', '2025-08-02 14:14:18', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 85.00),
(9, 'P001', 'Raw Material A', 'High quality raw material', NULL, 'KG', NULL, 18.00, 9.00, 9.00, 18.00, 150.00, 0.00, 0.00, 0.00, 'active', '2025-08-03 12:34:42', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 150.00),
(10, 'P002', 'Component B', 'Electronic component', NULL, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 25.50, 0.00, 0.00, 0.00, 'active', '2025-08-03 12:34:42', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 25.50),
(11, 'P003', 'Tool C', 'Manufacturing tool', NULL, 'SET', NULL, 18.00, 9.00, 9.00, 18.00, 500.00, 0.00, 0.00, 0.00, 'active', '2025-08-03 12:34:42', '2025-08-06 00:55:37', 0.00, 'raw_material', 0.00, 0, 1, 500.00),
(12, 'FG001', 'Industrial Motor Assembly', 'Complete industrial motor with housing and mounting brackets', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 2800.00, 0.00, 0.00, 'active', '2025-08-06 04:15:48', '2025-08-27 16:56:00', 0.00, 'finished_goods', 0.00, 0, 1, 2500.00),
(13, 'FG002', 'Hydraulic Pump Unit', 'High-pressure hydraulic pump with control valves', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 2000.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-27 16:56:00', 0.00, 'finished_goods', 0.00, 0, 1, 1800.00),
(14, 'FG003', 'Electronic Control Panel', 'Programmable control panel with touch screen interface', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 3500.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-27 16:56:00', 0.00, 'finished_goods', 0.00, 0, 1, 3200.00),
(15, 'FG004', 'Conveyor Belt System', 'Automated conveyor belt with motor and sensors', 1, 'SET', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 5000.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-27 16:49:33', 0.00, 'finished_goods', 0.00, 0, 1, 4500.00),
(16, 'FG005', 'Welding Machine', 'Industrial welding machine with safety features', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 4200.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-27 16:56:00', 0.00, 'finished_goods', 0.00, 0, 1, 3800.00),
(17, 'PK001', 'Cardboard Boxes', 'Heavy-duty cardboard boxes for industrial products', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-06 04:15:49', 0.00, 'packaging', 0.00, 0, 1, 25.00),
(18, 'PK002', 'Bubble Wrap', 'Protective bubble wrap for fragile items', 1, 'ROLL', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-06 04:15:49', 0.00, 'packaging', 0.00, 0, 1, 45.00),
(19, 'PK003', 'Wooden Pallets', 'Standard wooden pallets for heavy machinery', 1, 'PCS', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-06 04:15:49', 0.00, 'packaging', 0.00, 0, 1, 120.00),
(20, 'PK004', 'Plastic Strapping', 'Heavy-duty plastic strapping for securing loads', 1, 'ROLL', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-06 04:15:49', 0.00, 'packaging', 0.00, 0, 1, 35.00),
(21, 'PK005', 'Foam Inserts', 'Custom foam inserts for product protection', 1, 'SET', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-06 04:15:49', '2025-08-06 04:15:49', 0.00, 'packaging', 0.00, 0, 1, 85.00),
(22, 'RMR0001', 'Rawmaterial 15', '', 1, 'kg', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-17 03:16:20', '2025-08-17 03:16:41', 0.00, 'raw_material', 1.00, 0, 1, 100.00),
(23, 'RMR0002', 'Rawmaterial 1', '', 1, 'kg', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-18 03:36:30', '2025-08-18 03:36:30', 1.00, 'raw_material', 1.00, 0, 1, 1.00),
(24, 'FGFG0001', 'Finished Good 1', '', 2, 'pcs', NULL, 18.00, 9.00, 9.00, 18.00, 0.00, 0.00, 0.00, 0.00, 'active', '2025-08-18 03:42:42', '2025-08-18 03:42:42', 500.00, 'finished_goods', 1.00, 0, 1, 25.00),
(25, 'FGFG0002', 'Finished Good 2', '', 2, 'l', '', 18.00, 9.00, 9.00, 0.00, 0.00, 20.00, 0.00, 0.00, 'active', '2025-08-27 16:08:43', '2025-08-28 04:47:32', 0.00, 'finished_goods', 0.00, 0, 1, 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_bills`
--

CREATE TABLE `purchase_bills` (
  `id` int(11) NOT NULL,
  `bill_number` varchar(20) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','received','paid','overdue','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_bills`
--

INSERT INTO `purchase_bills` (`id`, `bill_number`, `po_id`, `supplier_id`, `bill_date`, `due_date`, `invoice_number`, `subtotal`, `gst_amount`, `total_amount`, `paid_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'BILL2025120001', NULL, 1, '2025-12-29', NULL, '', 13000.00, 2340.00, 15340.00, 0.00, 'draft', 1, '2025-12-29 00:07:19', '2025-12-29 00:07:19'),
(2, 'BILL2025120002', NULL, 3, '2025-12-29', NULL, '', 2595.00, 467.10, 3062.10, 0.00, 'draft', 1, '2025-12-29 00:35:02', '2025-12-29 00:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_bill_items`
--

CREATE TABLE `purchase_bill_items` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `cgst_rate` decimal(5,2) DEFAULT 9.00,
  `sgst_rate` decimal(5,2) DEFAULT 9.00,
  `igst_rate` decimal(5,2) DEFAULT 18.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_bill_items`
--

INSERT INTO `purchase_bill_items` (`id`, `bill_id`, `product_id`, `quantity`, `unit_price`, `gst_rate`, `cgst_rate`, `sgst_rate`, `igst_rate`, `gst_amount`, `total_amount`) VALUES
(1, 1, 7, 52.00, 250.00, 18.00, 9.00, 9.00, 18.00, 2340.00, 15340.00),
(2, 2, 2, 21.00, 120.00, 18.00, 9.00, 9.00, 18.00, 453.60, 2973.60),
(3, 2, 5, 3.00, 25.00, 18.00, 9.00, 9.00, 18.00, 13.50, 88.50);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `po_number` varchar(20) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `pr_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `expected_date` date NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','sent','confirmed','received','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_urgent` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag to mark purchase order as urgent',
  `payment_terms` varchar(255) DEFAULT NULL COMMENT 'Payment terms for the purchase order',
  `notes` text DEFAULT NULL COMMENT 'Additional notes for the purchase order',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount amount applied to the order',
  `updated_by` int(11) DEFAULT NULL COMMENT 'User ID who last updated the record',
  `approved_by` int(11) DEFAULT NULL COMMENT 'User ID who approved the purchase order',
  `ordered_by` int(11) DEFAULT NULL COMMENT 'User ID who placed the order',
  `received_by` int(11) DEFAULT NULL COMMENT 'User ID who received the goods',
  `cancelled_by` int(11) DEFAULT NULL COMMENT 'User ID who cancelled the order',
  `approved_at` datetime DEFAULT NULL COMMENT 'Timestamp when the order was approved',
  `ordered_at` datetime DEFAULT NULL COMMENT 'Timestamp when the order was placed',
  `received_at` datetime DEFAULT NULL COMMENT 'Timestamp when the goods were received',
  `cancelled_at` datetime DEFAULT NULL COMMENT 'Timestamp when the order was cancelled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `pr_id`, `order_date`, `expected_date`, `delivery_address`, `terms_conditions`, `subtotal`, `tax_amount`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`, `is_urgent`, `payment_terms`, `notes`, `discount_amount`, `updated_by`, `approved_by`, `ordered_by`, `received_by`, `cancelled_by`, `approved_at`, `ordered_at`, `received_at`, `cancelled_at`) VALUES
(9, 'PO2025120001', 3, NULL, '2025-12-20', '2025-12-27', 'Toss solution Jabalpur', 'i want fast deliveri', 240000.00, 43200.00, 283200.00, 'draft', 1, '2025-12-20 17:52:16', '2025-12-20 17:52:16', 1, 'immediate', 'krishna ', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_id`, `product_id`, `quantity`, `unit_price`, `gst_rate`, `gst_amount`, `total_amount`) VALUES
(1, 9, 14, 30000.00, 8.00, 18.00, 0.00, 240000.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requisitions`
--

CREATE TABLE `purchase_requisitions` (
  `id` int(11) NOT NULL,
  `pr_number` varchar(20) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('draft','pending','approved','rejected','ordered') DEFAULT 'draft',
  `required_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requisitions`
--

INSERT INTO `purchase_requisitions` (`id`, `pr_number`, `requested_by`, `department`, `priority`, `status`, `required_date`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'PR2025120001', 1, 'it', 'urgent', 'draft', '2025-12-26', '', '2025-12-16 17:11:27', '2025-12-16 17:29:46'),
(2, 'PR2025120002', 1, 'it', 'high', 'draft', '2025-12-27', 'kkv', '2025-12-16 17:19:15', '2025-12-16 18:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requisition_items`
--

CREATE TABLE `purchase_requisition_items` (
  `id` int(11) NOT NULL,
  `pr_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requisition_items`
--

INSERT INTO `purchase_requisition_items` (`id`, `pr_id`, `product_id`, `quantity`, `unit_price`, `total_amount`, `remarks`) VALUES
(4, 1, 2, 9.00, 90.00, 810.00, 'k'),
(5, 2, 4, 7.00, 6.00, 42.00, 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` int(11) NOT NULL,
  `return_number` varchar(50) NOT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `return_date` date NOT NULL,
  `status` enum('draft','pending','approved','completed','cancelled') DEFAULT 'draft',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` int(11) NOT NULL,
  `purchase_return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `condition_status` enum('good','damaged','defective') DEFAULT 'good',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `quotation_number` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `quotation_date` date NOT NULL,
  `valid_until` date NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','rejected','expired') NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int(11) NOT NULL,
  `so_number` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','confirmed','in_production','ready','dispatched','delivered','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_number`, `customer_id`, `order_date`, `delivery_date`, `delivery_address`, `payment_terms`, `subtotal`, `gst_amount`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'SO2025120001', 6, '2025-12-21', NULL, NULL, NULL, 5000.00, 0.00, 5000.00, 'draft', 1, '2025-12-21 08:47:58', '2025-12-21 08:47:58'),
(4, 'SO2025120002', 6, '2025-12-29', NULL, NULL, NULL, 2000.00, 0.00, 2000.00, 'draft', 1, '2025-12-29 01:19:34', '2025-12-29 01:19:34'),
(5, 'SO2026020001', 5, '2026-02-04', NULL, NULL, NULL, 5000.00, 0.00, 4400.00, 'draft', 1, '2026-02-04 15:12:40', '2026-02-04 15:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `so_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT 18.00,
  `gst_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `so_id`, `product_id`, `quantity`, `unit_price`, `gst_rate`, `gst_amount`, `total_amount`) VALUES
(1, 3, 15, 1.00, 5000.00, 18.00, 900.00, 5900.00),
(2, 4, 13, 1.00, 2000.00, 18.00, 360.00, 2360.00),
(3, 5, 15, 1.00, 5000.00, 18.00, 792.00, 5192.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

CREATE TABLE `sales_returns` (
  `id` int(11) NOT NULL,
  `return_number` varchar(20) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `return_date` date NOT NULL,
  `return_reason` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','submitted','approved','processed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales_returns`
--

INSERT INTO `sales_returns` (`id`, `return_number`, `invoice_id`, `customer_id`, `return_date`, `return_reason`, `subtotal`, `gst_amount`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SR2026020001', 3, 5, '2026-02-06', 'not seles', 900.00, 162.00, 1062.00, 'approved', 1, '2026-02-06 08:08:28', '2026-02-06 08:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_items`
--

CREATE TABLE `sales_return_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `return_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales_return_items`
--

INSERT INTO `sales_return_items` (`id`, `return_id`, `product_id`, `quantity`, `unit_price`, `line_total`, `return_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 25, 45.00, 20.00, 900.00, '', '2026-02-06 08:08:28', '2026-02-06 08:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `status` enum('available','reserved','damaged','expired') DEFAULT 'available',
  `location` varchar(100) DEFAULT NULL,
  `rack` varchar(50) DEFAULT NULL,
  `bin` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `manufacturing_date` date DEFAULT NULL,
  `source_document` varchar(100) DEFAULT NULL,
  `source_document_id` int(11) DEFAULT NULL,
  `source_type` enum('purchase','production','transfer','adjustment','return') DEFAULT 'purchase',
  `transaction_date` date NOT NULL,
  `transaction_type` enum('in','out','transfer','adjustment') NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `item_id`, `warehouse_id`, `batch_number`, `quantity`, `unit_cost`, `total_cost`, `status`, `location`, `rack`, `bin`, `expiry_date`, `manufacturing_date`, `source_document`, `source_document_id`, `source_type`, `transaction_date`, `transaction_type`, `reference_number`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'BATCH001', 100.00, 50.00, 5000.00, 'available', 'A1', 'R1', 'B1', '2024-12-31', '2024-01-01', 'GRN001', 1, 'purchase', '2024-01-15', 'in', 'STK001', 'Initial stock', '2025-09-02 05:32:44', '2025-09-02 05:32:44'),
(2, 2, 1, 'BATCH002', 52.00, 25.00, 1300.00, 'available', 'A2', 'R1', 'B2', '2025-06-30', '2024-01-01', 'GRN002', 2, 'purchase', '2025-12-24', 'in', 'STK002', 'Packaging stock', '2025-09-02 05:32:45', '2025-12-24 17:54:55'),
(3, 3, 1, 'BATCH003', 25.00, 100.00, 2500.00, 'available', 'A3', 'R2', 'B1', '2025-12-31', '2024-01-01', 'PROD001', 3, 'production', '2025-12-24', 'in', 'STK003', 'Finished goods', '2025-09-02 05:32:45', '2025-12-24 17:55:10'),
(16, 3, 1, NULL, 1.00, 100.00, 100.00, 'available', NULL, NULL, NULL, NULL, NULL, 'QUICK-STOCK-OUT-20251224232510', NULL, 'adjustment', '2025-12-24', 'out', NULL, 'Quick stock out - Removed 1 unit', '2025-12-24 17:55:10', '2025-12-24 17:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL,
  `transfer_code` varchar(50) NOT NULL,
  `from_warehouse_id` int(11) NOT NULL,
  `to_warehouse_id` int(11) NOT NULL,
  `transfer_date` date NOT NULL,
  `status` enum('pending','in_transit','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

CREATE TABLE `stock_transfer_items` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_code` varchar(20) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `supplier_category` enum('raw_material','packaging','service') DEFAULT 'raw_material',
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_ifsc` varchar(20) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT 0.00,
  `return_policy` text DEFAULT NULL,
  `credit_terms` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_code`, `supplier_name`, `contact_person`, `email`, `phone`, `address`, `gst_number`, `pan_number`, `status`, `created_at`, `updated_at`, `supplier_category`, `bank_name`, `bank_account`, `bank_ifsc`, `payment_terms`, `credit_limit`, `return_policy`, `credit_terms`, `created_by`) VALUES
(1, 'MSCENEX3384', 'Ms Bob', 'Bob', 'test@test.com', '9999999999', 'Newyork', '44444444444', '', 'active', '2025-08-05 06:01:19', '2025-08-05 06:01:19', 'raw_material', '', '', '', '30_days', 100000.00, '', '', 1),
(2, 'SUP2025080001', 'RK Supplier', 'rupesh sahu', 'sahu.rupesh54@gmail.com', '0895917644', 'Ranjhi', '8877AA55440025', '', 'active', '2025-08-17 03:04:10', '2025-08-17 03:04:10', 'raw_material', '', '', '', '', 0.00, '', '', 1),
(3, 'SUP2025080002', 'RK Supplier 3', 'rupesh sahu', '', '0895917644', 'Ranjhi', '', '', 'active', '2025-08-21 15:28:36', '2025-08-21 15:28:36', 'raw_material', '', '', '', '7_days', 0.00, '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@manufacturingerp.com', '$2y$10$rL5QGi1efuiJWA6lRlOl0.yEsH4JdzXYvSO2xnagbTBbcK5fLirt.', 'System Administrator', 'super_admin', 'active', '2025-08-02 21:35:44', '2025-07-31 18:19:06', '2025-08-02 16:05:44'),
(3, 'purchase', 'purchase@prodx.com', '$2y$10$Q0/nb61Ci6ktB3GnEJmrLuy.6J4/SaMnkZlmtSn3nMIJRU3FKhLJq', 'Purchase Manager', 'purchase', 'active', NULL, '2025-08-02 14:28:23', '2025-08-02 15:54:56'),
(4, 'sales', 'sales@prodx.com', '$2y$10$K.LtUxsmHFO9ZPhe4YqR0OkypVwNSxOpS2q7T71NFbtawmiHV6kKO', 'Sales Manager', 'sales', 'active', NULL, '2025-08-02 14:28:23', '2025-08-02 15:54:56'),
(5, 'production', 'production@prodx.com', '$2y$10$.Z.bIHKA8aOT5jwWs59zUel3uVVTwW25NfYpbCICvcjEf2XKwigPC', 'Production Manager', 'production', 'active', NULL, '2025-08-02 14:28:24', '2025-08-02 15:54:56'),
(6, 'finance', 'finance@prodx.com', '$2y$10$pj0Pn6OlMrnPCFC3/lkZxuhsDKpSjbqasu5me56kbldjpvddbpUK6', 'Finance Manager', 'finance', 'active', NULL, '2025-08-02 14:28:24', '2025-08-02 15:54:56'),
(7, 'gate_entry', 'gate@prodx.com', '$2y$10$6yd/Cf5uuXSYv/tC5OTjBuMASIS45JVXRehiTdYqhwqtYVffftxfu', 'Gate Entry Officer', 'gate_entry', 'active', NULL, '2025-08-02 14:28:24', '2025-08-02 15:54:56'),
(8, 'hrm', 'hrm@prodx.com', '$2y$10$.3MWfMBEcEN.JPUr04SMgO0Rk8Xt2bn83YkXQT.5IIPTtkHRsl48O', 'HR Manager', 'hrm', 'active', NULL, '2025-08-02 14:28:25', '2025-08-02 15:54:56'),
(9, 'reception', 'reception@prodx.com', '$2y$10$dzmfTrYwKOWg7euzXqQj3.k9fymb0DYip5l0DPMMlTWcXlsEOhOsO', 'Receptionist', 'reception', 'active', NULL, '2025-08-02 14:28:25', '2025-08-02 15:54:56'),
(10, 'manager', 'manager@prodx.com', '$2y$10$14roRuXriq5Q7TQlJmpMY.rjDiPu/FVMinOuFBITmq48UE7KP2yoi', 'Manager', 'manager', 'active', NULL, '2025-08-03 12:34:41', '2025-08-03 12:34:41'),
(11, 'user', 'user@prodx.com', '$2y$10$73HNjY4CuxB9RpsDzWJRC.UfdffsQT.8KfezcWXuFKUZnXK9Xaxmm', 'User', 'user', 'active', NULL, '2025-08-03 12:34:41', '2025-08-03 12:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `warehouse_code` varchar(20) NOT NULL,
  `warehouse_name` varchar(100) NOT NULL,
  `location` text DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `warehouse_type` enum('head_office','factory','branch','distribution_center','retail_store') DEFAULT 'factory',
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'India',
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `capacity_total` decimal(15,2) DEFAULT 0.00,
  `capacity_used` decimal(15,2) DEFAULT 0.00,
  `capacity_unit` varchar(20) DEFAULT 'sqft',
  `in_charge_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `warehouse_code`, `warehouse_name`, `location`, `manager_id`, `status`, `created_at`, `updated_at`, `warehouse_type`, `address`, `city`, `state`, `pincode`, `country`, `contact_person`, `phone`, `email`, `capacity_total`, `capacity_used`, `capacity_unit`, `in_charge_id`, `description`) VALUES
(1, 'WH001', 'Main Warehouse', 'Factory Premises', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'factory', NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, 0.00, 0.00, 'sqft', NULL, NULL),
(2, 'WH002', 'Raw Material Store', 'Factory Premises', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'factory', NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, 0.00, 0.00, 'sqft', NULL, NULL),
(3, 'WH003', 'Finished Goods Store', 'Factory Premises', NULL, 'active', '2025-07-31 18:19:06', '2025-07-31 18:19:06', 'factory', NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, 0.00, 0.00, 'sqft', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `work_orders`
--

CREATE TABLE `work_orders` (
  `id` int(11) NOT NULL,
  `wo_number` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `bom_id` int(11) DEFAULT NULL,
  `planned_quantity` decimal(10,2) NOT NULL,
  `actual_quantity` decimal(10,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('draft','planned','in_progress','completed','cancelled') DEFAULT 'draft',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`employee_id`,`date`);

--
-- Indexes for table `bom`
--
ALTER TABLE `bom`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bom_number` (`bom_number`),
  ADD KEY `product_id` (`finished_product_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_id` (`bom_id`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `fk_categories_created_by` (`created_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`),
  ADD KEY `fk_customers_created_by` (`created_by`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_number` (`payment_number`),
  ADD KEY `customer_payments_created_by_foreign` (`created_by`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `payment_method` (`payment_method`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `dispatch_items`
--
ALTER TABLE `dispatch_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dn_id` (`dn_id`),
  ADD KEY `so_item_id` (`so_item_id`);

--
-- Indexes for table `dispatch_notes`
--
ALTER TABLE `dispatch_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dn_number` (`dn_number`),
  ADD KEY `so_id` (`so_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grn_number` (`grn_number`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `warehouse_id` (`warehouse_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `po_item_id` (`po_item_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `so_id` (`so_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `fk_items_preferred_supplier` (`preferred_supplier_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_tracking`
--
ALTER TABLE `production_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wo_id` (`wo_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_products_created_by` (`created_by`);

--
-- Indexes for table `purchase_bills`
--
ALTER TABLE `purchase_bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_number` (`bill_number`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `purchase_bill_items`
--
ALTER TABLE `purchase_bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `pr_id` (`pr_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pr_number` (`pr_number`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indexes for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pr_id` (`pr_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_id` (`purchase_return_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_number` (`quotation_number`),
  ADD KEY `quotations_created_by_foreign` (`created_by`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status` (`status`),
  ADD KEY `quotation_date` (`quotation_date`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `so_number` (`so_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `so_id` (`so_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `sales_returns_created_by_foreign` (`created_by`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock_item_warehouse` (`item_id`,`warehouse_id`,`batch_number`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_code` (`transfer_code`);

--
-- Indexes for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_code` (`supplier_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_code` (`warehouse_code`),
  ADD KEY `fk_warehouse_manager` (`manager_id`);

--
-- Indexes for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wo_number` (`wo_number`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `bom_id` (`bom_id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bom`
--
ALTER TABLE `bom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bom_items`
--
ALTER TABLE `bom_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dispatch_items`
--
ALTER TABLE `dispatch_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispatch_notes`
--
ALTER TABLE `dispatch_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_items`
--
ALTER TABLE `grn_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `production_tracking`
--
ALTER TABLE `production_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `purchase_bills`
--
ALTER TABLE `purchase_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_bill_items`
--
ALTER TABLE `purchase_bill_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `bom`
--
ALTER TABLE `bom`
  ADD CONSTRAINT `bom_ibfk_1` FOREIGN KEY (`finished_product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `bom_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD CONSTRAINT `bom_items_ibfk_1` FOREIGN KEY (`bom_id`) REFERENCES `bom` (`id`),
  ADD CONSTRAINT `bom_items_ibfk_2` FOREIGN KEY (`component_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_categories_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dispatch_items`
--
ALTER TABLE `dispatch_items`
  ADD CONSTRAINT `dispatch_items_ibfk_1` FOREIGN KEY (`dn_id`) REFERENCES `dispatch_notes` (`id`),
  ADD CONSTRAINT `dispatch_items_ibfk_2` FOREIGN KEY (`so_item_id`) REFERENCES `sales_order_items` (`id`);

--
-- Constraints for table `dispatch_notes`
--
ALTER TABLE `dispatch_notes`
  ADD CONSTRAINT `dispatch_notes_ibfk_1` FOREIGN KEY (`so_id`) REFERENCES `sales_orders` (`id`),
  ADD CONSTRAINT `dispatch_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD CONSTRAINT `goods_receipt_notes_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `goods_receipt_notes_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `goods_receipt_notes_ibfk_3` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  ADD CONSTRAINT `goods_receipt_notes_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grn_items_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`id`),
  ADD CONSTRAINT `grn_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `grn_items_ibfk_3` FOREIGN KEY (`po_item_id`) REFERENCES `purchase_order_items` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`so_id`) REFERENCES `sales_orders` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_preferred_supplier` FOREIGN KEY (`preferred_supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `production_tracking`
--
ALTER TABLE `production_tracking`
  ADD CONSTRAINT `production_tracking_ibfk_1` FOREIGN KEY (`wo_id`) REFERENCES `work_orders` (`id`),
  ADD CONSTRAINT `production_tracking_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `purchase_bills`
--
ALTER TABLE `purchase_bills`
  ADD CONSTRAINT `purchase_bills_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_bills_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_bills_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_bill_items`
--
ALTER TABLE `purchase_bill_items`
  ADD CONSTRAINT `purchase_bill_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `purchase_bills` (`id`),
  ADD CONSTRAINT `purchase_bill_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD CONSTRAINT `purchase_requisitions_ibfk_1` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  ADD CONSTRAINT `purchase_requisition_items_ibfk_1` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`),
  ADD CONSTRAINT `purchase_requisition_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_returns_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_returns_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_returns_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_ibfk_1` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_return_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`so_id`) REFERENCES `sales_orders` (`id`),
  ADD CONSTRAINT `sales_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD CONSTRAINT `sales_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_returns_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `sales_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_return_items_return_id_foreign` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `fk_warehouse_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `warehouses_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD CONSTRAINT `work_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `work_orders_ibfk_2` FOREIGN KEY (`bom_id`) REFERENCES `bom` (`id`),
  ADD CONSTRAINT `work_orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
