# Purchase Order API - Valid CRUD Payloads

Dokumentasi lengkap payload yang valid untuk operasi CRUD Purchase Order berdasarkan `routes/orders.php`.

---

## 1. CREATE Purchase Order

**Endpoint:** `POST /orders/purchase`

**Authentication:** Not Required

**Content-Type:** `application/json`

### Payload Structure

```json
{
  "order_number": "PO-2025-001",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 5000000.00,
  "tax": 500000.00,
  "total": 5500000.00,
  "vendor_id": "uuid-vendor-id-here",
  "product_lines": [
    {
      "product_id": "uuid-product-id-here",
      "line_number": 1,
      "description": "AC Split 2 PK",
      "qty": 10.00,
      "unit_price": 400000.00,
      "discount": 0.00,
      "line_total": 4000000.00
    },
    {
      "product_id": "uuid-product-id-here-2",
      "line_number": 2,
      "description": "Remote AC Universal",
      "qty": 10.00,
      "unit_price": 100000.00,
      "discount": 0.00,
      "line_total": 1000000.00
    }
  ]
}
```

### Field Descriptions

#### Purchase Order Fields (Required)

- `order_number` (string): Nomor purchase order unik
- `order_date` (date): Tanggal order format `YYYY-MM-DD`
- `status` (enum): Status order - nilai valid: `"draft"`, `"confirmed"`, `"paid"`, `"cancelled"`
- `subtotal` (decimal): Subtotal sebelum pajak
- `tax` (decimal): Nilai pajak
- `total` (decimal): Total keseluruhan (subtotal + tax)
- `vendor_id` (uuid): ID vendor yang valid

#### Product Lines (Optional Array)

- `product_id` (uuid): ID produk yang valid
- `line_number` (integer): Nomor urut baris
- `description` (string): Deskripsi produk
- `qty` (decimal): Jumlah/kuantitas
- `unit_price` (decimal): Harga per unit
- `discount` (decimal): Diskon
- `line_total` (decimal): Total baris (qty × unit_price - discount)

### Example Minimal Payload (Tanpa Lines)

```json
{
  "order_number": "PO-2025-002",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 0.00,
  "tax": 0.00,
  "total": 0.00,
  "vendor_id": "uuid-vendor-id-here"
}
```

### Example dengan Product Lines

```json
{
  "order_number": "PO-2025-003",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 3000000.00,
  "tax": 300000.00,
  "total": 3300000.00,
  "vendor_id": "uuid-vendor-id-here",
  "product_lines": [
    {
      "product_id": "uuid-product-id-here",
      "line_number": 1,
      "description": "AC Split 1.5 PK",
      "qty": 5.00,
      "unit_price": 600000.00,
      "discount": 0.00,
      "line_total": 3000000.00
    }
  ]
}
```

---

## 2. READ Purchase Order by ID

**Endpoint:** `GET /orders/purchase/{id}`

**Authentication:** Not Required

**Method:** GET

### URL Parameters

- `{id}`: UUID dari purchase order

### Example Request

```text
GET /orders/purchase/9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g
```

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
    "order_number": "PO-2025-001",
    "order_date": "2025-01-15",
    "status": "draft",
    "subtotal": "5000000.00",
    "tax": "500000.00",
    "total": "5500000.00",
    "vendor_id": "uuid-vendor-id",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00",
    "vendor": {
      "id": "uuid-vendor-id",
      "name": "PT. Supplier AC Indonesia",
      "email": "supplier@example.com",
      "phone": "021-12345678"
    },
    "product_lines": [
      {
        "id": "uuid-line-id",
        "purchase_order_id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
        "product_id": "uuid-product-id",
        "line_number": 1,
        "description": "AC Split 2 PK",
        "qty": "10.00",
        "unit_price": "400000.00",
        "discount": "0.00",
        "line_total": "4000000.00",
        "created_at": "2025-01-15 10:30:00",
        "updated_at": "2025-01-15 10:30:00",
        "product": {
          "id": "uuid-product-id",
          "name": "AC Split 2 PK",
          "code": "AC-002",
          "price": "400000.00"
        }
      },
      {
        "id": "uuid-line-id-2",
        "purchase_order_id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
        "product_id": "uuid-product-id-2",
        "line_number": 2,
        "description": "Remote AC Universal",
        "qty": "10.00",
        "unit_price": "100000.00",
        "discount": "0.00",
        "line_total": "1000000.00",
        "created_at": "2025-01-15 10:30:00",
        "updated_at": "2025-01-15 10:30:00",
        "product": {
          "id": "uuid-product-id-2",
          "name": "Remote AC Universal",
          "code": "RMT-001",
          "price": "100000.00"
        }
      }
    ]
  }
}
```

---

## 3. LIST All Purchase Orders

**Endpoint:** `GET /orders/purchase`

**Authentication:** Not Required

**Method:** GET

### Example Request

```text
GET /orders/purchase
```

### Example Response

```json
{
  "success": true,
  "data": [
    {
      "id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
      "order_number": "PO-2025-001",
      "order_date": "2025-01-15",
      "status": "draft",
      "subtotal": "5000000.00",
      "tax": "500000.00",
      "total": "5500000.00",
      "vendor_id": "uuid-vendor-id",
      "created_at": "2025-01-15 10:30:00",
      "updated_at": "2025-01-15 10:30:00",
      "vendor": {
        "id": "uuid-vendor-id",
        "name": "PT. Supplier AC Indonesia",
        "email": "supplier@example.com",
        "phone": "021-12345678"
      }
    },
    {
      "id": "8c3e4d5b-6a7b-8c9d-0e1f-2a3b4c5d6e7f",
      "order_number": "PO-2025-002",
      "order_date": "2025-01-16",
      "status": "confirmed",
      "subtotal": "8000000.00",
      "tax": "800000.00",
      "total": "8800000.00",
      "vendor_id": "uuid-vendor-id-2",
      "created_at": "2025-01-16 11:00:00",
      "updated_at": "2025-01-16 14:30:00",
      "vendor": {
        "id": "uuid-vendor-id-2",
        "name": "CV. Distributor Elektronik",
        "email": "info@distributor.com",
        "phone": "021-87654321"
      }
    }
  ]
}
```

---

## 4. UPDATE Purchase Order

**Endpoint:** `POST /orders/update/purchase/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari purchase order yang akan diupdate

### Payload Structure

```json
{
  "order_number": "PO-2025-001-UPDATED",
  "order_date": "2025-01-16",
  "status": "confirmed",
  "subtotal": 5500000.00,
  "tax": 550000.00,
  "total": 6050000.00,
  "vendor_id": "uuid-vendor-id-here"
}
```

### Example Partial Update (Hanya Status)

```json
{
  "status": "confirmed"
}
```

### Example Update dengan Perubahan Total

```json
{
  "subtotal": 6000000.00,
  "tax": 600000.00,
  "total": 6600000.00
}
```

### Important Notes

- Semua field bersifat optional, hanya field yang dikirim yang akan diupdate
- Jika status berubah dari selain `"confirmed"` menjadi `"confirmed"`, sistem akan otomatis:
  - **Apply stock** (menambah stok produk)
  - **Calculate average cost (HPP)** menggunakan weighted average method
  - **Create journal entry** (Debit: Inventory, Credit: Accounts Payable)
- Status yang valid: `"draft"`, `"confirmed"`, `"paid"`, `"cancelled"`

### Automatic HPP Calculation

Ketika Purchase Order dikonfirmasi (status → `"confirmed"`), sistem otomatis menghitung HPP (Harga Pokok Pembelian) dengan **Weighted Average Cost Method**:

**Formula:**
```
New HPP = (Old Stock × Old HPP + Purchase Qty × Purchase Price) / Total Stock
```

**Contoh:**
- Stok lama: 10 unit @ HPP Rp 100.000 = Rp 1.000.000
- Pembelian baru: 5 unit @ Rp 120.000 = Rp 600.000
- **HPP baru: (1.000.000 + 600.000) / 15 = Rp 106.667**

Sistem akan:
1. Update field `hpp` di tabel `products` otomatis
2. Catat perubahan HPP di `product_move_histories`
3. Simpan history calculation di note field

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
    "order_number": "PO-2025-001-UPDATED",
    "order_date": "2025-01-16",
    "status": "confirmed",
    "subtotal": "5500000.00",
    "tax": "550000.00",
    "total": "6050000.00",
    "vendor_id": "uuid-vendor-id",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-16 15:45:00"
  }
}
```

---

## 5. DELETE Purchase Order

**Endpoint:** `POST /orders/delete/purchase/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari purchase order yang akan dihapus

### Payload

```json
{}
```

atau tidak perlu body sama sekali

### Example Request

```text
POST /orders/delete/purchase/uuid-purchase-order-id-here
```

### Example Response

```json
{
  "success": true,
  "data": {
    "message": "Purchase Order deleted successfully"
  }
}
```

---

## 6. DELETE Purchase Order Line

**Endpoint:** `POST /orders/delete/purchase/product-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari purchase order line yang akan dihapus

### Payload

```json
{}
```

atau tidak perlu body sama sekali

### Example Request

```text
POST /orders/delete/purchase/product-lines/uuid-purchase-order-line-id-here
```

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-purchase-order-line-id",
    "purchase_order_id": "uuid-purchase-order-id",
    "product_id": "uuid-product-id",
    "line_number": 1,
    "description": "AC Split 2 PK",
    "qty": "10.00",
    "unit_price": "400000.00",
    "discount": "0.00",
    "line_total": "4000000.00",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

---

## 7. ADD Purchase Order Line

**Endpoint:** `POST /orders/add/purchase/product-lines/{id}`

**Authentication:** Required (JWT Token)

**Content-Type:** `application/json`

### URL Parameters

- `{id}`: UUID dari purchase order yang akan ditambahkan product line

### Payload Structure

```json
{
  "product_id": "uuid-product-id-here",
  "line_number": 3,
  "description": "Freon R32 1kg",
  "qty": 20.00,
  "unit_price": 150000.00,
  "discount": 0.00,
  "line_total": 3000000.00
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

### Example Response

```json
{
  "success": true,
  "data": {
    "id": "uuid-new-purchase-order-line-id",
    "purchase_order_id": "uuid-purchase-order-id",
    "product_id": "uuid-product-id-here",
    "line_number": 3,
    "description": "Freon R32 1kg",
    "qty": "20.00",
    "unit_price": "150000.00",
    "discount": "0.00",
    "line_total": "3000000.00",
    "created_at": "2025-01-15 16:30:00",
    "updated_at": "2025-01-15 16:30:00"
  }
}
```

---

## Status Enum Values

Purchase Order memiliki 4 status yang valid:

| Status | Deskripsi |
|--------|-----------|
| `draft` | Order masih dalam draft, belum dikonfirmasi |
| `confirmed` | Order sudah dikonfirmasi, stok akan ditambah |
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
  "error": "Purchase Order not found"
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

### 1. Create Purchase Order

```bash
curl -X POST http://localhost/orders/purchase \
  -H "Content-Type: application/json" \
  -d '{
    "order_number": "PO-2025-001",
    "order_date": "2025-01-15",
    "status": "draft",
    "subtotal": 5000000.00,
    "tax": 500000.00,
    "total": 5500000.00,
    "vendor_id": "uuid-vendor-id-here",
    "product_lines": [
      {
        "product_id": "uuid-product-id-here",
        "line_number": 1,
        "description": "AC Split 2 PK",
        "qty": 10.00,
        "unit_price": 400000.00,
        "discount": 0.00,
        "line_total": 4000000.00
      }
    ]
  }'
```

### 2. Get Purchase Order

```bash
curl -X GET http://localhost/orders/purchase/uuid-purchase-order-id
```

### 3. List All Purchase Orders

```bash
curl -X GET http://localhost/orders/purchase
```

### 4. Update Purchase Order

```bash
curl -X POST http://localhost/orders/update/purchase/uuid-purchase-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "status": "confirmed"
  }'
```

### 5. Delete Purchase Order

```bash
curl -X POST http://localhost/orders/delete/purchase/uuid-purchase-order-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 6. Delete Purchase Order Line

```bash
curl -X POST http://localhost/orders/delete/purchase/product-lines/uuid-purchase-order-line-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 7. Add Purchase Order Line

```bash
curl -X POST http://localhost/orders/add/purchase/product-lines/uuid-purchase-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "product_id": "uuid-product-id-here",
    "line_number": 3,
    "description": "Freon R32 1kg",
    "qty": 20.00,
    "unit_price": 150000.00,
    "discount": 0.00,
    "line_total": 3000000.00
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

1. **UUID Format**: Semua ID menggunakan format UUID (contoh: `9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g`)

2. **Decimal Format**: Semua nilai decimal (subtotal, tax, total, qty, unit_price, dll) menggunakan format dengan 2 desimal

3. **Date Format**: Tanggal menggunakan format `YYYY-MM-DD` (contoh: `2025-01-15`)

4. **JWT Token**: Endpoint yang memerlukan autentikasi harus menyertakan JWT token di header `Authorization: Bearer YOUR_TOKEN`

5. **Stock Management**: Ketika status berubah menjadi `"confirmed"`, sistem akan otomatis:
   - Menambah stok produk yang ada di product_lines (berbeda dengan Sale Order yang mengurangi stok)
   - **Calculate HPP (Harga Pokok Pembelian)** menggunakan weighted average cost method
   - Update field `hpp` di tabel `products` secara otomatis
   - Catat history perubahan HPP di `product_move_histories`

6. **HPP Calculation**: Sistem menggunakan **Weighted Average Cost Method** untuk menghitung HPP. Formula: `New HPP = (Old Stock × Old HPP + Purchase Qty × Purchase Price) / Total Stock`. Perhitungan dilakukan otomatis saat status berubah ke `"confirmed"`.

7. **Transaction**: Semua operasi create/update/delete menggunakan database transaction untuk menjaga konsistensi data

8. **Validation**: Pastikan vendor_id dan product_id yang digunakan sudah ada di database

9. **Line Total Calculation**: Sebaiknya line_total dihitung di client side dengan formula: `(qty × unit_price) - discount`

10. **No Service Lines**: Purchase Order hanya mendukung Product Lines, tidak ada Service Lines (berbeda dengan Sale Order)

11. **Authentication Differences**:
    - Create Purchase Order: Tidak perlu JWT
    - Update/Delete Purchase Order: Perlu JWT
    - Create Sale Order: Perlu JWT

12. **Accounting Integration**: Saat status `"confirmed"`, sistem otomatis membuat journal entry dengan COA:
    - Debit: 1130 - Persediaan (Inventory)
    - Credit: 2010 - Hutang Usaha (Accounts Payable) atau 2110 sebagai fallback
