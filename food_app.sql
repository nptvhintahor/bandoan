-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 09, 2026 lúc 02:21 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `food_app`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `receiver_name`, `phone`, `address`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 2, 'Quangvinh', '0332166390', 'Nhà mây, ngõ cá, hà kang, Ha Noi', 1, '2026-05-04 11:49:19', '2026-05-04 11:49:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `foods`
--

CREATE TABLE `foods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `voucher_price` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `foods`
--

INSERT INTO `foods` (`id`, `name`, `description`, `price`, `voucher_price`, `image`, `created_at`, `updated_at`, `is_active`, `is_hidden`) VALUES
(1, 'Nem cuốn sủi tôm', 'nem cuốn sủi tôm siêu ngon từ hương vị tôm và rau xanh tự nhiên', 50000, NULL, '/storage/foods/lVQGVzrDsblLjjwVHjGBOZHQk3eRI896UMkdSaO7.jpg', '2026-05-03 22:15:23', '2026-05-04 00:20:44', 1, 0),
(2, 'Bánh mì thịt nướng', 'Bánh mì thịt nướng siêu thơm ngon giòn rụm', 42000, NULL, '/storage/foods/LMFOouCQzupSbX3fJWTq0vXLN8d1K1Q46aGjueb6.jpg', '2026-05-04 00:48:57', '2026-05-04 00:48:57', 1, 0),
(3, 'Combo mẹt bún đậu', 'Combo mẹt bún đậu siêu ngon, giá ưu đãi', 55000, NULL, '/storage/foods/6oJtrlHYpvIDtUZIb91xgJlaT2a2S1tiXJE3rIaJ.jpg', '2026-05-04 00:56:44', '2026-05-04 00:56:44', 1, 0),
(4, 'Trà ô long xoài tươi', 'Trà ô long thanh mát cơ thể thêm xoài vị ngọt chua thanh thanh, cảm hứng vị giác cho những người thích sự mát lành', 37000, NULL, '/storage/foods/HMAVLB6u6fXbYevz9grpzOXZSd0o9Ia5A8b3s2Yp.jpg', '2026-05-04 02:03:26', '2026-05-04 02:03:26', 1, 0),
(5, 'Nem nướng nha trang ( combo lớn )', 'Nem nướng nha trang tươi mát từ các loại rau củ quả kèm thịt chả tươi ngon tạo nên hương vị đậm đà từ món ăn dân dã', 85000, NULL, '/storage/foods/BgIWKYs9ZBEUY320DPV3VEYfOMbrr7ps0xG931on.jpg', '2026-05-04 12:29:05', '2026-05-04 12:29:05', 1, 0),
(6, 'Matcha hoa ly kèm kem tráng miệng', 'Matcha mát lạnh kết hợp kem ngọt ngào tạo hương vị thanh thanh tự nhiên', 65000, NULL, '/storage/foods/zTg0MNmMEZr5bdOEQbRtRwnVfPuCDjPwSWImxJrt.jpg', '2026-05-04 12:34:23', '2026-05-04 12:34:23', 1, 0),
(7, 'Gà rán giòn rụm và gà mềm', 'Combo gà rán giòn và mềm tạo hương vị sảng khoái với cơn sốt thèm gà', 70000, NULL, '/storage/foods/d1ODkOOGzUkdkQaOK0H3nDQ2AU3eXoaRYoZaT1fd.jpg', '2026-05-05 18:46:14', '2026-05-05 18:46:14', 1, 0),
(8, 'Toboki siêu cấp', 'Toboki thơm ngon dai dai mềm mềm ăn là thích', 45000, NULL, '/storage/foods/vDE4YRbZWl2Do5HXy7eIGwsaM1Kxlg5xdrGrCoJ6.jpg', '2026-05-05 18:47:23', '2026-05-05 18:47:23', 1, 0),
(9, 'Bánh cuốn chả', 'Bánh cuốn truyền thống kèm với chả lụa thơm ngon đậm vị', 35000, NULL, '/storage/foods/3s5reS9Co6vtXZ20AyFglSEFHv47AExbOs2Uk1Bv.webp', '2026-05-05 18:49:09', '2026-05-05 18:49:09', 1, 0),
(10, 'Tôm càng hấp', 'Một chút hương vị biển cả, vị tôm tươi đậm đà hấp với xả tạo cảm giác khơi dậy sự sống', 175000, 169000, '/storage/foods/xnnzi0i3vLBfOGONQTfM7QbIVdN1RQS7vtITOW8i.jpg', '2026-05-05 18:51:32', '2026-05-05 19:43:16', 1, 0),
(11, 'Sủi cảo', 'Đổi mới hương vị bằng cách thưởng thức sủi cảo mang công thức chuyên nghiệp đến từ Việt Nam', 46000, NULL, '/storage/foods/YznBc6k24JBbOjcPvzZfGVxyZ6RtsouYi8NnFCjL.jpg', '2026-05-05 18:53:16', '2026-05-05 18:53:16', 1, 0),
(13, 'Phờ bò', 'Phở bò mang đậm ẩm thực cốt lõi ngon từ nước dùng cho đến từng sợi phở', 35000, 38000, '/storage/foods/C3PEgeMV2oqon5wcCl751ZMXovPFiAEN7DlEj4TF.avif', '2026-05-05 19:00:02', '2026-05-05 19:40:38', 1, 0),
(14, 'Cơm rang thập cẩm bò', 'Cơm rang là thức ăn đổi mới từ cơm kết hợp các nguyên liệu khác làm thêm phần ngon miệng', 45000, 37000, '/storage/foods/BT5X96yW6whiYtHWZuittzn6PrEJENfVkARpqziE.jpg', '2026-05-05 19:01:33', '2026-05-05 19:41:00', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `content`, `sender`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 4, 'hihi xin chào shop', 'user', 1, '2026-05-05 20:34:41', '2026-05-09 05:16:41'),
(2, 4, 'xin chào', 'admin', 1, '2026-05-05 20:34:56', '2026-05-05 20:35:49'),
(3, 2, 'xin chào shop', 'user', 1, '2026-05-05 20:41:54', '2026-05-09 05:16:11'),
(4, 2, 'shop xin chào bạn', 'admin', 1, '2026-05-05 20:42:09', '2026-05-09 05:14:48'),
(5, 5, 'shop cho mình xin menu', 'user', 1, '2026-05-09 05:16:10', '2026-05-09 05:16:41'),
(6, 5, 'có gì đâu mà xin', 'admin', 1, '2026-05-09 05:16:26', '2026-05-09 05:16:28'),
(7, 5, 'có nem chua với nước mắm thôi', 'admin', 1, '2026-05-09 05:16:32', '2026-05-09 05:16:33'),
(8, 5, 'ngon thí', 'user', 1, '2026-05-09 05:16:43', '2026-05-09 05:16:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_29_150048_create_foods_table', 1),
(5, '2026_04_29_162321_create_orders_table', 1),
(6, '2026_05_03_075656_add_is_active_to_foods_table', 2),
(7, '2026_05_04_044011_add_is_hidden_to_foods_table', 3),
(8, '2026_05_05_000000_add_profile_fields_to_users_table', 4),
(9, '2026_05_04_183909_create_addresses_table', 5),
(10, '2026_05_05_000001_add_points_to_users_table', 6),
(11, '2026_05_05_000002_create_point_histories_table', 6),
(12, '2026_05_06_add_voucher_price_to_foods', 7),
(13, '2026_05_06_create_messages_table', 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `items` text NOT NULL,
  `total` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Chờ xác nhận',
  `payment_method` varchar(255) NOT NULL DEFAULT 'cod',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `items`, `total`, `status`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 2, '{\"2\":{\"name\":\"Nem cu\\u1ed1n s\\u1ee7i t\\u00f4m\",\"price\":50000,\"image\":\"\\/storage\\/foods\\/c0rbWrvpbEt0dygdMUYDKIb5IfTtmbDpebg7GfMJ.jpg\",\"quantity\":1}}', 50000, 'Hoàn thành', 'cod', '2026-05-03 00:47:49', '2026-05-04 11:17:10'),
(2, 2, '{\"1\":{\"name\":\"Nem cu\\u1ed1n s\\u1ee7i t\\u00f4m\",\"price\":50000,\"image\":\"\\/storage\\/foods\\/lVQGVzrDsblLjjwVHjGBOZHQk3eRI896UMkdSaO7.jpg\",\"quantity\":1}}', 50000, 'Hoàn thành', 'cod', '2026-05-03 22:16:03', '2026-05-04 01:06:16'),
(3, 2, '{\"2\":{\"name\":\"B\\u00e1nh m\\u00ec th\\u1ecbt n\\u01b0\\u1edbng\",\"price\":42000,\"image\":\"\\/storage\\/foods\\/LMFOouCQzupSbX3fJWTq0vXLN8d1K1Q46aGjueb6.jpg\",\"quantity\":1}}', 42000, 'Đang xử lý', 'online', '2026-05-04 00:50:56', '2026-05-04 12:16:05'),
(4, 2, '{\"3\":{\"name\":\"Combo m\\u1eb9t b\\u00fan \\u0111\\u1eadu\",\"price\":55000,\"image\":\"\\/storage\\/foods\\/6oJtrlHYpvIDtUZIb91xgJlaT2a2S1tiXJE3rIaJ.jpg\",\"quantity\":1}}', 55000, 'Đang giao', 'online', '2026-05-04 01:05:49', '2026-05-04 01:06:01'),
(5, 2, '{\"4\":{\"name\":\"Tr\\u00e0 \\u00f4 long xo\\u00e0i t\\u01b0\\u01a1i\",\"price\":37000,\"image\":\"\\/storage\\/foods\\/HMAVLB6u6fXbYevz9grpzOXZSd0o9Ia5A8b3s2Yp.jpg\",\"quantity\":2}}', 74000, 'Hoàn thành', 'cod', '2026-05-04 12:12:01', '2026-05-04 12:15:31'),
(6, 3, '{\"5\":{\"name\":\"Nem n\\u01b0\\u1edbng nha trang ( combo l\\u1edbn )\",\"price\":85000,\"image\":\"\\/storage\\/foods\\/BgIWKYs9ZBEUY320DPV3VEYfOMbrr7ps0xG931on.jpg\",\"quantity\":1}}', 85000, 'Hoàn thành', 'cod', '2026-05-05 09:52:04', '2026-05-05 09:52:45'),
(7, 4, '{\"13\":{\"name\":\"Ph\\u1edd b\\u00f2\",\"price\":35000,\"image\":\"\\/storage\\/foods\\/C3PEgeMV2oqon5wcCl751ZMXovPFiAEN7DlEj4TF.avif\",\"quantity\":1}}', 35000, 'Đang giao', 'online', '2026-05-05 19:02:27', '2026-05-05 19:02:48'),
(8, 5, '{\"13\":{\"name\":\"Ph\\u1edd b\\u00f2\",\"price\":35000,\"image\":\"\\/storage\\/foods\\/C3PEgeMV2oqon5wcCl751ZMXovPFiAEN7DlEj4TF.avif\",\"quantity\":1}}', 35000, 'Hoàn thành', 'online', '2026-05-09 05:11:20', '2026-05-09 05:14:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `point_histories`
--

CREATE TABLE `point_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `point_histories`
--

INSERT INTO `point_histories` (`id`, `user_id`, `amount`, `description`, `order_id`, `created_at`, `updated_at`) VALUES
(1, 2, 100, 'Hoàn thành đơn hàng #5', 5, '2026-05-04 12:15:31', '2026-05-04 12:15:31'),
(2, 3, 100, 'Hoàn thành đơn hàng #6', 6, '2026-05-05 09:52:45', '2026-05-05 09:52:45'),
(3, 5, 100, 'Hoàn thành đơn hàng #8', 8, '2026-05-09 05:14:40', '2026-05-09 05:14:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `birthday`, `gender`, `email_verified_at`, `password`, `remember_token`, `points`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', NULL, NULL, NULL, '2026-05-03 00:56:05', '$2y$12$jzTtUj0eeREwMJEk07sZu.XhaPVyunx0mPuQY4JzS39a280iN7.BG', 'idDrGuFpZM', 0, '2026-05-03 00:56:06', '2026-05-03 00:56:06'),
(2, 'quang vinh', 'hihi123@gmail.com', '0853461759', '2026-05-14', 'Khác', NULL, '$2y$12$nNhbfTB4/qxXZiZi7rL1LOKNM78esdF7O1vkMmf6G.krCAeimuxCi', NULL, 100, '2026-05-03 22:13:51', '2026-05-04 12:15:31'),
(3, 'Văn Tuấn', 'vantuan123@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$W2Lpeqwx4VZ6SpNyfNJoh.Q98zc03Ewa.Dy7MzEnMZpOGy1B62hvi', NULL, 100, '2026-05-05 09:51:13', '2026-05-05 09:52:45'),
(4, 'Siêu nhân', 'sieunhan123@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$2n9.URRL4DeH1bF6FZaEneXkwZwXmxHdfbrtUF6XtYDCxyizGrgeO', NULL, 0, '2026-05-05 18:43:42', '2026-05-05 18:43:42'),
(5, 'Tuanpham', 'ptuan204108@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$IHs7JXvI13ARXzKKvD7KLOEHIPAJmr4GQKFEcZ4t2PIeI/uryOzJq', NULL, 100, '2026-05-09 05:09:33', '2026-05-09 05:14:40');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `point_histories`
--
ALTER TABLE `point_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `point_histories_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `foods`
--
ALTER TABLE `foods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `point_histories`
--
ALTER TABLE `point_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `point_histories`
--
ALTER TABLE `point_histories`
  ADD CONSTRAINT `point_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
