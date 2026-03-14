-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 08 mars 2026 à 22:33
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chronos_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `ADMIN`
--

CREATE TABLE `ADMIN` (
  `ID_ADMIN` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `CARTE_LAYOUT`
--

CREATE TABLE `CARTE_LAYOUT` (
  `id` int(11) NOT NULL,
  `grid_data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CARTE_LAYOUT`
--

INSERT INTO `CARTE_LAYOUT` (`id`, `grid_data`) VALUES
(1, '{}');

-- --------------------------------------------------------

--
-- Déchargement des données de la table `ADMIN`
--

INSERT INTO `ADMIN` (`ID_ADMIN`, `USERNAME`, `PASSWORD`) VALUES
(1, 'admin', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

-- --------------------------------------------------------

--
-- Structure de la table `CLASSE`
--

CREATE TABLE `CLASSE` (
  `ID_CLASSE` int(11) NOT NULL,
  `NUMERO` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CLASSE`
--

INSERT INTO `CLASSE` (`ID_CLASSE`, `NUMERO`) VALUES
(1, 'SIIA'),
(2, 'SMI');

-- --------------------------------------------------------

--
-- Structure de la table `COURS`
--

CREATE TABLE `COURS` (
  `ID_COURS` int(11) NOT NULL,
  `NOM_COURS` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `COURS`
--

INSERT INTO `COURS` (`ID_COURS`, `NOM_COURS`) VALUES
(1, 'Oracle'),
(2, 'big data '),
(3, 'machine learning');

-- --------------------------------------------------------

--
-- Structure de la table `EMPLOI_DU_TEMPS`
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
-- Déchargement des données de la table `EMPLOI_DU_TEMPS`
--

INSERT INTO `EMPLOI_DU_TEMPS` (`ID_EMPLOI`, `ID_PROF`, `ID_COURS`, `ID_SALLE`, `ID_CLASSE`, `JOUR`, `HEURE_DEB`, `HEURE_FIN`) VALUES
(3, 1, 2, 1, 1, 'Mardi', '08:30:00', '12:30:00'),
(4, 1, 1, 1, 1, 'Jeudi', '14:30:00', '18:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `PROF`
--

CREATE TABLE `PROF` (
  `ID_PROF` int(11) NOT NULL,
  `NOM_PROF` varchar(100) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PROF`
--

INSERT INTO `PROF` (`ID_PROF`, `NOM_PROF`, `EMAIL`, `PASSWORD`) VALUES
(1, 'Dr.khourdifi', 'khourdifi@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu'),
(2, 'Dr.Chekraoui', 'chekraoui@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

-- --------------------------------------------------------

--
-- Structure de la table `SALLE`
--

CREATE TABLE `SALLE` (
  `ID_SALLE` int(11) NOT NULL,
  `NOM_SALLE` varchar(100) NOT NULL,
  `CAPACITE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SALLE`
--

INSERT INTO `SALLE` (`ID_SALLE`, `NOM_SALLE`, `CAPACITE`) VALUES
(1, '1', 0),
(2, '12', 0);

-- --------------------------------------------------------

--
-- Structure de la table `STUDENT`
--

CREATE TABLE `STUDENT` (
  `ID_STUDENT` int(11) NOT NULL,
  `FULL_NAME` varchar(150) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `ID_CLASSE` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `STUDENT`
--

INSERT INTO `STUDENT` (`ID_STUDENT`, `FULL_NAME`, `EMAIL`, `PASSWORD`, `ID_CLASSE`) VALUES
(1, 'John Doe', 'student1@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu', 1),
(2, 'Jane Smith', 'student2@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu', 2);

-- --------------------------------------------------------

--
-- Structure de la table `SECURITY`
--

CREATE TABLE `SECURITY` (
  `ID_SEC` int(11) NOT NULL,
  `FULL_NAME` varchar(150) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SECURITY`
--

INSERT INTO `SECURITY` (`ID_SEC`, `FULL_NAME`, `EMAIL`, `PASSWORD`) VALUES
(1, 'Guard Michel', 'security@chronos.edu', '$2y$10$ZzaFX6VcU3oBWxGLvl0g2u9qu76gIJ5wbqohSaTZZsfBZnlAJ6UKu');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ADMIN`
--
ALTER TABLE `ADMIN`
  ADD PRIMARY KEY (`ID_ADMIN`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- Index pour la table `CARTE_LAYOUT`
--
ALTER TABLE `CARTE_LAYOUT`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `CLASSE`
--
ALTER TABLE `CLASSE`
  ADD PRIMARY KEY (`ID_CLASSE`);

--
-- Index pour la table `COURS`
--
ALTER TABLE `COURS`
  ADD PRIMARY KEY (`ID_COURS`);

--
-- Index pour la table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  ADD PRIMARY KEY (`ID_EMPLOI`),
  ADD KEY `ID_PROF` (`ID_PROF`),
  ADD KEY `ID_COURS` (`ID_COURS`),
  ADD KEY `ID_SALLE` (`ID_SALLE`),
  ADD KEY `ID_CLASSE` (`ID_CLASSE`);

--
-- Index pour la table `PROF`
--
ALTER TABLE `PROF`
  ADD PRIMARY KEY (`ID_PROF`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- Index pour la table `SALLE`
--
ALTER TABLE `SALLE`
  ADD PRIMARY KEY (`ID_SALLE`);

--
-- Index pour la table `STUDENT`
--
ALTER TABLE `STUDENT`
  ADD PRIMARY KEY (`ID_STUDENT`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
  ADD KEY `ID_CLASSE` (`ID_CLASSE`);

--
-- Index pour la table `SECURITY`
--
ALTER TABLE `SECURITY`
  ADD PRIMARY KEY (`ID_SEC`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ADMIN`
--
ALTER TABLE `ADMIN`
  MODIFY `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `CARTE_LAYOUT`
--
ALTER TABLE `CARTE_LAYOUT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `CLASSE`
--
ALTER TABLE `CLASSE`
  MODIFY `ID_CLASSE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `COURS`
--
ALTER TABLE `COURS`
  MODIFY `ID_COURS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  MODIFY `ID_EMPLOI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `PROF`
--
ALTER TABLE `PROF`
  MODIFY `ID_PROF` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `SALLE`
--
ALTER TABLE `SALLE`
  MODIFY `ID_SALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `STUDENT`
--
ALTER TABLE `STUDENT`
  MODIFY `ID_STUDENT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `SECURITY`
--
ALTER TABLE `SECURITY`
  MODIFY `ID_SEC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `EMPLOI_DU_TEMPS`
--
ALTER TABLE `EMPLOI_DU_TEMPS`
  ADD CONSTRAINT `emploi_du_temps_ibfk_1` FOREIGN KEY (`ID_PROF`) REFERENCES `PROF` (`ID_PROF`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_2` FOREIGN KEY (`ID_COURS`) REFERENCES `COURS` (`ID_COURS`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_3` FOREIGN KEY (`ID_SALLE`) REFERENCES `SALLE` (`ID_SALLE`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_4` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSE` (`ID_CLASSE`);

--
-- Contraintes pour la table `STUDENT`
--
ALTER TABLE `STUDENT`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSE` (`ID_CLASSE`);
--

--
-- API Tokens table for CHRONOS Mobile App authentication
-- Run this SQL to add mobile API support to the existing database
--

--
-- Structure de la table `API_TOKENS`
--
CREATE TABLE IF NOT EXISTS `API_TOKENS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','professor','security') NOT NULL DEFAULT 'student',
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 24 hour),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT pour la table `API_TOKENS`
--
ALTER TABLE `API_TOKENS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Note: Run this in phpMyAdmin or MySQL console:
-- USE chronos_db;
-- SOURCE api_tokens_table.sql;
--
