-- SQL Script: Update tanda_tangan_id di Pegawai
-- Date: 2025-12-31

-- ============================================
-- OPTION 1: Auto-assign sequential
-- ============================================
-- Jika Anda punya N pegawai dan M tanda_tangan,
-- Assign secara berurutan (pegawai ke-1 dapat tanda_tangan ke-1, dst)

UPDATE pegawai p
SET tanda_tangan_id = (
  SELECT id FROM tanda_tangan 
  ORDER BY id 
  LIMIT 1 OFFSET (
    SELECT COUNT(*) FROM pegawai p2 
    WHERE p2.id < p.id OR (p2.id = p.id)
  ) - 1
)
WHERE tanda_tangan_id IS NULL;

-- ============================================
-- OPTION 2: Manual - Assign per Employee ID
-- ============================================
-- Ganti PEGAWAI_ID dan TANDA_TANGAN_ID dengan nilai yang sesuai

UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE id = 'P001';

UPDATE pegawai 
SET tanda_tangan_id = 2 
WHERE id = 'P002';

UPDATE pegawai 
SET tanda_tangan_id = 3 
WHERE id = 'P003';

-- Lanjutkan untuk pegawai lainnya...

-- ============================================
-- OPTION 3: Bulk Update - Jika sudah punya list
-- ============================================
-- Import dari CSV atau list
-- Format: UPDATE pegawai SET tanda_tangan_id = ? WHERE id = ?

-- ============================================
-- OPTION 4: Assign same signature to all (test)
-- ============================================
-- Jika hanya punya 1 tanda_tangan untuk semua pegawai

UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE tanda_tangan_id IS NULL;

-- ============================================
-- VERIFY - Cek hasilnya
-- ============================================
SELECT 
    p.id,
    p.nama,
    p.tanda_tangan_id,
    t.id as sig_id,
    t.url_tanda_tangan,
    CASE 
        WHEN p.tanda_tangan_id IS NOT NULL THEN '✓ Filled'
        ELSE '✗ NULL'
    END as status
FROM pegawai p
LEFT JOIN tanda_tangan t ON p.tanda_tangan_id = t.id
ORDER BY p.id;

-- ============================================
-- Cek yang belum diisi
-- ============================================
SELECT id, nama, tanda_tangan_id 
FROM pegawai 
WHERE tanda_tangan_id IS NULL;

-- ============================================
-- Cek data tanda_tangan
-- ============================================
SELECT id, url_tanda_tangan FROM tanda_tangan;
