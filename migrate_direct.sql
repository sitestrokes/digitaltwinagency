USE digitaltwinagency;

-- ============================================================
-- Table 1: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(191) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `status` ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 2: user_settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NOT NULL,
  `openai_api_key` TEXT NULL,
  `openai_model` VARCHAR(100) NOT NULL DEFAULT 'gpt-4.1-nano',
  `agency_name` VARCHAR(255) NULL,
  `agency_email` VARCHAR(255) NULL,
  `agency_phone` VARCHAR(50) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_settings_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 3: prospects
-- ============================================================
CREATE TABLE IF NOT EXISTS `prospects` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `niche` VARCHAR(100) NOT NULL,
  `website_status` VARCHAR(50) NULL,
  `video_status` VARCHAR(50) NULL,
  `social_status` VARCHAR(50) NULL,
  `budget` VARCHAR(50) NULL,
  `competitors` VARCHAR(50) NULL,
  `score` TINYINT UNSIGNED NOT NULL,
  `readiness_level` ENUM('hot','warm','cold') NOT NULL,
  `pain_points` JSON NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_score` (`user_id`,`score`),
  CONSTRAINT `prospects_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 4: packages
-- ============================================================
CREATE TABLE IF NOT EXISTS `packages` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `selected_services` JSON NULL,
  `starter_price` INT UNSIGNED NOT NULL,
  `growth_price` INT UNSIGNED NOT NULL,
  `premium_price` INT UNSIGNED NOT NULL,
  `starter_services` JSON NULL,
  `growth_services` JSON NULL,
  `premium_services` JSON NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `packages_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 5: proposals
-- ============================================================
CREATE TABLE IF NOT EXISTS `proposals` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NOT NULL,
  `prospect_id` BIGINT(20) UNSIGNED NULL,
  `package_id` BIGINT(20) UNSIGNED NULL,
  `agency_name` VARCHAR(255) NOT NULL,
  `client_name` VARCHAR(255) NOT NULL,
  `contact_name` VARCHAR(255) NULL,
  `niche` VARCHAR(100) NULL,
  `tier` ENUM('starter','growth','premium','custom') NOT NULL DEFAULT 'growth',
  `price` INT UNSIGNED NOT NULL,
  `services` JSON NULL,
  `content` LONGTEXT NULL,
  `generation_mode` ENUM('ai','template') NOT NULL DEFAULT 'template',
  `notes` TEXT NULL,
  `valid_until` DATE NULL,
  `status` ENUM('draft','sent','accepted','rejected') NOT NULL DEFAULT 'draft',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_created_at` (`user_id`,`created_at`),
  CONSTRAINT `proposals_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table 6: audit_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NULL,
  `entity_id` BIGINT(20) UNSIGNED NULL,
  `meta` JSON NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(500) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CI4 migrations tracking table (marks all migrations as run)
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `group` VARCHAR(255) NOT NULL,
  `namespace` VARCHAR(255) NOT NULL,
  `time` INT NOT NULL,
  `batch` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES
('2026-02-23-000001','App\\Database\\Migrations\\CreateUsersTable','default','App',UNIX_TIMESTAMP(),1),
('2026-02-23-000002','App\\Database\\Migrations\\CreateUserSettingsTable','default','App',UNIX_TIMESTAMP(),1),
('2026-02-23-000003','App\\Database\\Migrations\\CreateProspectsTable','default','App',UNIX_TIMESTAMP(),1),
('2026-02-23-000004','App\\Database\\Migrations\\CreatePackagesTable','default','App',UNIX_TIMESTAMP(),1),
('2026-02-23-000005','App\\Database\\Migrations\\CreateProposalsTable','default','App',UNIX_TIMESTAMP(),1),
('2026-02-23-000006','App\\Database\\Migrations\\CreateAuditLogsTable','default','App',UNIX_TIMESTAMP(),1);

SELECT 'All tables created successfully!' AS status;
