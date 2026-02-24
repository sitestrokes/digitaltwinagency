-- ============================================================
-- TwinProfit HQ — Complete Database Setup
-- ============================================================
-- Run this file against a MySQL 5.7+ / 8.0 server to create
-- the database and all required tables from scratch.
--
-- Usage (command line):
--   mysql -u root -p < migrate_direct.sql
--
-- Usage (phpMyAdmin):
--   Import this file — ensure "Run these queries for the whole
--   file" is selected (not per-statement splitting).
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `digitaltwinagency`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `digitaltwinagency`;

-- ============================================================
-- Table 1: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`            BIGINT(20) UNSIGNED  NOT NULL AUTO_INCREMENT,
  `email`         VARCHAR(191)         NOT NULL,
  `password_hash` VARCHAR(255)         NOT NULL,
  `name`          VARCHAR(255)         NOT NULL,
  `role`          ENUM('user','admin') NOT NULL DEFAULT 'user',
  `status`        ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login_at` DATETIME             NULL,
  `created_at`    DATETIME             NULL,
  `updated_at`    DATETIME             NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 2: user_settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id`             BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT(20) UNSIGNED NOT NULL,
  `openai_api_key` TEXT                NULL,
  `openai_model`   VARCHAR(100)        NOT NULL DEFAULT 'gpt-4.1-nano',
  `agency_name`    VARCHAR(255)        NULL,
  `agency_email`   VARCHAR(255)        NULL,
  `agency_phone`   VARCHAR(50)         NULL,
  `created_at`     DATETIME            NULL,
  `updated_at`     DATETIME            NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_settings_user_id` (`user_id`),
  CONSTRAINT `fk_user_settings_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 3: prospects
-- ============================================================
CREATE TABLE IF NOT EXISTS `prospects` (
  `id`              BIGINT(20) UNSIGNED                 NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT(20) UNSIGNED                 NOT NULL,
  `name`            VARCHAR(255)                        NOT NULL,
  `website_url`     VARCHAR(512)                        NULL,
  `niche`           VARCHAR(100)                        NOT NULL,
  `website_status`  VARCHAR(50)                         NULL,
  `video_status`    VARCHAR(50)                         NULL,
  `social_status`   VARCHAR(50)                         NULL,
  `budget`          VARCHAR(50)                         NULL,
  `competitors`     VARCHAR(50)                         NULL,
  `score`           TINYINT UNSIGNED                    NOT NULL,
  `readiness_level` ENUM('hot','warm','cold')           NOT NULL,
  `pain_points`     LONGTEXT                            NULL  COMMENT 'JSON array',
  `notes`           TEXT                                NULL,
  `created_at`      DATETIME                            NULL,
  `updated_at`      DATETIME                            NULL,
  PRIMARY KEY (`id`),
  KEY `idx_prospects_user_id` (`user_id`),
  KEY `idx_prospects_user_score` (`user_id`, `score`),
  CONSTRAINT `fk_prospects_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 4: packages
-- ============================================================
CREATE TABLE IF NOT EXISTS `packages` (
  `id`               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`          BIGINT(20) UNSIGNED NOT NULL,
  `name`             VARCHAR(255)        NOT NULL,
  `selected_services` LONGTEXT           NULL  COMMENT 'JSON array',
  `starter_price`    INT UNSIGNED        NOT NULL,
  `growth_price`     INT UNSIGNED        NOT NULL,
  `premium_price`    INT UNSIGNED        NOT NULL,
  `starter_services` LONGTEXT            NULL  COMMENT 'JSON array',
  `growth_services`  LONGTEXT            NULL  COMMENT 'JSON array',
  `premium_services` LONGTEXT            NULL  COMMENT 'JSON array',
  `created_at`       DATETIME            NULL,
  `updated_at`       DATETIME            NULL,
  PRIMARY KEY (`id`),
  KEY `idx_packages_user_id` (`user_id`),
  CONSTRAINT `fk_packages_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 5: proposals
-- ============================================================
CREATE TABLE IF NOT EXISTS `proposals` (
  `id`              BIGINT(20) UNSIGNED                         NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT(20) UNSIGNED                         NOT NULL,
  `prospect_id`     BIGINT(20) UNSIGNED                         NULL,
  `package_id`      BIGINT(20) UNSIGNED                         NULL,
  `agency_name`     VARCHAR(255)                                NOT NULL,
  `client_name`     VARCHAR(255)                                NOT NULL,
  `contact_name`    VARCHAR(255)                                NULL,
  `niche`           VARCHAR(100)                                NULL,
  `tier`            ENUM('starter','growth','premium','custom') NOT NULL DEFAULT 'growth',
  `price`           INT UNSIGNED                                NOT NULL,
  `services`        LONGTEXT                                    NULL  COMMENT 'JSON array',
  `content`         LONGTEXT                                    NULL,
  `generation_mode` ENUM('ai','template')                       NOT NULL DEFAULT 'template',
  `notes`           TEXT                                        NULL,
  `valid_until`     DATE                                        NULL,
  `status`          ENUM('draft','sent','accepted','rejected')  NOT NULL DEFAULT 'draft',
  `created_at`      DATETIME                                    NULL,
  `updated_at`      DATETIME                                    NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proposals_user_id` (`user_id`),
  KEY `idx_proposals_user_created` (`user_id`, `created_at`),
  CONSTRAINT `fk_proposals_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 6: audit_logs
-- (insert-only — no updated_at column)
-- ============================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT(20) UNSIGNED NULL,
  `action`      VARCHAR(100)        NOT NULL,
  `entity_type` VARCHAR(50)         NULL,
  `entity_id`   BIGINT(20) UNSIGNED NULL,
  `meta`        LONGTEXT            NULL  COMMENT 'JSON object',
  `ip_address`  VARCHAR(45)         NULL,
  `user_agent`  VARCHAR(500)        NULL,
  `created_at`  DATETIME            NULL,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user_id` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CI4 migrations tracking table
-- Records all 6 migrations as already executed so that
-- "php spark migrate" does not re-run them on a fresh server
-- that used this SQL file instead.
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version`   VARCHAR(255)        NOT NULL,
  `class`     VARCHAR(255)        NOT NULL,
  `group`     VARCHAR(255)        NOT NULL,
  `namespace` VARCHAR(255)        NOT NULL,
  `time`      INT                 NOT NULL,
  `batch`     INT UNSIGNED        NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
  ('2026-02-23-000001', 'App\\Database\\Migrations\\CreateUsersTable',        'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-23-000002', 'App\\Database\\Migrations\\CreateUserSettingsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-23-000003', 'App\\Database\\Migrations\\CreateProspectsTable',    'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-23-000004', 'App\\Database\\Migrations\\CreatePackagesTable',     'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-23-000005', 'App\\Database\\Migrations\\CreateProposalsTable',    'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-23-000006', 'App\\Database\\Migrations\\CreateAuditLogsTable',    'default', 'App', UNIX_TIMESTAMP(), 1),
  ('2026-02-24-000007', 'App\\Database\\Migrations\\AddWebsiteUrlToProspects','default', 'App', UNIX_TIMESTAMP(), 2);

SELECT 'TwinProfit HQ — all tables created successfully!' AS status;
