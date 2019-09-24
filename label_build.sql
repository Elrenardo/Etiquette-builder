-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  lun. 21 jan. 2019 à 15:09
-- Version du serveur :  10.1.32-MariaDB
-- Version de PHP :  7.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `erpa_label`
--

-- --------------------------------------------------------

--
-- Structure de la table `label`
--

CREATE TABLE `label` (
  `uid` varchar(36) NOT NULL,
  `name` text NOT NULL,
  `recto` text,
  `verso` text,
  `horizontal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Hori, 0:Vert',
  `margin_x` int(11) NOT NULL DEFAULT '15',
  `margin_y` int(11) NOT NULL DEFAULT '15',
  `nb_x` int(11) NOT NULL DEFAULT '2',
  `spacing_x` int(11) NOT NULL DEFAULT '0',
  `spacing_y` int(11) NOT NULL DEFAULT '0',
  `nb_y` int(11) NOT NULL DEFAULT '2',
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
