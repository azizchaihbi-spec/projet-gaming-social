-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 24 nov. 2025 à 10:36
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
-- Base de données : `play_to_help`
--

-- --------------------------------------------------------

--
-- Structure de la table `banned_users`
--

CREATE TABLE `banned_users` (
  `id_ban` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `raison` text DEFAULT NULL,
  `date_ban` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `forum`
--

CREATE TABLE `forum` (
  `id_forum` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `couleur` varchar(7) DEFAULT '#6e6eff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `forum`
--

INSERT INTO `forum` (`id_forum`, `nom`, `description`, `couleur`) VALUES
(1, 'Général', 'Discussions générales sur le gaming', '#6e6eff'),
(2, 'Fortnite', 'Tout sur Fortnite et ses tournois', '#FF69B4'),
(3, 'D&D / Jeux de rôle', 'Donjons, dragons et jeux de rôle', '#39FF14'),
(4, 'Minecraft', 'Constructions, redstone et mods', '#00FF44'),
(5, 'Valorant', 'Stratégies, agents et compétition', '#FF4500'),
(6, 'League of Legends', 'LCK, LEC, Worlds et meta', '#FFD700');

-- --------------------------------------------------------

--
-- Structure de la table `publication`
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
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `publication`
--

INSERT INTO `publication` (`id_publication`, `id_forum`, `id_auteur`, `titre`, `contenu`, `image`, `date_publication`, `supprimee`, `validee`, `likes`) VALUES
(1, 1, 1, 'Bienvenue sur la communauté gaming !', 'Salut à tous les gamers ! Bienvenue sur notre plateforme. Partagez vos astuces, questions et expériences. 🎮', NULL, '2025-11-24 09:30:50', 0, 1, 0),
(2, 2, 2, 'Meta actuel Fortnite Chapitre 5', 'Quelles sont les meilleures armes et stratégies pour le chapitre 5 ? Je cherche à m\'améliorer.', NULL, '2025-11-24 09:30:50', 0, 1, 0),
(3, 3, 3, 'Campagne D&D pour débutants', 'Je veux créer une campagne pour nouveaux joueurs. Des idées de scénarios simples ?', NULL, '2025-11-24 09:30:50', 0, 1, 0),
(4, 4, 1, 'Meilleurs mods Minecraft 2024', 'Quels sont vos mods préférés pour Minecraft cette année ? Je cherche des recommendations.', NULL, '2025-11-24 09:30:50', 0, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `id_reponse` int(11) NOT NULL,
  `id_publication` int(11) NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_reponse` timestamp NOT NULL DEFAULT current_timestamp(),
  `supprimee` tinyint(4) DEFAULT 0,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reponse`
--

INSERT INTO `reponse` (`id_reponse`, `id_publication`, `id_auteur`, `contenu`, `date_reponse`, `supprimee`, `likes`) VALUES
(1, 1, 2, 'Super initiative ! Hâte de partager avec la communauté.', '2025-11-24 09:31:53', 0, 0),
(2, 1, 3, 'Enfin une plateforme dédiée aux gamers français !', '2025-11-24 09:31:53', 0, 0),
(3, 2, 1, 'Le fusil de sniper nouvelle génération est ultra OP en ce moment.', '2025-11-24 09:31:53', 0, 0),
(4, 2, 3, 'N\'oublie pas les grenades fumigènes, très utiles en fin de partie.', '2025-11-24 09:31:53', 0, 0),
(5, 3, 2, 'Commence par une quête de village contre des gobelins, classique mais efficace.', '2025-11-24 09:31:53', 0, 0),
(6, 4, 2, 'Je recommande Create et JourneyMap, indispensables !', '2025-11-24 09:31:53', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `prenom`, `nom`, `email`, `date_inscription`) VALUES
(1, 'Alex', 'Gamer', 'alex@gamer.com', '2025-11-24 09:30:16'),
(2, 'Sarah', 'Streamer', 'sarah@stream.com', '2025-11-24 09:30:16'),
(3, 'Mike', 'ProPlayer', 'mike@pro.com', '2025-11-24 09:30:16'),
(4, 'Admin', 'System', 'admin@system.com', '2025-11-24 09:30:16');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `banned_users`
--
ALTER TABLE `banned_users`
  ADD PRIMARY KEY (`id_ban`);

--
-- Index pour la table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id_forum`);

--
-- Index pour la table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id_publication`),
  ADD KEY `id_forum` (`id_forum`),
  ADD KEY `id_auteur` (`id_auteur`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id_reponse`),
  ADD KEY `id_publication` (`id_publication`),
  ADD KEY `id_auteur` (`id_auteur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `banned_users`
--
ALTER TABLE `banned_users`
  MODIFY `id_ban` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `forum`
--
ALTER TABLE `forum`
  MODIFY `id_forum` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `publication`
--
ALTER TABLE `publication`
  MODIFY `id_publication` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id_reponse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `publication`
--
ALTER TABLE `publication`
  ADD CONSTRAINT `publication_ibfk_1` FOREIGN KEY (`id_forum`) REFERENCES `forum` (`id_forum`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`id_publication`) REFERENCES `publication` (`id_publication`) ON DELETE CASCADE,
  ADD CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
