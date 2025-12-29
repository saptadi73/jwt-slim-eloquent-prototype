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

## File Storage

- **Upload Location:** `public/uploads/{resource}/`
- **Supported Formats:** JPG, PNG, GIF, WebP
- **Max Size:** 5 MB per file
- **Auto Delete:** File otomatis dihapus saat product dihapus atau diganti
