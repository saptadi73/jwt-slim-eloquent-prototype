# Sale Order API - Valid CRUD Payloads

Dokumentasi lengkap payload yang valid untuk operasi CRUD Sale Order berdasarkan `routes/orders.php`.

---

## 1. CREATE Sale Order

**Endpoint:** `POST /orders/sale`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### Payload Structure

```json
{
  "order_number": "SO-2025-001",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 1500000.00,
  "tax": 150000.00,
  "total": 1650000.00,
  "customer_id": "uuid-customer-id-here",
  "product_lines": [
    {
      "product_id": "uuid-product-id-here",
      "line_number": 1,
      "description": "AC Split 1 PK",
      "qty": 2.00,
      "unit_price": 500000.00,
      "discount": 0.00,
      "line_total": 1000000.00
    },
    {
      "product_id": "uuid-product-id-here-2",
      "line_number": 2,
      "description": "Remote AC",
      "qty": 2.00,
      "unit_price": 250000.00,
      "discount": 0.00,
      "line_total": 500000.00
    }
  ],
  "service_lines": [
    {
      "service_id": "uuid-service-id-here",
      "line_number": 1,
      "description": "Instalasi AC",
      "qty": 1.00,
      "unit_price": 300000.00,
      "discount": 50000.00,
      "line_total": 250000.00
    }
  ]
}
```

### Field Descriptions

#### Sale Order Fields (Required)

- `order_number` (string): Nomor sale order unik
- `order_date` (date): Tanggal order format `YYYY-MM-DD`
- `status` (enum): Status order - nilai valid: `"draft"`, `"confirmed"`, `"paid"`, `"cancelled"`
- `subtotal` (decimal): Subtotal sebelum pajak
- `tax` (decimal): Nilai pajak
- `total` (decimal): Total keseluruhan (subtotal + tax)
- `customer_id` (uuid): ID customer yang valid

#### Snapshot Customer Fields (Optional)

- `nama` (string, optional): Nama customer disalin ke sale order
- `alamat` (string, optional): Alamat customer disalin ke sale order
- `hp` (string, optional, max 30): Nomor HP disalin ke sale order
- `keterangan` (string, optional): Catatan/keterangan tambahan

#### Product Lines (Optional Array)

- `product_id` (uuid): ID produk yang valid
- `line_number` (integer): Nomor urut baris
- `description` (string): Deskripsi produk
- `qty` (decimal): Jumlah/kuantitas
- `unit_price` (decimal): Harga per unit
- `discount` (decimal): Diskon
- `line_total` (decimal): Total baris (qty × unit_price - discount)
- `hpp` (decimal, optional): Harga Pokok Penjualan untuk keperluan accounting

#### Service Lines (Optional Array)

- `service_id` (uuid): ID service yang valid
- `line_number` (integer): Nomor urut baris
- `description` (string): Deskripsi service
- `qty` (decimal): Jumlah/kuantitas
- `unit_price` (decimal): Harga per unit
- `discount` (decimal): Diskon
- `line_total` (decimal): Total baris (qty × unit_price - discount)

### Example Minimal Payload (Tanpa Lines)

```json
{
  "order_number": "SO-2025-002",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 0.00,
  "tax": 0.00,
  "total": 0.00,
  "customer_id": "uuid-customer-id-here"
}
```

### Example dengan Snapshot Customer Fields (Optional)

```json
{
  "order_number": "SO-2025-003",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 1500000.00,
  "tax": 150000.00,
  "total": 1650000.00,
  "customer_id": "uuid-customer-id-here",
  "nama": "PT. Mitra Usaha",
  "alamat": "Jl. Merdeka No. 123, Jakarta",
  "hp": "021-12345678",
  "keterangan": "Customer korporat terpercaya",
  "product_lines": [
    {
      "product_id": "uuid-product-id-here",
      "line_number": 1,
      "description": "AC Split 2 PK",
      "qty": 3.00,
      "unit_price": 450000.00,
      "discount": 0.00,
      "line_total": 1350000.00,
      "hpp": 300000.00
    }
  ],
  "service_lines": [
    {
      "service_id": "uuid-service-id-here",
      "line_number": 1,
      "description": "Garansi Extended 2 Tahun",
      "qty": 3.00,
      "unit_price": 100000.00,
      "discount": 100000.00,
      "line_total": 200000.00
    }
  ]
}
```

---

## 2. READ Sale Order by ID

**Endpoint:** `GET /orders/sale/{id}`

**Authentication:** Not Required

**Method:** GET

### URL Parameters

- `{id}`: UUID dari sale order

### Example Request

```text
GET /orders/sale/a1b2c3d4-e5f6-7890-abcd-ef1234567890
```

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "order_number": "SO-2025-001",
    "order_date": "2025-01-15",
    "status": "draft",
    "subtotal": "1500000.00",
    "tax": "150000.00",
    "total": "1650000.00",
    "customer_id": "uuid-customer-id",
    "nama": "PT. Mitra Usaha",
    "alamat": "Jl. Merdeka No. 123, Jakarta",
    "hp": "021-12345678",
    "keterangan": "Customer korporat terpercaya",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00",
    "customer": {
      "id": "uuid-customer-id",
      "nama": "PT. Mitra Usaha",
      "alamat": "Jl. Merdeka No. 123, Jakarta",
      "hp": "021-12345678",
      "email": "info@mitrausaha.com"
    },
    "product_lines": [
      {
        "id": "uuid-line-id",
        "sale_order_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
        "product_id": "uuid-product-id",
        "line_number": 1,
        "description": "AC Split 2 PK",
        "qty": "3.00",
        "unit_price": "450000.00",
        "discount": "0.00",
        "line_total": "1350000.00",
        "hpp": "300000.00",
        "created_at": "2025-01-15 10:30:00",
        "updated_at": "2025-01-15 10:30:00",
        "product": {
          "id": "uuid-product-id",
          "nama": "AC Split 2 PK",
          "kode": "AC-002",
          "harga": "450000.00"
        }
      }
    ],
    "service_lines": [
      {
        "id": "uuid-service-line-id",
        "sale_order_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
        "service_id": "uuid-service-id",
        "line_number": 1,
        "description": "Garansi Extended 2 Tahun",
        "qty": "3.00",
        "unit_price": "100000.00",
        "discount": "100000.00",
        "line_total": "200000.00",
        "created_at": "2025-01-15 10:30:00",
        "updated_at": "2025-01-15 10:30:00",
        "service": {
          "id": "uuid-service-id",
          "nama": "Garansi Extended 2 Tahun",
          "harga": "100000.00"
        }
      }
    ]
  }
}
```

---

## 3. LIST All Sale Orders

**Endpoint:** `GET /orders/sale`

**Authentication:** Not Required

**Method:** GET

### Example Request

```text
GET /orders/sale
```

### Example Response

```json
{
  "success": true,
  "data": [
    {
      "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
      "order_number": "SO-2025-001",
      "order_date": "2025-01-15",
      "status": "draft",
      "subtotal": "1500000.00",
      "tax": "150000.00",
      "total": "1650000.00",
      "customer_id": "uuid-customer-id",
      "nama": "PT. Mitra Usaha",
      "alamat": "Jl. Merdeka No. 123, Jakarta",
      "hp": "021-12345678",
      "created_at": "2025-01-15 10:30:00",
      "updated_at": "2025-01-15 10:30:00",
      "customer": {
        "id": "uuid-customer-id",
        "nama": "PT. Mitra Usaha",
        "alamat": "Jl. Merdeka No. 123, Jakarta",
        "hp": "021-12345678",
        "email": "info@mitrausaha.com"
      }
    }
  ]
}
```

---

## 4. UPDATE Sale Order

**Endpoint:** `POST /orders/update/sale/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order yang akan diupdate

### Payload Structure

```json
{
  "order_number": "SO-2025-001-UPDATED",
  "order_date": "2025-01-16",
  "status": "confirmed",
  "subtotal": 1500000.00,
  "tax": 150000.00,
  "total": 1650000.00,
  "customer_id": "uuid-customer-id-here"
}
```

### Example Partial Update (Hanya Status)

```json
{
  "status": "confirmed"
}
```

### Example Update dengan Snapshot Customer

```json
{
  "status": "confirmed",
  "nama": "PT. Mitra Usaha",
  "alamat": "Jl. Merdeka No. 123, Jakarta",
  "hp": "021-12345678",
  "keterangan": "Konfirmasi dari customer"
}
```

### Important Notes

- Semua field bersifat optional, hanya field yang dikirim yang akan diupdate
- Jika status berubah dari selain `"confirmed"` menjadi `"confirmed"`, sistem akan otomatis apply stock (mengurangi stok produk)
- Status yang valid: `"draft"`, `"confirmed"`, `"paid"`, `"cancelled"`

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "order_number": "SO-2025-001-UPDATED",
    "order_date": "2025-01-16",
    "status": "confirmed",
    "subtotal": "1500000.00",
    "tax": "150000.00",
    "total": "1650000.00",
    "customer_id": "uuid-customer-id",
    "nama": "PT. Mitra Usaha",
    "alamat": "Jl. Merdeka No. 123, Jakarta",
    "hp": "021-12345678",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-16 15:45:00"
  }
}
```

---

## 5. DELETE Sale Order

**Endpoint:** `POST /orders/delete/sale/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order yang akan dihapus

### Payload

```json
{}
```

atau tidak perlu body sama sekali

### Example Request

```text
POST /orders/delete/sale/uuid-sale-order-id-here
```

### Example Response

```json
{
  "success": true,
  "data": {
    "message": "Sale Order deleted successfully"
  }
}
```

---

## 6. DELETE Sale Order Product Line

**Endpoint:** `POST /orders/delete/sale/product-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order product line yang akan dihapus

### Payload

```json
{}
```

atau tidak perlu body sama sekali

### Example Request

```text
POST /orders/delete/sale/product-lines/uuid-sale-order-product-line-id-here
```

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-sale-order-product-line-id",
    "sale_order_id": "uuid-sale-order-id",
    "product_id": "uuid-product-id",
    "line_number": 1,
    "description": "AC Split 2 PK",
    "qty": "3.00",
    "unit_price": "450000.00",
    "discount": "0.00",
    "line_total": "1350000.00",
    "hpp": "300000.00",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

---

## 7. DELETE Sale Order Service Line

**Endpoint:** `POST /orders/delete/sale/service-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order service line yang akan dihapus

### Payload

```json
{}
```

atau tidak perlu body sama sekali

### Example Request

```text
POST /orders/delete/sale/service-lines/uuid-sale-order-service-line-id-here
```

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-sale-order-service-line-id",
    "sale_order_id": "uuid-sale-order-id",
    "service_id": "uuid-service-id",
    "line_number": 1,
    "description": "Garansi Extended 2 Tahun",
    "qty": "3.00",
    "unit_price": "100000.00",
    "discount": "100000.00",
    "line_total": "200000.00",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

---

## 8. ADD Sale Order Product Line

**Endpoint:** `POST /orders/add/sale/product-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order yang akan ditambahkan product line

### Payload Structure

```json
{
  "product_id": "uuid-product-id-here",
  "line_number": 2,
  "description": "AC Split 1.5 PK",
  "qty": 2.00,
  "unit_price": 400000.00,
  "discount": 0.00,
  "line_total": 800000.00,
  "hpp": 250000.00
}
```

### Field Descriptions

- `product_id` (uuid, required): ID produk yang valid
- `line_number` (integer, required): Nomor urut baris
- `description` (string, required): Deskripsi produk
- `qty` (decimal, required): Jumlah/kuantitas
- `unit_price` (decimal, required): Harga per unit
- `discount` (decimal, required): Diskon
- `line_total` (decimal, required): Total baris (qty × unit_price - discount)
- `hpp` (decimal, optional): Harga Pokok Penjualan

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-new-sale-order-product-line-id",
    "sale_order_id": "uuid-sale-order-id",
    "product_id": "uuid-product-id-here",
    "line_number": 2,
    "description": "AC Split 1.5 PK",
    "qty": "2.00",
    "unit_price": "400000.00",
    "discount": "0.00",
    "line_total": "800000.00",
    "hpp": "250000.00",
    "created_at": "2025-01-15 16:30:00",
    "updated_at": "2025-01-15 16:30:00"
  }
}
```

---

## 9. ADD Sale Order Service Line

**Endpoint:** `POST /orders/add/sale/service-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari sale order yang akan ditambahkan service line

### Payload Structure

```json
{
  "service_id": "uuid-service-id-here",
  "line_number": 1,
  "description": "Installasi dan Setting",
  "qty": 1.00,
  "unit_price": 250000.00,
  "discount": 0.00,
  "line_total": 250000.00
}
```

### Field Descriptions

- `service_id` (uuid, required): ID service yang valid
- `line_number` (integer, required): Nomor urut baris
- `description` (string, required): Deskripsi service
- `qty` (decimal, required): Jumlah/kuantitas
- `unit_price` (decimal, required): Harga per unit
- `discount` (decimal, required): Diskon
- `line_total` (decimal, required): Total baris (qty × unit_price - discount)

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-new-sale-order-service-line-id",
    "sale_order_id": "uuid-sale-order-id",
    "service_id": "uuid-service-id-here",
    "line_number": 1,
    "description": "Installasi dan Setting",
    "qty": "1.00",
    "unit_price": "250000.00",
    "discount": "0.00",
    "line_total": "250000.00",
    "created_at": "2025-01-15 16:30:00",
    "updated_at": "2025-01-15 16:30:00"
  }
}
```

---

## Status Enum Values

Sale Order memiliki 4 status yang valid:

| Status | Deskripsi |
|--------|-----------|
| `draft` | Order masih dalam draft, belum dikonfirmasi |
| `confirmed` | Order sudah dikonfirmasi, stok akan dikurangi |
| `paid` | Order sudah dibayar |
| `cancelled` | Order dibatalkan |

---

## Error Responses

### 400 Bad Request

```json
{
  "success": false,
  "error": "Invalid data provided"
}
```

### 401 Unauthorized (untuk endpoint yang memerlukan JWT)

```json
{
  "success": false,
  "error": "Unauthorized"
}
```

### 404 Not Found

```json
{
  "success": false,
  "error": "Sale Order not found"
}
```

### 500 Internal Server Error

```json
{
  "success": false,
  "error": "Database error message here"
}
```

---

## Testing dengan cURL

### 1. Create Sale Order

```bash
curl -X POST http://localhost/orders/sale \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "order_number": "SO-2025-001",
    "order_date": "2025-01-15",
    "status": "draft",
    "subtotal": 1500000.00,
    "tax": 150000.00,
    "total": 1650000.00,
    "customer_id": "uuid-customer-id-here",
    "product_lines": [
      {
        "product_id": "uuid-product-id-here",
        "line_number": 1,
        "description": "AC Split 2 PK",
        "qty": 3.00,
        "unit_price": 450000.00,
        "discount": 0.00,
        "line_total": 1350000.00,
        "hpp": 300000.00
      }
    ]
  }'
```

### 2. Get Sale Order

```bash
curl -X GET http://localhost/orders/sale/uuid-sale-order-id
```

### 3. List All Sale Orders

```bash
curl -X GET http://localhost/orders/sale
```

### 4. Update Sale Order

```bash
curl -X POST http://localhost/orders/update/sale/uuid-sale-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "status": "confirmed"
  }'
```

### 5. Delete Sale Order

```bash
curl -X POST http://localhost/orders/delete/sale/uuid-sale-order-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 6. Delete Product Line

```bash
curl -X POST http://localhost/orders/delete/sale/product-lines/uuid-product-line-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 7. Delete Service Line

```bash
curl -X POST http://localhost/orders/delete/sale/service-lines/uuid-service-line-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 8. Add Product Line

```bash
curl -X POST http://localhost/orders/add/sale/product-lines/uuid-sale-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "product_id": "uuid-product-id-here",
    "line_number": 2,
    "description": "AC Split 1.5 PK",
    "qty": 2.00,
    "unit_price": 400000.00,
    "discount": 0.00,
    "line_total": 800000.00,
    "hpp": 250000.00
  }'
```

### 9. Add Service Line

```bash
curl -X POST http://localhost/orders/add/sale/service-lines/uuid-sale-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "service_id": "uuid-service-id-here",
    "line_number": 1,
    "description": "Installasi dan Setting",
    "qty": 1.00,
    "unit_price": 250000.00,
    "discount": 0.00,
    "line_total": 250000.00
  }'
```

---

## Perbedaan Purchase Order vs Sale Order

| Aspek | Purchase Order | Sale Order |
|-------|----------------|------------|
| **Relasi** | Vendor (supplier) | Customer (pelanggan) |
| **Tujuan** | Pembelian barang dari supplier | Penjualan barang ke customer |
| **Stock Impact** | Menambah stok saat confirmed | Mengurangi stok saat confirmed |
| **Lines** | Hanya Product Lines | Product Lines + Service Lines |
| **Authentication** | Create tidak perlu JWT | Create perlu JWT |

---

## Notes Penting

1. **UUID Format**: Semua ID menggunakan format UUID (contoh: `a1b2c3d4-e5f6-7890-abcd-ef1234567890`)

2. **Decimal Format**: Semua nilai decimal (subtotal, tax, total, qty, unit_price, dll) menggunakan format dengan 2 desimal

3. **Date Format**: Tanggal menggunakan format `YYYY-MM-DD` (contoh: `2025-01-15`)

4. **JWT Token**: Endpoint yang memerlukan autentikasi harus menyertakan JWT token di header `Authorization: Bearer YOUR_TOKEN`

5. **Stock Management**: Ketika status berubah menjadi `"confirmed"`, sistem akan otomatis mengurangi stok produk yang ada di product_lines (berbeda dengan Purchase Order yang menambah stok)

6. **Transaction**: Semua operasi create/update/delete menggunakan database transaction untuk menjaga konsistensi data

7. **Validation**: Pastikan customer_id, product_id, dan service_id yang digunakan sudah ada di database

8. **Line Total Calculation**: Sebaiknya line_total dihitung di client side dengan formula: `(qty × unit_price) - discount`

9. **Service Lines**: Sale Order mendukung Service Lines (berbeda dengan Purchase Order yang hanya Product Lines)

10. **HPP (Harga Pokok Penjualan)**: Optional field untuk keperluan accounting dan perhitungan profit margin

11. **Snapshot Customer**: Nama, alamat, dan HP customer bisa disalin ke sale order untuk keperluan referensi di masa depan

12. **Authentication Differences**:
    - Create Sale Order: Perlu JWT
    - Update/Delete Sale Order: Perlu JWT
    - Read/List Sale Order: Tidak perlu JWT
    - Create Purchase Order: Tidak perlu JWT
