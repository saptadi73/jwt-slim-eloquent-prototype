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
  "order_number": "SO-2025-005",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 0.00,
  "tax": 0.00,
  "total": 0.00,
  "customer_id": "uuid-customer-id-here",
  "nama": "PT Angin Sepoi",
  "alamat": "Jl. Kenangan No. 12, Jakarta",
  "hp": "081234567890",
  "keterangan": "Catat permintaan delivery pagi"
}
```

### Example dengan Product Lines Saja

```json
{
  "order_number": "SO-2025-003",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 1000000.00,
  "tax": 100000.00,
  "total": 1100000.00,
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
    }
  ]
}
```

### Example LENGKAP - Dengan Semua Fields (Recommended)

```json
{
  "order_number": "SO-2025-006",
  "order_date": "2025-12-20",
  "status": "draft",
  "subtotal": 1500000.00,
  "tax": 150000.00,
  "total": 1650000.00,
  "customer_id": "550e8400-e29b-41d4-a716-446655440000",
  "nama": "PT Dingin Sejuk Indonesia",
  "alamat": "Jl. Merdeka No. 45, Jakarta Pusat",
  "hp": "081234567890",
  "keterangan": "Pengiriman ke gudang utama, jam kerja 08:00-17:00",
  "product_lines": [
    {
      "product_id": "660e8400-e29b-41d4-a716-446655440001",
      "line_number": 1,
      "description": "AC Split 1 PK Toshiba RAS-10U2KH3S",
      "qty": 2.00,
      "unit_price": 500000.00,
      "discount": 50000.00,
      "line_total": 950000.00,
      "hpp": 350000.00
    },
    {
      "product_id": "660e8400-e29b-41d4-a716-446655440002",
      "line_number": 2,
      "description": "Remote Control AC Original",
      "qty": 2.00,
      "unit_price": 250000.00,
      "discount": 0.00,
      "line_total": 500000.00,
      "hpp": 150000.00
    }
  ],
  "service_lines": [
    {
      "service_id": "770e8400-e29b-41d4-a716-446655440001",
      "line_number": 1,
      "description": "Instalasi AC Split + Pipa 10 meter",
      "qty": 1.00,
      "unit_price": 300000.00,
      "discount": 0.00,
      "line_total": 300000.00
    }
  ]
}
```

### Example dengan Service Lines Saja

```json
{
  "order_number": "SO-2025-004",
  "order_date": "2025-01-15",
  "status": "draft",
  "subtotal": 500000.00,
  "tax": 50000.00,
  "total": 550000.00,
  "customer_id": "uuid-customer-id-here",
  "service_lines": [
    {
      "service_id": "uuid-service-id-here",
      "line_number": 1,
      "description": "Service AC Rutin",
      "qty": 1.00,
      "unit_price": 500000.00,
      "discount": 0.00,
      "line_total": 500000.00
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
```
GET /orders/sale/9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g
```

### Example Response
```json
{
  "success": true,
  "data": {
    "id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
    "order_number": "SO-2025-001",
    "order_date": "2025-01-15",
    "status": "draft",
    "subtotal": "1500000.00",
    "tax": "150000.00",
    "total": "1650000.00",
    "customer_id": "uuid-customer-id",
    "nama": "PT Angin Sepoi",
    "alamat": "Jl. Kenangan No. 12, Jakarta",
    "hp": "081234567890",
    "keterangan": "Catat permintaan delivery pagi",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00",
    "customer": {
      "id": "uuid-customer-id",
      "name": "PT. Example Company",
      "email": "contact@example.com"
    },
    "product_lines": [
      {
        "id": "uuid-line-id",
        "sale_order_id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
        "product_id": "uuid-product-id",
        "line_number": 1,
        "description": "AC Split 1 PK",
        "qty": "2.00",
        "unit_price": "500000.00",
        "discount": "0.00",
        "line_total": "1000000.00",
        "product": {
          "id": "uuid-product-id",
          "name": "AC Split 1 PK",
          "code": "AC-001"
        }
      }
    ],
    "service_lines": [
      {
        "id": "uuid-service-line-id",
        "sale_order_id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
        "service_id": "uuid-service-id",
        "line_number": 1,
        "description": "Instalasi AC",
        "qty": "1.00",
        "unit_price": "300000.00",
        "discount": "50000.00",
        "line_total": "250000.00",
        "service": {
          "id": "uuid-service-id",
          "name": "Instalasi AC",
          "code": "SVC-001"
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
```
GET /orders/sale
```

### Example Response
```json
{
  "success": true,
  "data": [
    {
      "id": "9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g",
      "order_number": "SO-2025-001",
      "order_date": "2025-01-15",
      "status": "draft",
      "subtotal": "1500000.00",
      "tax": "150000.00",
      "total": "1650000.00",
      "customer_id": "uuid-customer-id",
      "created_at": "2025-01-15 10:30:00",
      "updated_at": "2025-01-15 10:30:00",
      "customer": {
        "id": "uuid-customer-id",
        "name": "PT. Example Company",
        "email": "contact@example.com"
      }
    },
    {
      "id": "8c3e4d5b-6a7b-8c9d-0e1f-2a3b4c5d6e7f",
      "order_number": "SO-2025-002",
      "order_date": "2025-01-16",
      "status": "confirmed",
      "subtotal": "2000000.00",
      "tax": "200000.00",
      "total": "2200000.00",
      "customer_id": "uuid-customer-id-2",
      "created_at": "2025-01-16 11:00:00",
      "updated_at": "2025-01-16 14:30:00",
      "customer": {
        "id": "uuid-customer-id-2",
        "name": "CV. Another Company",
        "email": "info@another.com"
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
  "subtotal": 1600000.00,
  "tax": 160000.00,
  "total": 1760000.00,
  "customer_id": "uuid-customer-id-here"
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
  "subtotal": 2000000.00,
  "tax": 200000.00,
  "total": 2200000.00
}
```

### Important Notes
- Semua field bersifat optional, hanya field yang dikirim yang akan diupdate
- Jika status berubah dari selain `"confirmed"` menjadi `"confirmed"`, sistem akan otomatis apply stock (mengurangi stok produk)
- Status yang valid: `"draft"`, `"confirmed"`, `"paid"`, `"cancelled"`

---

## 5. DELETE Product Line
**Endpoint:** `POST /orders/delete/sale/product-lines/{id}`  
**Authentication:** Required (JWT Token)  
**Content-Type:** `application/json`

### URL Parameters
- `{id}`: UUID dari product line yang akan dihapus

### Payload
```json
{}
```
atau tidak perlu body sama sekali

### Example Request
```
POST /orders/delete/sale/product-lines/uuid-product-line-id-here
```

### Example Response
```json
{
  "success": true,
  "data": {
    "message": "Product Order Line deleted successfully"
  }
}
```

---

## 6. ADD Service Line
**Endpoint:** `POST /orders/add/sale/service-lines/{id}`  
**Authentication:** Required (JWT Token)  
**Content-Type:** `application/json`

### URL Parameters
- `{id}`: UUID dari sale order yang akan ditambahkan service line

### Payload Structure

```json
{
  "service_id": "uuid-service-id-here",
  "line_number": 2,
  "description": "Service AC Tambahan",
  "qty": 1.00,
  "unit_price": 400000.00,
  "discount": 0.00,
  "line_total": 400000.00
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
    "id": "uuid-new-service-line-id",
    "sale_order_id": "uuid-sale-order-id",
    "service_id": "uuid-service-id-here",
    "line_number": 2,
    "description": "Service AC Tambahan",
    "qty": "1.00",
    "unit_price": "400000.00",
    "discount": "0.00",
    "line_total": "400000.00",
    "created_at": "2025-01-15 15:30:00",
    "updated_at": "2025-01-15 15:30:00"
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
        "description": "AC Split 1 PK",
        "qty": 2.00,
        "unit_price": 500000.00,
        "discount": 0.00,
        "line_total": 1000000.00
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

### 5. Delete Product Line
```bash
curl -X POST http://localhost/orders/delete/sale/product-lines/uuid-product-line-id \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 6. Add Service Line
```bash
curl -X POST http://localhost/orders/add/sale/service-lines/uuid-sale-order-id \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "service_id": "uuid-service-id-here",
    "line_number": 2,
    "description": "Service AC Tambahan",
    "qty": 1.00,
    "unit_price": 400000.00,
    "discount": 0.00,
    "line_total": 400000.00
  }'
```

---

## Notes Penting

1. **UUID Format**: Semua ID menggunakan format UUID (contoh: `9d4f5e6a-7b8c-9d0e-1f2a-3b4c5d6e7f8g`)

2. **Decimal Format**: Semua nilai decimal (subtotal, tax, total, qty, unit_price, dll) menggunakan format dengan 2 desimal

3. **Date Format**: Tanggal menggunakan format `YYYY-MM-DD` (contoh: `2025-01-15`)

4. **JWT Token**: Endpoint yang memerlukan autentikasi harus menyertakan JWT token di header `Authorization: Bearer YOUR_TOKEN`

5. **Stock Management**: Ketika status berubah menjadi `"confirmed"`, sistem akan otomatis mengurangi stok produk yang ada di product_lines

6. **Transaction**: Semua operasi create/update/delete menggunakan database transaction untuk menjaga konsistensi data

7. **Validation**: Pastikan customer_id, product_id, dan service_id yang digunakan sudah ada di database

8. **Line Total Calculation**: Sebaiknya line_total dihitung di client side dengan formula: `(qty × unit_price) - discount`
