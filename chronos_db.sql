-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-chronos.alwaysdata.net
-- Generation Time: Mar 21, 2026 at 03:24 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chronos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ADMIN`
--

CREATE TABLE `ADMIN` (
  `ID_ADMIN` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ADMIN`
--

INSERT INTO `ADMIN` (`ID_ADMIN`, `USERNAME`, `PASSWORD`) VALUES
(1, 'admin', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

-- --------------------------------------------------------

--
-- Table structure for table `API_TOKENS`
--

CREATE TABLE `API_TOKENS` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','professor','security') NOT NULL DEFAULT 'student',
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 24 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `API_TOKENS`
--

INSERT INTO `API_TOKENS` (`id`, `user_id`, `user_type`, `token`, `created_at`, `expires_at`) VALUES
(25, 2, 'professor', 'b6a4282349bc0bb5481ead402a91cb91e6c9cf5d5d5ca91f26f635c267d20aa7', '2026-03-21 14:03:25', '2026-03-22 14:03:25'),
(28, 1, 'professor', '1f63b57303c71a214cfb46eb6a41224e69d222ebd91a585e0044a99ed8f174a9', '2026-03-21 14:05:04', '2026-03-22 14:05:04'),
(30, 1, 'security', 'b1de664fd5e768d8baf8fa00667d8a4f6b281a0a9c5f8dfa767e7d809f0bfed8', '2026-03-21 14:06:01', '2026-03-22 14:06:01'),
(32, 1, 'student', 'ad5008872d2cb9ce4b4d586d19067afb9ec60a549c42506a81d1dda7f39d3562', '2026-03-21 14:22:02', '2026-03-22 14:22:02'),
(33, 2, 'student', 'de00738ea12940dcd00aa78a5035c4dd17c176cc118eb61f76478ddec1a6328f', '2026-03-21 14:22:49', '2026-03-22 14:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `CARTE_LAYOUT`
--

CREATE TABLE `CARTE_LAYOUT` (
  `id` int(11) NOT NULL,
  `grid_data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `CARTE_LAYOUT`
--

INSERT INTO `CARTE_LAYOUT` (`id`, `grid_data`) VALUES
(1, '[{\"index\":\"0\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"1\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"2\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"3\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"4\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"5\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"6\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"7\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"8\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"9\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"10\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"11\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"12\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"13\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"14\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"15\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"16\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"17\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"18\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"19\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"20\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"21\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"22\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"23\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"24\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"25\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"26\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"27\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"28\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"29\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"30\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"31\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"32\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"33\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"34\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"35\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"36\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"37\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"38\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"39\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"40\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"41\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"42\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"43\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"44\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"45\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"46\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"47\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"48\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"49\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"50\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"51\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"52\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"53\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"54\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"55\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"56\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"57\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"58\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"59\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"60\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"61\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"62\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"63\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"64\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"65\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"66\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"67\",\"type\":\"classroom\",\"name\":\"111\",\"id\":\"8\"},{\"index\":\"68\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"69\",\"type\":\"classroom\",\"name\":\"109\",\"id\":\"7\"},{\"index\":\"70\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"71\",\"type\":\"classroom\",\"name\":\"107\",\"id\":\"6\"},{\"index\":\"72\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"73\",\"type\":\"classroom\",\"name\":\"105\",\"id\":\"5\"},{\"index\":\"74\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"75\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"76\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"77\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"78\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"79\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"80\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"81\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"82\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"83\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"84\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"85\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"86\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"87\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"88\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"89\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"90\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"91\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"92\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"93\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"94\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"95\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"96\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"97\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"98\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"99\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"100\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"101\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"102\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"103\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"104\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"105\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"106\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"107\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"108\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"109\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"110\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"111\",\"type\":\"classroom\",\"name\":\"SC01\",\"id\":\"16\"},{\"index\":\"112\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"113\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"114\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"115\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"116\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"117\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"118\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"119\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"120\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"121\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"122\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"123\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"124\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"125\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"126\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"127\",\"type\":\"classroom\",\"name\":\"110\",\"id\":\"13\"},{\"index\":\"128\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"129\",\"type\":\"classroom\",\"name\":\"108\",\"id\":\"12\"},{\"index\":\"130\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"131\",\"type\":\"classroom\",\"name\":\"106\",\"id\":\"11\"},{\"index\":\"132\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"133\",\"type\":\"classroom\",\"name\":\"104\",\"id\":\"10\"},{\"index\":\"134\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"135\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"136\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"137\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"138\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"139\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"140\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"141\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"142\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"143\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"144\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"145\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"146\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"147\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"148\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"149\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"150\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"151\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"152\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"153\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"154\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"155\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"156\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"157\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"158\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"159\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"160\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"161\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"162\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"163\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"164\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"165\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"166\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"167\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"168\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"169\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"170\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"171\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"172\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"173\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"174\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"175\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"176\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"177\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"178\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"179\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"180\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"181\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"182\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"183\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"184\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"185\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"186\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"187\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"188\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"189\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"190\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"191\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"192\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"193\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"194\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"195\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"196\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"197\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"198\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"199\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"200\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"201\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"202\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"203\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"204\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"205\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"206\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"207\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"208\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"209\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"210\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"211\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"212\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"213\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"214\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"215\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"216\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"217\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"218\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"219\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"220\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"221\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"222\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"223\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"224\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"225\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"226\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"227\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"228\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"229\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"230\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"231\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"232\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"233\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"234\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"235\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"236\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"237\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"238\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"239\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"240\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"241\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"242\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"243\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"244\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"245\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"246\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"247\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"248\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"249\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"250\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"251\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"252\",\"type\":\"classroom\",\"name\":\"Salle Biblio\",\"id\":\"15\"},{\"index\":\"253\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"254\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"255\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"256\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"257\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"258\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"259\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"260\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"261\",\"type\":\"classroom\",\"name\":\"Salle Info\",\"id\":\"14\"},{\"index\":\"262\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"263\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"264\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"265\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"266\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"267\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"268\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"269\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"270\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"271\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"272\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"273\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"274\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"275\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"276\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"277\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"278\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"279\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"280\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"281\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"282\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"283\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"284\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"285\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"286\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"287\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"288\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"289\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"290\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"291\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"292\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"293\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"294\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"295\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"296\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"297\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"298\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"299\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"300\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"301\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"302\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"303\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"304\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"305\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"306\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"307\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"308\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"309\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"310\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"311\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"312\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"313\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"314\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"315\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"316\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"317\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"318\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"319\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"320\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"321\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"322\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"323\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"324\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"325\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"326\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"327\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"328\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"329\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"330\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"331\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"332\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"333\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"334\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"335\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"336\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"337\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"338\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"339\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"340\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"341\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"342\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"343\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"344\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"345\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"346\",\"type\":\"road\",\"name\":null,\"id\":null},{\"index\":\"347\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"348\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"349\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"350\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"351\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"352\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"353\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"354\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"355\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"356\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"357\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"358\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"359\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"360\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"361\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"362\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"363\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"364\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"365\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"366\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"367\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"368\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"369\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"370\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"371\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"372\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"373\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"374\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"375\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"376\",\"type\":\"entrance\",\"name\":null,\"id\":null},{\"index\":\"377\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"378\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"379\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"380\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"381\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"382\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"383\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"384\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"385\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"386\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"387\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"388\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"389\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"390\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"391\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"392\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"393\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"394\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"395\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"396\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"397\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"398\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"399\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"400\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"401\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"402\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"403\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"404\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"405\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"406\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"407\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"408\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"409\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"410\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"411\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"412\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"413\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"414\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"415\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"416\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"417\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"418\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"419\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"420\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"421\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"422\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"423\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"424\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"425\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"426\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"427\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"428\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"429\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"430\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"431\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"432\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"433\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"434\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"435\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"436\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"437\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"438\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"439\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"440\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"441\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"442\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"443\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"444\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"445\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"446\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"447\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"448\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"449\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"450\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"451\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"452\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"453\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"454\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"455\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"456\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"457\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"458\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"459\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"460\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"461\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"462\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"463\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"464\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"465\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"466\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"467\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"468\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"469\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"470\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"471\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"472\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"473\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"474\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"475\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"476\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"477\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"478\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"479\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"480\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"481\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"482\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"483\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"484\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"485\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"486\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"487\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"488\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"489\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"490\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"491\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"492\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"493\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"494\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"495\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"496\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"497\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"498\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"499\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"500\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"501\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"502\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"503\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"504\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"505\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"506\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"507\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"508\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"509\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"510\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"511\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"512\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"513\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"514\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"515\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"516\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"517\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"518\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"519\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"520\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"521\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"522\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"523\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"524\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"525\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"526\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"527\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"528\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"529\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"530\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"531\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"532\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"533\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"534\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"535\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"536\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"537\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"538\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"539\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"540\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"541\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"542\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"543\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"544\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"545\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"546\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"547\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"548\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"549\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"550\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"551\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"552\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"553\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"554\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"555\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"556\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"557\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"558\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"559\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"560\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"561\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"562\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"563\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"564\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"565\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"566\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"567\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"568\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"569\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"570\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"571\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"572\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"573\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"574\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"575\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"576\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"577\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"578\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"579\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"580\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"581\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"582\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"583\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"584\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"585\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"586\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"587\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"588\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"589\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"590\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"591\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"592\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"593\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"594\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"595\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"596\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"597\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"598\",\"type\":\"empty\",\"name\":null,\"id\":null},{\"index\":\"599\",\"type\":\"empty\",\"name\":null,\"id\":null}]');

-- --------------------------------------------------------

--
-- Table structure for table `CLASSE`
--

CREATE TABLE `CLASSE` (
  `ID_CLASSE` int(11) NOT NULL,
  `NUMERO` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `CLASSE`
--

INSERT INTO `CLASSE` (`ID_CLASSE`, `NUMERO`) VALUES
(1, 'SIIA'),
(2, 'SMI');

-- --------------------------------------------------------

--
-- Table structure for table `COURS`
--

CREATE TABLE `COURS` (
  `ID_COURS` int(11) NOT NULL,
  `NOM_COURS` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `COURS`
--

INSERT INTO `COURS` (`ID_COURS`, `NOM_COURS`) VALUES
(1, 'Oracle'),
(2, 'big data '),
(3, 'machine learning'),
(4, 'DeepLearing'),
(5, 'NoSQL');

-- --------------------------------------------------------

--
-- Table structure for table `EMPLOI_DU_TEMPS`
--

CREATE TABLE `EMPLOI_DU_TEMPS` (
  `ID_EMPLOI` int(11) NOT NULL,
  `ID_PROF` int(11) DEFAULT NULL,
  `ID_COURS` int(11) DEFAULT NULL,
  `ID_SALLE` int(11) DEFAULT NULL,
  `ID_CLASSE` int(11) DEFAULT NULL,
  `JOUR` varchar(20) DEFAULT NULL,
  `HEURE_DEB` time DEFAULT NULL,
  `HEURE_FIN` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `EMPLOI_DU_TEMPS`
--

INSERT INTO `EMPLOI_DU_TEMPS` (`ID_EMPLOI`, `ID_PROF`, `ID_COURS`, `ID_SALLE`, `ID_CLASSE`, `JOUR`, `HEURE_DEB`, `HEURE_FIN`) VALUES
(6, 2, 4, 14, 1, 'Lundi', '08:30:00', '12:30:00'),
(7, 1, 5, 15, 2, 'Mardi', '08:30:00', '12:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `PROF`
--

CREATE TABLE `PROF` (
  `ID_PROF` int(11) NOT NULL,
  `NOM_PROF` varchar(100) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PROF`
--

INSERT INTO `PROF` (`ID_PROF`, `NOM_PROF`, `EMAIL`, `PASSWORD`) VALUES
(1, 'Dr.khourdifi', 'khourdifi@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu'),
(2, 'Dr.Chekraoui', 'chekraoui@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

-- --------------------------------------------------------

--
-- Table structure for table `SALLE`
--

CREATE TABLE `SALLE` (
  `ID_SALLE` int(11) NOT NULL,
  `NOM_SALLE` varchar(100) NOT NULL,
  `CAPACITE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SALLE`
--

INSERT INTO `SALLE` (`ID_SALLE`, `NOM_SALLE`, `CAPACITE`) VALUES
(5, '105', 0),
(6, '107', 0),
(7, '109', 0),
(8, '111', 0),
(10, '104', 0),
(11, '106', 0),
(12, '108', 0),
(13, '110', 0),
(14, 'Salle Info', 0),
(15, 'Salle Biblio', 0),
(16, 'SC01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `SECURITY`
--

CREATE TABLE `SECURITY` (
  `ID_SEC` int(11) NOT NULL,
  `FULL_NAME` varchar(150) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SECURITY`
--

INSERT INTO `SECURITY` (`ID_SEC`, `FULL_NAME`, `EMAIL`, `PASSWORD`) VALUES
(1, 'Guard Michel', 'security@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

-- --------------------------------------------------------

--
-- Table structure for table `STUDENT`
--

CREATE TABLE `STUDENT` (
  `ID_STUDENT` int(11) NOT NULL,
  `FULL_NAME` varchar(150) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `ID_CLASSE` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `STUDENT`
--

INSERT INTO `STUDENT` (`ID_STUDENT`, `FULL_NAME`, `EMAIL`, `PASSWORD`, `ID_CLASSE`) VALUES
(1, 'John Doe', 'student1@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu', 1),
(2, 'Jane Smith', 'student2@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ADMIN`
--
ALTER TABLE `ADMIN`
  ADD PRIMARY KEY (`ID_ADMIN`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- Indexes for table `API_TOKENS`
--
ALTER TABLE `API_TOKENS`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `CARTE_LAYOUT`
--
ALTER TABLE `CARTE_LAYOUT`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `CLASSE`
--
ALTER TABLE `CLASSE`
  ADD PRIMARY KEY (`ID_CLASSE`);

--
-- Indexes for table `COURS`
--
ALTER TABLE `COURS`
  ADD PRIMARY KEY (`ID_COURS`);

--
-- Indexes for table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  ADD PRIMARY KEY (`ID_EMPLOI`),
  ADD KEY `ID_PROF` (`ID_PROF`),
  ADD KEY `ID_COURS` (`ID_COURS`),
  ADD KEY `ID_SALLE` (`ID_SALLE`),
  ADD KEY `ID_CLASSE` (`ID_CLASSE`);

--
-- Indexes for table `PROF`
--
ALTER TABLE `PROF`
  ADD PRIMARY KEY (`ID_PROF`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- Indexes for table `SALLE`
--
ALTER TABLE `SALLE`
  ADD PRIMARY KEY (`ID_SALLE`);

--
-- Indexes for table `SECURITY`
--
ALTER TABLE `SECURITY`
  ADD PRIMARY KEY (`ID_SEC`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- Indexes for table `STUDENT`
--
ALTER TABLE `STUDENT`
  ADD PRIMARY KEY (`ID_STUDENT`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
  ADD KEY `ID_CLASSE` (`ID_CLASSE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ADMIN`
--
ALTER TABLE `ADMIN`
  MODIFY `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `API_TOKENS`
--
ALTER TABLE `API_TOKENS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `CARTE_LAYOUT`
--
ALTER TABLE `CARTE_LAYOUT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `CLASSE`
--
ALTER TABLE `CLASSE`
  MODIFY `ID_CLASSE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `COURS`
--
ALTER TABLE `COURS`
  MODIFY `ID_COURS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  MODIFY `ID_EMPLOI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `PROF`
--
ALTER TABLE `PROF`
  MODIFY `ID_PROF` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `SALLE`
--
ALTER TABLE `SALLE`
  MODIFY `ID_SALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `SECURITY`
--
ALTER TABLE `SECURITY`
  MODIFY `ID_SEC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `STUDENT`
--
ALTER TABLE `STUDENT`
  MODIFY `ID_STUDENT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  ADD CONSTRAINT `emploi_du_temps_ibfk_1` FOREIGN KEY (`ID_PROF`) REFERENCES `PROF` (`ID_PROF`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_2` FOREIGN KEY (`ID_COURS`) REFERENCES `COURS` (`ID_COURS`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_3` FOREIGN KEY (`ID_SALLE`) REFERENCES `SALLE` (`ID_SALLE`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_4` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSE` (`ID_CLASSE`);

--
-- Constraints for table `STUDENT`
--
ALTER TABLE `STUDENT`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSE` (`ID_CLASSE`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
