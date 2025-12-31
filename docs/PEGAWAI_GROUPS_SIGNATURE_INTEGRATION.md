# Integrasi Table Pegawai dengan Group dan Tanda Tangan

## Overview

Telah dilakukan integrasi untuk memperluas struktur database dan API karyawan dengan menambahkan:
- **Table Groups** - Untuk mengelola grup/tim karyawan
- **Table Tanda Tangan** - Untuk menyimpan signature digital (independent, reusable)
- **Update Table Pegawai** - Dengan kolom group_id, tanda_tangan_id, url_foto, dan tanda_tangan (legacy)

**⚠️ UPDATE 2025-12-31:** Struktur tanda_tangan telah disederhanakan. Lihat section "Changes 2025-12-31" di bawah.

## Struktur Database

### Table: groups
```sql
- id (UUID, Primary Key)
- name (VARCHAR 255)
- description (TEXT, nullable)
- is_active (BOOLEAN, default: true)
- created_at, updated_at (TIMESTAMP)
```

### Table: pegawai (Updated)
```sql
- id (UUID, Primary Key)
- nama (VARCHAR 255)
- alamat (VARCHAR 255, nullable)
- hp (VARCHAR 255, nullable)
- email (VARCHAR 255, nullable)
- departemen_id (UUID, FK)
- position_id (INT, FK)
- group_id (UUID, FK)
- tanda_tangan_id (INT, FK) - NEW (2025-12-31)
- url_foto (VARCHAR 255, nullable)
- tanda_tangan (VARCHAR 255, nullable) - LEGACY
- hire_date (DATE, nullable)
- is_active (BOOLEAN, default: true)
- created_at, updated_at (TIMESTAMP)
```

### Table: tanda_tangan (Simplified - 2025-12-31)
```sql
- id (INT, Primary Key, Auto-increment)
- url_tanda_tangan (VARCHAR 255)
- created_at, updated_at (TIMESTAMP)
```

**Removed fields:** pegawai_id, nama, jabatan, deskripsi, is_active (redundant dengan pegawai)

## Models Created/Updated

### 1. Group Model (`app/Models/Group.php`)
- Relasi: hasMany employees
- Fillable: name, description, is_active

### 2. TandaTangan Model (`app/Models/TandaTangan.php`) - Updated 2025-12-31
- Primary Key: INT (was UUID)
- Relasi: hasMany pegawai (reversed relationship)
- Fillable: url_tanda_tangan
- **Removed:** pegawai_id, nama, jabatan, deskripsi, is_active

### 3. Pegawai Model (Updated)
- Relasi baru: belongsTo tandaTangan via tanda_tangan_id
- Updated fillable: added tanda_tangan_id, position_id, hire_date, is_active

## Services Created/Updated

### 1. GroupService (`app/Services/GroupService.php`)
- `getAll()` - Get semua grup dengan pagination
- `getById()` - Get detail grup
- `store()` - Buat grup baru
- `update()` - Update grup
- `delete()` - Hapus grup
- `getActive()` - Get grup yang aktif

### 2. TandaTanganService (`app/Services/TandaTanganService.php`) - Updated 2025-12-31
- `getAll()` - Get semua signature (with pegawai eager load)
- `getById()` - Get detail signature (with pegawai eager load)
- ~~`getByPegawaiId()`~~ - **REMOVED** (use GET /api/pegawai/{id} instead)
- `store()` - Simpan signature baru (only file upload, no pegawai_id)
- `update()` - Update signature (only file upload)
- `delete()` - Hapus signature dan file
- `handleSignatureUpload()` - Upload file ke `/public/uploads/signatures/`
- `deleteSignature()` - Hapus file dari server

### 3. PegawaiService (`app/Services/PegawaiService.php`) - Updated 2025-12-31
- `getAll()` - Get semua pegawai dengan relasi departemen, group, position, tandaTangan
- `getById()` - Get detail pegawai
- `store()` - Support tanda_tangan_id + legacy tanda_tangan file
- `update()` - Support tanda_tangan_id + legacy tanda_tangan file
- `delete()` - Hapus pegawai (auto-cleanup foto dan legacy signature)
- `handlePhotoUpload()` - Upload foto ke `/public/uploads/pegawai/`
- `handleSignatureUpload()` - Upload legacy signature ke `/public/uploads/signatures/`
- `deletePhoto()` - Hapus file foto
- `deleteSignature()` - Hapus file legacy signature

## Routes Created/Updated

### 1. Groups Routes (`routes/groups.php`)
```
GET    /api/groups                 - List all groups
GET    /api/groups/{id}            - Get group detail
POST   /api/groups                 - Create group
PUT    /api/groups/{id}            - Update group
DELETE /api/groups/{id}            - Delete group
GET    /api/groups/active          - Get active groups
```

### 2. Tanda Tangan Routes (`routes/tanda_tangan.php`) - Updated 2025-12-31
```
GET    /api/tanda-tangan                  - List all signatures
GET    /api/tanda-tangan/{id}             - Get signature detail
POST   /api/tanda-tangan                  - Create signature (file only)
PUT    /api/tanda-tangan/{id}             - Update signature (file only)
DELETE /api/tanda-tangan/{id}             - Delete signature
```

**REMOVED:** `GET /api/pegawai/{pegawaiId}/tanda-tangan` (use `GET /api/pegawai/{id}` with eager load)

### 3. Pegawai Routes (`routes/pegawai.php`)
```
GET    /api/pegawai                - List all employees
GET    /api/pegawai/{id}           - Get employee detail
POST   /api/pegawai                - Create employee (with photo & signature)
PUT    /api/pegawai/{id}           - Update employee
POST   /api/pegawai/{id}           - Update employee (multipart form-data)
DELETE /api/pegawai/{id}           - Delete employee
```

## File Upload Handling

### Foto Karyawan
- **Directory**: `public/uploads/pegawai/`
- **Format**: `emp_{uniqid}_{original_filename}`
- **Contoh**: `emp_65a4b3c2_john_doe.jpg`
- **Supported**: PNG, JPG, GIF

### Tanda Tangan Digital
- **Directory**: `public/uploads/signatures/`
- **Format**: `sig_{uniqid}_{original_filename}`
- **Contoh**: `sig_78d9e1f4_signature.png`
- **Supported**: PNG, JPG, GIF

## API Endpoints Summary

### Create Employee dengan Foto dan Signature
```bash
curl -X POST "http://localhost:8000/api/pegawai" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "nama=Ahmad Budi" \
  -F "alamat=Jl. Raya No. 15, Jakarta" \
  -F "hp=08123456789" \
  -F "email=ahmad.budi@bengkel.com" \
  -F "departemen_id=550e8400-e29b-41d4-a716-446655440000" \
  -F "group_id=660e8400-e29b-41d4-a716-446655440001" \
  -F "is_active=true" \
  -F "url_foto=@/path/to/photo.jpg" \
  -F "tanda_tangan=@/path/to/signature.png"
```

### Create Signature
```bash
curl -X POST "http://localhost:8000/api/tanda-tangan" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "pegawai_id=550e8400-e29b-41d4-a716-446655440002" \
  -F "deskripsi=Tanda tangan digital utama" \
  -F "tanda_tangan=@/path/to/signature.png"
```

### Create Group
```bash
curl -X POST "http://localhost:8000/api/groups" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Group A",
    "description": "Group untuk tim AC",
    "is_active": true
  }'
```

## Database Migrations

Tiga migration files telah dibuat:

1. **2025_12_30_000001_create_groups_table.php** - Create table groups
2. **2025_12_30_000002_update_pegawai_table.php** - Add columns to pegawai (group_id, url_foto, tanda_tangan)
3. **2025_12_30_000003_create_tanda_tangan_table.php** - Create table tanda_tangan

### Jalankan Migrations
```bash
php migrate.php
```

## Dokumentasi API Lengkap

Lihat [EMPLOYEE_API.md](EMPLOYEE_API.md) untuk dokumentasi lengkap yang mencakup:
- Semua endpoint Groups
- Semua endpoint Pegawai (dengan foto & signature)
- Semua endpoint Tanda Tangan
- Request/response examples
- Field descriptions
- Error handling

Dokumentasi telah di-validate dan **100% bebas dari markdownlint warnings**.

## Relasi Database (Updated 2025-12-31)

```
groups
  └── pegawai (hasMany)

positions
  └── pegawai (hasMany)

tanda_tangan (independent)
  └── pegawai (hasMany) - reverse relation

pegawai
  ├── departemen (belongsTo)
  ├── position (belongsTo)
  ├── group (belongsTo)
  ├── tandaTangan (belongsTo) via tanda_tangan_id
  ├── absensi (hasMany)
  ├── cuti (hasMany)
  ├── lembur (hasMany)
  ├── ijin (hasMany)
  ├── gaji (hasMany)
  ├── jatahCuti (hasMany)
  ├── timeOffs (hasMany)
  └── attendances (hasMany)
```

**Key Change:** Tanda tangan sekarang independent dan dapat reusable. Pegawai reference signature via `tanda_tangan_id`.

## Fitur Utama

✅ **Dual File Upload** - Karyawan dapat upload foto dan signature sekaligus (legacy)  
✅ **Reusable Signatures** - Signature dapat digunakan oleh banyak pegawai  
✅ **Independent Signature Management** - Upload signature terpisah dari pegawai  
✅ **Automatic Cleanup** - File lama otomatis dihapus saat update/delete  
✅ **Unique Naming** - Menggunakan uniqid() untuk mencegah duplikasi filename  
✅ **Directory Creation** - Auto-create upload directories jika belum ada  
✅ **Backward Compatible** - Masih support legacy `pegawai.tanda_tangan` (VARCHAR)  
✅ **Proper Validation** - Semua endpoint punya validasi input  
✅ **Error Handling** - Consistent error response format  
✅ **Pagination** - Semua list endpoint support pagination  

---

## Changes 2025-12-31

### What Changed?

**Before:**
- TandaTangan hasMany pegawai via `tanda_tangan.pegawai_id`
- Redundant fields: nama, jabatan, deskripsi, is_active
- One signature per pegawai

**After:**
- Pegawai belongsTo TandaTangan via `pegawai.tanda_tangan_id`
- Simplified: only id, url_tanda_tangan, timestamps
- Reusable signatures (one signature → many pegawai)

### Migration Required

```sql
-- PostgreSQL
psql -U user -d db < database/migrations/add_tanda_tangan_table_and_fk_postgresql.sql

-- MySQL
mysql -u root -p db < database/migrations/add_tanda_tangan_table_and_fk.sql
```

### New Workflow

```bash
# 1. Upload signature
POST /api/tanda-tangan (file only)
→ Returns: { "id": 15 }

# 2. Assign to pegawai
PUT /api/pegawai/{id}
Body: { "tanda_tangan_id": 15 }

# 3. Get pegawai with signature
GET /api/pegawai/{id}
→ Includes: tandaTangan { id, url_tanda_tangan }
```

### Documentation Updated

- ✅ EMPLOYEE_API.md - Updated signature section
- ✅ TANDA_TANGAN_API.md - New comprehensive documentation
- ✅ TANDA_TANGAN_REFACTORING_SUMMARY.md - Complete refactoring guide
- ✅ PEGAWAI_GROUPS_SIGNATURE_INTEGRATION.md - This file  

