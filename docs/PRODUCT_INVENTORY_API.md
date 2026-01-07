# Product & Inventory API

Dokumentasi ringkas untuk routes dan services terkait Produk dan Inventory.

## Product

- Base path: `/products`
- Service: [app/Services/ProductService.php](../app/Services/ProductService.php)
- Model: [app/Models/Product.php](../app/Models/Product.php)
- Routes: [routes/products.php](../routes/products.php)

### GET /products
- Deskripsi: Ambil daftar produk lengkap beserta relasi `kategori`, `satuan`, `brand`.
- Query: none
- Respon: 200 OK
  - body: array of product dengan relasi

### GET /products/{id}
- Deskripsi: Ambil detail satu produk.
- Path: `id` (UUID)
- Respon:
  - 200 OK: objek produk
  - 404 Not Found: produk tidak ditemukan

### POST /products
- Deskripsi: Buat produk baru.
- Body (form/json):
  - nama: string (wajib, <=191)
  - kode: string (opsional)
  - harga: number (opsional, >=0)
  - hpp: number (opsional, >=0)
  - stok: integer (opsional, default 0)
  - tipe: string (opsional). Alias `type` juga diterima dan akan dipetakan ke `tipe`.
  - brand_id: UUID (opsional, harus valid jika dikirim)
  - kategori_id: UUID (opsional, harus valid jika dikirim)
  - satuan_id: UUID (opsional, harus valid jika dikirim)
- File upload (multipart/form-data):
  - gambar | file | photo: file gambar produk
- Respon:
  - 201 Created: objek produk, info upload
  - 400/422: pesan error validasi

### PUT /products/{id}
- Deskripsi: Update produk (partial update didukung).
- Path: `id` (UUID)
- Body: sama dengan POST (opsional). Nilai kosong string untuk `harga`/`hpp` akan diperlakukan sebagai null.
- File upload: sama seperti POST, mengganti gambar lama bila ada.
- Respon:
  - 200 OK: objek produk diperbarui
  - 404 Not Found: produk tidak ditemukan

### POST /products/{id}
- Deskripsi: Alternatif update untuk klien yang hanya mendukung POST multipart.
- Path/Body: sama seperti PUT
- Respon: sama seperti PUT

### DELETE /products/{id}
- Deskripsi: Hapus produk (ikut menghapus file gambar jika ada).
- Path: `id` (UUID)
- Respon:
  - 200 OK: konfirmasi penghapusan
  - 404 Not Found: produk tidak ditemukan

## Inventory

- Services:
  - Laporan pergerakan: [app/Services/InventoryReportService.php](../app/Services/InventoryReportService.php)
  - Perhitungan stok/HPP: [app/Services/ProductStockService.php](../app/Services/ProductStockService.php)
- Routes laporan: [routes/reports.php](../routes/reports.php)
- Base path laporan: `/api/reports`
- Tabel riwayat pergerakan: [app/Models/ProductMoveHistory.php](../app/Models/ProductMoveHistory.php)

### GET /api/reports/product-movements
- Deskripsi: Laporan pergerakan barang (detail transaksi) per produk untuk periode.
- Query:
  - product_id: UUID (opsional) — filter 1 produk
  - start_date: YYYY-MM-DD (opsional, default hari pertama bulan ini)
  - end_date: YYYY-MM-DD (opsional, default hari ini)
- Respon: 200 OK
  - report_name, period, filter
  - items[] per produk:
    - product { id, kode, nama, satuan, brand }
    - opening_qty, total_in, total_out, net_movement, closing_qty
    - transactions[]: { date, move_type, direction, qty_in, qty_out, stock_before, stock_after, source_table, source_id, source_order_id, note }

Response Wrapper:

```json
{
  "status": true,
  "message": "Laporan pergerakan barang berhasil dibuat",
  "data": {
    "report_name": "Laporan Pergerakan Barang",
    "period": {
      "start_date": "2026-01-01",
      "end_date": "2026-01-07"
    },
    "filter": { "product_id": null },
    "items": [
      {
        "product": {
          "id": "6a9b8f3d-1e2c-4b65-9d40-3a8d7a1a2b3c",
          "kode": "PRD-001",
          "nama": "AC Split 1PK",
          "satuan": "unit",
          "brand": "Toshiba"
        },
        "opening_qty": 10,
        "total_in": 5,
        "total_out": 3,
        "net_movement": 2,
        "closing_qty": 12,
        "transactions": [
          {
            "date": "2026-01-02 10:21:00",
            "move_type": "purchase",
            "direction": "in",
            "qty_in": 5,
            "qty_out": 0,
            "stock_before": 10,
            "stock_after": 15,
            "source_table": "purchase_order_line",
            "source_id": "a1b2c3...",
            "source_order_id": "b2c3d4...",
            "note": "Purchase order SO-2026-0001 | HPP: 2,500,000.00 -> 2,600,000.00"
          },
          {
            "date": "2026-01-05 14:00:00",
            "move_type": "sale",
            "direction": "out",
            "qty_in": 0,
            "qty_out": 3,
            "stock_before": 15,
            "stock_after": 12,
            "source_table": "product_order_line",
            "source_id": "c3d4e5...",
            "source_order_id": "SO-2026-0009",
            "note": "Sale order SO-2026-0009"
          }
        ]
      }
    ]
  }
}
```

### GET /api/reports/product-movements/summary
- Deskripsi: Ringkasan pergerakan barang (tanpa transaksi) per produk untuk periode wajib.
- Query (wajib):
  - start_date: YYYY-MM-DD
  - end_date: YYYY-MM-DD
  - product_id: UUID (opsional)
- Respon: 200 OK
  - report_name, period, filter
  - items[] per produk: { product, opening_qty, total_in, total_out, net_movement, closing_qty }
- Error: 400 bila `start_date`/`end_date` tidak diberikan.

Response Wrapper (200 OK):

```json
{
  "status": true,
  "message": "Ringkasan pergerakan barang berhasil dibuat",
  "data": {
    "report_name": "Ringkasan Pergerakan Barang",
    "period": { "start_date": "2026-01-01", "end_date": "2026-01-31" },
    "filter": { "product_id": null },
    "items": [
      {
        "product": {
          "id": "6a9b8f3d-1e2c-4b65-9d40-3a8d7a1a2b3c",
          "kode": "PRD-001",
          "nama": "AC Split 1PK",
          "satuan": "unit",
          "brand": "Toshiba"
        },
        "opening_qty": 12,
        "total_in": 8,
        "total_out": 5,
        "net_movement": 3,
        "closing_qty": 15
      }
    ]
  }
}
```

Error (400 Bad Request) contoh saat parameter periode tidak lengkap:

```json
{
  "status": false,
  "message": "Invalid data provided",
  "errors": [
    "Parameter start_date dan end_date wajib diisi"
  ],
  "data": []
}
```

## Catatan Implementasi

- Pergerakan stok dihitung dari `product_move_histories.qty` (positif = masuk, negatif = keluar). Lihat [app/Services/ProductStockService.php](../app/Services/ProductStockService.php) untuk logika penambahan stok dari pembelian dan pengurangan stok dari penjualan, serta perhitungan HPP rata-rata tertimbang.
- Field `move_date` digunakan untuk filter periode. Jika null, service memakai `created_at` sebagai fallback saat membentuk tanggal transaksi untuk output (hanya tampilan; filter tetap menggunakan `move_date`).
- Endpoint laporan berada di file routes yang sama dengan laporan akuntansi, namun terpisah secara service di `InventoryReportService`.

## Troubleshooting

- 404 Not Found: pastikan memakai prefix `/api`, contoh `GET /api/reports/product-movements` atau `/api/reports/product-movements/summary`.
- Saat menjalankan built-in server, jalankan dari root project dengan router publik: `php -S localhost:8080 -t public public/index.php`.

## Contoh Pemakaian

- Daftar produk:
  - GET `/products`
- Buat produk dengan gambar (multipart):
  - POST `/products` dengan field `nama`, `harga`, `hpp`, `stok`, dan file `gambar`
- Laporan pergerakan (detail) satu produk di periode:
  - GET `/api/reports/product-movements?product_id=<UUID>&start_date=2026-01-01&end_date=2026-01-07`
- Laporan ringkasan semua produk periode berjalan:
  - GET `/api/reports/product-movements/summary?start_date=2026-01-01&end_date=2026-01-31`
