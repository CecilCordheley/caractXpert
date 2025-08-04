-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 04 août 2025 à 12:34
-- Version du serveur : 5.7.40
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `expertsys`
--

-- --------------------------------------------------------

--
-- Structure de la table `caracteristiques`
--

DROP TABLE IF EXISTS `caracteristiques`;
CREATE TABLE IF NOT EXISTS `caracteristiques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_caracteristiques_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `caracteristiques`
--

INSERT INTO `caracteristiques` (`id`, `label`, `client_id`) VALUES
(1, 'voyant allumé', 2),
(2, 'pas d\'affichage écran', 2),
(3, 'fait un bip continu', 2),
(4, 'provoque des courts-circuits', 2),
(5, 'affichage error 1', 2),
(6, 'tourne en sous régime', 2),
(7, 'voyant éteint', 2),
(8, 'affichage error 6', 2),
(9, 'fait un bip discontinu', 2);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `idcategorie` int(11) NOT NULL AUTO_INCREMENT,
  `LibCategorie` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcategorie`),
  KEY `fk_categorie_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='			';

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`idcategorie`, `LibCategorie`, `client_id`) VALUES
(1, 'categorie_1', 2),
(2, 'categorie_2', 2);

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_uuid` char(36) COLLATE utf8mb4_bin NOT NULL,
  `client_name` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `client_info` json DEFAULT NULL,
  `date_client` date DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_uuid_UNIQUE` (`client_uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`client_id`, `client_uuid`, `client_name`, `client_info`, `date_client`) VALUES
(2, '6889c2aeee8ff', 'MyCompany_test', '{\"CP\": \"\", \"tel\": \"\", \"ville\": \"\", \"adresse\": \"\"}', NULL),
(3, '6889cc2d682f4', 'client_2', '{\"CP\": \"\", \"tel\": \"\", \"ville\": \"\", \"adresse\": \"\"}', '2025-07-30');

-- --------------------------------------------------------

--
-- Structure de la table `pannes`
--

DROP TABLE IF EXISTS `pannes`;
CREATE TABLE IF NOT EXISTS `pannes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `diagnostique` longtext COLLATE utf8mb4_bin NOT NULL,
  `categorie` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`categorie`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_pannes_categorie1_idx` (`categorie`),
  KEY `fk_pannes_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `pannes`
--

INSERT INTO `pannes` (`id`, `code`, `diagnostique`, `categorie`, `client_id`) VALUES
(1, 'p1', 'new Diag1', 1, 2),
(2, 'p2', 'diagnostique 2', 1, 2),
(3, 'p3', 'diagnostique 3', 2, 2),
(4, 'panne_4', 'Il s\'agit d\'un problème de la valve de soupape', 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `panne_caracteristique`
--

DROP TABLE IF EXISTS `panne_caracteristique`;
CREATE TABLE IF NOT EXISTS `panne_caracteristique` (
  `panne_id` int(11) NOT NULL,
  `caracteristique_id` int(11) NOT NULL,
  PRIMARY KEY (`panne_id`,`caracteristique_id`),
  KEY `caracteristique_id` (`caracteristique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `panne_caracteristique`
--

INSERT INTO `panne_caracteristique` (`panne_id`, `caracteristique_id`) VALUES
(2, 1),
(4, 2),
(1, 4),
(3, 4),
(1, 5),
(2, 5),
(2, 6),
(3, 6),
(4, 6),
(1, 7),
(3, 7),
(4, 7),
(3, 8),
(1, 9),
(3, 9);

-- --------------------------------------------------------

--
-- Structure de la table `panne_event`
--

DROP TABLE IF EXISTS `panne_event`;
CREATE TABLE IF NOT EXISTS `panne_event` (
  `idEvent` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `event_callBack` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `refEvent` char(5) COLLATE utf8mb4_bin NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idEvent`),
  KEY `fk_panne_event_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `panne_event`
--

INSERT INTO `panne_event` (`idEvent`, `event_name`, `event_callBack`, `refEvent`, `client_id`) VALUES
(1, 'Event 1', 'alert(panne.code);', 'EVT1', 2);

-- --------------------------------------------------------

--
-- Structure de la table `panne_has_event`
--

DROP TABLE IF EXISTS `panne_has_event`;
CREATE TABLE IF NOT EXISTS `panne_has_event` (
  `idEvent` int(11) NOT NULL,
  `pannes_id` int(11) NOT NULL,
  PRIMARY KEY (`idEvent`,`pannes_id`),
  KEY `fk_panne_event_has_pannes_pannes1_idx` (`pannes_id`),
  KEY `fk_panne_event_has_pannes_panne_event1_idx` (`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `panne_has_event`
--

INSERT INTO `panne_has_event` (`idEvent`, `pannes_id`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idusers` int(11) NOT NULL AUTO_INCREMENT,
  `uuidUser` char(36) COLLATE utf8mb4_bin NOT NULL,
  `nomUser` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `prenomUser` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `mailUser` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `password_hash` char(64) COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `roleUser` enum('agent','manager','admin','dev') COLLATE utf8mb4_bin NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `client` int(11) NOT NULL,
  PRIMARY KEY (`idusers`),
  UNIQUE KEY `uuidUser_UNIQUE` (`uuidUser`),
  KEY `fk_users_users1_idx` (`manager_id`),
  KEY `fk_users_client1_idx` (`client`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`idusers`, `uuidUser`, `nomUser`, `prenomUser`, `mailUser`, `password_hash`, `created_at`, `roleUser`, `manager_id`, `client`) VALUES
(1, '684a997e1bee6', 'Paul', 'Alain', 'palain@mail.com', 'cbd3d648aec1d615f934739802cb6808925136dd795f00a775844d95cf4387d9', '2025-06-12 09:10:22', 'admin', 0, 2),
(4, '6851642ec429e', 'test', 'pop', 'tpop@mail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-06-17 12:48:46', 'manager', 0, 2),
(5, '685a5f1b6e793', 'agent', 'test', 'atest@mail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-06-24 08:17:31', 'agent', 4, 2),
(6, '685d11529a51e', 'manager2', 'test', 'mantest2@mail.com', NULL, '2025-06-26 09:22:26', 'manager', NULL, 2),
(7, '685fe9327095d', 'developer', 'test', 'tdev@mail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-06-28 13:08:02', 'dev', NULL, 2),
(8, '68833cfdf2763', 'Agent', 'test2', 'atest2@mail.com', NULL, '2025-07-25 08:14:53', 'agent', 4, 2),
(10, '6889cc73d2391', 'Paul', 'Attreide', 'pattreide@mail.com', 'fe4c94717fd56814604954255396a0a43a0c08a28c662c69a03aeccee9024b22', '2025-07-30 07:40:35', 'admin', NULL, 3);

-- --------------------------------------------------------

--
-- Structure de la table `users_found_pannes`
--

DROP TABLE IF EXISTS `users_found_pannes`;
CREATE TABLE IF NOT EXISTS `users_found_pannes` (
  `user` int(11) NOT NULL,
  `panne` int(11) NOT NULL,
  `date_found` datetime NOT NULL,
  `comment_pannes` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`user`,`panne`,`date_found`),
  KEY `fk_users_has_pannes_pannes1_idx` (`panne`),
  KEY `fk_users_has_pannes_users1_idx` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `users_found_pannes`
--

INSERT INTO `users_found_pannes` (`user`, `panne`, `date_found`, `comment_pannes`) VALUES
(5, 1, '2025-06-24 11:04:14', NULL),
(5, 1, '2025-07-11 08:51:51', NULL),
(5, 1, '2025-07-11 11:08:04', NULL),
(5, 1, '2025-07-11 11:08:25', NULL),
(5, 1, '2025-07-11 11:21:56', NULL),
(5, 1, '2025-07-11 11:22:53', NULL),
(5, 1, '2025-07-28 09:39:13', NULL),
(5, 1, '2025-07-29 08:01:41', NULL),
(5, 1, '2025-07-29 08:03:47', NULL),
(5, 1, '2025-07-29 08:13:00', NULL),
(5, 2, '2025-07-28 09:37:49', NULL),
(5, 2, '2025-07-29 08:01:16', NULL),
(5, 2, '2025-07-29 08:10:50', NULL),
(5, 3, '2025-06-24 11:45:37', NULL),
(5, 3, '2025-07-25 08:09:10', NULL),
(5, 3, '2025-07-28 09:39:37', NULL),
(5, 3, '2025-07-29 08:06:57', NULL),
(5, 3, '2025-07-29 08:08:27', NULL),
(5, 3, '2025-07-29 08:15:03', NULL),
(5, 3, '2025-07-29 08:20:59', NULL),
(5, 4, '2025-07-11 08:50:12', NULL),
(5, 4, '2025-07-11 11:08:08', NULL),
(5, 4, '2025-07-11 11:08:59', NULL),
(5, 4, '2025-07-11 11:10:29', NULL),
(5, 4, '2025-07-11 11:21:47', NULL),
(5, 4, '2025-07-11 11:23:01', NULL),
(5, 4, '2025-07-25 08:34:38', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `caracteristiques`
--
ALTER TABLE `caracteristiques`
  ADD CONSTRAINT `fk_caracteristiques_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD CONSTRAINT `fk_categorie_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pannes`
--
ALTER TABLE `pannes`
  ADD CONSTRAINT `fk_pannes_categorie1` FOREIGN KEY (`categorie`) REFERENCES `categorie` (`idcategorie`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pannes_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `panne_caracteristique`
--
ALTER TABLE `panne_caracteristique`
  ADD CONSTRAINT `fk_panne_caracteristique_caracteristiques1` FOREIGN KEY (`caracteristique_id`) REFERENCES `caracteristiques` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_panne_caracteristique_pannes` FOREIGN KEY (`panne_id`) REFERENCES `pannes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `panne_event`
--
ALTER TABLE `panne_event`
  ADD CONSTRAINT `fk_panne_event_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `panne_has_event`
--
ALTER TABLE `panne_has_event`
  ADD CONSTRAINT `fk_panne_event_has_pannes_panne_event1` FOREIGN KEY (`idEvent`) REFERENCES `panne_event` (`idEvent`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_panne_event_has_pannes_pannes1` FOREIGN KEY (`pannes_id`) REFERENCES `pannes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_client1` FOREIGN KEY (`client`) REFERENCES `client` (`client_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_users1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `users_found_pannes`
--
ALTER TABLE `users_found_pannes`
  ADD CONSTRAINT `fk_users_has_pannes_pannes1` FOREIGN KEY (`panne`) REFERENCES `pannes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_pannes_users1` FOREIGN KEY (`user`) REFERENCES `users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
