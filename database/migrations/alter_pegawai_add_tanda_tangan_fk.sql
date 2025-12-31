-- Migration: Alter table pegawai - Add tanda_tangan_id foreign key
-- Date: 2025-12-31
-- Description: Menambahkan kolom tanda_tangan_id dan foreign key ke tabel tanda_tangan

-- Step 1: Add tanda_tangan_id column to pegawai table
ALTER TABLE `pegawai` 
ADD COLUMN `tanda_tangan_id` INT NULL COMMENT 'Foreign key ke tanda_tangan' AFTER `url_foto`;

-- Step 2: Add index for better performance
ALTER TABLE `pegawai` 
ADD INDEX `idx_tanda_tangan_id` (`tanda_tangan_id`);

-- Step 3: Add foreign key constraint
-- Pastikan tabel tanda_tangan sudah dibuat terlebih dahulu
ALTER TABLE `pegawai` 
ADD CONSTRAINT `fk_pegawai_tanda_tangan` 
FOREIGN KEY (`tanda_tangan_id`) 
REFERENCES `tanda_tangan`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Note: 
-- 1. Kolom 'tanda_tangan' VARCHAR yang lama tetap ada untuk backward compatibility
-- 2. Kolom 'tanda_tangan_id' INT yang baru adalah foreign key ke tabel tanda_tangan
-- 3. ON DELETE SET NULL: jika tanda_tangan dihapus, pegawai.tanda_tangan_id akan NULL
-- 4. ON UPDATE CASCADE: jika tanda_tangan.id berubah, pegawai.tanda_tangan_id otomatis update

-- Jika ingin menghapus kolom 'tanda_tangan' VARCHAR yang lama, uncomment baris berikut:
-- ALTER TABLE `pegawai` DROP COLUMN `tanda_tangan`;
