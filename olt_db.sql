-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 10 Agu 2025 pada 21.34
-- Versi server: 8.0.42-0ubuntu0.24.04.2
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `olt_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `oid_mappings`
--

CREATE TABLE `oid_mappings` (
  `vendor` varchar(50) NOT NULL,
  `oid_name` varchar(255) DEFAULT NULL,
  `oid_tx` varchar(255) DEFAULT NULL,
  `oid_rx` varchar(255) DEFAULT NULL,
  `oid_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `oid_mappings`
--

INSERT INTO `oid_mappings` (`vendor`, `oid_name`, `oid_tx`, `oid_rx`, `oid_status`) VALUES
('hioso', '.1.3.6.1.4.1.25355.3.2.6.3.2.1.37', '.1.3.6.1.4.1.25355.3.2.6.14.2.1.4', '.1.3.6.1.4.1.25355.3.2.6.14.2.1.8', '.1.3.6.1.4.1.25355.3.2.6.3.2.1.39'),
('hsgq', '.1.3.6.1.4.1.50224.3.12.2.1.2', '.1.3.6.1.4.1.50224.3.12.3.1.5', '.1.3.6.1.4.1.50224.3.12.3.1.4', '.1.3.6.1.4.1.50224.3.12.2.1.5');

-- --------------------------------------------------------

--
-- Struktur dari tabel `olts`
--

CREATE TABLE `olts` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `community` varchar(50) DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `vendor` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `onus`
--

CREATE TABLE `onus` (
  `id` int NOT NULL,
  `olt_id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `tx_power` varchar(20) DEFAULT NULL,
  `rx_power` varchar(20) DEFAULT NULL,
  `status` enum('Up','Down') DEFAULT 'Down',
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `update_logs`
--

CREATE TABLE `update_logs` (
  `id` int NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `success_count` int DEFAULT NULL,
  `failed_count` int DEFAULT NULL,
  `details` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `oid_mappings`
--
ALTER TABLE `oid_mappings`
  ADD PRIMARY KEY (`vendor`);

--
-- Indeks untuk tabel `olts`
--
ALTER TABLE `olts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `onus`
--
ALTER TABLE `onus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `olt_id` (`olt_id`);

--
-- Indeks untuk tabel `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `olts`
--
ALTER TABLE `olts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `onus`
--
ALTER TABLE `onus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `onus`
--
ALTER TABLE `onus`
  ADD CONSTRAINT `onus_ibfk_1` FOREIGN KEY (`olt_id`) REFERENCES `olts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
