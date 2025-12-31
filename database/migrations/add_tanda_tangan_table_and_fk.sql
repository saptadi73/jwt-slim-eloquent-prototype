-- Migration: Create tanda_tangan table and add foreign key to pegawai
-- Date: 2025-12-31
-- Note: Table tanda_tangan will use INT primary key instead of UUID

-- Step 1: Create tanda_tangan table
CREATE TABLE IF NOT EXISTS `tanda_tangan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `url_tanda_tangan` VARCHAR(255) NOT NULL COMMENT 'URL/Path file tanda tangan',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Add tanda_tangan_id column to pegawai table
ALTER TABLE `pegawai` 
ADD COLUMN `tanda_tangan_id` INT NULL COMMENT 'Foreign key ke tanda_tangan' AFTER `url_foto`;

-- Step 3: Add foreign key constraint
ALTER TABLE `pegawai` 
ADD CONSTRAINT `fk_pegawai_tanda_tangan` 
FOREIGN KEY (`tanda_tangan_id`) 
REFERENCES `tanda_tangan`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Step 4: Add index for better performance
ALTER TABLE `pegawai` 
ADD INDEX `idx_tanda_tangan_id` (`tanda_tangan_id`);

-- Note: Kolom 'tanda_tangan' yang lama (VARCHAR) masih ada untuk backward compatibility
-- Jika ingin menghapus kolom lama, uncomment baris berikut:
-- ALTER TABLE `pegawai` DROP COLUMN `tanda_tangan`;
