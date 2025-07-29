-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 29. čec 2025, 20:03
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `dr_web`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `apartments`
--

CREATE TABLE `apartments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `main_image_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `apartments`
--

INSERT INTO `apartments` (`id`, `name`, `capacity`, `description`, `price`, `main_image_name`, `created_at`, `updated_at`) VALUES
(9, 'AHOJ', 80, 'BZUMBZUM', 500.00, 'main_1753781884.jpeg', '2025-07-29 09:38:04', '2025-07-29 09:38:04');

-- --------------------------------------------------------

--
-- Struktura tabulky `apartment_images`
--

CREATE TABLE `apartment_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `apartment_id` int(10) UNSIGNED NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `apartment_images`
--

INSERT INTO `apartment_images` (`id`, `apartment_id`, `is_main`, `position`, `created_at`) VALUES
(17, 9, 0, 0, '2025-07-29 09:38:04'),
(18, 9, 0, 1, '2025-07-29 09:38:04'),
(22, 9, 0, 0, '2025-07-29 11:28:22');

-- --------------------------------------------------------

--
-- Struktura tabulky `reservations`
--

CREATE TABLE `reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `apartment_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `second_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$Ai36f9gt36UO69YEtq2yseJ5aUIz8OsVdqNeF0mFf8O2vCayeouEm', 'user');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `apartments`
--
ALTER TABLE `apartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `apartment_images`
--
ALTER TABLE `apartment_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_apartment` (`apartment_id`);

--
-- Indexy pro tabulku `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservations_apartment` (`apartment_id`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `apartments`
--
ALTER TABLE `apartments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pro tabulku `apartment_images`
--
ALTER TABLE `apartment_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pro tabulku `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `apartment_images`
--
ALTER TABLE `apartment_images`
  ADD CONSTRAINT `fk_apartment_images_apartment` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_apartment` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
