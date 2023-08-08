-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2023 at 04:19 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restful_api_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `first_name`, `last_name`, `email`, `created_at`, `updated_at`) VALUES
(1, 1, 'Alessandro', 'Tara', 'alessandrotara@gmail.com', '2023-07-29', '2023-07-29'),
(2, 1, 'Francesco', 'Tarao', 'alessandrotara@gmail.com', '2023-07-29', '2023-08-07'),
(3, 1, 'Mateo', 'Fornara', 'mateofornara@gmail.com', '2023-07-29', '2023-08-07'),
(4, 1, 'Helene', 'Fischer', 'helenefischer@gmail.com', '2023-07-29', '2023-07-31'),
(6, 1, 'Aurora', 'Gaze', 'auroragaze@gmail.com', '2023-07-31', '0000-00-00'),
(7, 2, 'Klea', 'Tare', 'kleatare@gmail.com', '2023-07-31', '0000-00-00'),
(8, 2, 'Antonetta', 'Carra', 'antonettacarra@gmail.com', '2023-07-31', '0000-00-00'),
(10, 2, 'Jurgen', 'Klinsmann', 'jurgenklinsmann@gmail.com', '2023-07-31', '0000-00-00'),
(11, 2, 'Greta', 'Talite', 'gretaTalite@gmail.com', '2023-07-31', '0000-00-00'),
(14, 1, 'Ornela', 'Fado', 'ornelafado@gmail.com', '2023-08-02', '0000-00-00'),
(15, 1, 'Greta', 'Keci', 'gretakeci@gmail.com', '2023-08-02', '0000-00-00'),
(16, 1, 'Amanda', 'Figo', 'amandafigo@gmail.com', '2023-08-02', '0000-00-00'),
(17, 1, 'Sara', 'Capri', 'saracapri@gmail.com', '2023-08-03', '0000-00-00'),
(18, 2, 'Bleona', 'Dino', 'bleonadino@gmail.com', '2023-08-03', '0000-00-00'),
(19, 2, 'Alice', 'Beato', 'alicebeato@gmail.com', '2023-08-03', '0000-00-00'),
(21, 1, 'Emanuela', 'Ferrero', 'emanuelacamascella@gmail.com', '2023-08-04', '2023-08-07'),
(23, 1, 'Luis', 'Figo', 'luisfigo@gmail.com', '2023-08-04', '0000-00-00'),
(24, 1, 'Fabio', 'Matarella', 'fabiomatarella@gmail.com', '2023-08-04', '0000-00-00'),
(25, 1, 'Laura', 'Castagna', 'lauracastagna@gmail.com', '2023-08-05', '0000-00-00'),
(26, 1, 'Orlando', 'Brown', 'orlandobrown@gmail.com', '2023-08-05', '0000-00-00'),
(27, 1, 'Lotar', 'Mateus', 'lotarmateus@gmail.com', '2023-08-05', '2023-08-05'),
(28, 1, 'Rudi', 'Voeller', 'rudivoeller@gmail.com', '2023-08-05', '2023-08-06'),
(34, 1, 'Marlon', 'Brando', 'marlonbrando@gmail.com', '2023-08-05', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'leonard', 'leonard@gmail.com', '123456', '2023-08-01 13:40:33', '2023-08-01 13:40:33'),
(2, 'eva', 'eva@gmail.com', '123456', '2023-08-01 13:40:33', '2023-08-01 13:40:33'),
(3, 'maria', 'maria@gmail.com', '123456', '2023-08-01 13:41:20', '2023-08-01 13:41:20'),
(4, 'andrea', 'andrea@gmail.com', '123456', '2023-08-01 13:42:54', '2023-08-01 13:42:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
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
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
