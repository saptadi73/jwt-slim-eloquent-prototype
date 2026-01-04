# Pegawai & Group Management API Documentation

Dokumentasi lengkap untuk API Manajemen Pegawai dan Group pada sistem AC Service.

## Daftar Isi

- [Groups](#groups)
- [Positions](#positions)
- [Departments](#departments)
- [Pegawai](#pegawai)
- [Tanda Tangan Pegawai](#employee-signatures)
- [Time Offs](#time-offs)
- [Attendances](#attendances)

---

## Groups

API untuk mengelola data grup karyawan.

### Groups List Endpoint

```text
GET /api/groups
```

### Groups List Query Parameters

| Parameter | Type    | Required | Default | Description          |
|-----------|---------|----------|---------|----------------------|
| page      | integer | No       | 1       | Page number          |
| limit     | integer | No       | 10      | Items per page       |

### Groups List Request Example

```bash
curl -X GET "http://localhost:8000/api/groups?page=1&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Groups List Response Example

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "data": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "name": "Group A",
        "description": "Group untuk tim AC",
        "is_active": true,
        "created_at": "2025-12-30T10:00:00Z",
        "updated_at": "2025-12-30T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total": 1,
      "per_page": 10
    }
  }
}
```

### Get Group Detail Endpoint

```text
GET /api/groups/{id}
```

### Get Group Detail Request Example

```bash
curl -X GET "http://localhost:8000/api/groups/550e8400-e29b-41d4-a716-446655440000" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Get Group Detail Response Example

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Group A",
    "description": "Group untuk tim AC",
    "is_active": true,
    "created_at": "2025-12-30T10:00:00Z",
    "updated_at": "2025-12-30T10:00:00Z"
  }
}
```

### Create Group Endpoint

```text
POST /api/groups
```

### Create Group Request Body

```json
{
  "name": "Group B",
  "description": "Group untuk tim service",
  "is_active": true
}
```

### Create Group Request Example

```bash
curl -X POST "http://localhost:8000/api/groups" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Group B",
    "description": "Group untuk tim service",
    "is_active": true
  }'
```

### Create Group Response Example

```json
{
  "status": "success",
  "message": "Group created",
  "data": {
    "id": "660e8400-e29b-41d4-a716-446655440001",
    "name": "Group B",
    "description": "Group untuk tim service",
    "is_active": true,
    "created_at": "2025-12-30T10:15:00Z",
    "updated_at": "2025-12-30T10:15:00Z"
  }
}
```

### Update Group Endpoint

```text
PUT /api/groups/{id}
```

### Update Group Request Body

```json
{
  "name": "Group B Updated",
  "description": "Group untuk tim service updated",
  "is_active": true
}
```

### Update Group Request Example

```bash
curl -X PUT "http://localhost:8000/api/groups/660e8400-e29b-41d4-a716-446655440001" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Group B Updated",
    "description": "Group untuk tim service updated",
    "is_active": true
  }'
```

### Update Group Response Example

```json
{
  "status": "success",
  "message": "Group updated",
  "data": {
    "id": "660e8400-e29b-41d4-a716-446655440001",
    "name": "Group B Updated",
    "description": "Group untuk tim service updated",
    "is_active": true,
    "created_at": "2025-12-30T10:15:00Z",
    "updated_at": "2025-12-30T10:20:00Z"
  }
}
```

### Delete Group Endpoint

```text
DELETE /api/groups/{id}
```

### Delete Group Request Example

```bash
curl -X DELETE "http://localhost:8000/api/groups/660e8400-e29b-41d4-a716-446655440001" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Delete Group Response Example

```json
{
  "status": "success",
  "message": "Group deleted",
  "data": null
}
```

### Get Active Groups Endpoint

```text
GET /api/groups/active
```

### Get Active Groups Request Example

```bash
curl -X GET "http://localhost:8000/api/groups/active" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Get Active Groups Response Example

```json
{
  "status": "success",
  "message": "Success",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "Group A",
      "description": "Group untuk tim AC",
      "is_active": true,
      "created_at": "2025-12-30T10:00:00Z",
      "updated_at": "2025-12-30T10:00:00Z"
    }
  ]
}
```

---

## Positions

API untuk mengelola data posisi/jabatan karyawan. Setiap position memiliki UUID unik yang dihasilkan secara otomatis.

### Positions List Endpoint

```text
GET /api/positions
```

### Positions List Query Parameters

| Parameter            | Type    | Required | Default | Description                    |
|----------------------|---------|----------|---------|--------------------------------|
| is_active            | boolean | No       | -       | Filter by active status        |
| with_employee_count  | boolean | No       | false   | Include employee count         |

### Positions List Request Example

```bash
curl -X GET "http://localhost:8080/api/positions?is_active=true&with_employee_count=true" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Positions List Response Example

```json
{
  "status": true,
  "message": "Positions retrieved successfully",
  "data": [
    {
      "id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
      "name": "Sales/Marketing Officer",
      "description": "",
      "is_active": true,
      "created_at": "2026-01-04T10:00:00.000000Z",
      "updated_at": "2026-01-04T10:00:00.000000Z"
    }
  ]
}
```

### Get Position Detail Endpoint

```text
GET /api/positions/{id}
```

### Get Position Detail Response Example

```json
{
  "status": true,
  "message": "Position retrieved successfully",
  "data": {
    "id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
    "name": "Sales/Marketing Officer",
    "description": "",
    "is_active": true,
    "created_at": "2026-01-04T10:00:00.000000Z",
    "updated_at": "2026-01-04T10:00:00.000000Z",
    "employees": []
  }
}
```

### Create Position Endpoint

```text
POST /api/positions
```

### Create Position Request Body

**Note:** Field `id` bersifat optional. Jika tidak disediakan, UUID akan dihasilkan secara otomatis.

```json
{
  "name": "Sales/Marketing Officer",
  "description": "",
  "is_active": true
}
```

### Create Position Response Example

```json
{
  "status": true,
  "message": "Position created successfully",
  "data": {
    "name": "Sales/Marketing Officer",
    "description": "",
    "is_active": true,
    "id": "d50b33b8-3f50-4239-8055-1e9c3476ef48",
    "updated_at": "2026-01-04T09:46:27.000000Z",
    "created_at": "2026-01-04T09:46:27.000000Z"
  }
}
```

### Create Position Request Example

```bash
curl -X POST "http://localhost:8080/api/positions" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "name": "Sales/Marketing Officer",
    "description": "",
    "is_active": true
  }'
```

### Update Position Endpoint

```text
PUT /api/positions/{id}
```

### Update Position Request Body

```json
{
  "name": "Senior Sales Officer",
  "description": "Senior position for sales team",
  "is_active": true
}
```

### Delete Position Endpoint

```text
DELETE /api/positions/{id}
```

### Delete Position Response Example

```json
{
  "status": true,
  "message": "Position deleted successfully",
  "data": null
}
```

**Note:** Tidak dapat menghapus position yang masih memiliki employees/pegawai yang terikat padanya.

---

## Departments

API untuk mengelola data departemen. Menggunakan UUID untuk identifikasi unik.

### Departments Summary with Count Endpoint

Endpoint untuk mendapatkan jumlah karyawan per departemen (grouped by departemen).

```text
GET /api/departments/summary/count
```

### Departments Summary Request Example

```bash
curl -X GET "http://localhost:8080/api/departments/summary/count" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Departments Summary Response Example

```json
{
  "status": true,
  "message": "Departments with employee count retrieved successfully",
  "data": [
    {
      "id": "#dfe75975-2fb8-4233-a35e-f27632d8f5bd",
      "nama": "Finance",
      "jumlah_karyawan": 1
    },
    {
      "id": "#ee9ba10e-36ee-4446-b36f-49014f949793",
      "nama": "HRD",
      "jumlah_karyawan": 1
    },
    {
      "id": "#d1ee2662-99f0-4ac1-ba52-b5c14990af38",
      "nama": "Teknisi",
      "jumlah_karyawan": 8
    },
    {
      "id": "#b0e8d3cd-e102-4a03-821e-75b5b391d870",
      "nama": "Sales/Marketing",
      "jumlah_karyawan": 0
    }
  ]
}
```

---

### Departments List Endpoint

```text
GET /api/departments
```

### Departments List Query Parameters

| Parameter            | Type    | Required | Default | Description                    |
|----------------------|---------|----------|---------|--------------------------------|
| is_active            | boolean | No       | -       | Filter by active status        |
| with_employee_count  | boolean | No       | false   | Include employee count         |

### Departments List Request Example

```bash
curl -X GET "http://localhost:8080/api/departments?is_active=true" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Departments List Response Example

```json
{
  "status": true,
  "message": "Departments retrieved successfully",
  "data": [
    {
      "id": "#d1ee2662-99f0-4ac1-ba52-b5c14990af38",
      "nama": "Teknisi",
      "created_at": "2025-09-14T01:03:24.000000Z",
      "updated_at": "2025-09-14T01:03:24.000000Z",
      "pegawai_count": 8
    },
    {
      "id": "#dfe75975-2fb8-4233-a35e-f27632d8f5bd",
      "nama": "Finance",
      "created_at": "2025-09-14T01:03:24.000000Z",
      "updated_at": "2025-09-14T01:03:24.000000Z",
      "pegawai_count": 1
    }
  ]
}
```

### Get Department Detail Endpoint

```text
GET /api/departments/{id}
```

### Create Department Endpoint

```text
POST /api/departments
```

### Create Department Request Body

**Note:** Field `id` bersifat optional. Jika tidak disediakan, UUID akan dihasilkan secara otomatis.

```json
{
  "nama": "Petugas Gudang"
}
```

### Create Department Response Example

```json
{
  "status": true,
  "message": "Department created successfully",
  "data": {
    "nama": "Petugas Gudang",
    "id": "d50b33b8-3f50-4239-8055-1e9c3476ef48",
    "updated_at": "2026-01-04T09:46:27.000000Z",
    "created_at": "2026-01-04T09:46:27.000000Z"
  }
}
```

### Create Department Request Example

```bash
curl -X POST "http://localhost:8080/api/departments" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "nama": "Petugas Gudang"
  }'
```

### Update Department Endpoint

```text
PUT /api/departments/{id}
```

### Update Department Request Body

```json
{
  "nama": "Petugas Gudang Utama"
}
```

### Delete Department Endpoint

```text
DELETE /api/departments/{id}
```

---

## Pegawai

API untuk mengelola data pegawai (karyawan).

**Note:** Sejak update terbaru, pegawai dapat memiliki referensi ke tanda tangan melalui field `tanda_tangan_id` (relasi 1-to-1).

### Pegawai List Endpoint

```text
GET /api/employees
GET /api/pegawai      (alternative endpoint)
```

### Pegawai List Query Parameters

| Parameter      | Type    | Required | Default | Description                        |
|----------------|---------|----------|---------|------------------------------------|
| page           | integer | No       | 1       | Page number for pagination         |
| limit          | integer | No       | 10      | Items per page                     |
| department_id  | string  | No       | -       | Filter by department UUID          |
| group_id       | string  | No       | -       | Filter by group UUID               |
| position_id    | string  | No       | -       | Filter by position UUID            |
| is_active      | boolean | No       | -       | Filter by active status (true/false) |
| search         | string  | No       | -       | Search by name (case insensitive)  |

### Pegawai List Request Example

```bash
# Get all pegawai with pagination
curl -X GET "http://localhost:8080/api/pegawai?page=1&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Filter by department
curl -X GET "http://localhost:8080/api/pegawai?department_id=d1ee2662-99f0-4ac1-ba52-b5c14990af38" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Filter by group
curl -X GET "http://localhost:8080/api/pegawai?group_id=6a620ea4-936f-4ab3-a945-512ad19cc774" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Search by name
curl -X GET "http://localhost:8080/api/pegawai?search=andi" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Multiple filters
curl -X GET "http://localhost:8080/api/pegawai?department_id=d1ee2662-99f0-4ac1-ba52-b5c14990af38&is_active=true&search=budi" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Pegawai List Response Example

```json
{
  "status": true,
  "message": "Success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "32099271-b7d1-4b70-8597-a22b129c9741",
        "nama": "Andi Setiawan",
        "alamat": "Jl. Andi No. 1",
        "hp": "081234567890",
        "email": "andi@example.com",
        "departemen_id": "d1ee2662-99f0-4ac1-ba52-b5c14990af38",
        "group_id": "6a620ea4-936f-4ab3-a945-512ad19cc774",
        "position_id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
        "tanda_tangan_id": 1,
        "url_foto": "uploads/pegawai/cowok1.jpg",
        "tanda_tangan": null,
        "hire_date": null,
        "is_active": true,
        "created_at": "2025-09-14T02:43:07.000000Z",
        "updated_at": "2025-09-14T02:43:07.000000Z",
        "departemen": {
          "id": "d1ee2662-99f0-4ac1-ba52-b5c14990af38",
          "nama": "Teknisi",
          "created_at": "2025-09-14T01:03:24.000000Z",
          "updated_at": "2025-09-14T01:03:24.000000Z"
        },
        "group": {
          "id": "6a620ea4-936f-4ab3-a945-512ad19cc774",
          "nama": "Teknisi Group A",
          "created_at": "2025-09-14T01:03:12.000000Z",
          "updated_at": "2025-09-14T01:03:12.000000Z"
        },
        "position": {
          "id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
          "nama": "Teknisi AC",
          "created_at": "2025-09-14T01:03:20.000000Z",
          "updated_at": "2025-09-14T01:03:20.000000Z"
        },
        "tandaTangan": {
          "id": 1,
          "url_tanda_tangan": "uploads/signatures/ttd1.png",
          "created_at": "2025-12-31T04:14:51.968496Z",
          "updated_at": "2025-12-31T04:40:32.279959Z"
        }
      }
    ],
    "first_page_url": "/api/pegawai?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "/api/pegawai?page=2",
    "links": [
      {"url": null, "label": "Previous", "page": null, "active": false},
      {"url": "/api/pegawai?page=1", "label": "1", "page": 1, "active": true},
      {"url": "/api/pegawai?page=2", "label": "2", "page": 2, "active": false},
      {"url": "/api/pegawai?page=2", "label": "Next", "page": 2, "active": false}
    ],
    "next_page_url": "/api/pegawai?page=2",
    "path": "/api/pegawai",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 11
  }
}
```

**Response Structure:**
- `status` (boolean) - Request status
- `message` (string) - Response message
- `data` (object) - Pagination object containing:
  - `current_page` - Current page number
  - `data` - Array of pegawai objects
  - `total` - Total number of records
  - `per_page` - Items per page (default: 10)
  - `last_page` - Last page number
  - `from` - Starting record number on current page
  - `to` - Ending record number on current page
  - `links` - Array of pagination links for UI navigation
  - `next_page_url` - URL to next page (null if on last page)
  - `prev_page_url` - URL to previous page (null if on first page)
  - `first_page_url` - URL to first page
  - `last_page_url` - URL to last page

**Pagination Navigation:**

```bash
# Get page 2
curl -X GET "http://localhost:8080/api/pegawai?page=2&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Get page 3 with 20 items per page
curl -X GET "http://localhost:8080/api/pegawai?page=3&limit=20" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Combine pagination with filters
curl -X GET "http://localhost:8080/api/pegawai?page=1&limit=5&department_id=d1ee2662-99f0-4ac1-ba52-b5c14990af38" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Note:** 
- Response includes relasi `position`, `departemen`, `group`, dan `tandaTangan` dengan nama field masing-masing untuk kemudahan frontend development.
- Total 11 pegawai dengan `limit=10` akan menghasilkan 2 halaman (page 1: 10 records, page 2: 1 record)
- Use `links` array untuk membuat pagination UI dengan Previous/Next buttons

### Get Employee Detail Endpoint

```text
GET /api/employees/{id}
```

### Get Employee Detail Response Example

```json
{
  "status": "success",
  "message": "Employee retrieved successfully",
  "data": {
    "id": 1,
    "name": "Ahmad Budi",
    "address": "Jl. Raya No. 15, Jakarta",
    "phone": "08123456789",
    "department_id": 1,
    "position_id": 1,
    "email": "ahmad.budi@bengkel.com",
    "url_photo": "/uploads/employees/emp_123456_photo.jpg",
    "hire_date": "2024-01-01",
    "is_active": true,
    "created_at": "2024-01-15T10:00:00.000000Z",
    "updated_at": "2024-01-15T10:00:00.000000Z",
    "department": {
      "id": 1,
      "name": "Technical",
      "description": "Departemen teknis lapangan"
    },
    "position": {
      "id": 1,
      "name": "Teknisi AC",
      "description": "Teknisi pemasangan dan perbaikan AC"
    },
    "time_offs": [],
    "attendances": []
  }
}
```

### Create Employee Endpoint

```text
POST /api/employees
POST /api/pegawai
```

### Create Employee Request Body (JSON)

```json
{
  "name": "Ahmad Budi",
  "address": "Jl. Raya No. 15, Jakarta",
  "phone": "08123456789",
  "department_id": 1,
  "position_id": 1,
  "email": "ahmad.budi@bengkel.com",
  "tanda_tangan_id": 5,
  "hire_date": "2024-01-01",
  "is_active": true
}
```

**Note:** Untuk menggunakan `tanda_tangan_id`, upload signature terlebih dahulu ke endpoint `/api/tanda-tangan`, kemudian gunakan ID yang diberikan.

### Create Employee with Photo and Signature Request (Multipart Form-Data)

Gunakan `multipart/form-data` dengan parameter:

- `name` atau `nama` (string, required) - Nama karyawan
- `address` atau `alamat` (string, optional) - Alamat karyawan
- `phone` atau `hp` (string, optional) - Nomor telepon
- `department_id` atau `departemen_id` (string, optional) - ID departemen
- `position_id` atau `position_id` (string, optional) - ID posisi
- `email` (string, optional) - Email karyawan
- `group_id` (string, optional) - ID grup karyawan
- `tanda_tangan_id` (integer, optional) - ID tanda tangan (referensi ke tabel tanda_tangan)
- `hire_date` (date, optional) - Tanggal hire
- `is_active` (boolean, optional) - Status aktif
- `url_foto` (file, optional) - File foto karyawan (PNG, JPG, GIF)
- `tanda_tangan` (file, optional) - File tanda tangan (PNG, JPG, GIF) - **Legacy support**

**Recommended:** Upload signature ke `/api/tanda-tangan` terlebih dahulu, lalu gunakan `tanda_tangan_id`.

```bash
curl -X POST "http://localhost:8000/api/pegawai" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "nama=Ahmad Budi" \
  -F "alamat=Jl. Raya No. 15, Jakarta" \
  -F "hp=08123456789" \
  -F "departemen_id=550e8400-e29b-41d4-a716-446655440000" \
  -F "group_id=660e8400-e29b-41d4-a716-446655440001" \
  -F "email=ahmad.budi@bengkel.com" \
  -F "is_active=true" \
  -F "url_foto=@/path/to/photo.jpg" \
  -F "tanda_tangan=@/path/to/signature.png"
```

### Create Employee Response Example

```json
{
  "status": "success",
  "message": "Employee created successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440002",
    "nama": "Ahmad Budi",
    "alamat": "Jl. Raya No. 15, Jakarta",
    "hp": "08123456789",
    "departemen_id": "550e8400-e29b-41d4-a716-446655440000",
    "group_id": "660e8400-e29b-41d4-a716-446655440001",
    "email": "ahmad.budi@bengkel.com",
    "url_foto": "/uploads/pegawai/emp_678901_photo.jpg",
    "tanda_tangan_id": null,
    "tanda_tangan": "/uploads/signatures/sig_123abc_signature.png",
    "is_active": true,
    "created_at": "2024-12-30T10:00:00Z",
    "updated_at": "2024-12-30T10:00:00Z",
    "departemen": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "Technical"
    },
    "group": {
      "id": "660e8400-e29b-41d4-a716-446655440001",
      "name": "Group A"
    },
    "tandaTangan": null
  }
}
```

### Update Employee Endpoint

```text
PUT /api/employees/{id}
PUT /api/pegawai/{id}
POST /api/employees/{id}  (for file upload with multipart form-data)
POST /api/pegawai/{id}     (for file upload with multipart form-data)
```

### Update Employee Request Example

```bash
curl -X POST "http://localhost:8000/api/employees/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "name=Ahmad Budi Updated" \
  -F "phone=08198765432" \
  -F "photo=@/path/to/new_photo.jpg"
```

### Delete Employee Endpoint

```text
DELETE /api/employees/{id}
```

---

## Employee Signatures

API untuk mengelola tanda tangan digital. Tanda tangan disimpan secara independen dan dapat direferensikan oleh pegawai melalui `pegawai.tanda_tangan_id`.

**Architecture:**
- Tabel `tanda_tangan` hanya berisi: `id`, `url_tanda_tangan`, `created_at`, `updated_at`
- Relasi: Pegawai `belongsTo` TandaTangan via `tanda_tangan_id`
- Satu signature dapat direferensikan oleh banyak pegawai (reusable)

**Workflow:**
1. Upload signature: `POST /api/tanda-tangan` → dapat signature ID
2. Assign ke pegawai: `PUT /api/pegawai/{id}` dengan `tanda_tangan_id`
3. Retrieve pegawai: Response include relasi `tandaTangan`

### Employee Signatures List Endpoint

```text
GET /api/tanda-tangan
```

### Employee Signatures List Query Parameters

| Parameter | Type    | Required | Default | Description |
|-----------|---------|----------|---------|-------------|
| page      | integer | No       | 1       | Page number |
| limit     | integer | No       | 10      | Per page    |

### Employee Signatures List Request Example

```bash
curl -X GET "http://localhost:8000/api/tanda-tangan?page=1&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Employee Signatures List Response Example

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "data": [
      {
        "id": 1,
        "url_tanda_tangan": "/uploads/signatures/sig_123abc_signature.png",
        "created_at": "2025-12-30T10:00:00Z",
        "updated_at": "2025-12-30T10:00:00Z",
        "pegawai": [
          {
            "id": "P001",
            "nama": "Ahmad Budi",
            "departemen_id": 1,
            "position_id": 1
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "total": 1,
      "per_page": 10
    }
  }
}
```

### Get Employee Signature Detail Endpoint

```text
GET /api/tanda-tangan/{id}
```

### Get Employee Signature Detail Request Example

```bash
curl -X GET "http://localhost:8000/api/tanda-tangan/550e8400-e29b-41d4-a716-446655440000" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Get Employee Signature Detail Response Example

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "id": 1,
    "url_tanda_tangan": "/uploads/signatures/sig_123abc_signature.png",
    "created_at": "2025-12-30T10:00:00Z",
    "updated_at": "2025-12-30T10:00:00Z",
    "pegawai": [
      {
        "id": "P001",
        "nama": "Ahmad Budi"
      }
    ]
  }
}
```

**Note:** Field `pegawai` menunjukkan pegawai yang menggunakan signature ini.

---

### ~~Get Tanda Tangan by Pegawai ID Endpoint~~ [DEPRECATED]

**⚠️ DEPRECATED:** Endpoint ini sudah tidak digunakan karena perubahan struktur relasi.

**Alternative:** Gunakan `GET /api/pegawai/{id}` untuk mendapatkan pegawai beserta relasi `tandaTangan`.

```bash
# Old way (deprecated):
GET /api/pegawai/{pegawaiId}/tanda-tangan

# New way (recommended):
GET /api/pegawai/{id}
# Response akan include field: tandaTangan { id, url_tanda_tangan }
```

---

### Create Employee Signature Endpoint

```text
POST /api/tanda-tangan
```

### Create Employee Signature Request Body

Gunakan `multipart/form-data` dengan parameter:

- `tanda_tangan` (file, required) - File gambar tanda tangan (PNG, JPG, GIF)

**Note:** Field `pegawai_id` dan `deskripsi` sudah tidak digunakan. Signature disimpan secara independen.

### Create Employee Signature Request Example

```bash
curl -X POST "http://localhost:8000/api/tanda-tangan" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "tanda_tangan=@/path/to/signature.png"
```

### Create Employee Signature Response Example

```json
{
  "status": "success",
  "message": "Signature created",
  "data": {
    "id": 15,
    "url_tanda_tangan": "/uploads/signatures/sig_654def_signature.png",
    "created_at": "2025-12-30T10:15:00Z",
    "updated_at": "2025-12-30T10:15:00Z"
  }
}
```

**Next Step:** Gunakan signature ID (15) untuk assign ke pegawai:

```bash
curl -X PUT "http://localhost:8000/api/pegawai/P001" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"tanda_tangan_id": 15}'
```

### Update Employee Signature Endpoint

```text
PUT /api/tanda-tangan/{id}
```

### Update Employee Signature Request Body

Gunakan `multipart/form-data` dengan parameter:

- `tanda_tangan` (file, optional) - File gambar tanda tangan baru

**Note:** Update hanya menerima file baru. File lama akan dihapus otomatis.

### Update Employee Signature Request Example

```bash
curl -X PUT "http://localhost:8000/api/tanda-tangan/15" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "tanda_tangan=@/path/to/new_signature.png"
```

### Update Employee Signature Response Example

```json
{
  "status": "success",
  "message": "Signature updated",
  "data": {
    "id": 15,
    "url_tanda_tangan": "/uploads/signatures/sig_789ghi_signature.png",
    "created_at": "2025-12-30T10:15:00Z",
    "updated_at": "2025-12-30T10:20:00Z"
  }
}
```

### Delete Employee Signature Endpoint

```text
DELETE /api/tanda-tangan/{id}
```

### Delete Employee Signature Request Example

```bash
curl -X DELETE "http://localhost:8000/api/tanda-tangan/15" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Delete Employee Signature Response Example

```json
{
  "status": "success",
  "message": "Signature deleted",
  "data": []
}
```

**Note:** File signature akan dihapus otomatis dari server. Pegawai yang menggunakan signature ini akan memiliki `tanda_tangan_id = NULL`.

---

### Employee Signature Integration Workflow

#### Workflow 1: Create Pegawai with Signature

**Step 1:** Upload signature file
```bash
curl -X POST "http://localhost:8000/api/tanda-tangan" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "tanda_tangan=@signature.png"
# Response: { "data": { "id": 15 } }
```

**Step 2:** Create pegawai with signature ID
```bash
curl -X POST "http://localhost:8000/api/pegawai" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Ahmad Budi",
    "email": "ahmad@example.com",
    "tanda_tangan_id": 15
  }'
```

#### Workflow 2: Assign Signature to Existing Pegawai

**Step 1:** Upload signature
```bash
POST /api/tanda-tangan
# Response: { "data": { "id": 15 } }
```

**Step 2:** Update pegawai
```bash
curl -X PUT "http://localhost:8000/api/pegawai/P001" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"tanda_tangan_id": 15}'
```

#### Workflow 3: Legacy - Direct Upload (Backward Compatible)

```bash
curl -X POST "http://localhost:8000/api/pegawai" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "nama=Ahmad Budi" \
  -F "tanda_tangan=@signature.png"
# File tersimpan di pegawai.tanda_tangan (VARCHAR)
# Tidak create record di tabel tanda_tangan
```

### Employee Signature Field Descriptions

- `id` - Unique identifier untuk tanda tangan (INT, auto-increment)
- `url_tanda_tangan` - URL path ke file gambar tanda tangan (VARCHAR 255)
- `created_at` - Timestamp pembuatan record
- `updated_at` - Timestamp update terakhir

**Relasi:**
- Pegawai dapat reference signature via `pegawai.tanda_tangan_id`
- Satu signature dapat digunakan oleh banyak pegawai (reusable)
- Eager load: `GET /api/pegawai/{id}` include relasi `tandaTangan`

---

## Time Offs

API untuk mengelola cuti karyawan.

### Time Off Types

- `annual_leave` - Cuti tahunan
- `sick_leave` - Cuti sakit
- `unpaid_leave` - Cuti tanpa gaji
- `maternity_leave` - Cuti melahirkan
- `paternity_leave` - Cuti ayah
- `other` - Lainnya

### Time Off Status

- `pending` - Menunggu persetujuan
- `approved` - Disetujui
- `rejected` - Ditolak
- `cancelled` - Dibatalkan

### Time Offs List Endpoint

```text
GET /api/timeoffs
```

### Time Offs List Query Parameters

| Parameter    | Type       | Required | Default | Description                     |
|--------------|------------|----------|---------|---------------------------------|
| employee_id  | integer    | No       | -       | Filter by employee              |
| status       | string     | No       | -       | Filter by status                |
| type         | string     | No       | -       | Filter by type                  |
| start_date   | date       | No       | -       | Filter by start date (>=)       |
| end_date     | date       | No       | -       | Filter by end date (<=)         |

### Time Offs List Request Example

```bash
curl -X GET "http://localhost:8000/api/timeoffs?employee_id=1&status=pending" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Time Offs List Response Example

```json
{
  "status": "success",
  "message": "Time offs retrieved successfully",
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "type": "annual_leave",
      "start_date": "2024-12-25",
      "end_date": "2024-12-27",
      "total_days": 3,
      "reason": "Liburan akhir tahun",
      "status": "pending",
      "approved_by": null,
      "approved_at": null,
      "notes": null,
      "created_at": "2024-12-20T10:00:00.000000Z",
      "updated_at": "2024-12-20T10:00:00.000000Z",
      "employee": {
        "id": 1,
        "name": "Ahmad Budi",
        "email": "ahmad.budi@bengkel.com"
      },
      "approver": null
    }
  ]
}
```

### Get Time Off Detail Endpoint

```text
GET /api/timeoffs/{id}
```

### Create Time Off Request Endpoint

```text
POST /api/timeoffs
```

### Create Time Off Request Body

```json
{
  "employee_id": 1,
  "type": "annual_leave",
  "start_date": "2024-12-25",
  "end_date": "2024-12-27",
  "reason": "Liburan akhir tahun"
}
```

### Create Time Off Response Example

```json
{
  "status": "success",
  "message": "Time off request created successfully",
  "data": {
    "id": 1,
    "employee_id": 1,
    "type": "annual_leave",
    "start_date": "2024-12-25",
    "end_date": "2024-12-27",
    "total_days": 3,
    "reason": "Liburan akhir tahun",
    "status": "pending",
    "approved_by": null,
    "approved_at": null,
    "notes": null,
    "created_at": "2024-12-30T10:00:00.000000Z",
    "updated_at": "2024-12-30T10:00:00.000000Z",
    "employee": {
      "id": 1,
      "name": "Ahmad Budi",
      "email": "ahmad.budi@bengkel.com"
    },
    "approver": null
  }
}
```

### Update Time Off Endpoint

```text
PUT /api/timeoffs/{id}
```

### Approve Time Off Endpoint

```text
POST /api/timeoffs/{id}/approve
```

### Approve Time Off Request Body

```json
{
  "approved_by": 2,
  "notes": "Disetujui"
}
```

### Approve Time Off Response Example

```json
{
  "status": "success",
  "message": "Time off approved successfully",
  "data": {
    "id": 1,
    "employee_id": 1,
    "type": "annual_leave",
    "start_date": "2024-12-25",
    "end_date": "2024-12-27",
    "total_days": 3,
    "reason": "Liburan akhir tahun",
    "status": "approved",
    "approved_by": 2,
    "approved_at": "2024-12-30T11:00:00.000000Z",
    "notes": "Disetujui",
    "created_at": "2024-12-30T10:00:00.000000Z",
    "updated_at": "2024-12-30T11:00:00.000000Z",
    "employee": {
      "id": 1,
      "name": "Ahmad Budi"
    },
    "approver": {
      "id": 2,
      "name": "Budi Santoso"
    }
  }
}
```

### Reject Time Off Endpoint

```text
POST /api/timeoffs/{id}/reject
```

### Reject Time Off Request Body

```json
{
  "approved_by": 2,
  "notes": "Ditolak karena jadwal padat"
}
```

### Cancel Time Off Endpoint

```text
POST /api/timeoffs/{id}/cancel
```

### Delete Time Off Endpoint

```text
DELETE /api/timeoffs/{id}
```

---

## Attendances

API untuk mengelola kehadiran karyawan.

### Attendance Status

- `present` - Hadir
- `absent` - Tidak hadir
- `late` - Terlambat
- `half_day` - Setengah hari
- `on_leave` - Cuti
- `sick` - Sakit
- `holiday` - Libur

### Attendances List Endpoint

```text
GET /api/attendances
```

### Attendances List Query Parameters

| Parameter    | Type    | Required | Default | Description                     |
|--------------|---------|----------|---------|---------------------------------|
| employee_id  | integer | No       | -       | Filter by employee              |
| start_date   | date    | No       | -       | Filter by date range (>=)       |
| end_date     | date    | No       | -       | Filter by date range (<=)       |
| status       | string  | No       | -       | Filter by status                |
| date         | date    | No       | -       | Filter by specific date         |

### Attendances List Request Example

```bash
curl -X GET "http://localhost:8000/api/attendances?employee_id=1&start_date=2024-12-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Attendances List Response Example

```json
{
  "status": "success",
  "message": "Attendances retrieved successfully",
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "date": "2024-12-30",
      "check_in": "2024-12-30T08:00:00.000000Z",
      "check_out": "2024-12-30T17:00:00.000000Z",
      "status": "present",
      "work_hours": 9.00,
      "overtime_hours": 0.00,
      "notes": null,
      "location": "Jakarta Office",
      "check_in_photo": "/uploads/attendance/checkin_123_photo.jpg",
      "check_out_photo": "/uploads/attendance/checkout_123_photo.jpg",
      "created_at": "2024-12-30T08:00:00.000000Z",
      "updated_at": "2024-12-30T17:00:00.000000Z",
      "employee": {
        "id": 1,
        "name": "Ahmad Budi",
        "email": "ahmad.budi@bengkel.com"
      }
    }
  ]
}
```

### Get Attendance Detail Endpoint

```text
GET /api/attendances/{id}
```

### Get Attendance Summary Endpoint

```text
GET /api/attendances/summary/employee
```

### Get Attendance Summary Query Parameters

| Parameter    | Type    | Required | Description                     |
|--------------|---------|----------|---------------------------------|
| employee_id  | integer | Yes      | Employee ID                     |
| month        | integer | No       | Month (1-12), default: current  |
| year         | integer | No       | Year, default: current          |

### Get Attendance Summary Request Example

```bash
curl -X GET "http://localhost:8000/api/attendances/summary/employee?employee_id=1&month=12&year=2024" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Get Attendance Summary Response Example

```json
{
  "status": "success",
  "message": "Attendance summary retrieved successfully",
  "data": {
    "total_days": 22,
    "present": 20,
    "absent": 0,
    "late": 2,
    "half_day": 0,
    "on_leave": 2,
    "sick": 0,
    "total_work_hours": 180.50,
    "total_overtime_hours": 5.00
  }
}
```

### Check In Endpoint

```text
POST /api/attendances/checkin
```

### Check In Request Body (JSON)

```json
{
  "employee_id": 1,
  "location": "Jakarta Office",
  "notes": "Check in pagi"
}
```

### Check In with Photo Request (Multipart Form-Data)

```bash
curl -X POST "http://localhost:8000/api/attendances/checkin" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "employee_id=1" \
  -F "location=Jakarta Office" \
  -F "notes=Check in pagi" \
  -F "photo=@/path/to/checkin_photo.jpg"
```

### Check In Response Example

```json
{
  "status": "success",
  "message": "Check in successful",
  "data": {
    "id": 1,
    "employee_id": 1,
    "date": "2024-12-30",
    "check_in": "2024-12-30T08:05:00.000000Z",
    "check_out": null,
    "status": "present",
    "work_hours": null,
    "overtime_hours": null,
    "notes": "Check in pagi",
    "location": "Jakarta Office",
    "check_in_photo": "/uploads/attendance/checkin_456_photo.jpg",
    "check_out_photo": null,
    "created_at": "2024-12-30T08:05:00.000000Z",
    "updated_at": "2024-12-30T08:05:00.000000Z",
    "employee": {
      "id": 1,
      "name": "Ahmad Budi",
      "email": "ahmad.budi@bengkel.com"
    }
  }
}
```

### Check Out Endpoint

```text
POST /api/attendances/{id}/checkout
```

### Check Out Request Body (JSON)

```json
{
  "notes": "Check out sore",
  "overtime_hours": 1.5
}
```

### Check Out with Photo Request (Multipart Form-Data)

```bash
curl -X POST "http://localhost:8000/api/attendances/1/checkout" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "notes=Check out sore" \
  -F "overtime_hours=1.5" \
  -F "photo=@/path/to/checkout_photo.jpg"
```

### Check Out Response Example

```json
{
  "status": "success",
  "message": "Check out successful",
  "data": {
    "id": 1,
    "employee_id": 1,
    "date": "2024-12-30",
    "check_in": "2024-12-30T08:05:00.000000Z",
    "check_out": "2024-12-30T17:10:00.000000Z",
    "status": "present",
    "work_hours": 9.08,
    "overtime_hours": 1.50,
    "notes": "Check out sore",
    "location": "Jakarta Office",
    "check_in_photo": "/uploads/attendance/checkin_456_photo.jpg",
    "check_out_photo": "/uploads/attendance/checkout_789_photo.jpg",
    "created_at": "2024-12-30T08:05:00.000000Z",
    "updated_at": "2024-12-30T17:10:00.000000Z",
    "employee": {
      "id": 1,
      "name": "Ahmad Budi",
      "email": "ahmad.budi@bengkel.com"
    }
  }
}
```

### Create Manual Attendance Endpoint

```text
POST /api/attendances
```

### Create Manual Attendance Request Body

```json
{
  "employee_id": 1,
  "date": "2024-12-29",
  "check_in": "2024-12-29 08:00:00",
  "check_out": "2024-12-29 17:00:00",
  "status": "present",
  "location": "Jakarta Office",
  "notes": "Manual entry"
}
```

### Update Attendance Endpoint

```text
PUT /api/attendances/{id}
```

### Delete Attendance Endpoint

```text
DELETE /api/attendances/{id}
```

---

## General Notes

### Authentication

Semua endpoint memerlukan JWT token yang valid. Sertakan token di header:

```text
Authorization: Bearer YOUR_JWT_TOKEN
```

### Date Format

Semua tanggal menggunakan format ISO 8601:

- Date: `YYYY-MM-DD` (e.g., `2024-12-30`)
- DateTime: `YYYY-MM-DD HH:MM:SS` (e.g., `2024-12-30 08:00:00`)

### Response Format

Semua response menggunakan format JSON standar:

```json
{
  "status": "success" | "error",
  "message": "Response message",
  "data": {}
}
```

### Error Handling

Error responses akan mengembalikan HTTP status code yang sesuai:

- `400 Bad Request` - Parameter tidak valid
- `401 Unauthorized` - Token tidak valid atau expired
- `404 Not Found` - Resource tidak ditemukan
- `500 Internal Server Error` - Error server

### File Upload

Untuk endpoint yang mendukung upload file (foto):

1. Gunakan `Content-Type: multipart/form-data`
2. Field name untuk foto: `photo`
3. Format yang didukung: JPG, JPEG, PNG
4. Maksimal ukuran file: 5MB
5. Foto disimpan di folder `public/uploads/`

### Photo Storage

**Employee Photos:**

- Lokasi: `public/uploads/employees/`
- Format nama: `emp_[uniqid]_[original_filename]`

**Attendance Photos:**

- Lokasi: `public/uploads/attendance/`
- Format nama: `checkin_[uniqid]_[filename]` atau `checkout_[uniqid]_[filename]`

---

## Quick Reference

### Group API Endpoints

| Method | Endpoint           | Description        |
|--------|--------------------|--------------------|
| GET    | `/api/groups`      | List all groups    |
| GET    | `/api/groups/{id}` | Get group detail   |
| POST   | `/api/groups`      | Create group       |
| PUT    | `/api/groups/{id}` | Update group       |
| DELETE | `/api/groups/{id}` | Delete group       |

### Position API Endpoints

| Method | Endpoint                | Description          |
|--------|-------------------------|----------------------|
| GET    | `/api/positions`        | List all positions   |
| GET    | `/api/positions/{id}`   | Get position detail  |
| POST   | `/api/positions`        | Create position      |
| PUT    | `/api/positions/{id}`   | Update position      |
| DELETE | `/api/positions/{id}`   | Delete position      |

### Department API Endpoints

| Method | Endpoint                  | Description            |
|--------|---------------------------|------------------------|
| GET    | `/api/departments`        | List all departments   |
| GET    | `/api/departments/{id}`   | Get department detail  |
| POST   | `/api/departments`        | Create department      |
| PUT    | `/api/departments/{id}`   | Update department      |
| DELETE | `/api/departments/{id}`   | Delete department      |

### Employee API Endpoints

| Method | Endpoint                | Description                 |
|--------|-------------------------|-----------------------------|
| GET    | `/api/employees`        | List all employees          |
| GET    | `/api/employees/{id}`   | Get employee detail         |
| POST   | `/api/employees`        | Create employee             |
| PUT    | `/api/employees/{id}`   | Update employee             |
| POST   | `/api/employees/{id}`   | Update with files           |
| DELETE | `/api/employees/{id}`   | Delete employee             |
| GET    | `/api/pegawai`          | List pegawai (alt)          |
| GET    | `/api/pegawai/{id}`     | Get pegawai detail (alt)    |
| POST   | `/api/pegawai`          | Create pegawai (alt)        |
| PUT    | `/api/pegawai/{id}`     | Update pegawai (alt)        |
| POST   | `/api/pegawai/{id}`     | Update pegawai with files   |
| DELETE | `/api/pegawai/{id}`     | Delete pegawai (alt)        |

### Employee Signature API Endpoints

| Method | Endpoint                               | Description          |
|--------|----------------------------------------|----------------------|
| GET    | `/api/tanda-tangan`                    | List all signatures  |
| GET    | `/api/tanda-tangan/{id}`               | Get signature detail |
| GET    | `/api/pegawai/{pegawaiId}/tanda-tangan`| Get by employee      |
| POST   | `/api/tanda-tangan`                    | Create signature     |
| PUT    | `/api/tanda-tangan/{id}`               | Update signature     |
| DELETE | `/api/tanda-tangan/{id}`               | Delete signature     |

### Time Off API Endpoints

| Method | Endpoint                       | Description              |
|--------|--------------------------------|--------------------------|
| GET    | `/api/timeoffs`                | List all time offs       |
| GET    | `/api/timeoffs/{id}`           | Get time off detail      |
| POST   | `/api/timeoffs`                | Create time off request  |
| PUT    | `/api/timeoffs/{id}`           | Update time off          |
| POST   | `/api/timeoffs/{id}/approve`   | Approve time off         |
| POST   | `/api/timeoffs/{id}/reject`    | Reject time off          |
| POST   | `/api/timeoffs/{id}/cancel`    | Cancel time off          |
| DELETE | `/api/timeoffs/{id}`           | Delete time off          |

### Attendance API Endpoints

| Method | Endpoint                            | Description                  |
|--------|-------------------------------------|------------------------------|
| GET    | `/api/attendances`                  | List all attendances         |
| GET    | `/api/attendances/{id}`             | Get attendance detail        |
| GET    | `/api/attendances/summary/employee` | Get attendance summary       |
| POST   | `/api/attendances/checkin`          | Check in                     |
| POST   | `/api/attendances/{id}/checkout`    | Check out                    |
| POST   | `/api/attendances`                  | Create manual attendance     |
| PUT    | `/api/attendances/{id}`             | Update attendance            |
| DELETE | `/api/attendances/{id}`             | Delete attendance            |

---

## Example Workflows

### Workflow 1: Onboarding Karyawan Baru

**Step 1:** Create Position (jika belum ada)

```bash
curl -X POST http://localhost:8000/api/positions \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Teknisi AC","description":"Teknisi pemasangan AC","is_active":true}'
```

**Step 2:** Create Department (jika belum ada)

```bash
curl -X POST http://localhost:8000/api/departments \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Technical","description":"Departemen teknis","is_active":true}'
```

**Step 3:** Create Employee dengan foto

```bash
curl -X POST http://localhost:8000/api/employees \
  -H "Authorization: Bearer TOKEN" \
  -F "name=Ahmad Budi" \
  -F "email=ahmad@bengkel.com" \
  -F "phone=08123456789" \
  -F "address=Jakarta" \
  -F "department_id=1" \
  -F "position_id=1" \
  -F "hire_date=2024-01-01" \
  -F "is_active=true" \
  -F "photo=@photo.jpg"
```

### Workflow 2: Proses Cuti

**Step 1:** Karyawan mengajukan cuti

```bash
curl -X POST http://localhost:8000/api/timeoffs \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id":1,
    "type":"annual_leave",
    "start_date":"2024-12-25",
    "end_date":"2024-12-27",
    "reason":"Liburan keluarga"
  }'
```

**Step 2:** Supervisor menyetujui cuti

```bash
curl -X POST http://localhost:8000/api/timeoffs/1/approve \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "approved_by":2,
    "notes":"Disetujui"
  }'
```

### Workflow 3: Absensi Harian

**Step 1:** Check in pagi

```bash
curl -X POST http://localhost:8000/api/attendances/checkin \
  -H "Authorization: Bearer TOKEN" \
  -F "employee_id=1" \
  -F "location=Jakarta Office" \
  -F "photo=@checkin_photo.jpg"
```

**Step 2:** Check out sore

```bash
curl -X POST http://localhost:8000/api/attendances/1/checkout \
  -H "Authorization: Bearer TOKEN" \
  -F "overtime_hours=1.5" \
  -F "photo=@checkout_photo.jpg"
```

**Step 3:** Lihat summary attendance bulan ini

```bash
curl -X GET "http://localhost:8000/api/attendances/summary/employee?employee_id=1&month=12&year=2024" \
  -H "Authorization: Bearer TOKEN"
```

---

## Support

Untuk pertanyaan atau bantuan teknis, silakan hubungi tim developer atau buka issue di repository project.
