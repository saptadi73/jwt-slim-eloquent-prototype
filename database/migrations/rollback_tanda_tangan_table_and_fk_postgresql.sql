-- Rollback: Remove tanda_tangan table and foreign key from pegawai (PostgreSQL)
-- Date: 2025-12-31

-- Step 1: Drop foreign key constraint
ALTER TABLE pegawai 
DROP CONSTRAINT IF EXISTS fk_pegawai_tanda_tangan;

-- Step 2: Drop index
DROP INDEX IF EXISTS idx_pegawai_tanda_tangan_id;

-- Step 3: Drop tanda_tangan_id column
ALTER TABLE pegawai 
DROP COLUMN IF EXISTS tanda_tangan_id;

-- Step 4: Drop trigger and function
DROP TRIGGER IF EXISTS trigger_tanda_tangan_updated_at ON tanda_tangan;
DROP FUNCTION IF EXISTS update_tanda_tangan_updated_at();

-- Step 5: Drop tanda_tangan table
DROP TABLE IF EXISTS tanda_tangan CASCADE;
