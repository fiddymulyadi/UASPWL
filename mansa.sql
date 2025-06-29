-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 06:36 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mansa`
--

-- --------------------------------------------------------

--
-- Table structure for table `school_profile`
--

CREATE TABLE `school_profile` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `npsn` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sejarah` text DEFAULT NULL,
  `akreditasi` varchar(10) DEFAULT NULL,
  `nsm` varchar(50) DEFAULT NULL,
  `status_sekolah` enum('Negeri','Swasta') DEFAULT 'Negeri',
  `jenjang` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_profile`
--

INSERT INTO `school_profile` (`id`, `school_name`, `npsn`, `address`, `logo`, `sejarah`, `akreditasi`, `nsm`, `status_sekolah`, `jenjang`) VALUES
(1, 'MAN 1 PONTIANAK', '10816420', 'Jl. H. Haruna Kel. Sungai Jawi Dalam Kec. Pontianak Barat Kota Pontianak Kalimantan Barat', '1750902333_newicon.png', 'MAN 1 Pontianak berdiri pada tahun 1978. Sebelumnya Nama MAN 1 Pontianak adalah SP. IAIN (Sekolah Persiapan IAIN) yang beralamat di Jl. Merdeka Barat no.173. SP IAIN diresmikan menjadi sekolah negeri pada tahun 1965 yang merupakan sekolah turunan dari IAIN Sunan Kalijaga Jogjakarta. Seiring berkembangnya SP IAIN di Indonesia, maka SP. IAIN Pontianak berinduk kepada IAIN Syarif Hidayatullah Pontianak.\r\n\r\nSesuai SK MENAG No. 17 tanggal 16 Maret 1978, terjadi perubahan nama dari SP. IAIN Syarif Hidayatullah menjadi Madrasah Aliyah Negeri 1 Pontianak yang beralamat di Jl. H. Haruna (sebelumya Jl. Apel VIPada tahun 2021, MAN 1 Pontianak mendapatkan bantuan dana hibah dari Kementrian PUPR untuk peremajaan/renovasi bangunan MAN 1 Pontianak yang sudah mulai rusak. Kini MAN 1 Pontianak menjadi salah satu sekolah favorit di Kota Pontianak.', 'A', '131161710001', 'Negeri', 'Madrasah Aliyah');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'administrator', '$2y$10$CIEu9UmB/s5sSZUl7a1uM.aRPFrDQ9HM0ayi6a7b8IRFfE41r3qZS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `school_profile`
--
ALTER TABLE `school_profile`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `school_profile`
--
ALTER TABLE `school_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
