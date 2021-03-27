-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 27, 2021 at 03:33 PM
-- Server version: 8.0.17
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `estore`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `spAddProduct` (IN `name` VARCHAR(255), IN `price` VARCHAR(255), IN `image` VARCHAR(255), IN `brand` VARCHAR(255), IN `quantity` VARCHAR(255), IN `description` TEXT)  NO SQL
INSERT INTO
	products
    	(name, price, image, brand, quantity, description)
    VALUES
    	(name, price, image, brand, quantity, description)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spAddReview` (IN `rating` TINYINT, IN `comments` TEXT, IN `dateReviewed` TIMESTAMP, IN `productId` INT, IN `userId` INT)  INSERT INTO reviews
	(rating, comments, dateReviewed, productId, userId)
VALUES
	(rating, comments, dateReviewed, productId, userId)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDeleteProduct` (IN `id` INT)  NO SQL
UPDATE products p SET
	active = 0
    WHERE p.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spFindUserByEmail` (IN `email` VARCHAR(255))  NO SQL
SELECT 
	*
FROM users u
	WHERE u.email = email$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spGetProductById` (IN `id` INT)  NO SQL
SELECT
	p.id as productId,
    p.name,
    p.price,
    p.image,
    p.brand,
    p.quantity,
    p.description,
    ROUND(0.5 * 
    	(SELECT
         	AVG(rating) 
         FROM reviews 
         	WHERE productId = p.id) / 0.5, 1) as reviewRating,
    (SELECT
    	COUNT(id) 
         FROM reviews 
         	WHERE productId = p.id) as reviewCount,
    r.id as reviewId,
    r.rating,
    r.comments,
    r.dateReviewed,
    u.id as userId,
    u.name as userName
FROM products p
	LEFT JOIN reviews r ON p.id = r.productId
    LEFT JOIN users u ON r.userId = u.id
    WHERE p.id = id
    ORDER BY p.id asc, r.id desc$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spGetProducts` ()  SELECT 
	p.*, 
    ROUND(0.5 * 
    	(SELECT
         	AVG(rating) 
         FROM reviews 
         	WHERE productId = p.id) / 0.5, 1) as reviewRating,
    (SELECT
    	COUNT(id) 
         FROM reviews 
         	WHERE productId = p.id) as reviewCount
FROM products p
	WHERE p.active = 1
	ORDER BY p.id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spGetReviewById` (IN `id` INT)  NO SQL
SELECT 
    r.*, 
    u.id as userId,
    u.name as userName
FROM reviews r
    JOIN users u ON r.userId = u.id
    WHERE r.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spGetUserById` (IN `id` INT)  NO SQL
SELECT 
	*
FROM users u
	WHERE u.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spRegisterUser` (IN `name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255), IN `role` VARCHAR(255))  NO SQL
INSERT INTO 
	users (name, email, password, role)
	VALUES(name, email, password, role)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSearchOtherUsersEmails` (IN `id` INT, IN `email` VARCHAR(255))  NO SQL
SELECT 
	*
FROM users u
	WHERE u.email = email && u.id != id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdateProduct` (IN `id` INT, IN `name` VARCHAR(255), IN `price` VARCHAR(255), IN `image` VARCHAR(255), IN `brand` VARCHAR(255), IN `quantity` VARCHAR(255), IN `description` TEXT)  NO SQL
UPDATE products p SET
	p.name = name,
    p.price = price,
    p.image = image,
    p.brand = brand,
    p.quantity = quantity,
    p.description = description
    WHERE p.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdateUser` (IN `id` INT, IN `name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255))  NO SQL
UPDATE users u
	SET 
    	u.name = COALESCE(name, u.name),
        u.email = COALESCE(email,u.email),
        u.password = COALESCE(password, u.password)
    WHERE u.id = id$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `brand` varchar(255) COLLATE utf8_bin NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `brand`, `quantity`, `description`, `active`) VALUES
(1, 'Airpods Wireless Bluetooth Headphones', '89.99', '/assets/images/airpods.jpg', 'Apple', 3, 'Bluetooth technology lets you connect to compatible devices wirelessly. High-quality AAC audio offers immersive listening experience. Built-in microphone allows you to take calls while working.', 1),
(2, 'iPhone 11 Pro 256GB Memory', '599.99', '/assets/images/phone.jpg', 'Apple', 10, 'Triple-lens cameras with new ultra wide-angle lens. More durable, water resistant body. Matte finish and new dark green color. Night Mode for better low-light images. Haptic Touch instead of 3D Touch. Ultra Wideband support. A13 chip. Faster WiFi and LTE.', 1),
(3, 'Cannon E0S 80D DSLR Camera', '929.99', '/assets/images/camera.jpg', 'Cannon', 10, 'Offers pair of robust focusing systems and intuitive design. Features 24.2MP APS-C CMOS sensor and DIGIC 6 image processor to capture high-resolution images up to 7 fps and Full HD 1080p60 video, both with reduced noise and high sensitivity up to an expanded ISO 25600 for working in difficult lighting conditions.', 1),
(4, 'Sony Playstation 4 Pro White Version', '399.99', '/assets/images/playstation.jpg', 'Sony', 10, 'Updated graphics architecture and support for 4K gaming provides increased detail, higher-resolution graphics, and faster or more stable frame rates. High Dynamic Range gaming and content take advantage of increased color depth and contrast on compatible displays. Allows for 4K video playback from streaming services.', 1),
(5, 'Logitech G-Series Gaming Mouse', '49.99', '/assets/images/mouse.jpg', 'Logitech', 10, 'Ergonomic and customizable mouse designed to provide you with accuracy and performance needed for gaming. Power efficient optical sensor has 200 to 12,000 DPI range and zero smoothing. With the included USB receiver and Bluetooth connectivity, it is compatible with a wide range of devices and can be paired to two devices simultaneously. Powered by two AA batteries, yet can work with only one installed without a performance drop.', 1),
(6, 'Amazon Echo Dot 3rd Generation', '29.99', '/assets/images/dot.jpg', 'Amazon', 10, 'Equipped with Bluetooth and WiFi connectivity and Amazon Alexa to make nearly any wired speaker system wireless and voice-controlled. Onboard speaker for streaming and Alexa functionality at more moderate volumes. Provides access to Internet-based functions such as online shopping, weather reports, sports results, etc.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comments` text COLLATE utf8_bin NOT NULL,
  `dateReviewed` timestamp NOT NULL,
  `productId` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `rating`, `comments`, `dateReviewed`, `productId`, `userId`) VALUES
(1, 5, 'Really nice product. Superior quality.', '2021-03-18 09:30:00', 1, 2),
(2, 4, 'Not a bad choice. Sound is pretty clear.', '2021-03-18 10:15:00', 1, 3),
(37, 2, 'so so', '2021-03-25 06:18:16', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `role` varchar(25) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'John Doe', 'john.doe@gmail.com', '$2y$10$765pKeJ1Ipd/KA8MV9C/O.OrlQQOAYgD.04VqHxL/l/nqHjs7KfqK', 'administrator'),
(2, 'Jane Smith', 'jane.smith@gmail.com', '$2y$10$g/yXa0E8oFidvP6boyboseOGAWGpahZFMFj2BmDyLMA/yKfVNyRyu', 'customer'),
(3, 'Jack Jones', 'jack.jones@gmail.com', '$2y$10$23glBE/X6aq8oZ.4943nauHqeQQyB7snrXHLdUUCXDa9esNglci76', 'customer'),
(4, 'Tom Tuttle', 'tom.tuttle@gmail.com', '$2y$10$4wz1X5sFu2oYEYgSgZOdnOG2v0Jf6mT.HI/tTgFTcDSPstL3jGI/2', 'customer'),
(7, 'David Donaldson', 'david.donaldson@gmail.com', '$2y$10$fuBfpGxkGHdhlPFY/smzQOqpnnt3d5syH9YbtIzYoAN/q290UwFF2', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
