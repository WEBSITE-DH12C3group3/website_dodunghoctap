-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 08:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sellschoolsupplies`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `description`) VALUES
(1, 'Thiên Long', 'Nhà sản xuất văn phòng phẩm lớn tại Việt Nam'),
(2, 'Deli', 'Thương hiệu đồ dùng học tập nổi tiếng'),
(3, 'Pilot', 'Hãng sản xuất bút chất lượng cao từ Nhật Bản'),
(4, 'Staedtler', 'Thương hiệu văn phòng phẩm Đức, nổi bật về bút chì và compa'),
(5, 'M&G', 'Thương hiệu Trung Quốc chuyên cung cấp đồ dùng học sinh'),
(11, 'Faber-Castell', 'Thương hiệu Đức nổi tiếng về bút chì và dụng cụ mỹ thuật'),
(12, 'Pentel', 'Nhà sản xuất văn phòng phẩm và dụng cụ mỹ thuật Nhật Bản'),
(13, 'Maped', 'Thương hiệu Pháp chuyên về dụng cụ học tập và văn phòng phẩm'),
(14, 'Crayola', 'Thương hiệu Mỹ nổi tiếng về bút màu và sản phẩm nghệ thuật cho trẻ em'),
(15, 'Stabilo', 'Nhà sản xuất Đức chuyên bút highlight và bút viết chất lượng cao'),
(16, 'Uni-ball', 'Thương hiệu Nhật nổi tiếng về bút mực gel và bút bi chất lượng cao');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(3, 'Bút', NULL),
(4, 'Thước kẻ', NULL),
(5, 'Tẩy - Gọt', NULL),
(6, 'Compa - Thước đo góc', NULL),
(7, 'Hộp bút', NULL),
(8, 'Giấy - Vở', NULL),
(9, 'Bìa - File hồ sơ', NULL),
(10, 'Dụng cụ mỹ thuật', NULL),
(11, 'Dụng cụ văn phòng', NULL),
(12, 'Sticker - Note dán', NULL),
(13, 'Bao tập - Bao sách', NULL),
(14, 'Bình nước - Hộp cơm', NULL),
(16, 'Bao tập - Bao sách22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_admin_id` int(11) DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `comment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `product_id`, `user_id`, `rating`, `comment`, `comment_date`) VALUES
(1, 11, NULL, 4, 'qư', '2025-09-22 08:00:54'),
(2, 15, NULL, 3, 'ôi cÁI', '2025-09-23 02:52:43'),
(3, 14, NULL, 4, NULL, '2025-09-24 15:37:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `submission_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `discount_percent` tinyint(4) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT 'pending',
  `expected_delivery_date` date DEFAULT NULL,
  `shipping_type` enum('standard','express') NOT NULL DEFAULT 'standard',
  `shipping_provider` enum('GHTK','GHN','Viettel Post','Other') NOT NULL DEFAULT 'GHTK'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `order_id`, `receiver_name`, `phone`, `email`, `address`, `note`, `delivery_status`, `expected_delivery_date`, `shipping_type`, `shipping_provider`) VALUES
(2, 7, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'pending', '2025-09-28', 'express', 'GHN'),
(3, 8, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'shipping', '2025-09-28', 'standard', 'GHTK'),
(4, 6, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'null', '2025-09-28', 'standard', 'GHTK'),
(5, 4, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'null', '2025-09-28', 'standard', 'GHTK'),
(6, 2, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'delivered', '2025-09-28', 'standard', 'GHTK'),
(7, 9, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'shipping', '2025-09-28', 'express', 'GHTK'),
(8, 3, 'Unknown', 'Unknown', 'unknown@example.com', 'Unknown', NULL, 'delivered', '2025-09-25', 'express', 'Viettel Post'),
(9, 18, 'phùng thị trang', '+84349459165', NULL, 'fffffff', NULL, 'pending', NULL, 'standard', 'GHTK');

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `favourite_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favourite`
--

INSERT INTO `favourite` (`favourite_id`, `user_id`, `product_id`, `added_date`) VALUES
(1, 4, 16, '2025-09-25 04:24:01');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `momo`
--

CREATE TABLE `momo` (
  `momo_payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `total_amount` decimal(15,2) DEFAULT NULL,
  `payment_method` enum('cod','bank_transfer','momo','vnpay') NOT NULL DEFAULT 'cod',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `status`, `total_amount`, `payment_method`, `order_date`) VALUES
(2, 2, 'confirmed', 2600000.00, 'cod', '2025-09-24 16:28:03'),
(3, 2, 'delivered', 12000.00, 'momo', '2025-09-24 16:28:03'),
(4, 4, 'pending', 1300000.00, 'vnpay', '2025-09-24 16:28:03'),
(5, 4, 'pending', 24000.00, 'cod', '2025-09-24 16:28:03'),
(6, 5, 'pending', 2600000.00, 'bank_transfer', '2025-09-24 16:28:03'),
(7, 5, 'pending', 24000.00, 'cod', '2025-09-24 16:47:54'),
(8, 5, 'confirmed', 1300000.00, 'momo', '2025-09-24 16:47:54'),
(9, 5, 'confirmed', 36000.00, 'cod', '2025-09-24 19:04:53'),
(10, 1, 'processing', 30000.00, 'cod', '2025-09-25 03:56:08'),
(11, 4, 'pending', 1300000.00, 'cod', '2025-09-25 03:56:56'),
(12, 4, 'pending', 1300000.00, 'cod', '2025-09-25 03:56:59'),
(14, 4, 'pending', 1300000.00, 'cod', '2025-09-25 03:57:14'),
(15, 4, 'pending', 1300000.00, 'cod', '2025-09-25 04:02:26'),
(17, 1, 'processing', 12000.00, 'cod', '2025-09-25 08:39:12'),
(18, 15, 'processing', 1312000.00, 'cod', '2025-10-01 08:42:35');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
<<<<<<< Updated upstream
=======

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 11, 2, 1300000.00),
(2, 3, 14, 1, 12000.00),
(3, 4, 12, 1, 1300000.00),
(4, 5, 15, 2, 12000.00),
(5, 6, 13, 2, 1300000.00),
(6, 2, 15, 2, 12000.00),
(7, 3, 12, 1, 1300000.00),
(8, 9, 15, NULL, 12000.00),
(9, 10, 16, 1, 30000.00),
(10, 11, 13, 1, 1300000.00),
(11, 12, 13, 1, 1300000.00),
(13, 14, 13, 1, 1300000.00),
(14, 15, 13, 1, 1300000.00),
(16, 17, 14, 1, 12000.00),
(17, 18, 14, 1, 12000.00),
(18, 18, 11, 1, 1300000.00);

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `trg_oi_after_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
  DECLARE st VARCHAR(50);
  SELECT status INTO st FROM orders WHERE order_id = OLD.order_id;
  IF st = 'paid' THEN
    UPDATE products SET sold = GREATEST(COALESCE(sold,0) - OLD.quantity, 0)
    WHERE product_id = OLD.product_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_oi_after_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
  DECLARE st VARCHAR(50);
  SELECT status INTO st FROM orders WHERE order_id = NEW.order_id;
  IF st = 'paid' THEN
    UPDATE products SET sold = COALESCE(sold,0) + NEW.quantity
    WHERE product_id = NEW.product_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_oi_after_update` AFTER UPDATE ON `order_items` FOR EACH ROW BEGIN
  DECLARE st_old VARCHAR(50); DECLARE st_new VARCHAR(50);
  SELECT status INTO st_old FROM orders WHERE order_id = OLD.order_id;
  SELECT status INTO st_new FROM orders WHERE order_id = NEW.order_id;

  -- nếu cùng order và đều 'paid', hiệu chỉnh chênh lệch
  IF OLD.order_id = NEW.order_id AND st_old='paid' AND st_new='paid' THEN
    UPDATE products SET sold = GREATEST(COALESCE(sold,0) + (NEW.quantity - OLD.quantity), 0)
    WHERE product_id = NEW.product_id;
  END IF;
END
$$
DELIMITER ;
>>>>>>> Stashed changes

-- --------------------------------------------------------

--
-- Table structure for table `password_otps`
--

CREATE TABLE `password_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_otps`
--

INSERT INTO `password_otps` (`id`, `user_id`, `code`, `expires_at`, `used_at`, `created_at`, `updated_at`) VALUES
(1, 2, '037646', '2025-09-25 14:47:21', NULL, '2025-09-25 07:37:21', '2025-09-25 07:37:21'),
(2, 14, '385318', '2025-09-25 15:08:03', NULL, '2025-09-25 07:58:03', '2025-09-25 07:58:03'),
(4, 7, '603656', '2025-09-25 16:00:40', '2025-09-25 15:51:12', '2025-09-25 08:50:40', '2025-09-25 08:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `description`, `created_at`) VALUES
(0, 'manage_suppliers', 'Quản lý nhà cung cấp', '2025-09-18 05:54:07'),
(1, 'manage_products', 'Quản lý sản phẩm', '2025-08-22 06:56:54'),
(2, 'manage_users', 'Quản lý người dùng', '2025-08-22 06:56:54'),
(3, 'view_statistics', 'Xem thống kê', '2025-08-22 06:56:54'),
(4, 'manage_orders', 'Quản lý đơn hàng', '2025-08-22 06:56:54'),
(5, 'manage_categories', 'Quản lý danh mục', '2025-09-15 14:08:54'),
(6, 'manage_purchases', 'Quản lý phiếu nhập kho', '2025-09-17 14:02:03'),
(8, 'manage_brands', 'Quản lý thương hiệu', '2025-09-18 06:29:35'),
(9, 'manage_livechat', 'Quản lý chat', '2025-09-30 15:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `sold` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `brand_id`, `description`, `price`, `stock_quantity`, `image_url`, `sold`, `is_featured`, `created_at`) VALUES
(11, 'Mùa hè', 3, NULL, 'cưcs', 1300000.00, 3457, '1755592645088-488150833.jpg', 2, 0, '2025-08-19 08:37:25'),
(12, 'Hoa Hồng Đỏ', 7, NULL, '2222', 1300000.00, 11112, NULL, 1, 0, '2025-08-19 16:49:40'),
(13, 'Hoa Hồng Đỏ', 13, NULL, '11', 1300000.00, 1112, '1755849769419-355125970.jpg', 2, 0, '2025-08-22 08:02:49'),
(14, 'gậy bóng đá', 11, 1, 'đập nhau fen', 12000.00, 120, 'products/U30gU2Pe4uW284yUMUocaCc16EwaPfzvnMcchPaF.png', 0, 0, '2025-09-18 07:19:46'),
(15, 'sừo', 9, 16, '1111', 12000.00, 126, 'products/XMwCIY8gwpvXn4pCRJYVwGBsX65DLOD8FkGgGNnl.jpg', 2, 0, '2025-09-19 05:27:04'),
(16, 'bút chì wibu', 3, 11, 'ôi', 30000.00, 100, 'products/1758740094_z6991041784098_f2b66ee3bf17a3cd018aba68e9f0bb9c.jpg', 0, 0, '2025-09-24 18:54:54'),
(17, 'hộp bút vàng 9999', 7, 13, 'bò biết bay', 40000.00, 100, 'products/1758740213_492013561_655045020664689_5854916036349762659_n.jpg', 0, 0, '2025-09-24 18:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `purchase_order_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`purchase_order_id`, `supplier_id`, `order_date`, `total_amount`, `created_by`, `code`) VALUES
(3, 1, '2025-09-18', 1000000.00, 1, 'PN-202509-003'),
(4, 1, '2025-09-19', 1210000.00, 1, 'PN-202509-004'),
(5, 1, '2025-09-22', 1000000.00, 3, 'PN-202509-005'),
(6, 1, '2025-09-25', 1000000.00, 3, 'PN-202509-006'),
(7, 1, '2025-09-25', 1000000.00, 3, 'PN-202509-007'),
(8, 1, '2025-09-25', 123000000.00, 3, 'PN-202509-008');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `purchase_order_item_id` int(11) NOT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`purchase_order_item_id`, `purchase_order_id`, `product_id`, `quantity`, `price`) VALUES
(2, 3, 13, 1, 1000000.00),
(3, 4, 15, 1, 10000.00),
(4, 4, 11, 1, 1200000.00),
(5, 5, 15, 1, 1000000.00),
(6, 6, 16, 100, 10000.00),
(7, 7, 17, 100, 10000.00),
(8, 8, 11, 123, 1000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'admin', 'Quản trị viên hệ thống', '2025-08-22 06:56:47'),
(2, 'employee', 'Nhân viên', '2025-08-22 06:56:47'),
(3, 'customer', 'Khách hàng', '2025-08-22 06:56:47'),
(7, 'staff', 'nhân viên sale', '2025-09-25 06:07:20');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 2),
(1, 5),
(1, 6),
(1, 0),
(1, 8),
(7, 1),
(1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_info` text DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_info`, `description`) VALUES
(1, 'ncca', '0961208936', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT 3,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `last_activity` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `address`, `password`, `google_id`, `facebook_id`, `role_id`, `created_at`, `is_active`, `last_activity`) VALUES
(1, '1', '1@1', '1', '1', '$2y$10$3CqGdOLVg/zAhjPwv83MjOQeNVTC1CG7KlARiC5MMzem/UcJPUr8i', NULL, NULL, 1, '2025-09-18 16:39:42', 1, '2025-09-30 20:02:00'),
(2, '1', '1@2', '0349459165', '1', '$2y$10$pZJHCIkN7HElvBnOOP0GPe4r1aPizcwcdgwSn3nkJMg5Klb8SsbDy', NULL, NULL, 7, '2025-09-18 16:40:02', 1, '2025-09-30 16:13:13'),
(3, 'full', 'full@gmail.com', NULL, NULL, '$2y$10$dr.czSZf4m/W47jiq3V.uOAYu47EdFX2k1SXEtvDQ12.O6RGmnWkm', NULL, NULL, 1, '2025-09-15 10:16:33', 1, '2025-09-25 02:42:49'),
(4, 'Lê Trường Giang', '22111061403@hunre.edu.vn', NULL, NULL, '$2y$10$s2hx4ODaRbTUJ8cSdmgnh.7iM3LIP6EBNPQQPcGdCEqbD7j1TA6P6', '114118200846899184853', NULL, 3, '2025-09-19 07:15:47', 1, '2025-09-25 04:30:34'),
(5, 'Lê Trường Giang', 'letruonggiang2004hhbg@gmail.com', NULL, NULL, '$2y$10$aUfPCVmeh81MQ0x4ctWRMef3q4dyJzIWGCgS59p1N8NPHT8xXcOKW', NULL, '796737086574204', 3, '2025-09-22 15:12:15', 1, '2025-09-24 19:06:18'),
(6, 'Giang Lê', 'gianglehhbg2004@gmail.com', NULL, NULL, '$2y$10$0Tw0t2FED86jZ1WKCChPvewpOMixb0oIz6qwAc9GH0tRTTbiBE6xW', '116654594578248973228', NULL, 3, '2025-09-25 01:48:02', 1, '2025-09-25 01:48:49'),
(7, 'phùng thị trang', 'phungtrang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$ysgsZOy.ndgomdjJEwdAyuJ8fmbu5okBfUTSfrS3sUv/FTE71GHVe', NULL, NULL, 3, '2025-09-25 07:37:59', 1, '2025-09-25 08:58:24'),
(8, 'giaicuuthienthan', 'mynameistrang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$BRgsqlqOoFwZqOmvD465ne5sH3k4jRhmv8JqxXYOBsAKC6VTte14q', NULL, NULL, 3, '2025-09-25 07:40:19', 1, '2025-09-25 08:13:48'),
(9, 'KHÁ BẢNH', 'banh@gmail.com', '+84368498289', 'ss', '$2y$10$PullQW1zoEdwcQeRAshjfOQvsezjv0B84Neg8Od7jgNerlKss9kOy', NULL, NULL, 3, '2025-09-25 07:41:45', 1, '2025-09-25 07:49:05'),
(10, 'Nguyễn Văn A@#123', 'hoahoavuive2004@gmail.com', '+84349459165', 'nam dinh', '$2y$10$u/T2CoeeGcYwwukFPLhwEuBqMbvMqYHYmCGOA7vISMWmqpXqD5fSK', NULL, NULL, 3, '2025-09-25 07:49:22', 1, '2025-09-25 07:50:17'),
(11, 'Nguyễn Văn A@#123', 'vuive2004@gmail.com', '+84349459165', 'nam dinh', '$2y$10$Em0OVdJU7Zb5MbMpKOkHA.Be0eRbd9akuIqyOkOf9xU2vRJbLTNae', NULL, NULL, 3, '2025-09-25 07:50:37', 1, '2025-09-25 07:51:14'),
(12, 'KHÁ BẢNH', 'anh@gmail.com', '+84368498289', 'ss', '$2y$10$I6bvmy0ThBI.oe6S.S.WIOH5tZqdUE2EKojOkP3/o9pNNob5IZpp6', NULL, NULL, 3, '2025-09-25 07:51:34', 1, '2025-09-25 07:53:23'),
(13, 'phùng thị trang', 'trang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$Ihp4kKQLpmBIQGtLFUdWc.fNIgvdHZRE1QWbIqmgfUnks0XGi9xpC', NULL, NULL, 3, '2025-09-25 07:53:38', 1, '2025-09-25 07:55:25'),
(14, 'phùng thị trang', 'trag@gmail.com', '+84349459165', 'fffffff', '$2y$10$BXZj8UmWgS9tbepaC289rO.eaStSCAr.xxabv9A4qSBpnuP.JA5v6', NULL, NULL, 3, '2025-09-25 07:56:48', 1, '2025-09-25 07:59:02'),
(15, 'phùng thị trang', 'nam@gmail.com', '+84349459165', 'fffffff', '$2y$10$PXw7faGiSOMrIVbRDhYZne9OzUt962YlPHyim2tcoMAysVrtzqqo6', NULL, NULL, 3, '2025-09-25 08:02:08', 1, '2025-10-01 18:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `vnpay`
--

CREATE TABLE `vnpay` (
  `vnpay_payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `chat_sessions_user_id_idx` (`user_id`),
  ADD KEY `chat_sessions_assigned_admin_id_idx` (`assigned_admin_id`),
  ADD KEY `chat_sessions_last_message_at_idx` (`last_message_at`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`submission_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`favourite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `messages_session_id_created_at_idx` (`session_id`,`created_at`),
  ADD KEY `messages_sender_id_idx` (`sender_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `momo`
--
ALTER TABLE `momo`
  ADD PRIMARY KEY (`momo_payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_otps`
--
ALTER TABLE `password_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_otps_user_code` (`user_id`,`code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_products_brand_id` (`brand_id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`purchase_order_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `fk_purchase_orders_created_by` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`purchase_order_item_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `uk_roles_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD KEY `fk_rp_role` (`role_id`),
  ADD KEY `fk_rp_perm` (`permission_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_users_email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indexes for table `vnpay`
--
ALTER TABLE `vnpay`
  ADD PRIMARY KEY (`vnpay_payment_id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `favourite`
--
ALTER TABLE `favourite`
  MODIFY `favourite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `momo`
--
ALTER TABLE `momo`
  MODIFY `momo_payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_otps`
--
ALTER TABLE `password_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `purchase_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `purchase_order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vnpay`
--
ALTER TABLE `vnpay`
  MODIFY `vnpay_payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_admin_fk` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_sessions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_sender_fk` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_session_fk` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `fk_purchase_orders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
