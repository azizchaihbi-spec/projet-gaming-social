-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 02:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `playtohelp_merged`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_user` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `association`
--

CREATE TABLE `association` (
  `id_association` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_dons` decimal(12,2) DEFAULT 0.00,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `association`
--

INSERT INTO `association` (`id_association`, `name`, `description`, `total_dons`, `date_creation`) VALUES
(1, 'UNICEF', 'L’UNICEF œuvre pour les droits et le bien-être des enfants dans le monde, en leur assurant santé, éducation et protection.', 0.00, '2025-11-30 20:39:41'),
(2, 'WWF', 'Le WWF protège la nature et les espèces menacées tout en promouvant un développement durable.', 0.00, '2025-11-30 20:40:42');

-- --------------------------------------------------------

--
-- Table structure for table `banned_users`
--

CREATE TABLE `banned_users` (
  `id_ban` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `raison` text DEFAULT NULL,
  `date_ban` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `challenge`
--

CREATE TABLE `challenge` (
  `id_challenge` int(11) NOT NULL,
  `id_association` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `objectif` decimal(12,2) NOT NULL,
  `recompense` text DEFAULT NULL,
  `progression` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenge`
--

INSERT INTO `challenge` (`id_challenge`, `id_association`, `name`, `objectif`, `recompense`, `progression`) VALUES
(3, 1, '20 kills', 500.00, 'badge', 170.00),
(4, 1, '10 kills', 500.00, 'badge', 100.00),
(6, 1, 'win a game with 0 deaths in league of legends', 700.00, 'badges', 50.00),
(7, 2, '20 kills', 700.00, 'shoutout stream', 300.00),
(9, 1, 'win a game with 0 deaths in league of legends', 399.98, 'badge', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `clip`
--

CREATE TABLE `clip` (
  `id_clip` int(11) NOT NULL,
  `id_stream` int(11) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `url_video` varchar(500) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `nb_vues` int(11) DEFAULT 0,
  `nb_likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clip`
--

INSERT INTO `clip` (`id_clip`, `id_stream`, `titre`, `description`, `url_video`, `date_creation`, `nb_vues`, `nb_likes`) VALUES
(1, 10, 'lessgo', 'ak,dad', 'https://www.youtube.com/watch?v=eq8GccdVg9A', '2025-12-10 12:55:42', 3, 0),
(2, 10, 'jkzj a', 'ôkopkpok', 'https://www.youtube.com/watch?v=eq8GccdVg9A&pp=ygUKMW1pbiB2aWRlbw%3D%3D', '2025-12-10 13:05:52', 0, 0),
(3, 10, 'g\"rg\"tg', 'fo\"ioâi\'', 'https://www.youtube.com/watch?v=rMPkUuMq024&pp=ygUKMW1pbiB2aWRlbw%3D%3D', '2025-12-10 13:07:42', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `don`
--

CREATE TABLE `don` (
  `id_don` int(11) NOT NULL,
  `id_association` int(11) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `montant` decimal(12,2) NOT NULL CHECK (`montant` > 0),
  `date_don` datetime DEFAULT current_timestamp(),
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don`
--

INSERT INTO `don` (`id_don`, `id_association`, `prenom`, `nom`, `email`, `montant`, `date_don`, `message`) VALUES
(1, 1, 'abdou', 'magdoud', 'abdou@gmail.com', 32.00, '2025-11-30 20:41:08', NULL),
(4, 2, 'dridi', 'molka', NULL, 40.00, '2025-12-03 22:31:04', NULL),
(11, 2, 'kallel', 'maya', 'mayakalle@gmail.com', 40.00, '2025-12-04 12:00:14', NULL),
(12, 1, '', 'Anonyme', '', 99.99, '2025-12-05 22:59:21', NULL),
(16, 2, '', 'Anonyme', '', 19.98, '2025-12-06 00:36:24', NULL),
(27, 2, 'sinda', 'hedhli', 'sindahedhli082@gmail.com', 49.98, '2025-12-06 15:16:15', NULL),
(29, 1, 'abdou', 'magdoud', 'makdoud05@gmail.com', 100.00, '2025-12-09 22:32:41', NULL),
(30, 2, 'dina', 'meddeb', 'diina.meddeb@gmail.com', 560.00, '2025-12-10 00:06:59', NULL),
(32, 1, 'ikram', 'ouizini', 'ikramouizini07@gmail.com', 15000.00, '2025-12-10 15:34:48', NULL),
(33, 2, '', 'ouizini', 'ikramouizini07@gmail.com', 14.99, '2025-12-11 09:41:44', NULL),
(34, 2, 'sinda12', 'hedhli', 'sindahedhli082@gmail.com', 20.00, '2025-12-11 09:44:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `theme` varchar(150) DEFAULT NULL,
  `banner_url` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `objectif` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `titre`, `theme`, `banner_url`, `description`, `date_debut`, `date_fin`, `objectif`) VALUES
(2, 'ouiii', 'League of Legends', '/uploads/thumbnails/event_2.jpg', NULL, '2025-11-15', '2222-02-22', '5849'),
(3, 'feéf', 'Fortnite', '/uploads/thumbnails/event_3.jpg', NULL, '2222-02-22', '9859-12-10', '88'),
(4, 'dzerv', 'Valorant', '/uploads/banners/event_1765375710_11a7ae02.jpg', NULL, '7777-07-07', '9999-02-22', '877'),
(5, 'eéoi', 'League of Legends', '/uploads/banners/event_1765375458_f1555d1a.jpg', NULL, '2222-02-22', '3333-03-31', '5555'),
(6, 'ffffff', 'Fortnite', '/uploads/thumbnails/event_1765376183_3f38f05e.jpg', NULL, '2222-02-22', '3333-03-31', '9'),
(7, 'jemad', 'GTA V', NULL, 'mmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', '2025-12-05', '2025-12-06', '55');

-- --------------------------------------------------------

--
-- Table structure for table `evenement_streamer`
--

CREATE TABLE `evenement_streamer` (
  `id_evenement` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `id_forum` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `couleur` varchar(7) DEFAULT '#6e6eff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`id_forum`, `nom`, `description`, `date_creation`, `couleur`) VALUES
(1, 'Général', 'Discussions générales sur le gaming', '2025-12-12 16:29:47', '#6e6eff'),
(2, 'Fortnite', 'Tout sur Fortnite et ses tournois', '2025-12-12 16:29:47', '#FF69B4'),
(3, 'D&D / Jeux de rôle', 'Donjons, dragons et jeux de rôle', '2025-12-12 16:29:47', '#39FF14'),
(4, 'Minecraft', 'Constructions, redstone et mods', '2025-12-12 16:29:47', '#00FF44'),
(5, 'Valorant', 'Stratégies, agents et compétition', '2025-12-12 16:29:47', '#FF4500'),
(6, 'League of Legends', 'LCK, LEC, Worlds et meta', '2025-12-12 16:29:47', '#FFD700');

-- --------------------------------------------------------

--
-- Table structure for table `friendships`
--

CREATE TABLE `friendships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Utilisateur qui envoie la demande',
  `friend_id` int(11) NOT NULL COMMENT 'Utilisateur qui reçoit la demande',
  `status` enum('pending','accepted','rejected','blocked') NOT NULL DEFAULT 'pending' COMMENT 'Statut de la relation',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `friendships`
--

INSERT INTO `friendships` (`id`, `user_id`, `friend_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 49, 'accepted', '2025-12-10 15:49:14', '2025-12-10 15:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `friend_notifications`
--

CREATE TABLE `friend_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Utilisateur qui reçoit la notification',
  `from_user_id` int(11) NOT NULL COMMENT 'Utilisateur qui déclenche la notification',
  `type` enum('friend_request','friend_accepted','friend_online') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `friend_notifications`
--

INSERT INTO `friend_notifications` (`id`, `user_id`, `from_user_id`, `type`, `is_read`, `created_at`) VALUES
(1, 49, 3, 'friend_request', 0, '2025-12-10 15:49:14'),
(2, 3, 49, 'friend_accepted', 0, '2025-12-10 15:49:19'),
(3, 49, 3, 'friend_online', 0, '2025-12-10 15:49:31'),
(4, 3, 49, 'friend_online', 0, '2025-12-10 15:50:22'),
(5, 49, 3, 'friend_online', 0, '2025-12-10 15:51:40'),
(6, 3, 49, 'friend_online', 0, '2025-12-10 15:52:22'),
(7, 49, 3, 'friend_online', 0, '2025-12-10 15:53:40'),
(8, 3, 49, 'friend_online', 0, '2025-12-10 15:54:22'),
(9, 49, 3, 'friend_online', 0, '2025-12-10 15:55:40'),
(10, 3, 49, 'friend_online', 0, '2025-12-10 15:56:22'),
(11, 49, 3, 'friend_online', 0, '2025-12-10 15:57:40'),
(12, 3, 49, 'friend_online', 0, '2025-12-10 15:58:22'),
(13, 49, 3, 'friend_online', 0, '2025-12-10 15:59:40'),
(14, 3, 49, 'friend_online', 0, '2025-12-10 16:00:22'),
(15, 49, 3, 'friend_online', 0, '2025-12-10 16:01:40'),
(16, 3, 49, 'friend_online', 0, '2025-12-10 16:02:39'),
(17, 49, 3, 'friend_online', 0, '2025-12-10 16:03:40'),
(18, 3, 49, 'friend_online', 0, '2025-12-10 16:04:39'),
(19, 49, 3, 'friend_online', 0, '2025-12-10 16:05:40'),
(20, 3, 49, 'friend_online', 0, '2025-12-10 16:06:39'),
(21, 49, 3, 'friend_online', 0, '2025-12-10 16:07:40'),
(22, 3, 49, 'friend_online', 0, '2025-12-10 16:08:39'),
(23, 49, 3, 'friend_online', 0, '2025-12-10 16:09:40'),
(24, 3, 49, 'friend_online', 0, '2025-12-10 16:10:39'),
(25, 49, 3, 'friend_online', 0, '2025-12-10 16:12:39'),
(26, 3, 49, 'friend_online', 0, '2025-12-10 16:12:39'),
(27, 49, 3, 'friend_online', 0, '2025-12-10 16:14:39'),
(28, 3, 49, 'friend_online', 0, '2025-12-10 16:14:39'),
(29, 49, 3, 'friend_online', 0, '2025-12-10 16:16:39'),
(30, 3, 49, 'friend_online', 0, '2025-12-10 16:16:39'),
(31, 49, 3, 'friend_online', 0, '2025-12-10 16:18:39'),
(32, 3, 49, 'friend_online', 0, '2025-12-10 16:18:39'),
(33, 49, 3, 'friend_online', 0, '2025-12-10 16:20:39'),
(34, 3, 49, 'friend_online', 0, '2025-12-10 16:20:39'),
(35, 3, 49, 'friend_online', 0, '2025-12-10 16:22:39'),
(36, 49, 3, 'friend_online', 0, '2025-12-10 16:22:39'),
(37, 49, 3, 'friend_online', 0, '2025-12-10 16:24:39'),
(38, 3, 49, 'friend_online', 0, '2025-12-10 16:24:39'),
(39, 49, 3, 'friend_online', 0, '2025-12-10 16:26:39'),
(40, 3, 49, 'friend_online', 0, '2025-12-10 16:26:39'),
(41, 3, 49, 'friend_online', 0, '2025-12-10 16:28:39'),
(42, 49, 3, 'friend_online', 0, '2025-12-10 16:28:39'),
(43, 49, 3, 'friend_online', 0, '2025-12-10 18:43:19'),
(44, 3, 49, 'friend_online', 0, '2025-12-10 18:43:19'),
(45, 49, 3, 'friend_online', 0, '2025-12-10 18:44:19'),
(46, 3, 49, 'friend_online', 0, '2025-12-10 18:44:39'),
(47, 49, 3, 'friend_online', 0, '2025-12-10 18:45:40'),
(48, 3, 49, 'friend_online', 0, '2025-12-10 18:46:22'),
(49, 49, 3, 'friend_online', 0, '2025-12-10 18:48:21'),
(50, 3, 49, 'friend_online', 0, '2025-12-10 18:48:24'),
(51, 49, 3, 'friend_online', 0, '2025-12-10 18:50:11'),
(52, 3, 49, 'friend_online', 0, '2025-12-10 18:50:39'),
(53, 49, 3, 'friend_online', 0, '2025-12-10 18:52:13'),
(54, 3, 49, 'friend_online', 0, '2025-12-10 18:52:39'),
(55, 49, 3, 'friend_online', 0, '2025-12-10 18:54:15'),
(56, 3, 49, 'friend_online', 0, '2025-12-10 18:54:39'),
(57, 3, 49, 'friend_online', 0, '2025-12-10 18:55:25'),
(58, 3, 49, 'friend_online', 0, '2025-12-10 18:55:28'),
(59, 49, 3, 'friend_online', 0, '2025-12-10 18:56:17'),
(60, 3, 49, 'friend_online', 0, '2025-12-10 18:57:29'),
(61, 3, 49, 'friend_online', 0, '2025-12-10 18:58:17'),
(62, 49, 3, 'friend_online', 0, '2025-12-10 18:58:19'),
(63, 3, 49, 'friend_online', 0, '2025-12-10 18:59:04'),
(64, 3, 49, 'friend_online', 0, '2025-12-10 18:59:06'),
(65, 3, 49, 'friend_online', 0, '2025-12-10 18:59:35'),
(66, 49, 3, 'friend_online', 0, '2025-12-10 19:00:21'),
(67, 3, 49, 'friend_online', 0, '2025-12-10 19:00:36'),
(68, 3, 49, 'friend_online', 0, '2025-12-10 19:00:44'),
(69, 3, 49, 'friend_online', 0, '2025-12-10 19:00:47'),
(70, 49, 3, 'friend_online', 0, '2025-12-10 19:01:18'),
(71, 49, 3, 'friend_online', 0, '2025-12-10 19:01:21'),
(72, 3, 49, 'friend_online', 0, '2025-12-10 19:01:24'),
(73, 49, 3, 'friend_online', 0, '2025-12-10 19:01:32'),
(74, 49, 3, 'friend_online', 0, '2025-12-10 19:01:42'),
(75, 49, 3, 'friend_online', 0, '2025-12-10 19:01:43'),
(76, 49, 3, 'friend_online', 0, '2025-12-10 19:01:46'),
(77, 49, 3, 'friend_online', 0, '2025-12-10 19:01:56'),
(78, 49, 3, 'friend_online', 0, '2025-12-10 19:01:57'),
(79, 49, 3, 'friend_online', 0, '2025-12-10 19:01:57'),
(80, 49, 3, 'friend_online', 0, '2025-12-10 19:01:58'),
(81, 49, 3, 'friend_online', 0, '2025-12-10 19:01:58'),
(82, 49, 3, 'friend_online', 0, '2025-12-10 19:02:10'),
(83, 3, 49, 'friend_online', 0, '2025-12-10 19:02:20'),
(84, 49, 3, 'friend_online', 0, '2025-12-10 19:02:37'),
(85, 49, 3, 'friend_online', 0, '2025-12-10 19:03:00'),
(86, 3, 49, 'friend_online', 0, '2025-12-10 19:04:20'),
(87, 49, 3, 'friend_online', 0, '2025-12-10 19:05:00'),
(88, 3, 49, 'friend_online', 0, '2025-12-10 19:06:20'),
(89, 49, 3, 'friend_online', 0, '2025-12-10 19:07:00'),
(90, 3, 49, 'friend_online', 0, '2025-12-10 19:08:20'),
(91, 49, 3, 'friend_online', 0, '2025-12-10 19:09:00'),
(92, 3, 49, 'friend_online', 0, '2025-12-10 19:10:20'),
(93, 49, 3, 'friend_online', 0, '2025-12-10 19:11:00'),
(94, 3, 49, 'friend_online', 0, '2025-12-10 19:12:20'),
(95, 49, 3, 'friend_online', 0, '2025-12-10 19:13:00'),
(96, 3, 49, 'friend_online', 0, '2025-12-10 19:14:20'),
(97, 49, 3, 'friend_online', 0, '2025-12-10 19:15:00'),
(98, 3, 49, 'friend_online', 0, '2025-12-10 19:16:39'),
(99, 49, 3, 'friend_online', 0, '2025-12-10 19:17:39'),
(100, 3, 49, 'friend_online', 0, '2025-12-10 19:18:39'),
(101, 49, 3, 'friend_online', 0, '2025-12-10 19:19:39'),
(102, 3, 49, 'friend_online', 0, '2025-12-10 19:20:39'),
(103, 49, 3, 'friend_online', 0, '2025-12-10 19:21:39'),
(104, 3, 49, 'friend_online', 0, '2025-12-10 19:22:39'),
(105, 49, 3, 'friend_online', 0, '2025-12-10 19:23:39'),
(106, 3, 49, 'friend_online', 0, '2025-12-10 19:24:39'),
(107, 49, 3, 'friend_online', 0, '2025-12-10 19:25:39'),
(108, 3, 49, 'friend_online', 0, '2025-12-10 19:26:39'),
(109, 49, 3, 'friend_online', 0, '2025-12-10 19:27:39'),
(110, 3, 49, 'friend_online', 0, '2025-12-10 19:28:39'),
(111, 49, 3, 'friend_online', 0, '2025-12-10 19:29:39'),
(112, 3, 49, 'friend_online', 0, '2025-12-10 19:30:39'),
(113, 49, 3, 'friend_online', 0, '2025-12-10 19:31:39'),
(114, 3, 49, 'friend_online', 0, '2025-12-10 19:32:39'),
(115, 49, 3, 'friend_online', 0, '2025-12-10 19:33:39'),
(116, 3, 49, 'friend_online', 0, '2025-12-10 19:34:39'),
(117, 49, 3, 'friend_online', 0, '2025-12-10 19:35:39'),
(118, 3, 49, 'friend_online', 0, '2025-12-10 19:36:39'),
(119, 49, 3, 'friend_online', 0, '2025-12-10 19:37:39'),
(120, 3, 49, 'friend_online', 0, '2025-12-10 19:38:39'),
(121, 49, 3, 'friend_online', 0, '2025-12-10 19:41:13'),
(122, 3, 49, 'friend_online', 0, '2025-12-10 19:41:13'),
(123, 3, 49, 'friend_online', 0, '2025-12-10 19:42:39'),
(124, 49, 3, 'friend_online', 0, '2025-12-10 19:43:39'),
(125, 3, 49, 'friend_online', 0, '2025-12-10 19:44:39'),
(126, 49, 3, 'friend_online', 0, '2025-12-10 19:45:39'),
(127, 3, 49, 'friend_online', 0, '2025-12-10 19:46:39'),
(128, 49, 3, 'friend_online', 0, '2025-12-10 19:47:39'),
(129, 3, 49, 'friend_online', 0, '2025-12-10 19:48:39'),
(130, 49, 3, 'friend_online', 0, '2025-12-10 19:49:39'),
(131, 3, 49, 'friend_online', 0, '2025-12-10 19:50:39'),
(132, 49, 3, 'friend_online', 0, '2025-12-10 19:51:39'),
(133, 3, 49, 'friend_online', 0, '2025-12-10 19:52:39'),
(134, 49, 3, 'friend_online', 0, '2025-12-10 19:53:39'),
(135, 3, 49, 'friend_online', 0, '2025-12-10 19:54:39'),
(136, 49, 3, 'friend_online', 0, '2025-12-10 19:55:39'),
(137, 3, 49, 'friend_online', 0, '2025-12-10 19:56:39'),
(138, 49, 3, 'friend_online', 0, '2025-12-10 19:57:39'),
(139, 3, 49, 'friend_online', 0, '2025-12-10 19:58:39'),
(140, 49, 3, 'friend_online', 0, '2025-12-10 19:59:39'),
(141, 3, 49, 'friend_online', 0, '2025-12-10 20:00:39'),
(142, 49, 3, 'friend_online', 0, '2025-12-10 20:01:39'),
(143, 3, 49, 'friend_online', 0, '2025-12-10 20:02:39'),
(144, 49, 3, 'friend_online', 0, '2025-12-10 20:03:39'),
(145, 3, 49, 'friend_online', 0, '2025-12-10 20:04:39'),
(146, 49, 3, 'friend_online', 0, '2025-12-10 20:05:39'),
(147, 3, 49, 'friend_online', 0, '2025-12-10 20:06:39'),
(148, 49, 3, 'friend_online', 0, '2025-12-10 20:07:39'),
(149, 3, 49, 'friend_online', 0, '2025-12-10 20:08:39'),
(150, 49, 3, 'friend_online', 0, '2025-12-10 20:09:27'),
(151, 3, 49, 'friend_online', 0, '2025-12-10 20:10:20'),
(152, 49, 3, 'friend_online', 0, '2025-12-10 20:11:00'),
(153, 3, 49, 'friend_online', 0, '2025-12-10 20:12:39'),
(154, 49, 3, 'friend_online', 0, '2025-12-10 20:13:00'),
(155, 3, 49, 'friend_online', 0, '2025-12-10 20:14:39'),
(156, 49, 3, 'friend_online', 0, '2025-12-10 20:15:00'),
(157, 3, 49, 'friend_online', 0, '2025-12-10 20:16:39'),
(158, 49, 3, 'friend_online', 0, '2025-12-10 20:17:11'),
(159, 3, 49, 'friend_online', 0, '2025-12-10 20:18:39'),
(160, 49, 3, 'friend_online', 0, '2025-12-10 20:19:39'),
(161, 3, 49, 'friend_online', 0, '2025-12-10 20:20:39'),
(162, 49, 3, 'friend_online', 0, '2025-12-10 20:21:10'),
(163, 3, 49, 'friend_online', 0, '2025-12-10 20:22:39'),
(164, 49, 3, 'friend_online', 0, '2025-12-10 20:23:00'),
(165, 3, 49, 'friend_online', 0, '2025-12-10 20:24:39'),
(166, 49, 3, 'friend_online', 0, '2025-12-10 20:25:00'),
(167, 3, 49, 'friend_online', 0, '2025-12-10 20:26:39'),
(168, 49, 3, 'friend_online', 0, '2025-12-10 20:27:00'),
(169, 3, 49, 'friend_online', 0, '2025-12-10 20:28:39'),
(170, 49, 3, 'friend_online', 0, '2025-12-10 20:29:07'),
(171, 3, 49, 'friend_online', 0, '2025-12-10 20:30:39'),
(172, 49, 3, 'friend_online', 0, '2025-12-10 20:31:36'),
(173, 3, 49, 'friend_online', 0, '2025-12-10 20:32:39'),
(174, 49, 3, 'friend_online', 0, '2025-12-10 20:33:06'),
(175, 3, 49, 'friend_online', 0, '2025-12-10 20:34:39'),
(176, 49, 3, 'friend_online', 0, '2025-12-10 20:35:39'),
(177, 49, 3, 'friend_online', 0, '2025-12-10 20:37:04'),
(178, 49, 3, 'friend_online', 0, '2025-12-10 20:39:39'),
(179, 49, 3, 'friend_online', 0, '2025-12-10 20:41:39'),
(180, 49, 3, 'friend_online', 0, '2025-12-10 20:43:39'),
(181, 49, 3, 'friend_online', 0, '2025-12-10 20:45:39'),
(182, 49, 3, 'friend_online', 0, '2025-12-10 20:47:39'),
(183, 49, 3, 'friend_online', 0, '2025-12-10 20:49:39'),
(184, 49, 3, 'friend_online', 0, '2025-12-10 20:51:39'),
(185, 49, 3, 'friend_online', 0, '2025-12-10 20:53:39'),
(186, 49, 3, 'friend_online', 0, '2025-12-10 20:55:39'),
(187, 49, 3, 'friend_online', 0, '2025-12-10 20:57:39'),
(188, 49, 3, 'friend_online', 0, '2025-12-10 20:59:39'),
(189, 49, 3, 'friend_online', 0, '2025-12-10 21:01:39'),
(190, 49, 3, 'friend_online', 0, '2025-12-10 21:03:39'),
(191, 49, 3, 'friend_online', 0, '2025-12-10 21:05:39'),
(192, 49, 3, 'friend_online', 0, '2025-12-10 21:07:39'),
(193, 49, 3, 'friend_online', 0, '2025-12-10 22:07:25'),
(194, 49, 3, 'friend_online', 0, '2025-12-10 22:09:00'),
(195, 49, 3, 'friend_online', 0, '2025-12-10 22:11:00'),
(196, 49, 3, 'friend_online', 0, '2025-12-10 22:13:00'),
(197, 49, 3, 'friend_online', 0, '2025-12-12 17:14:31'),
(198, 49, 3, 'friend_online', 0, '2025-12-12 17:16:31'),
(199, 49, 3, 'friend_online', 0, '2025-12-12 17:18:31'),
(200, 49, 3, 'friend_online', 0, '2025-12-12 17:20:31'),
(201, 49, 3, 'friend_online', 0, '2025-12-12 17:22:31'),
(202, 49, 3, 'friend_online', 0, '2025-12-12 17:23:56'),
(203, 49, 3, 'friend_online', 0, '2025-12-12 17:25:39'),
(204, 49, 3, 'friend_online', 0, '2025-12-12 17:28:31'),
(205, 49, 3, 'friend_online', 0, '2025-12-12 17:29:39'),
(206, 49, 3, 'friend_online', 0, '2025-12-12 17:30:21'),
(207, 49, 3, 'friend_online', 0, '2025-12-12 17:30:28'),
(208, 49, 3, 'friend_online', 0, '2025-12-12 17:32:31'),
(209, 49, 3, 'friend_online', 0, '2025-12-12 17:34:31'),
(210, 49, 3, 'friend_online', 0, '2025-12-12 17:36:31'),
(211, 49, 3, 'friend_online', 0, '2025-12-12 19:40:51'),
(212, 49, 3, 'friend_online', 0, '2025-12-12 19:40:52'),
(213, 49, 3, 'friend_online', 0, '2025-12-12 19:40:53'),
(214, 49, 3, 'friend_online', 0, '2025-12-12 19:40:54'),
(215, 49, 3, 'friend_online', 0, '2025-12-12 19:40:58'),
(216, 49, 3, 'friend_online', 0, '2025-12-13 16:24:48'),
(217, 49, 3, 'friend_online', 0, '2025-12-13 16:42:09'),
(218, 49, 3, 'friend_online', 0, '2025-12-13 16:42:10'),
(219, 49, 3, 'friend_online', 0, '2025-12-13 16:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `moderation_publication`
--

CREATE TABLE `moderation_publication` (
  `id_admin` int(11) NOT NULL,
  `id_publication` int(11) NOT NULL,
  `action` enum('validee','refusee','supprimee') NOT NULL,
  `date_action` datetime NOT NULL DEFAULT current_timestamp(),
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderation_reponse`
--

CREATE TABLE `moderation_reponse` (
  `id_admin` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `action` enum('supprimee','masquee') NOT NULL,
  `date_action` datetime DEFAULT current_timestamp(),
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

CREATE TABLE `publication` (
  `id_publication` int(11) NOT NULL,
  `id_forum` int(11) NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `date_publication` timestamp NOT NULL DEFAULT current_timestamp(),
  `supprimee` tinyint(4) DEFAULT 0,
  `validee` tinyint(4) DEFAULT 1,
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0,
  `auteur` varchar(50) DEFAULT 'Anonyme',
  `emojis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emojis`)),
  `gif_url` varchar(500) DEFAULT NULL,
  `sticker_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id_publication`, `id_forum`, `id_auteur`, `titre`, `contenu`, `image`, `date_publication`, `supprimee`, `validee`, `likes`, `dislikes`, `auteur`, `emojis`, `gif_url`, `sticker_url`) VALUES
(1, 1, 1, 'Bienvenue sur la communauté gaming !', 'Salut à tous les gamers ! Bienvenue sur notre plateforme. Partagez vos astuces, questions et expériences ?', NULL, '2025-11-24 09:30:50', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(2, 2, 2, 'Meta actuel Fortnite Chapitre 5', 'Quelles sont les meilleures armes et stratégies pour le chapitre 5 ? Je cherche à m\'améliorer.', NULL, '2025-11-24 09:30:50', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(3, 3, 3, 'Campagne D&D pour débutants', 'Je veux créer une campagne pour nouveaux joueurs. Des idées de scénarios simples ?', NULL, '2025-11-24 09:30:50', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(4, 4, 1, 'Meilleurs mods Minecraft 2024', 'Quels sont vos mods préférés pour Minecraft cette année ? Je cherche des recommendations.', NULL, '2025-11-24 09:30:50', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(5, 1, 1, 'Test manuel', 'Ceci est un test manuel', NULL, '2025-11-24 10:55:03', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(7, 2, 1, 'J\'aime bien cette nouvelle mise a jour', 'très pratiques !!!!', NULL, '2025-11-24 20:57:42', 0, 1, 2, 0, 'Anonyme', NULL, NULL, NULL),
(8, 3, 1, '', '', NULL, '2025-11-27 11:10:06', 1, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(9, 3, 1, '', '', NULL, '2025-11-27 11:10:07', 1, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(10, 3, 1, '', '', NULL, '2025-11-27 11:20:43', 1, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(11, 3, 1, 'testtttttttttttttttttttttttttt', 'je teste woopwooop', NULL, '2025-11-27 11:50:27', 1, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(17, 1, 1, 'Comment améliorer son aim sur Valorant ?', 'Salut la team ! J’ai du mal à viser proprement, surtout avec les armes à rafale. Vous avez des exos ou astuces ? Merci d’avance !', NULL, '2025-12-09 22:23:39', 0, 1, 2, 0, 'Anonyme', NULL, NULL, NULL),
(18, 2, 1, 'Meilleure compo pour le nouveau chapitre Fortnite ?', 'Le chapitre 6 vient de sortir, qui a déjà trouvé une méta solide ? J’ai vu beaucoup de gens jouer le pompe mythique + AR. Vos retours ?', 'upload/fortnite-meta.jpg', '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(19, 3, 1, 'Idées de campagne D&D pour 4 joueurs débutants', 'Je vais maître du jeu pour la première fois avec des potes qui découvrent D&D. Vous avez des one-shots ou campagnes faciles à prendre en main ?', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(20, 4, 1, 'Serveur Minecraft survie vanilla 1.21 dispo ?', 'Quelqu’un a un serveur survie vanilla ouvert ? Je cherche une petite commu chill sans pay-to-win. 18+ préféré.', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(21, 5, 1, 'Astuces pour monter Challenger sur LoL ?', 'Je suis coincé en Diamant 2 depuis 3 saisons… J’ai besoin de conseils macro/micro. Duo queue acceptée aussi !', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(22, 6, 1, 'Qui veut faire ranked Valorant ce soir ?', 'Je suis Immortal 2, cherche duo/trio sérieux pour push Ascendant. Mic obligatoire, pas de toxic svp.', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(23, 1, 1, 'Meilleurs builds Minecraft 1.21 ?', 'J’ai vu des farms à redstone de fou sur TikTok, vous avez des tutos ou schémas à partager ?', 'upload/minecraft-farm.jpg', '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(24, 2, 1, 'Skin Fortnite Battle Pass S6 vaut-il le coup ?', 'J’hésite à prendre le pass, les skins ont l’air ouf mais 950 V-Bucks… Votre avis ?', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(25, 3, 1, 'Outils pour maîtriser D&D en ligne ?', 'On joue tous en visio, quel site utilisez-vous pour les cartes, jets de dés, fiches persos ?', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(26, 5, 1, 'TFT : meilleure compo set 12 ?', 'Le nouveau set est sorti, qui a déjà trouvé la méta ? J’ai vu beaucoup de reroll Kaisa.', NULL, '2025-12-09 22:23:39', 0, 1, 0, 0, 'Anonyme', NULL, NULL, NULL),
(27, 1, 2, 'Meilleur micro/casque pour streamer pas cher ?', 'Je débute le stream, j’ai 80€ max. Vous avez des retours sur le HyperX Cloud Stinger ou le Logitech G432 ?', NULL, '2025-12-09 22:37:49', 0, 1, 24, 3, 'Anonyme', NULL, NULL, NULL),
(28, 2, 3, 'Qui veut duo ranked Fortnite ce soir ?', 'Je suis Platine 2, cherche quelqu’un de chill pour push Diamant. Mic obligatoire !', NULL, '2025-12-09 22:37:49', 0, 1, 18, 2, 'Anonyme', NULL, NULL, NULL),
(29, 3, 1, 'Idées de boss final épique pour D&D niveau 10 ?', 'Mes joueurs arrivent au climax de la campagne, j’ai besoin d’un combat légendaire !', NULL, '2025-12-09 22:37:49', 0, 1, 31, 1, 'Anonyme', NULL, NULL, NULL),
(30, 4, 2, 'Serveur Minecraft moddé 1.21 (Create + Biomes O Plenty) ?', 'On recrute 5 joueurs pour notre serveur moddé. Whitelist, 18+, ambiance RP léger.', 'upload/minecraft-serveur.jpg', '2025-12-09 22:37:49', 0, 1, 29, 4, 'Anonyme', NULL, NULL, NULL),
(31, 5, 3, 'Comment carry en soloQ Valorant quand tes mates sont afk ?', 'J’ai perdu 5 games d’affilée à cause de teammates inutiles… Des conseils pour carry 1v9 ?', NULL, '2025-12-09 22:37:49', 0, 1, 42, 15, 'Anonyme', NULL, NULL, NULL),
(32, 6, 1, 'Meilleure équipe TFT Set 12 actuel ?', 'Je suis coincé top 6, qui a la compo qui marche vraiment ?', NULL, '2025-12-09 22:37:49', 0, 1, 27, 6, 'Anonyme', NULL, NULL, NULL),
(33, 1, 3, 'Astuce pour ne plus rager en ranked ?', 'Je tilt trop facilement… Vous avez des techniques pour rester zen ?', NULL, '2025-12-09 22:37:49', 0, 1, 58, 9, 'Anonyme', NULL, NULL, NULL),
(34, 2, 2, 'Le nouveau pompe Fortnite est-il broken ou équilibré ?', 'J’ai vu des clips où il one-shot à 20m… Votre avis ?', NULL, '2025-12-09 22:37:49', 0, 1, 33, 12, 'Anonyme', NULL, NULL, NULL),
(35, 3, 2, 'Outils gratuits pour faire des cartes D&D magnifiques ?', 'Je veux impressionner mes joueurs avec de belles cartes !', NULL, '2025-12-09 22:37:49', 0, 1, 41, 2, 'Anonyme', NULL, NULL, NULL),
(36, 5, 1, 'Qui pour duoQ LoL ce soir ? (Gold 1 main ADC)', 'Cherche support sérieux, pas de flamme svp', NULL, '2025-12-09 22:37:49', 0, 1, 19, 5, 'Anonyme', NULL, NULL, NULL),
(37, 4, 3, 'Farm à fer la plus rapide en 1.21 ?', 'J’en ai marre des farms lentes, donnez-moi la meilleure !', NULL, '2025-12-09 22:37:49', 0, 1, 36, 3, 'Anonyme', NULL, NULL, NULL),
(38, 1, 2, 'Meilleur jeu pour jouer en couple ?', 'Ma copine veut jouer avec moi, on a aimé It Takes Two. Des suggestions ?', NULL, '2025-12-09 22:37:49', 0, 1, 67, 4, 'Anonyme', NULL, NULL, NULL),
(39, 6, 3, 'Comment counter la méta Portal + Exalted TFT ?', 'Je perds tout le temps contre ça…', NULL, '2025-12-09 22:37:49', 0, 1, 22, 7, 'Anonyme', NULL, NULL, NULL),
(40, 2, 1, 'Clip de ouf : 23 kills en solo squad Fortnite !', 'Regardez ça les gars → https://clips.twitch.tv/xxxx', 'upload/fortnite-23kills.jpg', '2025-12-09 22:37:49', 0, 1, 89, 11, 'Anonyme', NULL, NULL, NULL),
(41, 3, 2, 'Donjon homebrew gratuit à télécharger ?', 'Quelqu’un a un bon donjon prêt à jouer à partager ?', NULL, '2025-12-09 22:37:49', 0, 1, 45, 1, 'Anonyme', NULL, NULL, NULL),
(42, 1, 2, 'Meilleur micro/casque pour streamer pas cher ?', 'Je débute le stream, j’ai 80€ max. Vous avez des retours sur le HyperX Cloud Stinger ou le Logitech G432 ?', NULL, '2025-12-09 22:38:18', 0, 1, 24, 3, 'Anonyme', NULL, NULL, NULL),
(43, 2, 3, 'Qui veut duo ranked Fortnite ce soir ?', 'Je suis Platine 2, cherche quelqu’un de chill pour push Diamant. Mic obligatoire !', NULL, '2025-12-09 22:38:18', 0, 1, 18, 2, 'Anonyme', NULL, NULL, NULL),
(44, 3, 1, 'Idées de boss final épique pour D&D niveau 10 ?', 'Mes joueurs arrivent au climax de la campagne, j’ai besoin d’un combat légendaire !', NULL, '2025-12-09 22:38:18', 0, 1, 31, 1, 'Anonyme', NULL, NULL, NULL),
(45, 4, 2, 'Serveur Minecraft moddé 1.21 (Create + Biomes O Plenty) ?', 'On recrute 5 joueurs pour notre serveur moddé. Whitelist, 18+, ambiance RP léger.', 'upload/minecraft-serveur.jpg', '2025-12-09 22:38:18', 0, 1, 29, 4, 'Anonyme', NULL, NULL, NULL),
(46, 5, 3, 'Comment carry en soloQ Valorant quand tes mates sont afk ?', 'J’ai perdu 5 games d’affilée à cause de teammates inutiles… Des conseils pour carry 1v9 ?', NULL, '2025-12-09 22:38:18', 0, 1, 42, 15, 'Anonyme', NULL, NULL, NULL),
(47, 6, 1, 'Meilleure équipe TFT Set 12 actuel ?', 'Je suis coincé top 6, qui a la compo qui marche vraiment ?', NULL, '2025-12-09 22:38:18', 0, 1, 27, 6, 'Anonyme', NULL, NULL, NULL),
(48, 1, 3, 'Astuce pour ne plus rager en ranked ?', 'Je tilt trop facilement… Vous avez des techniques pour rester zen ?', NULL, '2025-12-09 22:38:18', 0, 1, 58, 9, 'Anonyme', NULL, NULL, NULL),
(49, 2, 2, 'Le nouveau pompe Fortnite est-il broken ou équilibré ?', 'J’ai vu des clips où il one-shot à 20m… Votre avis ?', NULL, '2025-12-09 22:38:18', 0, 1, 33, 12, 'Anonyme', NULL, NULL, NULL),
(50, 3, 2, 'Outils gratuits pour faire des cartes D&D magnifiques ?', 'Je veux impressionner mes joueurs avec de belles cartes !', NULL, '2025-12-09 22:38:18', 0, 1, 41, 2, 'Anonyme', NULL, NULL, NULL),
(51, 5, 1, 'Qui pour duoQ LoL ce soir ? (Gold 1 main ADC)', 'Cherche support sérieux, pas de flamme svp', NULL, '2025-12-09 22:38:18', 0, 1, 19, 5, 'Anonyme', NULL, NULL, NULL),
(52, 4, 3, 'Farm à fer la plus rapide en 1.21 ?', 'J’en ai marre des farms lentes, donnez-moi la meilleure !', NULL, '2025-12-09 22:38:18', 0, 1, 36, 3, 'Anonyme', NULL, NULL, NULL),
(53, 1, 2, 'Meilleur jeu pour jouer en couple ?', 'Ma copine veut jouer avec moi, on a aimé It Takes Two. Des suggestions ?', NULL, '2025-12-09 22:38:18', 0, 1, 67, 4, 'Anonyme', NULL, NULL, NULL),
(54, 6, 3, 'Comment counter la méta Portal + Exalted TFT ?', 'Je perds tout le temps contre ça…', NULL, '2025-12-09 22:38:18', 0, 1, 22, 7, 'Anonyme', NULL, NULL, NULL),
(55, 2, 1, 'Clip de ouf : 23 kills en solo squad Fortnite !', 'Regardez ça les gars → https://clips.twitch.tv/xxxx', 'upload/fortnite-23kills.jpg', '2025-12-09 22:38:18', 0, 1, 89, 11, 'Anonyme', NULL, NULL, NULL),
(56, 3, 2, 'Donjon homebrew gratuit à télécharger ?', 'Quelqu’un a un bon donjon prêt à jouer à partager ?', NULL, '2025-12-09 22:38:18', 0, 1, 45, 1, 'Anonyme', NULL, NULL, NULL),
(57, 1, 1, 'On a gagné la game !!', 'CLUTCH 1V4 EN RANKED IMMORTAL fire fire fire', NULL, '2025-12-10 22:08:16', 0, 1, 89, 0, 'Anonyme', '[\"fire\",\"100\",\"exploding_head\"]', 'https://tenor.com/view/valorant-clutch-gif-123456.gif', NULL),
(58, 2, 1, 'Nouveau skin Fortnite trop stylé', 'Regardez ce skin réactif les gars !!', NULL, '2025-12-10 22:08:16', 0, 1, 156, 0, 'Anonyme', '[\"star_struck\",\"drooling_face\",\"crown\"]', 'https://tenor.com/view/fortnite-dance-gif-789101.gif', 'sticker_pepe_clap.png'),
(59, 3, 1, 'Mon MJ nous a tous tués hier soir', 'Il a sorti un dragon ancien à la session 2 laughing laughing laughing', NULL, '2025-12-10 22:08:16', 0, 1, 234, 0, 'Anonyme', '[\"skull\",\"crying_laughing\",\"screaming\"]', NULL, 'sticker_dnd_d20_fail.png'),
(60, 5, 1, 'ACE avec Phoenix !!', 'Je suis trop chaud ce soir les gars', NULL, '2025-12-10 22:08:16', 0, 1, 312, 0, 'Anonyme', '[\"fire\",\"fire\",\"fire\",\"crown\",\"100\"]', 'https://tenor.com/view/valorant-ace-gif-246813.gif', NULL),
(61, 3, 1, 'Laggy house in my world', 'So I have this Minecraft survival world with my little cousin (he plays mostly creative mode and I in survival). He recently built this house made with a ton of sea lanterns and itemframes containing every fkng item in the game. The problem I have with this house is that it makes my fps drop down a ton every time I look in the direction of the house. Is there any way to fix this? Maybe a way to turn off the house for me or something? We play on bedbrock btw but im on pc (dont know if that makes any difference)\n\n\n[GIF sélectionné]', NULL, '2025-12-10 22:09:54', 0, 1, 0, 0, 'Anonyme', '[]', 'https://media2.giphy.com/media/v1.Y2lkPWE1YTU4ZDcwdHFvcGNqZHVhczJkM3BteHQzazQ3d3I3aW52dXprMTN2MXJic2M4ZCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/H6cmWzp6LGFvqjidB7/200.gif', NULL),
(62, 3, 1, 'So I have a few questions, and hope u guys can help me with them.', 'I played the NickEh30 Cup, and just wanted enough points to get the emoji. We ended with 164 points and my duo got the emoji, but I didn’t?\n\nMy duo has received multiple free cars from Fortnite, cars you usually have to pay for, where I have never received any? He just received the Metallica car, which u can only get by completing quests in Rocket Racing.', NULL, '2025-12-10 22:13:25', 0, 1, 0, 0, 'Anonyme', '[]', 'https://media0.giphy.com/media/v1.Y2lkPWE1YTU4ZDcwaThwbzNvOXh1b3l1dG55ZThyM29kMWt6N2dnejgwcmI1anpjNTVncCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/hCJX4KX8mmAwpIB0Z4/200.gif', NULL),
(63, 6, 1, 'New to League of Legends. I have a few questions to ask.', 'I just started playing League of Legends and right now I\'m really enjoying it, but there\'s a few things I don\'t really know yet and I this is the best place I know to ask, so here goes:\n\nI\'m currently playing as Sivir as often as I can and am following this guide and wanted to make sure I was getting the best, most up to date information. Also, could anyone recommend any good uploaders / youtube channels that produce good guides? It doesn\'t just have to be for sivir. I keep looking at random gameplay videos to try and learn but I\'m not sure of the quality of what I\'m watching.\n\nAre there any large competitive community sites that I can get involved with? To potentially form a team when I get a little better.', NULL, '2025-12-10 22:15:37', 0, 1, 0, 0, 'Anonyme', '[]', NULL, 'https://media0.giphy.com/media/v1.Y2lkPWE1YTU4ZDcwNWZqOTFkeHU2MWp1eG55ODhpMzg2cnZwa3Nna2FrM2puODhvd3ZzYyZlcD12MV9zdGlja2Vyc19zZWFyY2gmY3Q9cw/SYvBdbOBWN9tt2C0Hs/200.gif');

-- --------------------------------------------------------

--
-- Table structure for table `reponse`
--

CREATE TABLE `reponse` (
  `id_reponse` int(11) NOT NULL,
  `id_publication` int(11) NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_reponse` timestamp NOT NULL DEFAULT current_timestamp(),
  `supprimee` tinyint(4) DEFAULT 0,
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reponse`
--

INSERT INTO `reponse` (`id_reponse`, `id_publication`, `id_auteur`, `contenu`, `date_reponse`, `supprimee`, `likes`, `dislikes`) VALUES
(1, 1, 2, 'Super initiative ! Hâte de partager avec la communauté.', '2025-11-24 09:31:53', 0, 0, 0),
(2, 1, 3, 'Enfin une plateforme dédiée aux gamers français !', '2025-11-24 09:31:53', 0, 0, 0),
(3, 2, 1, 'Le fusil de sniper nouvelle génération est ultra OP en ce moment.', '2025-11-24 09:31:53', 0, 0, 0),
(4, 2, 3, 'N\'oublie pas les grenades fumigènes, très utiles en fin de partie.', '2025-11-24 09:31:53', 0, 0, 0),
(5, 3, 2, 'Commence par une quête de village contre des gobelins, classique mais efficace.', '2025-11-24 09:31:53', 0, 0, 0),
(6, 4, 2, 'Je recommande Create et JourneyMap, indispensables !', '2025-11-24 09:31:53', 0, 0, 0),
(7, 5, 1, 'ceci est un test manuel pour voir si ca marche ', '2025-11-27 13:02:50', 0, 0, 0),
(9, 7, 1, 'j\'adore moi aussi!!!', '2025-12-02 22:14:07', 0, 0, 0),
(10, 7, 1, 'womp womp', '2025-12-02 22:20:30', 1, 0, 0),
(11, 4, 1, 'je n\'ai pas triuvé de nouveaux mods', '2025-12-04 11:36:56', 1, 0, 0),
(12, 7, 1, 'Bonne question! Je te recommande de rejoindre une communauté active où tu pourras échanger des astuces. Ça aide beaucoup! ????', '2025-12-09 22:19:42', 0, 0, 0),
(24, 17, 1, 'Salut! Je pense que la meilleure approche est de tester différentes stratégies et de voir ce qui fonctionne pour toi. Bon courage! ????', '2025-12-09 22:25:00', 0, 0, 0),
(48, 63, 1, 'Salut! Je pense que la meilleure approche est de tester différentes stratégies et de voir ce qui fonctionne pour toi. Bon courage! ????', '2025-12-11 09:03:28', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stream`
--

CREATE TABLE `stream` (
  `id_stream` int(11) NOT NULL,
  `id_streamer` int(11) NOT NULL,
  `id_association` int(11) DEFAULT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `plateforme` varchar(100) DEFAULT NULL,
  `id_theme` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `don_total` decimal(12,2) DEFAULT 0.00,
  `type_contenu` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `objectif_don` decimal(12,2) DEFAULT 0.00,
  `etat` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `total_dons` decimal(12,2) DEFAULT 0.00,
  `nb_commentaires` int(11) DEFAULT 0,
  `nb_likes` int(11) DEFAULT 0,
  `nb_dislikes` int(11) DEFAULT 0,
  `nb_vues` int(11) DEFAULT 0,
  `nb_notification` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stream`
--

INSERT INTO `stream` (`id_stream`, `id_streamer`, `id_association`, `id_evenement`, `titre`, `plateforme`, `id_theme`, `url`, `statut`, `don_total`, `type_contenu`, `description`, `objectif_don`, `etat`, `date_debut`, `date_fin`, `total_dons`, `nb_commentaires`, `nb_likes`, `nb_dislikes`, `nb_vues`, `nb_notification`) VALUES
(10, 10, NULL, NULL, 'gta rp', 'YouTube', NULL, 'http://gtarp.com', 'en_cours', 600.00, NULL, NULL, 0.00, 'planifie', '2025-12-03 11:10:00', '2025-12-03 18:00:00', 0.00, 0, 2, 0, 21, 0),
(11, 2, NULL, NULL, 'atrah valo fil 5fif', 'Twitch', NULL, 'http://d0wnnvalo.com', 'planifie', 0.00, NULL, NULL, 0.00, 'planifie', '2025-06-12 08:00:00', '2025-06-12 12:00:00', 0.00, 0, 1, 0, 0, 0),
(12, 4, NULL, NULL, 'gali gotlek', 'Twitch', NULL, 'http://dahma.com', 'termine', 1587.00, NULL, NULL, 0.00, 'planifie', '2025-04-12 23:00:00', '2025-05-12 03:00:00', 0.00, 0, 5, 5, 20, 0),
(13, 7, NULL, NULL, 'road to platinium', 'Twitch', NULL, 'https://babyvalo.com', 'en_cours', 182.00, NULL, NULL, 0.00, 'planifie', '2025-05-12 16:00:00', '2025-05-12 23:00:00', 0.00, 1, 7, 1, 8, 0),
(14, 9, NULL, NULL, 'fifaaaa', 'YouTube', NULL, 'http://gooba.com', 'planifie', 0.00, NULL, NULL, 0.00, 'planifie', '2025-07-12 20:00:00', '2025-07-12 23:30:00', 0.00, 5, 3, 1, 29, 0),
(15, 3, NULL, NULL, 'w9ayet narjaa lil LoL', 'Twitch', NULL, 'http://jelylol.com', 'termine', 3520.00, NULL, NULL, 0.00, 'planifie', '2025-02-12 15:30:00', '2025-02-12 22:45:00', 0.00, 6, 2, 1, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `streamer`
--

CREATE TABLE `streamer` (
  `id_user` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `lien_stream` varchar(255) DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `plateforme` varchar(100) NOT NULL DEFAULT 'Twitch'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `streamer`
--

INSERT INTO `streamer` (`id_user`, `pseudo`, `description`, `lien_stream`, `date_validation`, `plateforme`) VALUES
(2, 'd0wwn', NULL, 'https://twitch.tv/d0wwn', NULL, 'Youtube'),
(3, 'jelyfishtn', NULL, 'https://youtube.com/@jelyfishtn', NULL, 'Twitch'),
(4, 'dahmax', NULL, 'https://twitch.tv/dahmax', NULL, 'Twitch'),
(5, 'loj', NULL, 'https://twitch.tv/loj', NULL, 'Twitch'),
(6, 'psycom', NULL, 'https://twitch.tv/psycom', NULL, 'Twitch'),
(7, 'Evillishbaby', NULL, 'https://twitch.tv/evillishbaby', NULL, 'Twitch'),
(8, 'm3ky', NULL, 'https://twitch.tv/m3ky', NULL, 'Twitch'),
(9, 'goobaa', NULL, 'https://twitch.tv/goobaa', NULL, 'Twitch'),
(10, 'chafcha', NULL, 'https://twitch.tv/chafcha', NULL, 'Twitch,YouTube');

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

CREATE TABLE `theme` (
  `id_theme` int(11) NOT NULL,
  `nom_theme` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `icon_url` varchar(500) DEFAULT NULL,
  `couleur` varchar(7) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`id_theme`, `nom_theme`, `description`, `image_url`, `icon_url`, `couleur`, `date_creation`) VALUES
(1, 'Fortnite', 'Battle royale epic avec construction', NULL, NULL, '#7B68EE', '2025-12-10 13:36:09'),
(2, 'League of Legends', 'MOBA compétitif de Riot Games', NULL, NULL, '#0A8CC9', '2025-12-10 13:36:09'),
(3, 'Valorant', 'FPS compétitif 5v5 tactique', NULL, NULL, '#FA4454', '2025-12-10 13:36:09'),
(4, 'Rocket League', 'Voitures jouant au football', NULL, NULL, '#33B5E5', '2025-12-10 13:36:09'),
(5, 'Just Chatting', 'Chat et discussion en live', NULL, NULL, '#9146FF', '2025-12-10 13:36:09'),
(6, 'GTA V', 'Grand Theft Auto V - Jeu d action open world', NULL, NULL, '#FF0000', '2025-12-10 13:37:04'),
(7, 'FC 2024', 'EA Sports FC 2024 - Jeu de football', NULL, NULL, '#1F51BA', '2025-12-10 13:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','other','prefer-not') DEFAULT 'prefer-not',
  `country` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `role` enum('viewer','streamer','admin') NOT NULL DEFAULT 'viewer',
  `stream_link` varchar(255) DEFAULT NULL,
  `stream_description` text DEFAULT NULL,
  `stream_platform` enum('twitch','youtube','kick','other') DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL COMMENT 'Hash SHA256 du token de réinitialisation',
  `reset_token_expires` datetime DEFAULT NULL COMMENT 'Expiration du token (1 heure)',
  `reset_token_used` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Token utilisé (0=non utilisé, 1=utilisé) - pour prévenir réutilisation',
  `profile_image` varchar(255) DEFAULT NULL,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_banned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Utilisateur banni (1) ou actif (0)',
  `ban_type` enum('soft','permanent') DEFAULT NULL COMMENT 'Type de bannissement: soft (temporaire) ou permanent',
  `ban_reason` text DEFAULT NULL COMMENT 'Raison du bannissement',
  `banned_at` datetime DEFAULT NULL COMMENT 'Date et heure du bannissement',
  `banned_until` datetime DEFAULT NULL COMMENT 'Date d''expiration du bannissement (NULL si permanent)',
  `banned_by` int(11) DEFAULT NULL COMMENT 'ID de l''admin qui a banni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `birthdate`, `gender`, `country`, `city`, `role`, `stream_link`, `stream_description`, `stream_platform`, `password`, `reset_token`, `reset_token_expires`, `reset_token_used`, `profile_image`, `join_date`, `created_at`, `updated_at`, `is_banned`, `ban_type`, `ban_reason`, `banned_at`, `banned_until`, `banned_by`) VALUES
(3, 'monia', 'bouzaiane', '241JMT3650', 'zizouchaibi2005@gmail.com', '2005-06-05', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$a/9JM8T9VSSmKoyLUJphqeCBOSfJ7p9R2MHNIbW/kWyVgh9Bp3iSK', 'c5bde998cb785bb3acdf6fadbb10be3d046eff9a21edcc993f6c20cf47534033', '2025-12-10 16:28:19', 0, 'assets/images/avatars/avatar5.png', '2025-11-19 12:04:50', '2025-11-19 12:04:50', '2025-12-10 15:49:00', 0, NULL, NULL, NULL, NULL, NULL),
(4, 'monia', 'bouzaiane', 'azzzzzzzzzzzzzzz', 'z@gmail.com', '2000-02-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$E/squ2gL1nYLJWB9aw/qiuJ9Wo7GSY56abawUmUjo7FFClmz8d4sq', NULL, NULL, 0, 'assets/images/avatars/avatar2.png', '2025-11-19 12:13:44', '2025-11-19 12:13:44', '2025-12-10 14:16:13', 0, NULL, NULL, NULL, NULL, NULL),
(8, 'aziz', 'chaibi', 'jh', 'yu@gmail.com', '2000-06-05', 'other', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$WRWI04xTy/zAttGzUYrp..s3P5jQRq79eHQ71rvGJbfuzmHXhhtY6', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-11-19 22:46:10', '2025-11-19 22:46:10', '2025-11-19 22:46:10', 0, NULL, NULL, NULL, NULL, NULL),
(9, 'te', 'tr', 'c', 'hhr@gmail.com', '2000-02-23', 'male', 'TN', 'Ariana', 'streamer', '88888888888888', 'ezae', 'twitch', '$2y$10$QI53U2NheVr.FYph.C94De1icGjkvsaFmC5B5xGYC73aQCeTfrccS', NULL, NULL, 0, 'assets/images/avatars/avatar1.png', '2025-11-20 14:06:13', '2025-11-20 14:06:13', '2025-12-03 11:56:32', 0, NULL, NULL, NULL, NULL, NULL),
(16, 'Test', 'User', 'testuser', 'test@test.com', '2000-01-01', 'male', 'FR', '', 'viewer', '', '', '', '$2y$10$GpYJnCl1PlYFdvCQq6uu4u21/WuJ1cCW/j1ldgVAYwk/tMYJqoDUG', '99f5f762cc87c7baced8d50b1bf77d7b94fc522ad97dc825c42a0cbbbfea0b67', '2025-12-10 16:24:00', 1, 'assets/images/avatars/avatar1.png', '2025-11-20 15:57:15', '2025-11-20 15:57:15', '2025-12-10 14:24:01', 0, NULL, NULL, NULL, NULL, NULL),
(17, 'iaydh', 'chaibi', 'dadou', 'dadou@gmail.com', '2005-02-23', 'male', 'FR', 'Ariana', 'streamer', 'hhtazhe', 'streamera', 'twitch', '$2y$10$9PmImsFyaGrYp96rGliMKOMTo46wtrUOm9Xn/52CdGzkCTRGc1feW', NULL, NULL, 0, 'assets/images/avatars/avatar1.png', '2025-11-20 18:07:48', '2025-11-20 18:07:48', '2025-11-20 18:07:48', 0, NULL, NULL, NULL, NULL, NULL),
(18, 'da', 'd', 'dra', 'x@gmail.com', '2005-10-23', 'male', 'TN', 'Ariana', 'streamer', 'https://www.twitch.tv/', 'dqsd', 'twitch', '$2y$10$j9OGo/mBW7R5E5hFJuowMuBHxcMIyqK.AGeaX3mpYWdizWoXJugWC', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f/View/FrontOffice/assets/images/avatars/avatar1.png', '2025-11-20 18:08:59', '2025-11-20 18:08:59', '2025-11-22 14:00:49', 0, NULL, NULL, NULL, NULL, NULL),
(20, 'H', 'H', 'H', 'test@gmail.com', '2000-02-23', 'female', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$UbE235ZGBntwDi7SamLrKOWrrNx0R0.VPld/tCIzVOkL2bFi6U43W', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f/View/FrontOffice/assets/images/avatars/avatar5.png', '2025-11-20 18:34:50', '2025-11-20 18:34:50', '2025-11-20 18:35:10', 0, NULL, NULL, NULL, NULL, NULL),
(22, 'mariem', 'you', 'walahy manref', 'mariem@gmail.com', '2005-10-26', 'female', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$J7I/Lv4jvMltbp/o8sKbnug7TGbHzh4Ym0H7fw7v1bney3v09Aou2', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f/View/FrontOffice/assets/images/avatars/avatar4.png', '2025-11-20 23:24:23', '2025-11-20 23:24:23', '2025-11-20 23:25:33', 0, NULL, NULL, NULL, NULL, NULL),
(27, 'Admin', 'System', 'admin', 'admin@playtohelp.com', '1990-01-01', 'male', 'FR', 'Paris', 'admin', NULL, NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f/View/FrontOffice/assets/images/avatars/avatar1.png', '2025-11-22 14:18:21', '2025-11-22 14:18:21', '2025-11-22 14:24:32', 0, NULL, NULL, NULL, NULL, NULL),
(30, 'Testez', 'User', 'testuser2', 'test2@test.com', '2000-01-01', 'male', 'FR', '', 'viewer', '', '', '', '$2y$10$ZpYqEGHW2dCO5YLRoXK7K.xu3n22OAqLWFACKcfHUyLtOr8dtILdG', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-11-22 15:54:16', '2025-11-22 15:54:16', '2025-11-22 15:54:16', 0, NULL, NULL, NULL, NULL, NULL),
(31, 'test3', 'test3', 'test3', 'test3@gmail.com', '2005-10-23', 'male', 'TN', 'Ariana', 'streamer', 'https://www.deepseek.com/', 'test3', 'twitch', '$2y$10$xZrvSjdXKvAnZX392VNqy.AuuglLC8FDJ6T.SphtDsOJDzoxPjrDu', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f%20-%20d1/View/FrontOffice/assets/images/avatars/avatar5.png', '2025-11-22 16:32:10', '2025-11-22 16:32:10', '2025-12-06 17:26:37', 1, 'permanent', 'test', '2025-12-06 18:26:37', NULL, 32),
(34, 'youssef', 'mest', 'rox', 'rox@gmail.com', '2005-10-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$XJ69gFDifqA73v5m4DhYe.I82Nrl12NM.2r4d5731PZYsRbBg69dK', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f%20-%20d1/View/FrontOffice/assets/images/avatars/avatar5.png', '2025-12-01 09:39:23', '2025-12-01 09:39:23', '2025-12-01 09:39:44', 0, NULL, NULL, NULL, NULL, NULL),
(35, '12', '12', '12', 'iyadhchaibi2005@gmail.com', '0000-00-00', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$PbGHhDsAGA4HDuwghBSTGeBxZ.KJDCqgkgw6n4RLwPNMJdybEWb9G', NULL, NULL, 0, 'assets/images/avatars/avatar5.png', '2025-12-01 10:02:38', '2025-12-01 10:02:38', '2025-12-02 22:46:05', 0, NULL, NULL, NULL, NULL, NULL),
(36, 'iyadh23', 'chaibi23', '2315', '213@gmail.com', '0000-00-00', 'prefer-not', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$onw/GLl8If6eearUqrc2/OKefZ18sBIQcZnyI69m9qys.rcpLXz56', NULL, NULL, 0, 'assets/images/avatars/avatar1.png', '2025-12-01 10:05:48', '2025-12-01 10:05:48', '2025-12-02 22:50:05', 0, NULL, NULL, NULL, NULL, NULL),
(37, 'iyadh213', 'chaibi32', 'eaze', 'ez@gmail.com', '0000-00-00', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$u9O/0Mr4fhXnhpU6WtqHje1ozHm7LsVliIdrRr4d0uoTHFFETgSce', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:19:39', '2025-12-01 10:19:39', '2025-12-01 10:19:39', 0, NULL, NULL, NULL, NULL, NULL),
(38, 'iyadh123', 'chaibi123', 'ezarze', 'er@gmail.com', '0000-00-00', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$BDx2vDTxRz7VvElaw525CupBA9lgSadUN9qqmNBWoiuzwLDY0QkVC', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:23:32', '2025-12-01 10:23:32', '2025-12-02 20:50:38', 1, 'permanent', 'teeeeeeee', '2025-12-02 21:50:38', NULL, 32),
(39, 'iyadh2315', 'chaibi32153', 'rere', '315@gmail.com', '0000-00-00', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$nZE/kJLQyoXZ0KqOTr0mWeI.Sy8cJcAPpSfsKzzSITsrWrj2M5fHK', NULL, NULL, 0, 'assets/images/avatars/avatar2.png', '2025-12-01 10:27:04', '2025-12-01 10:27:04', '2025-12-06 17:27:51', 1, 'soft', 'test', '2025-12-06 18:27:51', '2025-12-07 18:27:00', 32),
(40, 'iyadh1232', 'chaibi', 'er', '53@gmail.com', '2005-02-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$Ww4jLhFwQMutC2YdV3puRemTWShAHTUDawT82fVv5bswv27iHylPe', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:29:35', '2025-12-01 10:29:35', '2025-12-02 19:47:19', 1, 'permanent', 'ttttttttttttttttt', '2025-12-02 20:47:19', NULL, 32),
(41, 'a', 'z', 'z', '54@gmail.com', '2000-10-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$oGOp2KqtdkBacqr/VEBl2.HnfCu4HTH/yuLglI62Ma5ZmYlfpYjy6', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:30:55', '2025-12-01 10:30:55', '2025-12-02 19:38:45', 1, 'permanent', 'haya', '2025-12-02 20:38:45', NULL, 32),
(42, 'a', 'z', 'w', 'w@gmail.com', '2000-10-23', 'prefer-not', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$PBbuClVUvgoHzQKuipmlwutT15sKIImeTkcxzVUXVR/YiLEgt2JUO', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:31:47', '2025-12-01 10:31:47', '2025-12-02 20:35:08', 1, 'permanent', '', '2025-12-02 21:35:08', NULL, 32),
(43, 'e', 'e', 're', 'sd@gmail.com', '2005-10-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$eAFzBI7k0UK.nKK1di1IyOvwabyjYk8WFwbaaFlv9Ud6mRbC5bRLC', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:52:50', '2025-12-01 10:52:50', '2025-12-02 19:30:14', 1, 'permanent', 'zaez', '2025-12-02 20:30:14', NULL, 32),
(44, '123', '123', 'ab', 'tes@gmail.com', '2005-10-23', 'male', 'FR', 'Ariana', 'viewer', '', '', '', '$2y$10$HQM9LlcOV4WJQm44OMqBX.Do/uUCh0qM30PqF32vZE/GTfzkvl9ha', NULL, NULL, 0, 'assets/images/profile.jpg', '2025-12-01 10:56:44', '2025-12-01 10:56:44', '2025-12-03 10:56:45', 0, NULL, NULL, NULL, NULL, NULL),
(45, '123', '123', 'cv', 'zd@gmail.com', '2005-10-23', 'male', 'TN', 'Ariana', 'viewer', '', '', '', '$2y$10$s7MGB6NbSIbD0t44LdmxweyQDi0iOf3VMvn8JmVYVkFdTw5r5NdA6', NULL, NULL, 0, 'http://localhost/play%20to%20help%20mvc%20f%20-%20d1/View/FrontOffice/assets/images/avatars/avatar4.png', '2025-12-01 11:14:22', '2025-12-01 11:14:22', '2025-12-02 19:21:14', 1, 'permanent', 'test', '2025-12-02 20:21:04', NULL, NULL),
(47, 'ronaldo', 'cristiano', 'cis', 'cris@gmail.com', '1998-10-23', 'male', 'CA', 'Ariana', 'viewer', '', '', '', '$2y$10$DbeqglHw6n1qrWliQoDZYeiRe2BiNplISYpcSxpDImA9UYloK7phW', NULL, NULL, 0, 'assets/images/avatars/avatar2.png', '2025-12-03 11:55:45', '2025-12-03 11:55:45', '2025-12-03 11:56:00', 0, NULL, NULL, NULL, NULL, NULL),
(48, 'mostfa', 'jaziri', 'joe', 'jaziri@gmail.com', '2000-10-23', 'male', 'MA', '', 'viewer', '', '', '', '$2y$10$Jp.x07SjYesGi9YKF76e3u/bLMxNevV/dMPkeWytTDLjEh1bYcpq2', 'fddc0398d09d0d8dc8aee164439d8aa9e133d721bb07ff8422904b4e245e6c2f', '2025-12-10 16:25:06', 1, 'assets/images/profile.jpg', '2025-12-10 13:04:26', '2025-12-10 13:04:26', '2025-12-10 14:25:06', 0, NULL, NULL, NULL, NULL, NULL),
(49, 'ya', 'chaha', 'chaha', 'azizchaihbi@gmail.com', '2000-02-23', 'male', 'DZ', 'Ariana', 'viewer', '', '', '', '$2y$10$yfapS3fzzZPXKjC6S8m1Jecoec9c/HpIcIC3OtG4Lmpes6o0ufZcy', '8085ac4c0c5c339e4db0a18020ab17ac8f28b59d77d14112c633dbee2bad6d02', '2025-12-10 16:36:19', 0, 'assets/images/avatars/uploaded/avatar_49_1765377910.png', '2025-12-10 14:20:30', '2025-12-10 14:20:30', '2025-12-13 14:45:42', 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id_user` int(11) NOT NULL,
  `preferences` text DEFAULT NULL,
  `inscrit_newsletter` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `user_id` int(11) NOT NULL,
  `status` enum('online','offline','away','busy') NOT NULL DEFAULT 'offline',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_message` varchar(255) DEFAULT NULL COMMENT 'Message personnalisé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_status`
--

INSERT INTO `user_status` (`user_id`, `status`, `last_activity`, `status_message`) VALUES
(3, 'online', '2025-12-13 16:42:15', NULL),
(49, 'offline', '2025-12-10 20:35:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `type_utilisateur` enum('User','Streamer','Admin') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `type_utilisateur`) VALUES
(1, 'Test', 'Streamer', 'test@streamer.com', 'password123', '2025-11-30 16:32:20', 'Streamer'),
(2, 'd0wwn', 'Streamer', 'd0wwn@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(3, 'jelyfishtn', 'Streamer', 'jelyfishtn@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(4, 'dahmax', 'Streamer', 'dahmax@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(5, 'loj', 'Streamer', 'loj@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(6, 'psycom', 'Streamer', 'psycom@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(7, 'Evillishbaby', 'Streamer', 'evillishbaby@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(8, 'm3ky', 'Streamer', 'm3ky@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(9, 'goobaa', 'Streamer', 'goobaa@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer'),
(10, 'chafcha', 'Streamer', 'chafcha@playtohelp.com', 'password123', '2025-11-30 16:39:44', 'Streamer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`id_association`);

--
-- Indexes for table `banned_users`
--
ALTER TABLE `banned_users`
  ADD PRIMARY KEY (`id_ban`);

--
-- Indexes for table `challenge`
--
ALTER TABLE `challenge`
  ADD PRIMARY KEY (`id_challenge`),
  ADD KEY `id_association` (`id_association`);

--
-- Indexes for table `clip`
--
ALTER TABLE `clip`
  ADD PRIMARY KEY (`id_clip`),
  ADD KEY `idx_clip_stream` (`id_stream`),
  ADD KEY `idx_clip_date` (`date_creation`),
  ADD KEY `idx_clip_views_likes` (`nb_vues`,`nb_likes`);

--
-- Indexes for table `don`
--
ALTER TABLE `don`
  ADD PRIMARY KEY (`id_don`),
  ADD KEY `id_association` (`id_association`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`);

--
-- Indexes for table `evenement_streamer`
--
ALTER TABLE `evenement_streamer`
  ADD PRIMARY KEY (`id_evenement`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id_forum`);

--
-- Indexes for table `friendships`
--
ALTER TABLE `friendships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`),
  ADD KEY `status` (`status`),
  ADD KEY `idx_friendships_lookup` (`user_id`,`friend_id`,`status`);

--
-- Indexes for table `friend_notifications`
--
ALTER TABLE `friend_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `fk_friend_notif_from` (`from_user_id`),
  ADD KEY `idx_notifications_unread` (`user_id`,`is_read`,`created_at`);

--
-- Indexes for table `moderation_publication`
--
ALTER TABLE `moderation_publication`
  ADD PRIMARY KEY (`id_admin`,`id_publication`,`date_action`),
  ADD KEY `id_publication` (`id_publication`);

--
-- Indexes for table `moderation_reponse`
--
ALTER TABLE `moderation_reponse`
  ADD PRIMARY KEY (`id_admin`,`id_reponse`),
  ADD KEY `id_reponse` (`id_reponse`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id_publication`),
  ADD KEY `id_forum` (`id_forum`),
  ADD KEY `id_auteur` (`id_auteur`),
  ADD KEY `idx_pub_date` (`date_publication`),
  ADD KEY `idx_pub_validee` (`validee`);

--
-- Indexes for table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id_reponse`),
  ADD KEY `id_publication` (`id_publication`),
  ADD KEY `id_auteur` (`id_auteur`);

--
-- Indexes for table `stream`
--
ALTER TABLE `stream`
  ADD PRIMARY KEY (`id_stream`),
  ADD KEY `id_streamer` (`id_streamer`),
  ADD KEY `id_association` (`id_association`),
  ADD KEY `id_evenement` (`id_evenement`),
  ADD KEY `idx_stream_etat` (`etat`),
  ADD KEY `idx_stream_date` (`date_debut`),
  ADD KEY `fk_stream_theme` (`id_theme`);

--
-- Indexes for table `streamer`
--
ALTER TABLE `streamer`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Indexes for table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id_theme`),
  ADD UNIQUE KEY `nom_theme` (`nom_theme`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_reset_token` (`reset_token`),
  ADD KEY `idx_is_banned` (`is_banned`),
  ADD KEY `idx_banned_until` (`banned_until`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_user_status_online` (`status`,`last_activity`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `association`
--
ALTER TABLE `association`
  MODIFY `id_association` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banned_users`
--
ALTER TABLE `banned_users`
  MODIFY `id_ban` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `challenge`
--
ALTER TABLE `challenge`
  MODIFY `id_challenge` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `clip`
--
ALTER TABLE `clip`
  MODIFY `id_clip` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `don`
--
ALTER TABLE `don`
  MODIFY `id_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `forum`
--
ALTER TABLE `forum`
  MODIFY `id_forum` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `friendships`
--
ALTER TABLE `friendships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `friend_notifications`
--
ALTER TABLE `friend_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id_publication` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id_reponse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `stream`
--
ALTER TABLE `stream`
  MODIFY `id_stream` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `theme`
--
ALTER TABLE `theme`
  MODIFY `id_theme` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `challenge`
--
ALTER TABLE `challenge`
  ADD CONSTRAINT `challenge_ibfk_1` FOREIGN KEY (`id_association`) REFERENCES `association` (`id_association`) ON DELETE CASCADE;

--
-- Constraints for table `clip`
--
ALTER TABLE `clip`
  ADD CONSTRAINT `clip_ibfk_1` FOREIGN KEY (`id_stream`) REFERENCES `stream` (`id_stream`);

--
-- Constraints for table `don`
--
ALTER TABLE `don`
  ADD CONSTRAINT `don_ibfk_1` FOREIGN KEY (`id_association`) REFERENCES `association` (`id_association`) ON DELETE CASCADE;

--
-- Constraints for table `evenement_streamer`
--
ALTER TABLE `evenement_streamer`
  ADD CONSTRAINT `evenement_streamer_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `evenement_streamer_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `streamer` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `friendships`
--
ALTER TABLE `friendships`
  ADD CONSTRAINT `fk_friendships_friend` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friendships_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friend_notifications`
--
ALTER TABLE `friend_notifications`
  ADD CONSTRAINT `fk_friend_notif_from` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friend_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_publication`
--
ALTER TABLE `moderation_publication`
  ADD CONSTRAINT `moderation_publication_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderation_publication_ibfk_2` FOREIGN KEY (`id_publication`) REFERENCES `publication` (`id_publication`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_reponse`
--
ALTER TABLE `moderation_reponse`
  ADD CONSTRAINT `moderation_reponse_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderation_reponse_ibfk_2` FOREIGN KEY (`id_reponse`) REFERENCES `reponse` (`id_reponse`) ON DELETE CASCADE;

--
-- Constraints for table `publication`
--
ALTER TABLE `publication`
  ADD CONSTRAINT `publication_ibfk_1` FOREIGN KEY (`id_forum`) REFERENCES `forum` (`id_forum`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`id_publication`) REFERENCES `publication` (`id_publication`) ON DELETE CASCADE,
  ADD CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `stream`
--
ALTER TABLE `stream`
  ADD CONSTRAINT `fk_stream_theme` FOREIGN KEY (`id_theme`) REFERENCES `theme` (`id_theme`) ON DELETE SET NULL,
  ADD CONSTRAINT `stream_ibfk_1` FOREIGN KEY (`id_streamer`) REFERENCES `streamer` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `stream_ibfk_2` FOREIGN KEY (`id_association`) REFERENCES `association` (`id_association`),
  ADD CONSTRAINT `stream_ibfk_3` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE SET NULL;

--
-- Constraints for table `streamer`
--
ALTER TABLE `streamer`
  ADD CONSTRAINT `streamer_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `user_status`
--
ALTER TABLE `user_status`
  ADD CONSTRAINT `fk_user_status` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
