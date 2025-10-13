-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 04:55 PM
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

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Màu sắc', 'Ví dụ: Đỏ, Xanh, Đen, Trắng...', '2025-10-13 11:55:20'),
(2, 'Kích thước', 'Ví dụ: A4, A5, B5, 0.5mm...', '2025-10-13 11:55:20'),
(3, 'Chất liệu', 'Nhựa, Kim loại, Vải...', '2025-10-13 11:55:20'),
(4, 'Số trang', 'Áp dụng cho vở/sổ', '2025-10-13 11:55:20'),
(5, 'Độ cứng ngòi', 'HB, 2B, 4B... cho bút chì', '2025-10-13 11:55:20'),
(6, 'Đường kính nét', '0.3mm, 0.5mm, 0.7mm...', '2025-10-13 11:55:20'),
(7, 'Loại mực', 'Gel, Bi, Lông dầu, Lông bảng', '2025-10-13 11:55:20'),
(8, 'Khổ giấy', 'A3, A4, A5, B5...', '2025-10-13 11:55:20'),
(9, 'Dung tích', 'Áp dụng cho bình nước/hộp mực', '2025-10-13 11:55:20'),
(10, 'Bảo hành', 'Thời gian bảo hành (nếu có)', '2025-10-13 11:55:20');

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
(16, 'Bao tập - Bao sách22', NULL),
(17, 'Balo - Túi', 'Balo, túi đeo chéo, túi đựng bút vở'),
(18, 'Máy tính - Calculator', 'Máy tính bỏ túi cho học sinh, sinh viên'),
(19, 'USB - Ổ lưu trữ', 'USB, thẻ nhớ, ổ cứng di động mini'),
(20, 'Bút lông - Bút dạ', 'Bút board, bút lông dầu, bút lông bảng'),
(21, 'Màu nước - Màu sáp', 'Dụng cụ tô màu cho mỹ thuật'),
(22, 'Keo - Băng dính', 'Keo dán, băng dính trong/đục'),
(23, 'Kéo - Dao rọc giấy', 'Kéo học sinh, dao rọc giấy'),
(24, 'Bảng trắng - Bảng mini', 'Bảng mini, bảng trắng cầm tay'),
(25, 'Hộp đựng tài liệu', 'File hộp, cặp tài liệu'),
(26, 'Giấy note - Index', 'Giấy note, phân trang'),
(27, 'Thước cuộn - Eke', 'Thước cuộn, eke nhựa'),
(28, 'Sổ tay - Planner', 'Sổ tay, planner, bullet journal');

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

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`session_id`, `user_id`, `assigned_admin_id`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, 15, 1, '2025-10-11 16:46:47', '2025-10-10 10:54:59', '2025-10-11 16:46:47'),
(2, 8, 1, '2025-10-13 14:21:55', '2025-10-11 16:13:51', '2025-10-13 14:21:55'),
(3, 2, 1, '2025-10-12 10:54:23', '2025-10-12 09:48:56', '2025-10-12 10:54:23'),
(4, 1, NULL, '2025-10-13 14:25:46', '2025-10-13 14:25:46', '2025-10-13 14:25:46');

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
(9, 18, 'phùng thị trang', '+84349459165', NULL, 'fffffff', NULL, 'pending', NULL, 'standard', 'GHTK'),
(10, 19, '1', '1', '1@1', '1', NULL, 'pending', NULL, 'standard', 'GHTK'),
(11, 20, '1', '1', '1@1', '1', NULL, 'pending', NULL, 'standard', 'GHTK'),
(12, 21, 'mynameistrang', '+84349459165', 'phungtrang19012004@gmail.com', 'fffffff', NULL, 'pending', NULL, 'standard', 'GHTK');

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
(1, 4, 16, '2025-09-25 04:24:01'),
(2, 1, 18, '2025-10-13 13:53:11');

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

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `session_id`, `sender_id`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 'hello', 1, '2025-10-10 10:55:39', '2025-10-10 11:15:26'),
(2, 1, 15, 'xin chào', 1, '2025-10-10 11:21:48', '2025-10-10 11:22:00'),
(3, 1, 1, 'chào bạn', 1, '2025-10-10 11:22:16', '2025-10-10 11:22:39'),
(4, 1, 1, 'chào bạn', 1, '2025-10-10 11:49:14', '2025-10-10 12:47:48'),
(5, 1, 1, 'chào bạn', 1, '2025-10-10 11:49:28', '2025-10-10 12:47:48'),
(6, 1, 15, 'chào', 1, '2025-10-10 12:47:54', '2025-10-10 12:48:23'),
(7, 1, 15, 'ê', 1, '2025-10-10 12:48:03', '2025-10-10 12:48:23'),
(8, 1, 15, 'ê', 1, '2025-10-10 12:48:07', '2025-10-10 12:48:23'),
(9, 1, 15, 'ê', 1, '2025-10-10 12:49:20', '2025-10-10 12:49:31'),
(10, 1, 1, 'hello', 1, '2025-10-10 13:00:56', '2025-10-10 13:10:14'),
(11, 1, 1, 'hello', 1, '2025-10-10 13:01:12', '2025-10-10 13:10:14'),
(12, 1, 1, 'rafe', 1, '2025-10-10 13:09:52', '2025-10-10 13:10:14'),
(13, 1, 1, 'cai cho j', 1, '2025-10-10 13:10:02', '2025-10-10 13:10:14'),
(14, 1, 15, 'helo', 1, '2025-10-10 13:10:26', '2025-10-10 13:10:37'),
(15, 1, 15, 'hello', 1, '2025-10-11 14:57:54', '2025-10-11 16:38:59'),
(16, 2, 8, 'chào', 1, '2025-10-11 16:14:05', '2025-10-11 16:36:00'),
(17, 2, 1, 'chào bạn ạ', 1, '2025-10-11 16:36:16', '2025-10-11 16:42:17'),
(18, 2, 1, 'chào bạn ạ', 1, '2025-10-11 16:36:16', '2025-10-11 16:42:17'),
(19, 2, 1, 'chào nạn nhé', 1, '2025-10-11 16:36:40', '2025-10-11 16:42:17'),
(20, 2, 1, 'chào nạn nhé', 1, '2025-10-11 16:36:40', '2025-10-11 16:42:17'),
(21, 1, 1, 'hello', 0, '2025-10-11 16:39:03', '2025-10-11 16:39:03'),
(22, 1, 1, 'hello', 0, '2025-10-11 16:39:03', '2025-10-11 16:39:03'),
(23, 1, 1, 'chà', 0, '2025-10-11 16:46:45', '2025-10-11 16:46:45'),
(24, 1, 1, 'chà', 0, '2025-10-11 16:46:45', '2025-10-11 16:46:45'),
(25, 1, 1, 'chà', 0, '2025-10-11 16:46:45', '2025-10-11 16:46:45'),
(26, 1, 1, 'chà', 0, '2025-10-11 16:46:45', '2025-10-11 16:46:45'),
(27, 1, 1, 'uhfuhfui', 0, '2025-10-11 16:46:47', '2025-10-11 16:46:47'),
(28, 1, 1, 'uhfuhfui', 0, '2025-10-11 16:46:47', '2025-10-11 16:46:47'),
(29, 1, 1, 'uhfuhfui', 0, '2025-10-11 16:46:47', '2025-10-11 16:46:47'),
(30, 1, 1, 'uhfuhfui', 0, '2025-10-11 16:46:47', '2025-10-11 16:46:47'),
(31, 2, 8, 'drferf', 1, '2025-10-11 16:47:26', '2025-10-11 16:47:31'),
(32, 2, 1, 'ờ', 1, '2025-10-11 16:47:34', '2025-10-11 16:47:39'),
(33, 2, 1, 'ờ', 1, '2025-10-11 16:47:34', '2025-10-11 16:47:39'),
(34, 2, 1, 'ờ', 1, '2025-10-11 16:47:34', '2025-10-11 16:47:39'),
(35, 2, 1, 'ờ', 1, '2025-10-11 16:47:34', '2025-10-11 16:47:39'),
(36, 2, 1, 'hrfuef', 1, '2025-10-11 16:47:52', '2025-10-11 16:47:56'),
(37, 2, 1, 'hrfuef', 1, '2025-10-11 16:47:52', '2025-10-11 16:47:56'),
(38, 2, 1, 'hrfuef', 1, '2025-10-11 16:47:52', '2025-10-11 16:47:56'),
(39, 2, 1, 'hrfuef', 1, '2025-10-11 16:47:52', '2025-10-11 16:47:56'),
(40, 2, 1, 'jksf', 1, '2025-10-11 16:50:54', '2025-10-13 14:21:44'),
(41, 2, 1, 'jksf', 1, '2025-10-11 16:50:54', '2025-10-13 14:21:44'),
(42, 2, 1, 'jksf', 1, '2025-10-11 16:50:54', '2025-10-13 14:21:44'),
(43, 2, 1, 'jksf', 1, '2025-10-11 16:50:54', '2025-10-13 14:21:44'),
(44, 2, 1, 'sd', 1, '2025-10-11 16:50:56', '2025-10-13 14:21:44'),
(45, 2, 1, 'sd', 1, '2025-10-11 16:50:56', '2025-10-13 14:21:44'),
(46, 2, 1, 'sd', 1, '2025-10-11 16:50:56', '2025-10-13 14:21:44'),
(47, 2, 1, 'sd', 1, '2025-10-11 16:50:56', '2025-10-13 14:21:44'),
(48, 2, 1, 'dksdgjfh', 1, '2025-10-11 16:52:12', '2025-10-13 14:21:44'),
(49, 3, 2, 'hello', 1, '2025-10-12 10:10:53', '2025-10-12 10:12:18'),
(50, 3, 1, 'chào bạn', 1, '2025-10-12 10:12:37', '2025-10-12 10:12:44'),
(51, 3, 1, 'hello ạ', 1, '2025-10-12 10:12:58', '2025-10-12 10:13:05'),
(52, 3, 2, 'chào', 1, '2025-10-12 10:13:27', '2025-10-12 10:14:39'),
(53, 3, 2, 'hehe', 1, '2025-10-12 10:21:55', '2025-10-12 10:21:57'),
(54, 3, 1, 'cai j vay', 1, '2025-10-12 10:22:10', '2025-10-12 10:22:14'),
(55, 3, 2, '..', 1, '2025-10-12 10:40:44', '2025-10-12 10:40:51'),
(56, 3, 1, 'jdsfgh', 1, '2025-10-12 10:40:56', '2025-10-12 10:41:01'),
(57, 3, 1, 'ade', 1, '2025-10-12 10:41:05', '2025-10-12 10:41:13'),
(58, 3, 2, 'etger', 1, '2025-10-12 10:41:08', '2025-10-12 10:41:28'),
(59, 3, 2, 'ưewqrf', 1, '2025-10-12 10:41:20', '2025-10-12 10:41:28'),
(60, 3, 1, 'ưerwef', 1, '2025-10-12 10:41:25', '2025-10-12 10:53:45'),
(61, 3, 1, 'dfer', 1, '2025-10-12 10:44:58', '2025-10-12 10:53:45'),
(62, 3, 1, 'ẻger', 1, '2025-10-12 10:54:09', '2025-10-12 10:54:14'),
(63, 3, 2, 'ăef', 1, '2025-10-12 10:54:23', '2025-10-12 10:54:26'),
(64, 2, 8, 'hello', 0, '2025-10-13 14:21:55', '2025-10-13 14:21:55');

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
(18, 15, 'processing', 1312000.00, 'cod', '2025-10-01 08:42:35'),
(19, 1, 'processing', 24000.00, 'bank_transfer', '2025-10-02 05:53:02'),
(20, 1, 'pending', 5000.00, 'cod', '2025-10-13 14:27:17'),
(21, 7, 'processing', 5000.00, 'bank_transfer', '2025-10-13 14:32:12');

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
(18, 18, 11, 1, 1300000.00),
(19, 19, 15, 2, 12000.00),
(20, 20, 18, 1, 5000.00),
(21, 21, 18, 1, 5000.00);

--
-- Triggers `order_items`
--

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
(7, 'manage_discounts', 'Quản lý giảm giá', '2025-09-18 06:29:35'),
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
(14, 'gậy bóng đá', 11, 1, 'đập nhau fen', 12000.00, 120, 'products/1760363550_images.jpg', 0, 0, '2025-09-18 07:19:46'),
(17, 'hộp bút vàng 9999', 7, 13, 'bò biết bay', 40000.00, 100, 'products/1759381410_images.jpg', 0, 0, '2025-09-24 18:56:53'),
(18, 'Bút bi Thiên Long TL-027', 3, 1, 'Bút bi trơn, nét 0.5mm', 5000.00, 1200, 'products/1760363487_images (15).jpg', 0, 1, '2025-10-13 11:55:20'),
(19, 'Bút gel Deli 0.5mm', 3, 2, 'Mực gel êm, khô nhanh', 9000.00, 800, 'products/1760363471_images (8).jpg', 0, 0, '2025-10-13 11:55:20'),
(20, 'Bút mực Pilot G2 0.7', 3, 3, 'Nét 0.7mm, viết êm', 28000.00, 300, 'products/1760363442_images (12).jpg', 0, 0, '2025-10-13 11:55:20'),
(21, 'Bút chì gỗ HB Staedtler', 3, 4, 'Bút chì HB, dễ gọt', 6000.00, 1000, 'products/1760363427_images (11).jpg', 0, 0, '2025-10-13 11:55:20'),
(22, 'Bút chì kim 0.5mm Pentel', 3, 12, 'Thân chắc chắn, dễ dùng', 45000.00, 200, 'products/1760363399_download.jpg', 0, 0, '2025-10-13 11:55:20'),
(23, 'Bút highlight Stabilo Boss', 3, 15, 'Màu nổi, không lem', 32000.00, 350, 'products/1760363378_download (1).jpg', 0, 1, '2025-10-13 11:55:20'),
(24, 'Bút gel Uni-ball Signo', 3, 16, 'Mực gel chống lem', 36000.00, 280, 'products/1760363273_images (21).jpg', 0, 0, '2025-10-13 11:55:20'),
(25, 'Bút máy Thiên Long Student', 3, 1, 'Bút máy học sinh, kèm ống mực', 42000.00, 180, 'products/1760363259_images (20).jpg', 0, 0, '2025-10-13 11:55:20'),
(26, 'Thước thẳng 20cm Deli', 4, 2, 'Thước nhựa trong 20cm', 7000.00, 700, 'products/1760363242_images (16).jpg', 0, 0, '2025-10-13 11:55:20'),
(27, 'Bộ eke 30-60-90 Maped', 4, 13, 'Bộ 2 eke, thước đo góc', 42000.00, 250, 'products/1760363226_download.jpg', 0, 0, '2025-10-13 11:55:20'),
(28, 'Thước cuộn 3m Maped', 4, 13, 'Thước cuộn nhỏ gọn 3m', 55000.00, 140, 'products/1760363206_images (11).jpg', 0, 0, '2025-10-13 11:55:20'),
(29, 'Tẩy Pentel Hi-Polymer', 5, 12, 'Tẩy êm, ít bụi', 12000.00, 900, 'products/1760363187_images (2).jpg', 0, 0, '2025-10-13 11:55:20'),
(30, 'Gọt bút chì Maped 1 lỗ', 5, 13, 'Gọt nhựa nhỏ gọn', 9000.00, 600, 'products/1760363165_images (5).jpg', 0, 0, '2025-10-13 11:55:20'),
(31, 'Compa Staedtler School', 6, 4, 'Compa học sinh, kim chắc', 55000.00, 220, 'products/1760363150_images (13).jpg', 0, 0, '2025-10-13 11:55:20'),
(32, 'Thước đo góc 180° Deli', 6, 2, 'Thước đo góc bán nguyệt', 8000.00, 500, 'products/1760363127_images (3).jpg', 0, 0, '2025-10-13 11:55:20'),
(33, 'Hộp bút vải Deli 2 ngăn', 7, 2, 'Chống bám bẩn, 2 ngăn tiện lợi', 65000.00, 180, 'products/1760363096_images (6).jpg', 0, 0, '2025-10-13 11:55:20'),
(34, 'Hộp bút Maped PVC', 7, 13, 'PVC bền, khoá kéo mượt', 59000.00, 120, 'products/1760363075_images (10).jpg', 0, 0, '2025-10-13 11:55:20'),
(35, 'Vở Campus B5 80 trang', 8, 11, 'Giấy dày, mịn', 18000.00, 900, 'products/1760363049_download (2).jpg', 0, 0, '2025-10-13 11:55:20'),
(36, 'Vở kẻ ngang Thiên Long 200 trang', 8, 1, 'Bìa cứng, giấy trắng mịn', 26000.00, 600, 'products/1760363013_images (18).jpg', 0, 0, '2025-10-13 11:55:20'),
(37, 'Giấy A4 70gsm PaperOne 500 tờ', 8, NULL, 'Giấy in 70gsm', 78000.00, 300, 'products/1760362990_images (4).jpg', 0, 0, '2025-10-13 11:55:20'),
(38, 'Bìa còng A4 Deli', 9, 2, 'Khổ A4, gáy 5cm', 42000.00, 260, 'products/1760362974_images (14).jpg', 0, 0, '2025-10-13 11:55:20'),
(39, 'File lá A4 100 cái', 9, NULL, 'File lá mỏng trong suốt', 45000.00, 400, 'products/1760362952_images (6).jpg', 0, 0, '2025-10-13 11:55:20'),
(40, 'Bút màu sáp Crayola 24 màu', 10, 14, 'Sáp màu cho bé', 65000.00, 320, 'products/1760362921_images (11).jpg', 0, 1, '2025-10-13 11:55:21'),
(41, 'Màu nước Faber-Castell 12 màu', 10, 11, 'Màu tươi, dễ pha', 82000.00, 180, 'products/1760362898_images (20).jpg', 0, 0, '2025-10-13 11:55:21'),
(42, 'Cọ vẽ Pentel size 4', 10, 12, 'Lông mềm, thấm tốt', 29000.00, 150, 'products/1760362880_download (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(43, 'Tập vẽ A4 120gsm', 10, NULL, 'Giấy vẽ 120gsm, 20 tờ', 38000.00, 210, 'products/1760362865_images (18).jpg', 0, 0, '2025-10-13 11:55:21'),
(44, 'Kẹp bướm 32mm (hộp 12)', 11, NULL, 'Kẹp giấy 32mm', 25000.00, 220, 'products/1760362818_images (13).jpg', 0, 0, '2025-10-13 11:55:21'),
(45, 'Dập ghim Deli số 10', 11, 2, 'Dập ghim mini số 10', 52000.00, 140, 'products/1760362803_images (5).jpg', 0, 0, '2025-10-13 11:55:21'),
(46, 'Kim ghim số 10 (1000 viên)', 11, 2, 'Kim thay cho dập ghim số 10', 12000.00, 600, 'products/1760362786_images (8).jpg', 0, 0, '2025-10-13 11:55:21'),
(47, 'Giấy note 3x3 vàng', 12, NULL, 'Ghi chú 3x3 inch', 9000.00, 500, 'products/1760362770_images.jpg', 0, 0, '2025-10-13 11:55:21'),
(48, 'Index phân trang 5 màu', 12, 13, 'Bộ 5 màu, 100 lá', 18000.00, 380, 'products/1760362748_images (1).jpg', 0, 0, '2025-10-13 11:55:21'),
(49, 'Bình nước 500ml nhựa Tritan', 14, NULL, 'An toàn BPA free', 69000.00, 160, 'products/1760362731_images (10).jpg', 0, 0, '2025-10-13 11:55:21'),
(50, 'Hộp cơm 2 ngăn', 14, NULL, 'Microwave safe', 89000.00, 110, 'products/1760362716_images (9).jpg', 0, 0, '2025-10-13 11:55:21'),
(51, 'Balo học sinh Deli chữ U', 17, 2, 'Balo học sinh nhiều ngăn', 195000.00, 90, 'products/1760362703_images (23).jpg', 0, 0, '2025-10-13 11:55:21'),
(52, 'Balo thời trang Maped', 17, 13, 'Balo nhẹ, đệm lưng êm', 245000.00, 70, 'products/1760362688_images (7).jpg', 0, 0, '2025-10-13 11:55:21'),
(53, 'Sổ tay A5 bìa da', 28, NULL, 'Sổ tay bìa da A5 200 trang', 69000.00, 130, 'products/1760362676_download (1).jpg', 0, 1, '2025-10-13 11:55:21'),
(54, 'Planner 2025 weekly', 28, NULL, 'Planner theo tuần 2025', 99000.00, 80, 'products/1760362649_download (1).jpg', 0, 0, '2025-10-13 11:55:21'),
(55, 'Máy tính Casio fx-580VN X', 18, NULL, 'Máy tính khoa học cho HS-SV', 790000.00, 60, 'products/1760362635_images (11).jpg', 0, 0, '2025-10-13 11:55:21'),
(56, 'Máy tính mini bỏ túi Deli', 18, 2, 'Màn hình 12 số', 159000.00, 90, 'products/1760362618_images (22).jpg', 0, 0, '2025-10-13 11:55:21'),
(57, 'USB 32GB 3.0', 19, NULL, 'Tốc độ USB 3.0', 129000.00, 120, 'products/1760362600_images (17).jpg', 0, 0, '2025-10-13 11:55:21'),
(58, 'USB 64GB 3.2', 19, NULL, 'Tốc độ USB 3.2', 189000.00, 80, 'products/1760362585_images (7).jpg', 0, 0, '2025-10-13 11:55:21'),
(59, 'Bảng trắng mini A4', 24, NULL, 'Kèm bút lông & khăn lau', 39000.00, 140, 'products/1760362566_images (4).jpg', 0, 0, '2025-10-13 11:55:21'),
(60, 'Kéo học sinh 13cm', 23, NULL, 'Đầu tù an toàn', 15000.00, 400, 'products/1760357450_download (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(61, 'Dao rọc giấy Deli 18mm', 23, 2, 'Khóa lưỡi chắc chắn', 22000.00, 200, 'products/1760362537_images (21).jpg', 0, 0, '2025-10-13 11:55:21'),
(62, 'Keo khô Deli 15g', 22, 2, 'Dán giấy nhanh khô', 9000.00, 500, 'products/1760362508_images (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(63, 'Băng dính trong 1.8cm x 30y', 22, NULL, 'Độ dính tốt', 12000.00, 450, 'products/1760362484_images (10).jpg', 0, 0, '2025-10-13 11:55:21'),
(64, 'Note 3x5 kẻ ngang', 26, NULL, 'Ghi chú khổ lớn', 14000.00, 260, 'products/1760362469_images (18).jpg', 0, 0, '2025-10-13 11:55:21'),
(65, 'Hộp đựng tài liệu A4 5 ngăn', 25, NULL, 'Nhựa PP bền', 59000.00, 120, 'products/1760362450_images (23).jpg', 0, 0, '2025-10-13 11:55:21'),
(66, 'Bút bi M&G 0.5mm', 3, 5, 'Bi 0.5mm, trơn', 7000.00, 900, 'products/1760362424_images (15).jpg', 0, 0, '2025-10-13 11:55:21'),
(67, 'Bút lông dầu Thiên Long', 3, 1, 'Viết CD/DVD/kim loại', 15000.00, 350, 'products/1760362406_images (16).jpg', 0, 0, '2025-10-13 11:55:21'),
(68, 'Bút lông bảng Deli', 3, 2, 'Xoá khô dễ dàng', 12000.00, 400, 'products/1760362393_download (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(69, 'Bút chì 2B Faber-Castell', 3, 11, 'Hộp 12 cây', 52000.00, 220, 'products/1760362380_download.jpg', 0, 0, '2025-10-13 11:55:21'),
(70, 'Sổ chấm công A5', 11, NULL, 'Sổ văn phòng A5', 23000.00, 160, 'products/1760362353_images (18).jpg', 0, 0, '2025-10-13 11:55:21'),
(71, 'Túi zipper A5', 9, NULL, 'Đựng giấy tờ chống nước', 9000.00, 380, 'products/1760362336_download (1).jpg', 0, 0, '2025-10-13 11:55:21'),
(72, 'Vở ô ly A4 120 trang', 8, NULL, 'Giấy trắng, kẻ ô ly', 26000.00, 260, 'products/1760357450_download (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(73, 'Giấy A5 80gsm (500 tờ)', 8, NULL, 'Giấy in A5', 72000.00, 140, 'products/1760357562_images (14).jpg', 0, 0, '2025-10-13 11:55:21'),
(74, 'Set cọ vẽ 5 cây', 10, 12, 'Nhiều cỡ cho vẽ màu', 59000.00, 90, 'products/1760357533_download (1).jpg', 0, 0, '2025-10-13 11:55:21'),
(75, 'Bút dạ quang 6 màu Stabilo', 3, 15, 'Bộ 6 màu nổi bật', 149000.00, 110, 'products/1760357513_images (16).jpg', 0, 0, '2025-10-13 11:55:21'),
(76, 'Bút gel Pentel Energel 0.5', 3, 12, 'Mực gel khô nhanh', 35000.00, 180, 'products/1760357480_images (10).jpg', 0, 0, '2025-10-13 11:55:21'),
(77, 'Bút bi Uni-ball Jetstream', 3, 16, 'Bi trơn, chống lem', 39000.00, 150, 'products/1760357469_images (20).jpg', 0, 0, '2025-10-13 11:55:21'),
(78, 'Thước kẻ kim loại 30cm', 4, NULL, 'Thép không gỉ', 29000.00, 130, 'products/1760357450_download (2).jpg', 0, 0, '2025-10-13 11:55:21'),
(79, 'Kéo văn phòng 21cm', 11, NULL, 'Lưỡi sắc, tay cầm êm', 39000.00, 100, 'products/1760357430_images (13).jpg', 0, 0, '2025-10-13 11:55:21'),
(80, 'Dập ghim số 10 Thiên Long', 11, 1, 'Dập ghim bền', 59000.00, 95, 'products/1760357415_images (6).jpg', 0, 0, '2025-10-13 11:55:21'),
(81, 'Kim bấm số 10 Thiên Long', 11, 1, '1000 viên', 13000.00, 400, 'products/1760357394_images (4).jpg', 0, 0, '2025-10-13 11:55:21'),
(82, 'Giấy note phủ keo 4 màu', 12, NULL, 'Ghi chú nhiều màu', 19000.00, 240, 'products/1760357355_images (1).jpg', 0, 0, '2025-10-13 11:55:21'),
(83, 'Bút brush màu nước Pentel', 10, 12, 'Đầu cọ mềm, đậm nhạt tốt', 69000.00, 85, 'products/1760357333_download.jpg', 0, 0, '2025-10-13 11:55:21');

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

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `product_id`, `attribute_id`, `value`, `created_at`) VALUES
(1, 19, 1, 'Đen', '2025-10-13 11:55:21'),
(2, 19, 6, '0.5mm', '2025-10-13 11:55:21'),
(3, 23, 1, 'Vàng', '2025-10-13 11:55:21'),
(4, 35, 8, 'B5', '2025-10-13 11:55:21'),
(5, 35, 4, '80', '2025-10-13 11:55:21'),
(6, 55, 10, '12 tháng', '2025-10-13 11:55:21');

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
(1, 0),
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(7, 1);

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
(1, 'ncca', '0961208936', NULL),
(3, 'Công ty VPP Minh Long', '024-3711-2233 | sales@minhlong.vn', 'Nhà phân phối văn phòng phẩm miền Bắc'),
(4, 'Công ty Hồng Hà Trading', '028-3899-1122 | cskh@hongha.vn', 'Phân phối vở và giấy in'),
(5, 'An Phát Stationery', '0938 111 222 | anphat@stationery.vn', 'Phân phối đa ngành VPP'),
(6, 'Thiên Phú Supplies', '0988 333 444 | thienphu@sup.vn', 'Nhà cung cấp dụng cụ học tập'),
(7, 'Nova Edu Mall', 'support@novaedu.vn', 'Trung tâm phân phối thiết bị trường học'),
(8, 'Pencil House', '0909 123 789 | hello@pencilhouse.vn', 'Chuyên bút viết & phụ kiện'),
(9, 'Paper World Co.', 'paperworld@co.vn', 'Chuyên giấy & sổ tay'),
(10, 'Color Art VN', 'colorart@vn.com', 'Chuyên dụng cụ mỹ thuật'),
(11, 'Maped VN Distributor', 'maped-vn@dist.com', 'Nhà pp chính hãng Maped'),
(12, 'Deli Việt Nam', 'care@deli.vn', 'Đại lý Deli'),
(13, 'Uni Việt Nam', 'contact@univn.com', 'Đại lý Uni-ball'),
(14, 'Faber-Castell VN', 'fc@vietnam.com', 'Đại lý Faber-Castell'),
(15, 'Pentel VN', 'pentel@vn.com', 'Đại lý Pentel'),
(16, 'Crayola Partner VN', 'hello@crayola.vn', 'Đại lý Crayola'),
(17, 'VietOffice Hub', 'hotline 1900-6868', 'Hub tổng hợp văn phòng phẩm');

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
(1, '1', '1@1', '1', '1', '$2y$10$3CqGdOLVg/zAhjPwv83MjOQeNVTC1CG7KlARiC5MMzem/UcJPUr8i', NULL, NULL, 1, '2025-09-18 16:39:42', 1, '2025-10-13 14:47:28'),
(2, '1', '1@2', '0349459165', '1', '$2y$10$pZJHCIkN7HElvBnOOP0GPe4r1aPizcwcdgwSn3nkJMg5Klb8SsbDy', NULL, NULL, 7, '2025-09-18 16:40:02', 1, '2025-10-12 10:54:23'),
(3, 'full', 'full@gmail.com', NULL, NULL, '$2y$10$dr.czSZf4m/W47jiq3V.uOAYu47EdFX2k1SXEtvDQ12.O6RGmnWkm', NULL, NULL, 1, '2025-09-15 10:16:33', 1, '2025-09-25 02:42:49'),
(4, 'Lê Trường Giang', '22111061403@hunre.edu.vn', NULL, NULL, '$2y$10$s2hx4ODaRbTUJ8cSdmgnh.7iM3LIP6EBNPQQPcGdCEqbD7j1TA6P6', '114118200846899184853', NULL, 3, '2025-09-19 07:15:47', 1, '2025-09-25 04:30:34'),
(5, 'Lê Trường Giang', 'letruonggiang2004hhbg@gmail.com', NULL, NULL, '$2y$10$aUfPCVmeh81MQ0x4ctWRMef3q4dyJzIWGCgS59p1N8NPHT8xXcOKW', NULL, '796737086574204', 3, '2025-09-22 15:12:15', 1, '2025-09-24 19:06:18'),
(6, 'Giang Lê', 'gianglehhbg2004@gmail.com', NULL, NULL, '$2y$10$0Tw0t2FED86jZ1WKCChPvewpOMixb0oIz6qwAc9GH0tRTTbiBE6xW', '116654594578248973228', NULL, 3, '2025-09-25 01:48:02', 1, '2025-09-25 01:48:49'),
(7, 'mynameistrang', 'phungtrang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$2f4RUvFhO6h1FQ7T4nXdwOG0GODksyoW.mvXSvk0OjF6HAuBEHjMK', '110649348640287605072', NULL, 3, '2025-09-25 07:37:59', 1, '2025-10-13 14:47:41'),
(8, 'Trang Phùng', 'mynameistrang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$ki53nt3shPBfnW9ITYO0t.Cj3kHHycFA0MuJc9cCZeJJV9WeIoMSW', '101984925702797977265', NULL, 3, '2025-09-25 07:40:19', 1, '2025-10-13 14:23:46'),
(9, 'KHÁ BẢNH', 'banh@gmail.com', '+84368498289', 'ss', '$2y$10$PullQW1zoEdwcQeRAshjfOQvsezjv0B84Neg8Od7jgNerlKss9kOy', NULL, NULL, 3, '2025-09-25 07:41:45', 1, '2025-09-25 07:49:05'),
(10, 'Nguyễn Văn A@#123', 'hoahoavuive2004@gmail.com', '+84349459165', 'nam dinh', '$2y$10$u/T2CoeeGcYwwukFPLhwEuBqMbvMqYHYmCGOA7vISMWmqpXqD5fSK', NULL, NULL, 3, '2025-09-25 07:49:22', 1, '2025-09-25 07:50:17'),
(11, 'Nguyễn Văn A@#123', 'vuive2004@gmail.com', '+84349459165', 'nam dinh', '$2y$10$Em0OVdJU7Zb5MbMpKOkHA.Be0eRbd9akuIqyOkOf9xU2vRJbLTNae', NULL, NULL, 3, '2025-09-25 07:50:37', 1, '2025-09-25 07:51:14'),
(12, 'KHÁ BẢNH', 'anh@gmail.com', '+84368498289', 'ss', '$2y$10$I6bvmy0ThBI.oe6S.S.WIOH5tZqdUE2EKojOkP3/o9pNNob5IZpp6', NULL, NULL, 3, '2025-09-25 07:51:34', 1, '2025-09-25 07:53:23'),
(13, 'phùng thị trang', 'trang19012004@gmail.com', '+84349459165', 'fffffff', '$2y$10$Ihp4kKQLpmBIQGtLFUdWc.fNIgvdHZRE1QWbIqmgfUnks0XGi9xpC', NULL, NULL, 3, '2025-09-25 07:53:38', 1, '2025-09-25 07:55:25'),
(14, 'phùng thị trang', 'trag@gmail.com', '+84349459165', 'fffffff', '$2y$10$BXZj8UmWgS9tbepaC289rO.eaStSCAr.xxabv9A4qSBpnuP.JA5v6', NULL, NULL, 3, '2025-09-25 07:56:48', 1, '2025-09-25 07:59:02'),
(15, 'phùng thị trang', 'nam@gmail.com', '+84349459165', 'fffffff', '$2y$10$PXw7faGiSOMrIVbRDhYZne9OzUt962YlPHyim2tcoMAysVrtzqqo6', NULL, NULL, 3, '2025-09-25 08:02:08', 1, '2025-10-11 15:00:28');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `favourite`
--
ALTER TABLE `favourite`
  MODIFY `favourite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
