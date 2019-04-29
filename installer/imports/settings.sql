-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 14, 2019 at 06:38 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.0.33-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `limbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'active_theme', 'ivar'),
(2, 'core_directory', ''),
(3, 'core_url', ''),
(4, 'admin_theme', 'default'),
(5, 'title', 'Limbs'),
(6, 'title_separator', '-'),
(7, 'description', 'A video sharing script built with both developers and managers in mind'),
(9, 'uploads', 'yes'),
(10, 'signups', 'yes'),
(11, 'public_message', ''),
(12, 'upload_message', 'Please make sure you own the rights to upload this video'),
(13, 'comments', 'yes'),
(14, 'embeds', 'yes'),
(15, 'php', '/usr/bin/php'),
(16, 'ffmpeg', '/usr/bin/ffmpeg'),
(17, 'ffprobe', '/usr/bin/ffprobe'),
(18, 'fresh', '12'),
(19, 'trending', '4'),
(20, 'search', '5'),
(21, 'related', '8'),
(22, 'quality_240', 'yes'),
(23, 'quality_360', 'yes'),
(24, 'quality_480', 'yes'),
(25, 'quality_720', 'yes'),
(26, 'quality_1080', 'no'),
(27, 'ffmpeg_preset', 'medium'),
(28, 'video_codec', 'libx264'),
(29, 'audio_codec', 'libfdk_aac'),
(30, 'basic_vbitrate', '576'),
(31, 'basic_abitrate', '64'),
(32, 'normal_vbitrate', '896'),
(33, 'normal_abitrate', '64'),
(34, 'sd_vbitrate', '1536'),
(35, 'sd_abitrate', '96'),
(36, 'hd_vbitrate', '3072'),
(37, 'hd_abitrate', '96'),
(38, 'fullhd_vbitrate', '4992'),
(39, 'fullhd_abitrate', '128'),
(40, 'watermark_placement', 'right:bottom'),
(41, 'enable_watermark', 'no'),
(42, 'enable_pre_clip', 'no'),
(44, 'enable_post_clip', 'no'),
(45, 'mailer_host', 'smtp.mailgun.org'),
(46, 'mailer_port', '587'),
(47, 'mailer_smtp_secure', 'tls'),
(49, 'mailer_email', 'admin@yourwebsite.com'),
(50, 'mailer_smpt_username', ''),
(51, 'mailer_smpt_password', ''),
(53, 'mailer_sender_email', 'admin@yourwebsite.com'),
(54, 'mailer_sender_name', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
