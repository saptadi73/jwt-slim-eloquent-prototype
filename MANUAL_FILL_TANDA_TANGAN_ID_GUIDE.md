# Cara Mengisi Manual tanda_tangan_id di Pegawai

## 📋 Prerequisite
- Sudah ada data di tabel `tanda_tangan`
- Sudah ada data di tabel `pegawai`
- DBeaver atau MySQL/PostgreSQL client

---

## Cara 1: Menggunakan DBeaver GUI (Paling Mudah)

### Step 1: Lihat Data Tanda Tangan
1. Buka DBeaver
2. Klik kanan `tanda_tangan` table → Select Rows
3. **Catat ID-nya:** 1, 2, 3, dst

**Contoh:**
```
ID | url_tanda_tangan
1  | /uploads/signatures/sig_abc123.png
2  | /uploads/signatures/sig_def456.png
3  | /uploads/signatures/sig_ghi789.png
```

### Step 2: Edit Pegawai Table
1. Klik kanan `pegawai` table → Edit Table Data
2. Scroll ke kolom `tanda_tangan_id`
3. **Klik di cell tanda_tangan_id** untuk pegawai pertama
4. **Ketik ID** dari tanda_tangan (contoh: 1)
5. Tekan Enter, lanjut ke pegawai berikutnya
6. **CTRL+S** untuk save semua perubahan

**Tips:**
- Double-click cell untuk edit
- Kosong = NULL
- Tekan Tab untuk pindah ke baris berikutnya

---

## Cara 2: Menggunakan SQL Query

### Option A: Assign Sama Semua (Jika 1 Signature untuk semua)

```sql
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE tanda_tangan_id IS NULL;
```

**Penjelasan:**
- Assign tanda_tangan ID = 1 ke semua pegawai yang belum punya
- Ganti `1` dengan ID signature yang sesuai

### Option B: Assign Berdasarkan List

Jika punya daftar pegawai dan signature-nya:

```sql
-- Pegawai 1 → Signature 1
UPDATE pegawai SET tanda_tangan_id = 1 WHERE id = 'P001';

-- Pegawai 2 → Signature 2
UPDATE pegawai SET tanda_tangan_id = 2 WHERE id = 'P002';

-- Pegawai 3 → Signature 3
UPDATE pegawai SET tanda_tangan_id = 3 WHERE id = 'P003';

-- Pegawai 4 → Signature 1 (reuse)
UPDATE pegawai SET tanda_tangan_id = 1 WHERE id = 'P004';
```

**Langkah:**
1. Buka DBeaver → SQL Editor (atau MySQL client)
2. Copy-paste query di atas
3. Ubah nilai `'P001'`, `'P002'` sesuai ID pegawai Anda
4. Ubah nilai `1`, `2`, `3` sesuai ID signature
5. **Execute** (Ctrl+Enter)

### Option C: Verifikasi Hasilnya

```sql
-- Lihat mana yang sudah terisi
SELECT 
    p.id,
    p.nama,
    p.tanda_tangan_id,
    t.url_tanda_tangan
FROM pegawai p
LEFT JOIN tanda_tangan t ON p.tanda_tangan_id = t.id
ORDER BY p.id;

-- Lihat yang belum terisi
SELECT id, nama FROM pegawai WHERE tanda_tangan_id IS NULL;
```

---

## Cara 3: Menggunakan PHP Script

Jika punya mapping data, buat script `update_tanda_tangan.php`:

```php
<?php
// database/seeders/UpdateTandaTanganId.php

// Mapping pegawai_id => tanda_tangan_id
$mapping = [
    'P001' => 1,
    'P002' => 2,
    'P003' => 3,
    'P004' => 1, // reuse
];

foreach ($mapping as $pegawai_id => $tanda_tangan_id) {
    \App\Models\Pegawai::where('id', $pegawai_id)
        ->update(['tanda_tangan_id' => $tanda_tangan_id]);
    echo "✓ $pegawai_id → $tanda_tangan_id\n";
}

echo "Done!\n";
?>
```

**Cara jalankan:**
```bash
cd /path/to/project
php -r "require 'database/seeders/UpdateTandaTanganId.php';"
```

---

## Cara 4: Menggunakan Artisan Command (Advanced)

Buat file: `app/Console/Commands/UpdateTandaTanganId.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use Illuminate\Console\Command;

class UpdateTandaTanganId extends Command
{
    protected $signature = 'tanda-tangan:update {--all : Assign same signature to all}';
    protected $description = 'Update tanda_tangan_id untuk pegawai';

    public function handle()
    {
        if ($this->option('all')) {
            Pegawai::whereNull('tanda_tangan_id')
                ->update(['tanda_tangan_id' => 1]);
            
            $this->info('✓ All pegawai assigned to signature ID 1');
        } else {
            // Interactive mode
            $pegawai = Pegawai::whereNull('tanda_tangan_id')->get();
            
            foreach ($pegawai as $p) {
                $id = $this->ask("Signature ID untuk {$p->nama}?");
                $p->update(['tanda_tangan_id' => $id]);
                $this->line("✓ {$p->nama} → ID $id");
            }
        }
    }
}
```

**Cara jalankan:**
```bash
# Assign semua ke signature ID 1
php artisan tanda-tangan:update --all

# Interactive mode (tanya satu-satu)
php artisan tanda-tangan:update
```

---

## Skenario Praktis

### Skenario 1: 1 Signature untuk semua pegawai

```sql
-- Jika Anda hanya upload 1 file signature
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE tanda_tangan_id IS NULL;
```

### Skenario 2: Setiap pegawai punya signature berbeda

```sql
-- Jika upload N signature untuk N pegawai
-- Assume Pegawai sudah terurut sama dengan signature

UPDATE pegawai p
SET tanda_tangan_id = (
  SELECT ROW_NUMBER() OVER (ORDER BY id) 
  FROM pegawai p2 
  WHERE p2.id <= p.id
);
```

### Skenario 3: Sebagian saja yang punya signature

```sql
-- Hanya assign untuk pegawai tertentu
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE id IN ('P001', 'P003', 'P005');

-- Atau berdasarkan department
UPDATE pegawai 
SET tanda_tangan_id = 1 
WHERE departemen_id = 1;
```

---

## ✅ Checklist

Setelah update selesai, lakukan verifikasi:

- [ ] Buka pgAdmin/DBeaver
- [ ] SELECT * FROM pegawai WHERE tanda_tangan_id IS NULL;
  - Harusnya 0 rows (atau sesuai rencana)
- [ ] SELECT * FROM pegawai JOIN tanda_tangan...
  - Cek apakah relasi OK
- [ ] Test API: GET /api/pegawai/{id}
  - Response harus include `tandaTangan` relation

---

## 🚨 Troubleshooting

**Error: Foreign Key Constraint**
```
Error: Cannot add or update a child row: 
a foreign key constraint fails
```

**Solusi:**
- Pastikan `tanda_tangan_id` yang di-assign ada di tabel `tanda_tangan`
- Cek: `SELECT id FROM tanda_tangan;`

**Error: Data tidak tersimpan**
- Di DBeaver: **Ctrl+S** untuk save
- Atau beri konfirmasi "Save changes?"

**Mau undo (rollback)?**
```sql
UPDATE pegawai SET tanda_tangan_id = NULL;
```

---

## Tips & Best Practice

1. **Backup data dulu:**
   ```sql
   CREATE TABLE pegawai_backup AS SELECT * FROM pegawai;
   ```

2. **Test dengan 1 row dulu:**
   ```sql
   UPDATE pegawai SET tanda_tangan_id = 1 WHERE id = 'P001';
   -- Verify hasilnya
   SELECT * FROM pegawai WHERE id = 'P001';
   ```

3. **Gunakan transaction:**
   ```sql
   BEGIN;
   UPDATE pegawai SET tanda_tangan_id = ...;
   -- Check hasilnya
   COMMIT; -- atau ROLLBACK;
   ```

4. **Verify relasi:**
   ```sql
   SELECT p.id, p.nama, t.url_tanda_tangan
   FROM pegawai p
   LEFT JOIN tanda_tangan t ON p.tanda_tangan_id = t.id;
   ```

---

## Setelah Update Selesai

1. **Test API:**
   ```bash
   curl http://localhost:8080/api/pegawai/P001 \
     -H "Authorization: Bearer TOKEN"
   ```

2. **Cek response include relasi:**
   ```json
   {
     "id": "P001",
     "nama": "Ahmad Budi",
     "tanda_tangan_id": 1,
     "tandaTangan": {
       "id": 1,
       "url_tanda_tangan": "/uploads/signatures/sig_abc.png"
     }
   }
   ```

3. **Cleanup (optional):**
   Setelah yakin, hapus backup:
   ```sql
   DROP TABLE pegawai_backup;
   ```
