-- SQL: Update tanda_tangan_id di Pegawai (Quick & Simple)
-- Gunakan ini jika DBeaver READ ONLY

-- ============================================
-- LANGKAH 1: Cek data yang ada
-- ============================================

-- Lihat semua tanda_tangan
SELECT id, url_tanda_tangan FROM tanda_tangan;

-- Lihat semua pegawai
SELECT id, nama, tanda_tangan_id FROM pegawai;

-- ============================================
-- LANGKAH 2: Pilih salah satu opsi dibawah
-- ============================================

-- OPSI A: Assign semua pegawai ke 1 signature yang sama
-- Ganti nilai "1" dengan ID signature yang Anda mau
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE tanda_tangan_id IS NULL;

-- OPSI B: Assign per pegawai (Copy-paste dan modify sesuai kebutuhan)
-- Update pegawai dengan ID "P001" ke signature ID "1"
UPDATE pegawai SET tanda_tangan_id = 1 WHERE id = 'P001';
UPDATE pegawai SET tanda_tangan_id = 2 WHERE id = 'P002';
UPDATE pegawai SET tanda_tangan_id = 3 WHERE id = 'P003';
UPDATE pegawai SET tanda_tangan_id = 1 WHERE id = 'P004';
UPDATE pegawai SET tanda_tangan_id = 2 WHERE id = 'P005';

-- OPSI C: Assign hanya untuk tertentu
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE departemen_id = 1 AND tanda_tangan_id IS NULL;

-- ============================================
-- LANGKAH 3: Verifikasi hasil
-- ============================================

SELECT 
    p.id,
    p.nama,
    p.tanda_tangan_id,
    t.url_tanda_tangan
FROM pegawai p
LEFT JOIN tanda_tangan t ON p.tanda_tangan_id = t.id
ORDER BY p.id;

-- ============================================
-- LANGKAH 4: Cek yang masih kosong (jika ada)
-- ============================================

SELECT id, nama, tanda_tangan_id 
FROM pegawai 
WHERE tanda_tangan_id IS NULL
ORDER BY id;
