-- Migration: Create tanda_tangan table and add foreign key to pegawai (PostgreSQL)
-- Date: 2025-12-31

-- Step 1: Create tanda_tangan table
CREATE TABLE IF NOT EXISTS tanda_tangan (
    id SERIAL PRIMARY KEY,
    url_tanda_tangan VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 2: Add tanda_tangan_id column to pegawai table
ALTER TABLE pegawai 
ADD COLUMN IF NOT EXISTS tanda_tangan_id INTEGER;

-- Step 3: Add index for better performance
CREATE INDEX IF NOT EXISTS idx_pegawai_tanda_tangan_id ON pegawai(tanda_tangan_id);

-- Step 4: Add foreign key constraint
ALTER TABLE pegawai 
ADD CONSTRAINT fk_pegawai_tanda_tangan 
FOREIGN KEY (tanda_tangan_id) 
REFERENCES tanda_tangan(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Step 5: Create trigger for updated_at
CREATE OR REPLACE FUNCTION update_tanda_tangan_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_tanda_tangan_updated_at
    BEFORE UPDATE ON tanda_tangan
    FOR EACH ROW
    EXECUTE FUNCTION update_tanda_tangan_updated_at();

-- Note: Kolom 'tanda_tangan' yang lama (VARCHAR) masih ada untuk backward compatibility
-- Jika ingin menghapus kolom lama, uncomment baris berikut:
-- ALTER TABLE pegawai DROP COLUMN tanda_tangan;
