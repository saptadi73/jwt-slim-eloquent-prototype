# API Documentation - Product, Brand, Kategori, Satuan CRUD

## Overview

Dokumentasi lengkap untuk CRUD operations pada Product, Brand, Kategori, dan Satuan.

---

## 1. PRODUCT CRUD

### Base URL

```text
http://localhost:8080/products
```

### 1.1 List Products

**Endpoint:** `GET /products`

**Description:** Mendapatkan daftar semua produk

**Response:**

```json
{
  "status": true,
  "message": "Data produk",
  "data": [
    {
      "id": "uuid-string",
      "nama": "Laptop Dell",
      "gambar": "/uploads/products/abc123_20251214_120530.jpg",
      "deskripsi": "Laptop gaming high performance",
      "kode": "LAP001",
      "type": "Elektronik",
      "harga": 15000000,
      "hpp": 12000000,
      "stok": 10,
      "model": "XPS 15",
      "kategori_id": "uuid-kategori",
      "satuan_id": "uuid-satuan",
      "brand_id": "uuid-brand",
      "is_sealable": true,
      "created_at": "2025-12-14T12:00:00.000000Z",
      "updated_at": "2025-12-14T12:00:00.000000Z",
      "kategori": {
        "id": "uuid",
        "nama": "Elektronik"
      },
      "satuan": {
        "id": "uuid",
        "nama": "Pcs"
      },
      "brand": {
        "id": "uuid",
        "nama": "Dell"
      }
    }
  ]
}
```

---

### 1.2 Get Product Detail

**Endpoint:** `GET /products/{id}`

**Description:** Mendapatkan detail satu produk

**URL Parameters:**

- `id` (required): UUID produk

**Response:** Same as single product object in list

---

### 1.3 Create Product

**Endpoint:** `POST /products`

**Description:** Membuat produk baru

**Content-Type:** `multipart/form-data`

**Request Body:**

```text
nama: Laptop Dell (required)
deskripsi: Laptop gaming high performance
kode: LAP001
type: Elektronik
harga: 15000000
hpp: 12000000
stok: 10
model: XPS 15
kategori_id: uuid-kategori (optional)
satuan_id: uuid-satuan (optional)
brand_id: uuid-brand (optional)
is_sealable: true
gambar: file (optional - jpg, png, gif, webp max 5MB)
```

**Response:**

```json
{
  "status": true,
  "message": "Produk dibuat",
  "data": {
    "product": {
      "id": "uuid-baru",
      "nama": "Laptop Dell",
      "gambar": "/uploads/products/abc123_20251214_120530.jpg",
      "deskripsi": "Laptop gaming high performance",
      "type": "Elektronik",
      "harga": 15000000,
      "hpp": 12000000,
      "stok": 10,
      "model": "XPS 15",
      "kategori_id": "uuid-kategori",
      "satuan_id": "uuid-satuan",
      "brand_id": "uuid-brand",
      "is_sealable": true,
      "created_at": "2025-12-14T12:00:00.000000Z",
      "updated_at": "2025-12-14T12:00:00.000000Z"
    },
    "debug": {
      "file_present": true,
      "file_error": 0,
      "saved_path": "/uploads/products/abc123_20251214_120530.jpg"
    }
  }
}
```

**Error Response:**

```json
{
  "status": false,
  "message": "Nama produk wajib diisi",
  "code": 422
}
```

---

### 1.4 Update Product

**Endpoint:** `POST /products/{id}` (atau `PUT /products/{id}`)

**Description:** Mengupdate produk yang sudah ada

**Content-Type:** `multipart/form-data`

**URL Parameters:**

- `id` (required): UUID produk

**Request Body:** (semua field optional, hanya kirim yang ingin diubah)

```text
nama: Laptop Dell Updated
deskripsi: Laptop gaming updated
harga: 16000000
gambar: file (optional - jika ada, file lama akan dihapus)
```

**Response:**

```json
{
  "status": true,
  "message": "Produk diperbarui",
  "data": {
    "product": {
      "nama": "Laptop Dell Updated",
      "gambar": "/uploads/products/newfile_20251214_120530.jpg",
      "harga": 16000000,
      "updated_at": "2025-12-14T13:00:00.000000Z"
    },
    "debug": {
      "file_present": true,
      "file_error": 0,
      "saved_path": "/uploads/products/newfile_20251214_120530.jpg"
    }
  }
}
```

---

### 1.5 Delete Product

**Endpoint:** `DELETE /products/{id}`

**Description:** Menghapus produk dan file gambar-nya

**URL Parameters:**

- `id` (required): UUID produk

**Response:**

```json
{
  "status": true,
  "message": "Produk dihapus",
  "data": null
}
```

**Error Response:**

```json
{
  "status": false,
  "message": "Produk tidak ditemukan",
  "code": 404
}
```

---

## 2. BRAND CRUD

### Base URL

```text
http://localhost:8080/brands
```

### 2.1 List Brands

**Endpoint:** `GET /brands`

**Response:**

```json
{
  "status": true,
  "message": "Data brand",
  "data": [
    {
      "id": "uuid",
      "nama": "Dell",
      "created_at": "2025-12-14T10:00:00.000000Z",
      "updated_at": "2025-12-14T10:00:00.000000Z",
      "products": []
    }
  ]
}
```

---

### 2.2 Get Brand Detail

**Endpoint:** `GET /brands/{id}`

**URL Parameters:**

- `id` (required): UUID brand

---

### 2.3 Create Brand

**Endpoint:** `POST /brands`

**Content-Type:** `application/json` atau `application/x-www-form-urlencoded`

**Request Body:**

```json
{
  "nama": "Dell"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Brand dibuat",
  "data": {
    "id": "uuid-baru",
    "nama": "Dell",
    "created_at": "2025-12-14T12:00:00.000000Z",
    "updated_at": "2025-12-14T12:00:00.000000Z"
  }
}
```

---

### 2.4 Update Brand

**Endpoint:** `POST /brands/{id}` (atau `PUT /brands/{id}`)

**Request Body:**

```json
{
  "nama": "Dell Inc."
}
```

**Response:**

```json
{
  "status": true,
  "message": "Brand diperbarui",
  "data": {
    "id": "uuid",
    "nama": "Dell Inc.",
    "updated_at": "2025-12-14T13:00:00.000000Z"
  }
}
```

---

### 2.5 Delete Brand

**Endpoint:** `DELETE /brands/{id}`

**Response:**

```json
{
  "status": true,
  "message": "Brand dihapus",
  "data": null
}
```

---

## 3. KATEGORI CRUD

### Base URL

```text
http://localhost:8080/kategoris
```

### 3.1 List Kategoris

**Endpoint:** `GET /kategoris`

**Response:**

```json
{
  "status": true,
  "message": "Data kategori",
  "data": [
    {
      "id": "uuid",
      "nama": "Elektronik",
      "created_at": "2025-12-14T10:00:00.000000Z",
      "updated_at": "2025-12-14T10:00:00.000000Z",
      "products": []
    }
  ]
}
```

---

### 3.2 Get Kategori Detail

**Endpoint:** `GET /kategoris/{id}`

**URL Parameters:**

- `id` (required): UUID kategori

---

### 3.3 Create Kategori

**Endpoint:** `POST /kategoris`

**Content-Type:** `application/json` atau `application/x-www-form-urlencoded`

**Request Body:**

```json
{
  "nama": "Elektronik"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Kategori dibuat",
  "data": {
    "id": "uuid-baru",
    "nama": "Elektronik",
    "created_at": "2025-12-14T12:00:00.000000Z",
    "updated_at": "2025-12-14T12:00:00.000000Z"
  }
}
```

---

### 3.4 Update Kategori

**Endpoint:** `POST /kategoris/{id}` (atau `PUT /kategoris/{id}`)

**Request Body:**

```json
{
  "nama": "Elektronik & Gadget"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Kategori diperbarui",
  "data": {
    "id": "uuid",
    "nama": "Elektronik & Gadget",
    "updated_at": "2025-12-14T13:00:00.000000Z"
  }
}
```

---

### 3.5 Delete Kategori

**Endpoint:** `DELETE /kategoris/{id}`

**Response:**

```json
{
  "status": true,
  "message": "Kategori dihapus",
  "data": null
}
```

---

## 4. SATUAN CRUD

### Base URL

```text
http://localhost:8080/satuans
```

### 4.1 List Satuans

**Endpoint:** `GET /satuans`

**Response:**

```json
{
  "status": true,
  "message": "Data satuan",
  "data": [
    {
      "id": "uuid",
      "nama": "Pcs",
      "created_at": "2025-12-14T10:00:00.000000Z",
      "updated_at": "2025-12-14T10:00:00.000000Z",
      "products": []
    }
  ]
}
```

---

### 4.2 Get Satuan Detail

**Endpoint:** `GET /satuans/{id}`

**URL Parameters:**

- `id` (required): UUID satuan

---

### 4.3 Create Satuan

**Endpoint:** `POST /satuans`

**Content-Type:** `application/json` atau `application/x-www-form-urlencoded`

**Request Body:**

```json
{
  "nama": "Pcs"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Satuan dibuat",
  "data": {
    "id": "uuid-baru",
    "nama": "Pcs",
    "created_at": "2025-12-14T12:00:00.000000Z",
    "updated_at": "2025-12-14T12:00:00.000000Z"
  }
}
```

---

### 4.4 Update Satuan

**Endpoint:** `POST /satuans/{id}` (atau `PUT /satuans/{id}`)

**Request Body:**

```json
{
  "nama": "Pieces"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Satuan diperbarui",
  "data": {
    "id": "uuid",
    "nama": "Pieces",
    "updated_at": "2025-12-14T13:00:00.000000Z"
  }
}
```

---

### 4.5 Delete Satuan

**Endpoint:** `DELETE /satuans/{id}`

**Response:**

```json
{
  "status": true,
  "message": "Satuan dihapus",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Status | Meaning |
|------|--------|---------|
| 200 | OK | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 400 | Bad Request | Upload gagal atau error lainnya |
| 404 | Not Found | Resource tidak ditemukan |
| 422 | Unprocessable Entity | Validasi gagal |
| 500 | Internal Server Error | Error server |

---

## Common Error Responses

### Validation Error

```json
{
  "status": false,
  "message": "Nama produk wajib diisi",
  "code": 422
}
```

### Not Found Error

```json
{
  "status": false,
  "message": "Produk tidak ditemukan",
  "code": 404
}
```

### Upload Error

```json
{
  "status": false,
  "message": "Upload gagal: File too large (max 5 MB)",
  "code": 400
}
```

### Server Error

```json
{
  "status": false,
  "message": "Error message from exception",
  "type": "Exception class",
  "code": 500
}
```

---

## cURL Examples

### Create Product with Image

```bash
curl -X POST http://localhost:8080/products \
  -F "nama=Laptop Dell" \
  -F "harga=15000000" \
  -F "stok=10" \
  -F "gambar=@/path/to/image.jpg"
```

### Update Product

```bash
curl -X POST http://localhost:8080/products/{id} \
  -F "nama=Laptop Dell Updated" \
  -F "harga=16000000" \
  -F "gambar=@/path/to/newimage.jpg"
```

### Create Brand (JSON)

```bash
curl -X POST http://localhost:8080/brands \
  -H "Content-Type: application/json" \
  -d '{"nama": "Dell"}'
```

### List Products

```bash
curl -X GET http://localhost:8080/products
```

### Delete Product

```bash
curl -X DELETE http://localhost:8080/products/{id}
```

---

## 5. WORKORDER SERVICE API

### Base URL

```text
http://localhost:8080/wo
```

### 5.1 Get WorkOrder History by Customer Asset ID

**Endpoint:** `GET /wo/service/history/asset/{customer_asset_id}`

**Description:** Mendapatkan history semua workorder service berdasarkan customer asset ID, diurutkan dari terbaru

**URL Parameters:**

- `customer_asset_id` (required): UUID dari customer asset

**Response:**

```json
{
  "status": true,
  "message": "Berhasil mengambil history workorder service",
  "data": [
    {
      "id": "uuid-string",
      "workorder_id": "uuid-workorder",
      "customer_asset_id": "uuid-customer-asset",
      "teknisi_id": "uuid-teknisi",
      "keluhan": "AC tidak dingin",
      "keterangan": "Sudah dicek dan diperbaiki",
      "status": "selesai",
      "nowo": "WO-00001",
      "customerCode": "CUS123456",
      "tanda_tangan_pelanggan": "/uploads/signatures/abc123.jpg",
      "check_evaporator": true,
      "keterangan_evaporator": "Normal",
      "check_fan_indoor": true,
      "keterangan_fan_indoor": "Berputar lancar",
      "created_at": "2026-01-20T10:00:00.000000Z",
      "updated_at": "2026-01-20T15:30:00.000000Z",
      "customer_asset": {
        "id": "uuid",
        "kode_ac": "AC-001",
        "customer_id": "uuid-customer",
        "brand_id": "uuid-brand",
        "tipe_id": "uuid-tipe",
        "customer": {
          "id": "uuid",
          "nama": "PT. ABC",
          "alamat": "Jl. Sudirman No. 123"
        },
        "brand": {
          "id": "uuid",
          "nama": "Daikin"
        },
        "tipe": {
          "id": "uuid",
          "nama": "Split 1 PK"
        }
      },
      "pegawai": {
        "id": "uuid",
        "nama": "John Doe",
        "nip": "12345"
      }
    }
  ]
}
```

**Success Response (No History):**

```json
{
  "status": true,
  "message": "Tidak ada history workorder untuk asset ini",
  "data": []
}
```

**Error Response:**

```json
{
  "status": false,
  "message": "Error message",
  "data": null
}
```

**Example cURL:**

```bash
curl -X GET http://localhost:8080/wo/service/history/asset/uuid-customer-asset-id
```

---

## 6. CUSTOMER ASSETS API

### Base URL

```text
http://localhost:8080/customers
```

### 6.1 Update Next Service Date

**Endpoint:** `POST /customers/assets/update-next-service/{id}`

**Description:** Mengupdate tanggal next service untuk customer asset tertentu

**Authentication:** Required (JWT Token)

**URL Parameters:**

- `id` (required): UUID dari customer asset

**Request Body:**

```json
{
  "nextService": "2026-02-20"
}
```

**Response Success:**

```json
{
  "status": true,
  "message": "Next service date updated successfully",
  "data": {
    "id": "uuid-customer-asset",
    "customer_id": "uuid-customer",
    "brand_id": "uuid-brand",
    "tipe_id": "uuid-tipe",
    "model": "Inverter",
    "kapasitas": "1 PK",
    "freon": "R32",
    "lokasi": "Ruang Tamu",
    "keterangan": "AC Normal",
    "lastService": "2026-01-15",
    "nextService": "2026-02-20",
    "status": "aktif",
    "gambar": "/uploads/assets/abc123.jpg",
    "created_at": "2026-01-10T10:00:00.000000Z",
    "updated_at": "2026-01-20T10:30:00.000000Z"
  }
}
```

**Error Response (Asset Not Found):**

```json
{
  "status": false,
  "message": "Customer Asset not found",
  "data": null
}
```

**Error Response (Missing Field):**

```json
{
  "status": false,
  "message": "nextService field is required",
  "data": null
}
```

**Example cURL:**

```bash
curl -X POST http://localhost:8080/customers/assets/update-next-service/uuid-customer-asset-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"nextService": "2026-02-20"}'
```

---

## 7. KELUHAN CRUD

### Base URL

```text
http://localhost:8080/keluhan
```

### 7.1 List Keluhan

**Endpoint:** `GET /keluhan`

**Description:** Mendapatkan daftar semua keluhan (ordered by newest)

**Authentication:** Not required

**Response:**

```json
{
  "status": true,
  "message": "List of all keluhan",
  "data": [
    {
      "id": "uuid-string",
      "nama": "AC Tidak Dingin",
      "created_at": "2026-01-20T12:00:00.000000Z",
      "updated_at": "2026-01-20T12:00:00.000000Z"
    },
    {
      "id": "uuid-string",
      "nama": "AC Bocor",
      "created_at": "2026-01-19T10:00:00.000000Z",
      "updated_at": "2026-01-19T10:00:00.000000Z"
    }
  ]
}
```

---

### 7.2 Get Keluhan Detail

**Endpoint:** `GET /keluhan/{id}`

**Description:** Mendapatkan detail satu keluhan

**Authentication:** Not required

**URL Parameters:**

- `id` (required): UUID keluhan

**Response:**

```json
{
  "status": true,
  "message": "Keluhan detail",
  "data": {
    "id": "uuid-string",
    "nama": "AC Tidak Dingin",
    "created_at": "2026-01-20T12:00:00.000000Z",
    "updated_at": "2026-01-20T12:00:00.000000Z"
  }
}
```

**Error Response (Not Found):**

```json
{
  "status": false,
  "message": "Keluhan not found",
  "data": null
}
```

---

### 7.3 Create Keluhan

**Endpoint:** `POST /keluhan`

**Description:** Membuat keluhan baru

**Authentication:** 🔒 JWT Token required

**Content-Type:** `application/json`

**Request Body:**

```json
{
  "nama": "AC Berbunyi Berisik"
}
```

**Response Success:**

```json
{
  "status": true,
  "message": "Keluhan created successfully",
  "data": {
    "id": "uuid-string",
    "nama": "AC Berbunyi Berisik",
    "created_at": "2026-01-20T12:00:00.000000Z",
    "updated_at": "2026-01-20T12:00:00.000000Z"
  }
}
```

**Error Response (Missing Field):**

```json
{
  "status": false,
  "message": "nama field is required",
  "data": null
}
```

**Example cURL:**

```bash
curl -X POST http://localhost:8080/keluhan \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"nama": "AC Berbunyi Berisik"}'
```

---

### 7.4 Update Keluhan

**Endpoint:** `PUT /keluhan/{id}` or `POST /keluhan/{id}`

**Description:** Mengupdate keluhan yang sudah ada

**Authentication:** 🔒 JWT Token required

**URL Parameters:**

- `id` (required): UUID keluhan

**Content-Type:** `application/json`

**Request Body:**

```json
{
  "nama": "AC Tidak Dingin dan Bocor"
}
```

**Response Success:**

```json
{
  "status": true,
  "message": "Keluhan updated successfully",
  "data": {
    "id": "uuid-string",
    "nama": "AC Tidak Dingin dan Bocor",
    "created_at": "2026-01-20T12:00:00.000000Z",
    "updated_at": "2026-01-20T13:00:00.000000Z"
  }
}
```

**Error Response (Not Found):**

```json
{
  "status": false,
  "message": "Keluhan not found",
  "data": null
}
```

**Error Response (Missing Field):**

```json
{
  "status": false,
  "message": "nama field is required",
  "data": null
}
```

**Example cURL:**

```bash
curl -X PUT http://localhost:8080/keluhan/uuid-keluhan-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"nama": "AC Tidak Dingin dan Bocor"}'
```

---

### 7.5 Delete Keluhan

**Endpoint:** `DELETE /keluhan/{id}`

**Description:** Menghapus keluhan

**Authentication:** 🔒 JWT Token required

**URL Parameters:**

- `id` (required): UUID keluhan

**Response Success:**

```json
{
  "status": true,
  "message": "Keluhan deleted successfully",
  "data": null
}
```

**Error Response (Not Found):**

```json
{
  "status": false,
  "message": "Keluhan not found",
  "data": null
}
```

**Example cURL:**

```bash
curl -X DELETE http://localhost:8080/keluhan/uuid-keluhan-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## Models & Services

### Keluhan Model

**File:** `app/Models/Keluhan.php`

**Table:** `keluhan`

**Columns:**

- `id` (uuid, primary key)
- `nama` (string, required)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Model Configuration:**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keluhan extends Model
{
    protected $table = 'keluhan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'nama'
    ];
}
```

### KeluhanService

**File:** `app/Services/KeluhanService.php`

**Methods:**

1. **`createKeluhan($data)`**
   - Creates new keluhan
   - Validates `nama` field
   - Auto-generates UUID
   - Returns JsonResponder

2. **`listKeluhan()`**
   - Lists all keluhan
   - Ordered by `created_at` DESC (newest first)
   - Returns JsonResponder

3. **`getKeluhan($id)`**
   - Gets single keluhan by ID
   - Returns 404 if not found
   - Returns JsonResponder

4. **`updateKeluhan($id, $data)`**
   - Updates existing keluhan
   - Validates `nama` field
   - Returns 404 if not found
   - Returns JsonResponder

5. **`deleteKeluhan($id)`**
   - Deletes keluhan by ID
   - Returns 404 if not found
   - Returns JsonResponder

**Service Registration:**

Service registered in `bootstrap/app.php`:

```php
$container['KeluhanService'] = function ($c) {
    return new \App\Services\KeluhanService();
};
```

**Routes Registration:**

Routes loaded in `routes/index.php`:

```php
(require __DIR__ . '/keluhan.php')($app);
```

---

## File Storage

- **Upload Location:** `public/uploads/{resource}/`
- **Supported Formats:** JPG, PNG, GIF, WebP
- **Max Size:** 5 MB per file
- **Auto Delete:** File otomatis dihapus saat product dihapus atau diganti
