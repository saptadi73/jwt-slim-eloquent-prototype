# Integrasi Table Pegawai dengan Group dan Tanda Tangan

## Overview

Telah dilakukan integrasi untuk memperluas struktur database dan API karyawan dengan menambahkan:
- **Table Groups** - Untuk mengelola grup/tim karyawan
- **Table Tanda Tangan** - Untuk menyimpan signature digital karyawan
- **Update Table Pegawai** - Dengan kolom group_id, url_foto, dan tanda_tangan

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
- group_id (UUID, FK) - NEW
- url_foto (VARCHAR 255, nullable) - NEW
- tanda_tangan (VARCHAR 255, nullable) - NEW
- created_at, updated_at (TIMESTAMP)
```

### Table: tanda_tangan (New)
```sql
- id (UUID, Primary Key)
- pegawai_id (UUID, FK) - References pegawai
- url_tanda_tangan (VARCHAR 255, nullable)
- deskripsi (TEXT, nullable)
- created_at, updated_at (TIMESTAMP)
```

## Models Created/Updated

### 1. Group Model (`app/Models/Group.php`)
- Relasi: hasMany employees
- Fillable: name, description, is_active

### 2. TandaTangan Model (`app/Models/TandaTangan.php`)
- Relasi: belongsTo pegawai
- Fillable: pegawai_id, url_tanda_tangan, deskripsi

### 3. Pegawai Model (Updated)
- Relasi baru: hasMany tandaTangan
- Updated fillable fields

## Services Created/Updated

### 1. GroupService (`app/Services/GroupService.php`)
- `getAll()` - Get semua grup dengan pagination
- `getById()` - Get detail grup
- `store()` - Buat grup baru
- `update()` - Update grup
- `delete()` - Hapus grup
- `getActive()` - Get grup yang aktif

### 2. TandaTanganService (`app/Services/TandaTanganService.php`)
- `getAll()` - Get semua signature
- `getById()` - Get detail signature
- `getByPegawaiId()` - Get signature by employee
- `store()` - Simpan signature baru (dengan file upload)
- `update()` - Update signature (dengan opsi file upload baru)
- `delete()` - Hapus signature
- `handleSignatureUpload()` - Upload file ke `/public/uploads/signatures/`
- `deleteSignature()` - Hapus file dari server

### 3. PegawaiService (`app/Services/PegawaiService.php`)
- `getAll()` - Get semua pegawai dengan relasi departemen, group, tanda tangan
- `getById()` - Get detail pegawai
- `store()` - Buat pegawai baru (dengan 2 file upload: foto + signature)
- `update()` - Update pegawai (dengan opsi 2 file upload)
- `delete()` - Hapus pegawai (auto-cleanup foto dan signature)
- `handlePhotoUpload()` - Upload foto ke `/public/uploads/pegawai/`
- `handleSignatureUpload()` - Upload signature ke `/public/uploads/signatures/`
- `deletePhoto()` - Hapus file foto
- `deleteSignature()` - Hapus file signature

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

### 2. Tanda Tangan Routes (`routes/tanda_tangan.php`)
```
GET    /api/tanda-tangan                  - List all signatures
GET    /api/tanda-tangan/{id}             - Get signature detail
GET    /api/pegawai/{pegawaiId}/tanda-tangan - Get signatures by employee
POST   /api/tanda-tangan                  - Create signature (multipart)
PUT    /api/tanda-tangan/{id}             - Update signature (multipart)
DELETE /api/tanda-tangan/{id}             - Delete signature
```

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

## Relasi Database

```
groups
  └── pegawai (hasMany)

pegawai
  ├── departemen (belongsTo)
  ├── group (belongsTo)
  ├── absensi (hasMany)
  ├── cuti (hasMany)
  ├── lembur (hasMany)
  ├── ijin (hasMany)
  ├── gaji (hasMany)
  ├── jatahCuti (hasMany)
  └── tandaTangan (hasMany)

tanda_tangan
  └── pegawai (belongsTo)
```

## Fitur Utama

✅ **Dual File Upload** - Karyawan dapat upload foto dan signature sekaligus  
✅ **Automatic Cleanup** - File lama otomatis dihapus saat update  
✅ **Unique Naming** - Menggunakan uniqid() untuk mencegah duplikasi filename  
✅ **Directory Creation** - Auto-create upload directories jika belum ada  
✅ **Multiple Signatures** - Satu karyawan bisa punya multiple signature records  
✅ **Proper Validation** - Semua endpoint punya validasi input  
✅ **Error Handling** - Consistent error response format  
✅ **Pagination** - Semua list endpoint support pagination  

