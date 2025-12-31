# Tanda Tangan API Documentation

## Overview
API untuk mengelola tanda tangan digital. Relasi 1-to-1 dengan pegawai melalui `pegawai.tanda_tangan_id`.

## Struktur Tabel

### tanda_tangan
```sql
- id (INT, PRIMARY KEY)
- url_tanda_tangan (VARCHAR 255) - Path/URL file tanda tangan
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Relasi
- `pegawai.tanda_tangan_id` → `tanda_tangan.id` (belongs to)
- `tanda_tangan.pegawai` → hasMany Pegawai

---

## Endpoints

### 1. GET All Signatures
**Endpoint:** `GET /api/tanda-tangan`

**Query Parameters:**
- `page` (optional, default: 1)
- `limit` (optional, default: 10)

**Response:**
```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "url_tanda_tangan": "/uploads/signatures/sig_abc123.png",
        "created_at": "2025-12-31T10:00:00",
        "updated_at": "2025-12-31T10:00:00",
        "pegawai": [
          {
            "id": "P001",
            "nama": "Budi Santoso",
            "departemen_id": 1,
            "position_id": 1
          }
        ]
      }
    ],
    "total": 50,
    "per_page": 10
  }
}
```

---

### 2. GET Signature by ID
**Endpoint:** `GET /api/tanda-tangan/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "id": 1,
    "url_tanda_tangan": "/uploads/signatures/sig_abc123.png",
    "created_at": "2025-12-31T10:00:00",
    "updated_at": "2025-12-31T10:00:00",
    "pegawai": [
      {
        "id": "P001",
        "nama": "Budi Santoso"
      }
    ]
  }
}
```

**Error Response (404):**
```json
{
  "status": "error",
  "message": "Signature not found"
}
```

---

### 3. POST Create Signature
**Endpoint:** `POST /api/tanda-tangan`

**Content-Type:** `multipart/form-data`

**Form Data:**
- `tanda_tangan` (file, required) - Image file (png, jpg, jpeg)

**Response (201):**
```json
{
  "status": "success",
  "message": "Signature created",
  "data": {
    "id": 15,
    "url_tanda_tangan": "/uploads/signatures/sig_xyz789.png",
    "created_at": "2025-12-31T10:00:00",
    "updated_at": "2025-12-31T10:00:00"
  }
}
```

**Error Response (400):**
```json
{
  "status": "error",
  "message": "Signature file is required"
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8080/api/tanda-tangan \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "tanda_tangan=@/path/to/signature.png"
```

---

### 4. PUT Update Signature
**Endpoint:** `PUT /api/tanda-tangan/{id}`

**Content-Type:** `multipart/form-data`

**Form Data:**
- `tanda_tangan` (file, optional) - New image file

**Response:**
```json
{
  "status": "success",
  "message": "Signature updated",
  "data": {
    "id": 15,
    "url_tanda_tangan": "/uploads/signatures/sig_new123.png",
    "created_at": "2025-12-31T10:00:00",
    "updated_at": "2025-12-31T11:00:00"
  }
}
```

**Note:** File lama akan dihapus otomatis saat upload file baru.

---

### 5. DELETE Signature
**Endpoint:** `DELETE /api/tanda-tangan/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Signature deleted",
  "data": []
}
```

**Error Response (404):**
```json
{
  "status": "error",
  "message": "Signature not found"
}
```

---

## Integration dengan Pegawai

### Assign Signature ke Pegawai

**Cara 1: Saat Create/Update Pegawai**
```bash
curl -X POST http://localhost:8080/api/pegawai \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "nama": "Budi Santoso",
    "email": "budi@example.com",
    "tanda_tangan_id": 15
  }'
```

**Cara 2: Update Pegawai Existing**
```bash
curl -X PUT http://localhost:8080/api/pegawai/P001 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "tanda_tangan_id": 15
  }'
```

### Get Pegawai dengan Signature
```bash
curl -X GET http://localhost:8080/api/pegawai/P001
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": "P001",
    "nama": "Budi Santoso",
    "email": "budi@example.com",
    "tanda_tangan_id": 15,
    "tandaTangan": {
      "id": 15,
      "url_tanda_tangan": "/uploads/signatures/sig_xyz789.png"
    }
  }
}
```

---

## Workflow Rekomendasi

### 1. Upload Signature Baru
```
POST /api/tanda-tangan
→ Dapatkan signature ID (misal: 15)
```

### 2. Assign ke Pegawai
```
PUT /api/pegawai/{pegawai_id}
Body: { "tanda_tangan_id": 15 }
```

### 3. Lihat Signature di Data Pegawai
```
GET /api/pegawai/{pegawai_id}
→ Response include tandaTangan relation
```

---

## File Upload Notes

**Supported formats:** PNG, JPG, JPEG, GIF
**Upload directory:** `public/uploads/signatures/`
**URL access:** `/uploads/signatures/filename.png`
**Naming pattern:** `sig_{uniqid}_{original_filename}`

**Auto cleanup:** File lama dihapus otomatis saat:
- Update signature (replace file)
- Delete signature

---

## Migration Commands

### PostgreSQL
```bash
# Create table
psql -U user -d database < database/migrations/add_tanda_tangan_table_and_fk_postgresql.sql

# Rollback
psql -U user -d database < database/migrations/rollback_tanda_tangan_table_and_fk_postgresql.sql
```

### MySQL
```bash
# Create table
mysql -u root -p database < database/migrations/add_tanda_tangan_table_and_fk.sql

# Rollback
mysql -u root -p database < database/migrations/rollback_tanda_tangan_table_and_fk.sql
```

---

## Legacy Support

Kolom `pegawai.tanda_tangan` (VARCHAR) masih ada untuk backward compatibility dengan sistem lama yang upload signature langsung ke pegawai.

**Sistem Baru (Recommended):**
- Upload ke `/api/tanda-tangan` → dapat ID
- Assign ID ke `pegawai.tanda_tangan_id`

**Sistem Lama (Legacy):**
- Upload signature langsung saat create/update pegawai
- Tersimpan di kolom `pegawai.tanda_tangan` (VARCHAR)
