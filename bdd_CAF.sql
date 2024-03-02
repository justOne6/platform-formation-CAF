-- phpMyAdmin SQL Dump
-- version 4.9.10
-- https://www.phpmyadmin.net/
--
-- Hôte : db5006557029.hosting-data.io
-- Généré le : mer. 09 mars 2022 à 19:24
-- Version du serveur : 5.7.36-log
-- Version de PHP : 7.0.33-0+deb9u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbs5438948`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL,
  `title-category` varchar(52) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id_category`, `title-category`) VALUES
(1, 'prestations familiales'),
(2, 'relation de service'),
(3, 'performance et qualité');

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id_formation` int(11) NOT NULL,
  `category` int(11) DEFAULT NULL,
  `subcategory` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `descript` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `is_sub`
--

CREATE TABLE `is_sub` (
  `id_student` int(11) NOT NULL,
  `id_formation_sub` int(11) NOT NULL,
  `state_sub` varchar(56) NOT NULL,
  `date_inscription` date NOT NULL,
  `id_sub` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `subcategory`
--

CREATE TABLE `subcategory` (
  `id_sub` int(11) NOT NULL,
  `id_category_sub` int(11) NOT NULL,
  `title-subcategory` varchar(56) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `subcategory`
--

INSERT INTO `subcategory` (`id_sub`, `id_category_sub`, `title-subcategory`) VALUES
(1, 1, 'aah'),
(2, 1, 'aeeh'),
(3, 1, 'af / cf / ars'),
(4, 1, 'aides au logement'),
(5, 1, 'ajpa'),
(6, 1, 'ajpp'),
(7, 1, 'asf'),
(8, 1, 'avpf'),
(9, 1, 'adi'),
(10, 1, 'prime d\'activité '),
(11, 1, 'rsa'),
(12, 1, 'partage en résidence alternée'),
(13, 1, 'ressources - cgod');

-- --------------------------------------------------------

--
-- Structure de la table `tools_form`
--

CREATE TABLE `tools_form` (
  `id_tools` int(11) NOT NULL,
  `id_form` int(11) NOT NULL,
  `link` text NOT NULL,
  `t_type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `empreinte` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `profile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `empreinte`, `mail`, `profile`) VALUES
(1, 'admin', '$2y$10$nk4aLK.ggZgEXqKmMeLhi.SDlOhfLoPLHx2ygMqET/19i.k3ZzGsu', '', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id_formation`),
  ADD KEY `fk-category-form` (`category`),
  ADD KEY `fk-subcategory-form` (`subcategory`);

--
-- Index pour la table `is_sub`
--
ALTER TABLE `is_sub`
  ADD PRIMARY KEY (`id_sub`),
  ADD KEY `fk_users_sub` (`id_student`),
  ADD KEY `fk_form_sub` (`id_formation_sub`);

--
-- Index pour la table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id_sub`),
  ADD KEY `fk-category-sub` (`id_category_sub`);

--
-- Index pour la table `tools_form`
--
ALTER TABLE `tools_form`
  ADD PRIMARY KEY (`id_tools`),
  ADD KEY `fk-tools-form` (`id_form`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id_formation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `is_sub`
--
ALTER TABLE `is_sub`
  MODIFY `id_sub` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id_sub` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `tools_form`
--
ALTER TABLE `tools_form`
  MODIFY `id_tools` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `formations`
--
ALTER TABLE `formations`
  ADD CONSTRAINT `fk-category-form` FOREIGN KEY (`category`) REFERENCES `category` (`id_category`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-subcategory-form` FOREIGN KEY (`subcategory`) REFERENCES `subcategory` (`id_sub`);

--
-- Contraintes pour la table `is_sub`
--
ALTER TABLE `is_sub`
  ADD CONSTRAINT `fk_form_sub` FOREIGN KEY (`id_formation_sub`) REFERENCES `formations` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_sub` FOREIGN KEY (`id_student`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `fk-category-sub` FOREIGN KEY (`id_category_sub`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tools_form`
--
ALTER TABLE `tools_form`
  ADD CONSTRAINT `fk-tools-form` FOREIGN KEY (`id_form`) REFERENCES `formations` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
