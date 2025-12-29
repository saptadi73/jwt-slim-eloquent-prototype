# Service CRUD API

Dokumentasi singkat routes, auth, payload, dan contoh respons untuk resource `services`.

## Ringkasan Endpoints

- Base path: `/services`
- Auth: GET bebas; POST update/delete membutuhkan JWT (middleware `JwtMiddleware`).

| Method | Path | Deskripsi | Auth |
|--------|------|-----------|------|
| GET | /services | List semua service | Tidak |
| GET | /services/{id} | Detail service berdasarkan ID (UUID) | Tidak |
| POST | /services | Create service baru | Ya (JWT) |
| POST | /services/update/{id} | Update service | Ya (JWT) |
| POST | /services/delete/{id} | Hapus service | Ya (JWT) |

## Aturan Payload

Field yang didukung sesuai validasi di `ServiceService`:

- `nama` (string, wajib, max 191)
- `harga` (numeric, wajib pada create; pada update boleh absen jika tidak diubah; nilai >= 0)
- `deskripsi` (string, opsional)

> ID menggunakan UUID dan diberikan otomatis pada create.

## Contoh Request/Response

### Create

**Request**

- Method: POST `/services`
- Header: `Authorization: Bearer <token>`
- Body (JSON):

```json
{
  "nama": "Install AC Split 1PK",
  "deskripsi": "Pemasangan unit baru beserta pipa standar",
  "harga": 450000
}
```

**Response 200**

```json
{
  "status": true,
  "data": {
    "id": "<uuid>",
    "nama": "Install AC Split 1PK",
    "deskripsi": "Pemasangan unit baru beserta pipa standar",
    "harga": 450000,
    "created_at": "2025-08-24 10:00:00",
    "updated_at": "2025-08-24 10:00:00"
  }
}
```

**Response 400 (validasi gagal)**

```json
{
  "status": false,
  "errors": "Field harga wajib diisi"
}
```

### Get List

**Request**

- Method: GET `/services`

**Response 200 (ringkas)**

```json
{
  "status": true,
  "data": [
    {
      "id": "<uuid>",
      "nama": "Install AC Split 1PK",
      "deskripsi": "Pemasangan unit baru beserta pipa standar",
      "harga": 450000,
      "kategori": null,
      "created_at": "2025-08-24 10:00:00",
      "updated_at": "2025-08-24 10:00:00"
    }
  ]
}
```

### Get by ID

**Request**

- Method: GET `/services/{id}`

**Response 200** sama format dengan get list (single object).

### Update

**Request**

- Method: POST `/services/update/{id}`
- Header: `Authorization: Bearer <token>`
- Body (JSON) contoh partial update:

```json
{
  "harga": 500000,
  "deskripsi": "Update harga sesuai promo"
}
```

**Response 200**

```json
{
  "status": true,
  "data": {
    "id": "<uuid>",
    "nama": "Install AC Split 1PK",
    "deskripsi": "Update harga sesuai promo",
    "harga": 500000,
    "created_at": "2025-08-24 10:00:00",
    "updated_at": "2025-08-25 09:00:00"
  }
}
```

**Response 400 (validasi gagal)**

```json
{
  "status": false,
  "errors": "Field harga harus numerik"
}
```

### Delete

**Request**

- Method: POST `/services/delete/{id}`
- Header: `Authorization: Bearer <token>`

**Response 200**

```json
{
  "status": true,
  "data": {
    "message": "Service deleted successfully"
  }
}
```

## Catatan

- Semua respons mengikuti pola `JsonResponder` dengan kunci `status` (boolean) dan `data` atau `errors`.
- Error lain (server/exception) dikirim dengan status code 500 dan payload dari `JsonResponder::error`.
- GET endpoints tidak membutuhkan JWT sesuai konfigurasi route.
