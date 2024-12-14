-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 06:46 PM
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
-- Database: `public_transport`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `route` varchar(255) NOT NULL,
  `tickets` int(11) NOT NULL,
  `ticket_code` varchar(255) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending Payment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `phone`, `route`, `tickets`, `ticket_code`, `total`, `status`) VALUES
(1, '+250784242049', 'Kigali to Huye', 2, '', 4000.00, 'Paid'),
(2, '+250784242049\n', 'Kigali to Rubavu', 1, '', 3000.00, 'Pending Payment'),
(3, '+250784242049', 'Kigali to Huye', 1, 'TCK2271', 2000.00, 'Pending Payment'),
(4, '+250784242049', 'Kigali to Rubavu', 1, 'TCK3690', 3000.00, 'Pending Payment'),
(5, '+250785504239', 'Kigali to Musanze', 2, 'TCK6063', 5000.00, 'Paid'),
(6, '+250783912384\n', 'Kigali to Rubavu', 3, 'TCK4239', 9000.00, 'Pending Payment'),
(7, '+250783912381', 'Kigali to Musanze', 2, 'TCK6363', 5000.00, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `id_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `phone`, `full_name`, `id_number`) VALUES
(1, '+250784242049', 'uwera', '01023'),
(7, '+250784242049\n', 'Aime', '12121'),
(14, '+250785504239', 'Damascene', '212321'),
(16, '+250783912384\n', 'Patrick', '11111'),
(17, '+250783912380', 'hakizimana', '001100'),
(18, '+250784242011', 'Issa KUBWIMANA', '22222'),
(19, '+250783912381', 'SHEMA', '00227');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `route` varchar(255) NOT NULL,
  `FRW` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `route`, `FRW`) VALUES
(1, 'Kigali to Huye', 2000.00),
(2, 'Kigali to Musanze', 2500.00),
(3, 'Kigali to Rubavu', 3000.00),
(4, 'Kigali to Muhanga', 1200.00),
(5, 'Kigali to Bugesera', 800.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phone` (`phone`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `unique_phone` (`phone`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`phone`) REFERENCES `passengers` (`phone`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
