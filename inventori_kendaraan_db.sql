/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100432
 Source Host           : localhost:3306
 Source Schema         : inventori_kendaraan_db

 Target Server Type    : MySQL
 Target Server Version : 100432
 File Encoding         : 65001

 Date: 11/11/2026 13:21:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for carts
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `laptop_id` int UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT 1,
  `created_at` datetime NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `laptop_id`(`laptop_id`) USING BTREE,
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of carts
-- ----------------------------

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES (1, 'Motor Matic', 'Motor matic', '2026-11-11 11:14:51');
INSERT INTO `categories` VALUES (2, 'Motor Bebek', 'Motor Bebek', '2026-11-11 11:14:51');
INSERT INTO `categories` VALUES (3, 'Motor Sport', 'Motor Sport', '2026-11-11 11:14:51');

-- ----------------------------
-- Table structure for instansis
-- ----------------------------
DROP TABLE IF EXISTS `instansis`;
CREATE TABLE `instansis`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `code`(`code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of instansis
-- ----------------------------
INSERT INTO `instansis` VALUES (1, 'bpad', 'bpad', 'kebun jeruk', '08232222222', 'active', '2026-11-11 06:16:35', '2026-11-11 06:16:35');

-- ----------------------------
-- Table structure for kendaraans
-- ----------------------------
DROP TABLE IF EXISTS `kendaraans`;
CREATE TABLE `kendaraans`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Kode inventaris unit',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `brand` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `warna` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `specification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `stock` int NOT NULL DEFAULT 1 COMMENT 'Total unit fisik',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('baik','rusak_ringan','rusak_berat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'baik',
  `created_at` datetime NULL DEFAULT current_timestamp,
  `updated_at` datetime NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `code`(`code`) USING BTREE,
  INDEX `category_id`(`category_id`) USING BTREE,
  CONSTRAINT `kendaraans_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kendaraans
-- ----------------------------
INSERT INTO `kendaraans` VALUES (1, 1, 'mtc-001', 'vario', 'honda', NULL, 'bagus', 0, '1794374552_3b167e2c567de3d19f41.png', 'baik', '2026-11-11 04:45:16', '2026-11-11 06:16:55');

-- ----------------------------
-- Table structure for laptops
-- ----------------------------
DROP TABLE IF EXISTS `laptops`;
CREATE TABLE `laptops`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Kode inventaris unit',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `brand` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `processor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ram` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `storage` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `screen_size` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `specification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `price_per_day` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `price_per_month` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `stock` int NOT NULL DEFAULT 1 COMMENT 'Total unit fisik',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('available','maintenance','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `created_at` datetime NULL DEFAULT current_timestamp,
  `updated_at` datetime NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `code`(`code`) USING BTREE,
  INDEX `category_id`(`category_id`) USING BTREE,
  CONSTRAINT `laptops_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of laptops
-- ----------------------------
INSERT INTO `laptops` VALUES (1, 1, 'LPT-001', 'ASUS ROG Strix G15', 'ASUS', 'AMD Ryzen 7 5800H', '16GB', '512GB SSD', '15.6\"', 'GPU RTX 3060, layar 144Hz, cocok untuk gaming & render berat.', 150000.00, 3750000.00, 3, NULL, 'available', '2026-11-11 11:14:51', '2026-11-11 11:14:51');
INSERT INTO `laptops` VALUES (2, 2, 'LPT-002', 'Lenovo ThinkPad E14', 'Lenovo', 'Intel Core i5-1135G7', '8GB', '256GB SSD', '14\"', 'Ringan, baterai awet, cocok untuk kerja & presentasi.', 85000.00, 2125000.00, 5, NULL, 'available', '2026-11-11 11:14:51', '2026-11-11 11:14:51');
INSERT INTO `laptops` VALUES (3, 3, 'LPT-003', 'MacBook Pro 14\" M2', 'Apple', 'Apple M2 Pro', '16GB', '512GB SSD', '14.2\"', 'Layar Liquid Retina XDR, ideal untuk editing video & desain.', 250000.00, 6250000.00, 2, NULL, 'available', '2026-11-11 11:14:51', '2026-11-11 11:14:51');
INSERT INTO `laptops` VALUES (4, 2, 'LPT-004', 'HP Pavilion 14', 'HP', 'Intel Core i5-1235U', '8GB', '512GB SSD', '14\"', 'Desain tipis, cocok untuk mahasiswa dan pekerja kantoran.', 90000.00, 2250000.00, 4, NULL, 'available', '2026-11-11 11:14:51', '2026-11-11 11:14:51');
INSERT INTO `laptops` VALUES (5, 1, 'LPT-005', 'Acer Predator Helios 300', 'Acer', 'Intel Core i7-12700H', '16GB', '1TB SSD', '15.6\"', 'GPU RTX 3070Ti, performa tinggi untuk gaming kompetitif.', 175000.00, 4375000.00, 2, NULL, 'available', '2026-11-11 11:14:51', '2026-11-11 11:14:51');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2026-11-11-000001', 'App\\Database\\Migrations\\CreateInventoryRelations', 'default', 'App', 1794376499, 1);

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rental_id` int UNSIGNED NOT NULL,
  `amount` decimal(12, 2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'Transfer Bank',
  `proof_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('pending','verified','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `paid_at` datetime NULL DEFAULT NULL,
  `verified_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `rental_id`(`rental_id`) USING BTREE,
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payments
-- ----------------------------

-- ----------------------------
-- Table structure for pegawais
-- ----------------------------
DROP TABLE IF EXISTS `pegawais`;
CREATE TABLE `pegawais`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nip`(`nip`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pegawais
-- ----------------------------
INSERT INTO `pegawais` VALUES (1, '111', 'tes pegawai', 'pns', '082399349304', 'active', '2026-11-11 06:16:15', '2026-11-11 06:16:15');

-- ----------------------------
-- Table structure for penempatan_kendaraan
-- ----------------------------
DROP TABLE IF EXISTS `penempatan_kendaraan`;
CREATE TABLE `penempatan_kendaraan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kendaraan_id` int UNSIGNED NOT NULL,
  `pegawai_id` int UNSIGNED NOT NULL,
  `instansi_id` int UNSIGNED NOT NULL,
  `assigned_at` date NOT NULL,
  `returned_at` date NULL DEFAULT NULL,
  `status` enum('assigned','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'assigned',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `penempatan_kendaraan_kendaraan_id_foreign`(`kendaraan_id`) USING BTREE,
  INDEX `penempatan_kendaraan_pegawai_id_foreign`(`pegawai_id`) USING BTREE,
  INDEX `penempatan_kendaraan_instansi_id_foreign`(`instansi_id`) USING BTREE,
  CONSTRAINT `penempatan_kendaraan_instansi_id_foreign` FOREIGN KEY (`instansi_id`) REFERENCES `instansis` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `penempatan_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraans` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `penempatan_kendaraan_pegawai_id_foreign` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of penempatan_kendaraan
-- ----------------------------
INSERT INTO `penempatan_kendaraan` VALUES (1, 1, 1, 1, '2026-11-11', NULL, 'assigned', '', '2026-11-11 06:16:55', '2026-11-11 06:16:55');

-- ----------------------------
-- Table structure for rental_items
-- ----------------------------
DROP TABLE IF EXISTS `rental_items`;
CREATE TABLE `rental_items`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rental_id` int UNSIGNED NOT NULL,
  `laptop_id` int UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT 1,
  `price_per_day` decimal(12, 2) NOT NULL,
  `price_per_month` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `rental_id`(`rental_id`) USING BTREE,
  INDEX `laptop_id`(`laptop_id`) USING BTREE,
  CONSTRAINT `rental_items_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `rental_items_ibfk_2` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rental_items
-- ----------------------------

-- ----------------------------
-- Table structure for rentals
-- ----------------------------
DROP TABLE IF EXISTS `rentals`;
CREATE TABLE `rentals`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `actual_return_date` date NULL DEFAULT NULL,
  `total_days` int NOT NULL DEFAULT 1,
  `rental_type` enum('daily','monthly') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'daily',
  `subtotal` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `late_fee` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected','ongoing','returned','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_self_pickup` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'yes' COMMENT 'yes = diambil sendiri oleh peminjam',
  `recipient_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `recipient_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `recipient_relation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Hubungan dengan peminjam, mis: Rekan Kerja/Keluarga',
  `recipient_ktp_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `recipient_ktp_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `recipient_ktp_verified` enum('unverified','verified','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unverified',
  `recipient_ktp_verified_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT current_timestamp,
  `updated_at` datetime NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `invoice_code`(`invoice_code`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rentals
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ktp_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Nomor KTP untuk jaminan sewa',
  `ktp_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Foto/scan KTP peminjam',
  `ktp_verified` enum('unverified','verified','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unverified',
  `ktp_verified_at` datetime NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NULL DEFAULT current_timestamp,
  `updated_at` datetime NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Administrator', 'admin@inventori.test', '081234567890', 'Kantor Pusat', NULL, NULL, 'unverified', NULL, '$2y$10$6o.wz5RmboEQsJ9iz3T0Ge5vUdruPi/68cxrUKS6a8/LAPSYl/jNa', 'admin', NULL, 'active', '2026-11-11 11:14:51', '2026-11-11 11:18:14');
INSERT INTO `users` VALUES (2, 'Budi Santoso', 'budi@example.test', '081298765432', 'Jl. Merdeka No. 10, Yogyakarta', NULL, NULL, 'unverified', NULL, '$2y$10$6o.wz5RmboEQsJ9iz3T0Ge5vUdruPi/68cxrUKS6a8/LAPSYl/jNa', 'customer', NULL, 'active', '2026-11-11 11:14:51', '2026-11-11 11:18:16');
INSERT INTO `users` VALUES (3, 'tes', 'tes@gmail.com', '082399349304', 'jl', NULL, NULL, 'unverified', NULL, '$2y$10$6o.wz5RmboEQsJ9iz3T0Ge5vUdruPi/68cxrUKS6a8/LAPSYl/jNa', 'customer', NULL, 'active', '2026-11-11 04:17:56', '2026-11-11 05:43:44');

SET FOREIGN_KEY_CHECKS = 1;
