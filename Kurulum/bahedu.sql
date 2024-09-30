-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 30 Eyl 2024, 13:25:30
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `bahedu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bahedu`
--

CREATE TABLE `bahedu` (
  `id` int(11) NOT NULL,
  `kategori` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `sinav_url` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `on_test_url` varchar(255) DEFAULT NULL,
  `on_test` tinyint(1) DEFAULT 0,
  `sinav_baslangic` datetime DEFAULT NULL,
  `sinav_bitis` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `bahedu`
--

INSERT INTO `bahedu` (`id`, `kategori`, `baslik`, `foto`, `video`, `pdf`, `sinav_url`, `aciklama`, `on_test_url`, `on_test`, `sinav_baslangic`, `sinav_bitis`, `created_at`) VALUES
(4, 1, 'ffa', './eğitim/kapak_resmi/66dea7e4917c0-images (2).png', './eğitim/video/66dea7e491919-BABAANNEMİN KURGU DEĞİL DEDİĞİ VİDEO -D.mp4', './eğitim/pdf/66dea7e491a19-Hakkımızda (2).pdf', 'http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=6', 'xvzxv', 'http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=5', 1, '2024-09-09 10:46:00', '2024-09-10 14:50:00', '2024-09-10 11:21:14'),
(5, 1, 'a', './eğitim/kapak_resmi/66e02ae32d94b-WhatsApp Görsel 2024-09-09 saat 17.49.30_c201a54b.jpg', './eğitim/video/66e02ae32dca4-BABAANNEMİN KURGU DEĞİL DEDİĞİ VİDEO -D.mp4', './eğitim/pdf/66e02ae32de04-sertifika.pdf', 'https://codepen.io/search/pens?q=back+to+button&cursor=ZD0xJm89MCZwPTU=', 'a', '', 0, '2024-09-10 14:17:00', '2024-09-10 18:17:00', '2024-09-10 11:17:55'),
(6, 1, 'İsg Eğitim', './eğitim/kapak_resmi/66e133e71bb94-Ekran görüntüsü 2024-08-16 231517.png', './eğitim/video/66e133e71be76-BABAANNEMİN KURGU DEĞİL DEDİĞİ VİDEO -D.mp4', './eğitim/pdf/66e133e71c27f-sertifika.pdf', 'http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=8', 'a', 'http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=7', 1, '2024-09-11 09:08:00', '2024-09-11 14:08:00', '2024-09-11 06:08:39'),
(7, 1, 'fgd', './eğitim/kapak_resmi/66e1565e29203-4.jpg', './eğitim/video/66e1565e29552-BABAANNEMİN KURGU DEĞİL DEDİĞİ VİDEO -D.mp4', './eğitim/pdf/66e1565e29718-sertifika.pdf', 'http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=9', 'hf', '', 0, '2024-09-11 11:35:00', '2024-09-11 15:35:00', '2024-09-11 08:35:42');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `content_access`
--

CREATE TABLE `content_access` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `content_access`
--

INSERT INTO `content_access` (`id`, `content_id`, `user_id`) VALUES
(13, 4, 1),
(14, 4, 2),
(15, 4, 5),
(16, 4, 6),
(5, 5, 1),
(6, 5, 2),
(8, 5, 5),
(7, 5, 6),
(17, 6, 1),
(18, 6, 2),
(20, 6, 5),
(19, 6, 6),
(21, 7, 1),
(22, 7, 2),
(24, 7, 5),
(23, 7, 6);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `course_start_times`
--

CREATE TABLE `course_start_times` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `course_start_times`
--

INSERT INTO `course_start_times` (`id`, `user_id`, `course_id`, `start_time`, `end_time`) VALUES
(1, 6, 4, '2024-09-09 10:47:34', '2024-09-09 11:27:34'),
(2, 1, 5, '2024-09-10 14:18:30', '2024-09-10 14:58:30'),
(3, 1, 4, '2024-09-10 14:21:24', '2024-09-10 15:01:24'),
(4, 5, 6, '2024-09-11 09:09:19', '2024-09-11 09:49:19'),
(5, 1, 6, '2024-09-11 11:34:29', '2024-09-11 12:14:29'),
(6, 1, 7, '2024-09-11 11:35:55', '2024-09-11 12:15:55');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `departman`
--

CREATE TABLE `departman` (
  `id` int(11) NOT NULL,
  `Departman_adi` text NOT NULL,
  `Datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `departman`
--

INSERT INTO `departman` (`id`, `Departman_adi`, `Datetime`) VALUES
(1, 'Bilgi İşlem', '2024-09-06 08:03:25'),
(2, 'Grafik Tasarım', '2024-09-06 08:03:40'),
(3, 'Kurumsal Pazarlama', '2024-09-06 08:03:50');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `exams`
--

INSERT INTO `exams` (`id`, `title`, `description`) VALUES
(5, 'fsdd', 'fds'),
(6, 'fsa', 'sf'),
(7, 'İsg Ön Eğitim Sınavı', 'YOK'),
(8, 'Son test sınavı', 'a'),
(9, 'g', 'fg');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_title` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `exam_results`
--

INSERT INTO `exam_results` (`id`, `username`, `exam_id`, `exam_title`, `score`, `user_id`) VALUES
(1, 'kerem', 5, 'fsdd', 0, 6),
(2, 'kerem', 6, 'fsa', 12, 6),
(3, 'eren', 5, 'fsdd', 0, 1),
(4, 'eren', 5, 'fsdd', 12, 1),
(5, 'safa', 7, 'İsg Ön Eğitim Sınavı', 0, 5),
(6, 'safa', 8, 'Son test sınavı', 20, 5),
(7, 'eren', 7, 'İsg Ön Eğitim Sınavı', 10, 1),
(8, 'eren', 8, 'Son test sınavı', 20, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `hastane`
--

CREATE TABLE `hastane` (
  `id` int(11) NOT NULL,
  `hastane_adi` text NOT NULL,
  `Datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `hastane`
--

INSERT INTO `hastane` (`id`, `hastane_adi`, `Datetime`) VALUES
(1, 'Büyük Anadolu Darıca Hastanesi', '2024-09-06 08:55:24'),
(2, 'Büyük Anadolu Merkez Hastanesi', '2024-09-06 08:55:38'),
(3, 'Büyük Anadolu Meydan Hastanesi', '2024-09-06 08:55:45');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `kategori_name` varchar(255) NOT NULL,
  `seotitle` longtext NOT NULL,
  `seodescription` longtext NOT NULL,
  `g_durum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `kategori`
--

INSERT INTO `kategori` (`id`, `kategori_name`, `seotitle`, `seodescription`, `g_durum`) VALUES
(1, 'İSG', 'a', 'a', 1),
(2, 'Turizm  ve Otelcilik', 'a', 'a', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kulanicilar`
--

CREATE TABLE `kulanicilar` (
  `id` int(11) NOT NULL,
  `kullanici_ad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `user_rol` varchar(50) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `online` tinyint(1) DEFAULT 0,
  `hastane` varchar(255) NOT NULL,
  `telefon_no` varchar(20) NOT NULL,
  `bolum` varchar(255) NOT NULL,
  `departman` varchar(255) NOT NULL,
  `kayit_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `kulanicilar`
--

INSERT INTO `kulanicilar` (`id`, `kullanici_ad`, `email`, `sifre`, `user_rol`, `created_at`, `online`, `hastane`, `telefon_no`, `bolum`, `departman`, `kayit_tarihi`) VALUES
(1, 'eren', 'sokaktakiler21@gmail.com', '$2y$10$xB0JFiK3CwJksWcieDya8uGo9nw109Pag/LhONLzdrRoO5dXsdU5.', '1', '2024-09-02 10:49:25', 0, 'bah', '1', 'yazılım', '', '2024-09-02 13:58:33'),
(2, 'hatice', 'hatice@gmail.com', '$2y$10$sCjsx87cL/58CZA0dwQBLubEax3ZinZAlA6IWeKkW4LA0Am648oUa', '2', '2024-09-02 11:02:52', 0, 'Büyük Anaadolu Hastanesi', '1', '1', '', '2024-09-02 14:02:52'),
(5, 'safa', 'eren.ozkan@buyukanadolu.com.tr', '$2y$10$uzCQybp/3L81ZC9ESJ0Ype1L1o7clAwYRL5XgNsSRw3Lp8kqyqENa', '1', '2024-09-03 12:23:47', 0, 'Büyük Anadolu Darıca', '1', 'e', 'Bilgi İşlem', '2024-09-03 15:23:47'),
(6, 'kerem', 'info@minemacoffee.com', '$2y$10$ZVrxwbFoQsNAgE8aDqCMd.C9ezuu3nKjxcYP6Q83fBB4/PlXZRKLm', 'user', '2024-09-05 10:56:32', 0, 'Büyük Anadolu Darıca', '1', 'fas', 'Bilgi İşlem', '2024-09-05 13:56:32');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'd', 1),
(2, 1, 'g', 0),
(3, 1, 'h', 0),
(4, 1, 'sds', 0),
(5, 2, 'gs', 1),
(6, 2, 'gg', 0),
(7, 2, 'gs', 0),
(8, 2, 'dgs', 0),
(9, 3, 'fsa', 1),
(10, 4, 'a', 0),
(11, 4, 'b', 0),
(12, 4, 'c', 1),
(13, 4, 'd', 0),
(14, 5, 'Ab', 0),
(15, 5, 'Ba', 0),
(16, 5, 'Ca', 0),
(17, 5, 'Ac', 1),
(18, 6, 'g', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `question_text`, `points`) VALUES
(1, 5, 'a', 10),
(2, 5, 'gfdg', 12),
(3, 6, 'asf', 12),
(4, 7, 'Eren buügün Ne yaptı', 10),
(5, 8, 'Nerdens', 20),
(6, 9, 'df', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sifre_sifirlama`
--

CREATE TABLE `sifre_sifirlama` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_progress`
--

CREATE TABLE `user_progress` (
  `user_id` int(11) NOT NULL,
  `haber_id` int(11) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `start_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `verify_code`
--

CREATE TABLE `verify_code` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expiry_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bahedu`
--
ALTER TABLE `bahedu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori` (`kategori`);

--
-- Tablo için indeksler `content_access`
--
ALTER TABLE `content_access`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content_id` (`content_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `course_start_times`
--
ALTER TABLE `course_start_times`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`course_id`);

--
-- Tablo için indeksler `departman`
--
ALTER TABLE `departman`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_results_ibfk_1` (`exam_id`);

--
-- Tablo için indeksler `hastane`
--
ALTER TABLE `hastane`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kulanicilar`
--
ALTER TABLE `kulanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kullanici_ad` (`kullanici_ad`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Tablo için indeksler `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Tablo için indeksler `sifre_sifirlama`
--
ALTER TABLE `sifre_sifirlama`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`user_id`,`haber_id`);

--
-- Tablo için indeksler `verify_code`
--
ALTER TABLE `verify_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bahedu`
--
ALTER TABLE `bahedu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `content_access`
--
ALTER TABLE `content_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Tablo için AUTO_INCREMENT değeri `course_start_times`
--
ALTER TABLE `course_start_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `departman`
--
ALTER TABLE `departman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `hastane`
--
ALTER TABLE `hastane`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `kulanicilar`
--
ALTER TABLE `kulanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `sifre_sifirlama`
--
ALTER TABLE `sifre_sifirlama`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `verify_code`
--
ALTER TABLE `verify_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `bahedu`
--
ALTER TABLE `bahedu`
  ADD CONSTRAINT `bahedu_ibfk_1` FOREIGN KEY (`kategori`) REFERENCES `kategori` (`id`);

--
-- Tablo kısıtlamaları `content_access`
--
ALTER TABLE `content_access`
  ADD CONSTRAINT `content_access_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `bahedu` (`id`),
  ADD CONSTRAINT `content_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `kulanicilar` (`id`);

--
-- Tablo kısıtlamaları `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`);

--
-- Tablo kısıtlamaları `sifre_sifirlama`
--
ALTER TABLE `sifre_sifirlama`
  ADD CONSTRAINT `sifre_sifirlama_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kulanicilar` (`id`);

--
-- Tablo kısıtlamaları `verify_code`
--
ALTER TABLE `verify_code`
  ADD CONSTRAINT `verify_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kulanicilar` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
