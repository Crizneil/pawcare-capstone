SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00"; -- Philippine Standard Time

-- --------------------------------------------------------
-- 1. USERS TABLE (Security & Verification)
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `role` enum('admin', 'owner') NOT NULL DEFAULT 'owner',
  `id_type` varchar(255) DEFAULT NULL,    -- e.g., Barangay ID, Voter's ID
  `id_number` varchar(255) DEFAULT NULL,  -- Unique ID Number for verification
  `is_verified` tinyint(1) NOT NULL DEFAULT 0, -- 0=Pending, 1=Verified
  `house_number` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT 'Meycauayan',
  `province` varchar(255) DEFAULT 'Bulacan',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_id_num_unique` (`id_number`) -- Ensures One Account Policy
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. PETS TABLE (Medical Status & QR Logic)
-- --------------------------------------------------------
CREATE TABLE `pets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL, -- Linked to Verified Owner
  `pet_id_code` varchar(255) NOT NULL,    -- Unique ID for QR Code scanning
  `name` varchar(255) NOT NULL,
  `species` varchar(255) NOT NULL,
  `breed` varchar(255) NOT NULL,
  `gender` enum('Male', 'Female') NOT NULL,
  `birthday` date NOT NULL,
  `image_url` longtext DEFAULT NULL,
  `status` enum('ACTIVE', 'INACTIVE', 'DECEASED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pets_pet_id_unique` (`pet_id_code`),
  CONSTRAINT `pets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. VACCINE INVENTORIES (Stock & Reporting)
-- --------------------------------------------------------
CREATE TABLE `vaccine_inventories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `batch_no` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. APPOINTMENTS (Calendar-Grid Compatible)
-- --------------------------------------------------------
CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL, -- Owner
  `pet_id` bigint(20) UNSIGNED NOT NULL,  -- Specific Pet
  `appointment_date` date NOT NULL,      -- For Calendar Date
  `appointment_time` time NOT NULL,      -- For Time-Slot Chip
  `service_type` varchar(255) NOT NULL,
  `status` enum('pending','approved','completed','cancelled','missed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `app_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `app_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. VACCINATION HISTORY (Medical Logs)
-- --------------------------------------------------------
CREATE TABLE `vaccinations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pet_id` bigint(20) UNSIGNED NOT NULL,
  `vaccine_id` bigint(20) UNSIGNED DEFAULT NULL, -- Linked to Inventory
  `date_administered` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `vacc_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vacc_inv_fk` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccine_inventories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. SYSTEM LOGS (Audit Trail)
-- --------------------------------------------------------
CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL, -- e.g., 'Registered Pet', 'Updated Stock'
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- SEED DATA (Initial Admin)
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `is_verified`, `created_at`) VALUES
(1, 'PawCare Main Admin', 'admin@pawcare.com', '$2y$12$hZEURM0NjvkpAGwCeVhWZpNvn4i7Nc5i1Z/hJg5Q3LQ=', '09123456789', 'admin', 1, NOW());

COMMIT;