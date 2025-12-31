-- Rollback: Remove tanda_tangan table and foreign key from pegawai
-- Date: 2025-12-31

-- Step 1: Drop foreign key constraint
ALTER TABLE `pegawai` 
DROP FOREIGN KEY `fk_pegawai_tanda_tangan`;

-- Step 2: Drop index
ALTER TABLE `pegawai` 
DROP INDEX `idx_tanda_tangan_id`;

-- Step 3: Drop tanda_tangan_id column
ALTER TABLE `pegawai` 
DROP COLUMN `tanda_tangan_id`;

-- Step 4: Drop tanda_tangan table
DROP TABLE IF EXISTS `tanda_tangan`;
