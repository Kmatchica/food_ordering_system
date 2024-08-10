-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2024 at 03:40 AM
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
-- Database: `fos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Drinks'),
(3, 'Pares'),
(2, 'Silog');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image_url`, `category_id`) VALUES
(1, 'Coca Cola', 'Refreshing soda drink', 25.00, NULL, 1),
(2, 'Tapsilog', 'Tapa Sinangag Itlog', 90.00, NULL, 2),
(3, 'Overload Pares', 'Pares with Bituka, Lechon Kawali, Chicharon, etc', 120.00, NULL, 3),
(4, 'Hotsilog', 'Hotdog, Sinangag, Itlog', 80.00, NULL, 2),
(5, 'Sprite', 'Refreshing Drink', 25.00, NULL, 1),
(6, 'Original Pares', 'Taste our very own home made Pares', 90.00, NULL, 3),
(7, 'Mami Pares', 'Taste our very own home made Mami Pares', 90.00, NULL, 3),
(8, 'Pares with Egg', 'Pares with Egg', 100.00, NULL, 3),
(9, 'Pares with Chicaron Bulaklak', 'Pares with Chicaron Bulaklak', 90.00, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `ticket_number` varchar(5) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending payment','pending','completed','canceled') NOT NULL DEFAULT 'pending payment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `total_amount`, `ticket_number`, `order_date`, `status`) VALUES
(1, 460.00, '00002', '2024-08-02 19:47:47', 'pending'),
(2, 460.00, '00003', '2024-08-02 19:48:01', 'pending'),
(3, 0.00, '00004', '2024-08-02 19:54:41', 'pending payment'),
(4, 0.00, '00005', '2024-08-02 19:57:40', 'pending payment'),
(5, 0.00, '00006', '2024-08-02 22:15:56', 'pending payment'),
(6, 0.00, '00007', '2024-08-02 22:17:26', 'pending payment'),
(7, 115.00, '00001', '2024-08-02 22:29:32', 'pending'),
(8, 0.00, '00002', '2024-08-02 22:31:25', 'pending payment'),
(9, 0.00, '00003', '2024-08-02 22:59:44', 'pending payment'),
(10, 0.00, '00004', '2024-08-02 23:32:01', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 5, 2, 50.00),
(2, 1, 6, 2, 180.00),
(3, 2, 5, 2, 50.00),
(4, 2, 6, 2, 180.00),
(5, 3, 5, 2, 50.00),
(6, 4, 5, 1, 25.00),
(7, 4, 6, 1, 90.00),
(8, 4, 7, 1, 90.00),
(9, 5, 5, 2, 50.00),
(10, 5, 7, 1, 90.00),
(11, 5, 4, 1, 80.00),
(12, 6, 7, 1, 90.00),
(13, 6, 6, 1, 90.00),
(14, 6, 4, 1, 80.00),
(15, 7, 5, 1, 25.00),
(16, 7, 6, 1, 90.00),
(17, 8, 5, 1, 25.00),
(18, 8, 7, 1, 90.00),
(19, 9, 5, 1, 25.00),
(20, 10, 4, 1, 80.00),
(21, 10, 2, 1, 90.00);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_numbers`
--

CREATE TABLE `ticket_numbers` (
  `date` date NOT NULL,
  `current_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_numbers`
--

INSERT INTO `ticket_numbers` (`date`, `current_number`) VALUES
('2024-08-01', 7),
('2024-08-02', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('cashier','manager') NOT NULL DEFAULT 'cashier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(5, 'cashier', '$2y$10$7RSxk6UCEH/iqFyPDFdF5ORjv3jPnQ5hqlb7ou66MPH5xh4l2fy7u', 'cashier'),
(6, 'manager', '$2y$10$Dx5X3uHxhPvV2eLtPIYFROr5rb4vhVHYBddbGLW7hh.EnAORFKQUi', 'manager');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `ticket_numbers`
--
ALTER TABLE `ticket_numbers`
  ADD PRIMARY KEY (`date`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
