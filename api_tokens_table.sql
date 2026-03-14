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
