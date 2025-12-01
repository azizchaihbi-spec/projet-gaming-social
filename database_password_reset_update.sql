-- Script SQL pour ajouter les colonnes de réinitialisation de mot de passe
-- À exécuter dans phpMyAdmin ou via MySQL console

USE playtohelp;

-- Ajouter la colonne reset_token (token de réinitialisation)
ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(64) NULL DEFAULT NULL 
AFTER password;

-- Ajouter la colonne reset_token_expires (date d'expiration du token)
ALTER TABLE users 
ADD COLUMN reset_token_expires DATETIME NULL DEFAULT NULL 
AFTER reset_token;

-- Créer un index sur reset_token pour améliorer les performances de recherche
CREATE INDEX idx_reset_token ON users(reset_token);

-- Vérifier que les colonnes ont été ajoutées
DESCRIBE users;

-- Message de confirmation
SELECT 'Colonnes reset_token et reset_token_expires ajoutées avec succès!' AS Status;
