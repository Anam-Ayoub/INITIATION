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
-- Base de données : `timetable_system`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Structure de la table `classe`
--

CREATE TABLE `classe` (
  `ID_CLASSE` int(11) NOT NULL,
  `NUMERO` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classe`
--

INSERT INTO `classe` (`ID_CLASSE`, `NUMERO`) VALUES
(1, 'SIIA'),
(2, 'SMI');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `ID_COURS` int(11) NOT NULL,
  `NOM_COURS` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`ID_COURS`, `NOM_COURS`) VALUES
(1, 'Oracle'),
(2, 'big data '),
(3, 'machine learning');

-- --------------------------------------------------------

--
-- Structure de la table `emploi_du_temps`
--

CREATE TABLE `emploi_du_temps` (
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
-- Déchargement des données de la table `emploi_du_temps`
--

INSERT INTO `emploi_du_temps` (`ID_EMPLOI`, `ID_PROF`, `ID_COURS`, `ID_SALLE`, `ID_CLASSE`, `JOUR`, `HEURE_DEB`, `HEURE_FIN`) VALUES
(3, 1, 2, 1, 1, 'Mardi', '08:30:00', '12:30:00'),
(4, 1, 1, 1, 1, 'Jeudi', '14:30:00', '18:30:00');

--
-- Déclencheurs `emploi_du_temps`
--
DELIMITER $$
CREATE TRIGGER `check_timetable_conflict` BEFORE INSERT ON `emploi_du_temps` FOR EACH ROW BEGIN
    DECLARE conflict_count INT;

    -- Check if Prof or Salle or Classe is busy at that time
    SELECT COUNT(*) INTO conflict_count FROM EMPLOI_DU_TEMPS
    WHERE JOUR = NEW.JOUR 
    AND (
        ID_PROF = NEW.ID_PROF OR 
        ID_SALLE = NEW.ID_SALLE OR 
        ID_CLASSE = NEW.ID_CLASSE
    )
    AND (NEW.HEURE_DEB < HEURE_FIN AND NEW.HEURE_FIN > HEURE_DEB);

    IF conflict_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur: Conflit détecté (Prof/Salle/Classe déjà occupé)!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `prof`
--

CREATE TABLE `prof` (
  `ID_PROF` int(11) NOT NULL,
  `NOM_PROF` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prof`
--

INSERT INTO `prof` (`ID_PROF`, `NOM_PROF`) VALUES
(1, 'Dr.khourdifi'),
(2, 'Dr.Chekraoui');

-- --------------------------------------------------------

--
-- Structure de la table `salle`
--

CREATE TABLE `salle` (
  `ID_SALLE` int(11) NOT NULL,
  `NOM_SALLE` varchar(100) NOT NULL,
  `CAPACITE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `salle`
--

INSERT INTO `salle` (`ID_SALLE`, `NOM_SALLE`, `CAPACITE`) VALUES
(1, '1', 0),
(2, '12', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`ID_CLASSE`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`ID_COURS`);

--
-- Index pour la table `emploi_du_temps`
--
ALTER TABLE `emploi_du_temps`
  ADD PRIMARY KEY (`ID_EMPLOI`),
  ADD KEY `ID_PROF` (`ID_PROF`),
  ADD KEY `ID_COURS` (`ID_COURS`),
  ADD KEY `ID_SALLE` (`ID_SALLE`),
  ADD KEY `ID_CLASSE` (`ID_CLASSE`);

--
-- Index pour la table `prof`
--
ALTER TABLE `prof`
  ADD PRIMARY KEY (`ID_PROF`);

--
-- Index pour la table `salle`
--
ALTER TABLE `salle`
  ADD PRIMARY KEY (`ID_SALLE`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `classe`
--
ALTER TABLE `classe`
  MODIFY `ID_CLASSE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `ID_COURS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `emploi_du_temps`
--
ALTER TABLE `emploi_du_temps`
  MODIFY `ID_EMPLOI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `prof`
--
ALTER TABLE `prof`
  MODIFY `ID_PROF` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `salle`
--
ALTER TABLE `salle`
  MODIFY `ID_SALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `emploi_du_temps`
--
ALTER TABLE `emploi_du_temps`
  ADD CONSTRAINT `emploi_du_temps_ibfk_1` FOREIGN KEY (`ID_PROF`) REFERENCES `prof` (`ID_PROF`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_2` FOREIGN KEY (`ID_COURS`) REFERENCES `cours` (`ID_COURS`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_3` FOREIGN KEY (`ID_SALLE`) REFERENCES `salle` (`ID_SALLE`),
  ADD CONSTRAINT `emploi_du_temps_ibfk_4` FOREIGN KEY (`ID_CLASSE`) REFERENCES `classe` (`ID_CLASSE`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
