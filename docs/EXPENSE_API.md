# Expense API Documentation

## Overview

API untuk mengelola data pengeluaran/biaya (expenses) dalam sistem. Mendukung upload bukti pembayaran dan integrasi dengan sistem akuntansi.

## Base URL

```text
http://localhost:8080
```

---

## Chart of Accounts Endpoints

### 0. Get Expense Chart of Accounts

**Endpoint:** `GET /chart-of-accounts/expenses`

**Description:** Mengambil daftar semua Chart of Accounts dengan tipe expense. Endpoint ini berguna untuk mendapatkan pilihan akun expense saat membuat pengeluaran baru.

**Headers:**

```text
Authorization: Bearer {jwt_token} (optional)
```

**Response Success (200):**

```json
{
  "success": true,
  "message": "Expense chart of accounts retrieved successfully",
  "data": [
    {
      "id": "uuid-1",
      "code": "5100",
      "name": "Cost of Goods Sold",
      "type": "expense",
      "parent_id": null,
      "description": "Direct costs of producing goods",
      "is_active": true,
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    },
    {
      "id": "uuid-2",
      "code": "5200",
      "name": "Operating Expenses",
      "type": "expense",
      "parent_id": null,
      "description": "Day-to-day business operating costs",
      "is_active": true,
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    }
  ]
}
```

**Usage Example:**

```javascript
// Fetch expense accounts untuk dropdown/select
const response = await fetch('http://localhost:8080/chart-of-accounts/expenses');
const { data } = await response.json();

// Use in HTML select
const select = document.getElementById('expense-account');
data.forEach(account => {
  const option = new Option(`${account.code} - ${account.name}`, account.id);
  select.add(option);
});
```

---

### 1. Get Liability Chart of Accounts

**Endpoint:** `GET /chart-of-accounts/liabilities`

**Description:** Mengambil daftar semua Chart of Accounts dengan tipe liability (hutang). Endpoint ini berguna untuk mendapatkan pilihan akun hutang saat membuat journal expense.

**Headers:**

```text
Authorization: Bearer {jwt_token} (optional)
```

**Response Success (200):**

```json
{
  "success": true,
  "message": "Liability chart of accounts retrieved successfully",
  "data": [
    {
      "id": "uuid-1",
      "code": "2010",
      "name": "Hutang Usaha",
      "type": "liability",
      "parent_id": null,
      "description": "Accounts Payable",
      "is_active": true,
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    },
    {
      "id": "uuid-2",
      "code": "2030",
      "name": "Hutang Perusahaan",
      "type": "liability",
      "parent_id": null,
      "description": "Company Liabilities",
      "is_active": true,
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    }
  ]
}
```

**Usage Example:**

```javascript
// Fetch liability accounts untuk dropdown/select
const response = await fetch('http://localhost:8080/chart-of-accounts/liabilities');
const { data } = await response.json();

// Use in HTML select
const select = document.getElementById('liability-account');
data.forEach(account => {
  const option = new Option(`${account.code} - ${account.name}`, account.id);
  select.add(option);
});
```

---

## Endpoints

### 1. List All Expenses

**Endpoint:** `GET /expenses`

**Description:** Mengambil daftar semua pengeluaran/biaya.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**Response Success (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "tanggal": "2025-12-29",
      "keterangan": "Pembelian alat tulis kantor",
      "jumlah": 500000,
      "kategori": "Office Supplies",
      "vendor_id": "uuid",
      "vendor_name": "Toko ABC",
      "bukti": "uploads/expense_bukti_123.pdf",
      "chart_of_account_id": "uuid",
      "account_code": "5200",
      "account_name": "Operating Expenses",
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    }
  ]
}
```

---

### 2. Get Single Expense

**Endpoint:** `GET /expenses/{id}`

**Description:** Mengambil detail pengeluaran berdasarkan ID.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**URL Parameters:**

- `id` (required) - UUID expense

**Response Success (200):**

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "tanggal": "2025-12-29",
    "keterangan": "Pembelian alat tulis kantor",
    "jumlah": 500000,
    "kategori": "Office Supplies",
    "vendor_id": "uuid",
    "vendor": {
      "id": "uuid",
      "nama": "Toko ABC",
      "email": "toko@abc.com",
      "telepon": "081234567890"
    },
    "bukti": "uploads/expense_bukti_123.pdf",
    "chart_of_account_id": "uuid",
    "chart_of_account": {
      "id": "uuid",
      "code": "5200",
      "name": "Operating Expenses",
      "type": "expense"
    },
    "created_at": "2025-12-29T10:00:00.000000Z",
    "updated_at": "2025-12-29T10:00:00.000000Z"
  }
}
```

**Response Error (404):**

```json
{
  "success": false,
  "error": "Expense not found"
}
```

---

### 3. Create New Expense

**Endpoint:** `POST /expenses`

**Description:** Membuat pengeluaran/biaya baru dengan optional upload bukti.

**Headers:**

```text
Authorization: Bearer {jwt_token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**

```text
tanggal (required): "2025-12-29"
keterangan (required): "Pembelian alat tulis kantor"
jumlah (required): 500000
jenis (optional): "internal" | "workorder" | lainnya (default: "internal")
kategori (optional): "Office Supplies"
vendor_id (optional): "uuid"
chart_of_account_id (required): "uuid"
bukti (optional): [file upload - PDF/Image]
```

**Note:** Field `keterangan` juga bisa dikirim sebagai `deskripsi`, `notes`, atau `description` dan akan otomatis dimapping.

**Example using cURL:**

```bash
curl -X POST http://localhost:8080/expenses \
  -H "Authorization: Bearer {token}" \
  -F "tanggal=2025-12-29" \
  -F "keterangan=Pembelian alat tulis kantor" \
  -F "jumlah=500000" \
  -F "kategori=Office Supplies" \
  -F "vendor_id=uuid" \
  -F "chart_of_account_id=uuid" \
  -F "bukti=@/path/to/receipt.pdf"
```

**Response Success (201):**

```json
{
  "success": true,
  "message": "Expense created successfully",
  "data": {
    "id": "uuid",
    "tanggal": "2025-12-29",
    "keterangan": "Pembelian alat tulis kantor",
    "jumlah": 500000,
    "kategori": "Office Supplies",
    "vendor_id": "uuid",
    "bukti": "uploads/expense_bukti_20251229_abc123.pdf",
    "chart_of_account_id": "uuid",
    "created_at": "2025-12-29T10:00:00.000000Z",
    "updated_at": "2025-12-29T10:00:00.000000Z"
  }
}
```

**Response Error (400):**

```json
{
  "success": false,
  "error": "Validation failed",
  "details": {
    "tanggal": "Required field",
    "keterangan": "Required field",
    "jumlah": "Must be a positive number",
    "chart_of_account_id": "Required field"
  }
}
```

---

### 4. Update Expense

**Endpoint:** `PUT /expenses/{id}`

**Description:** Mengupdate data pengeluaran. Dapat mengupload bukti baru atau mempertahankan yang lama.

**Headers:**

```text
Authorization: Bearer {jwt_token}
Content-Type: multipart/form-data
```

**URL Parameters:**

- `id` (required) - UUID expense

**Request Body (Form Data):**

```text
tanggal (optional): "2025-12-29"
keterangan (optional): "Pembelian alat tulis kantor (Updated)"
jumlah (optional): 550000
kategori (optional): "Office Supplies"
vendor_id (optional): "uuid"
chart_of_account_id (optional): "uuid"
bukti (optional): [file upload - PDF/Image] - jika dikirim, akan replace bukti lama
```

**Note:** Field `keterangan` juga bisa dikirim sebagai `deskripsi`, `notes`, atau `description` dan akan otomatis dimapping.

**Example using cURL:**

```bash
curl -X PUT http://localhost:8080/expenses/{id} \
  -H "Authorization: Bearer {token}" \
  -F "keterangan=Pembelian alat tulis kantor (Updated)" \
  -F "jumlah=550000" \
  -F "bukti=@/path/to/new_receipt.pdf"
```

**Response Success (200):**

```json
{
  "success": true,
  "message": "Expense updated successfully",
  "data": {
    "id": "uuid",
    "tanggal": "2025-12-29",
    "keterangan": "Pembelian alat tulis kantor (Updated)",
    "jumlah": 550000,
    "kategori": "Office Supplies",
    "vendor_id": "uuid",
    "bukti": "uploads/expense_bukti_20251229_xyz789.pdf",
    "chart_of_account_id": "uuid",
    "created_at": "2025-12-29T10:00:00.000000Z",
    "updated_at": "2025-12-29T11:30:00.000000Z"
  }
}
```

**Response Error (404):**

```json
{
  "success": false,
  "error": "Expense not found"
}
```

---

### 5. Delete Expense

**Endpoint:** `DELETE /expenses/{id}`

**Description:** Menghapus pengeluaran berdasarkan ID. File bukti yang terkait juga akan dihapus.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**URL Parameters:**

- `id` (required) - UUID expense

**Response Success (200):**

```json
{
  "success": true,
  "message": "Expense deleted successfully"
}
```

**Response Error (404):**

```json
{
  "success": false,
  "error": "Expense not found"
}
```

---

## Biaya Workorder Endpoints

### 6. List All Biaya Workorder

**Endpoint:** `GET /biaya-workorder`

**Description:** Mengambil daftar semua biaya yang terkait dengan workorder.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**Response Success (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "workorder_id": "uuid",
      "product_id": "uuid",
      "jumlah": 3,
      "harga": 250000,
      "total": 750000,
      "bukti": "uploads/biaya_wo_123.pdf",
      "created_at": "2025-12-29T10:00:00.000000Z",
      "updated_at": "2025-12-29T10:00:00.000000Z"
    }
  ]
}
```

---

### 7. Get Single Biaya Workorder

**Endpoint:** `GET /biaya-workorder/{id}`

**Description:** Mengambil detail biaya workorder berdasarkan ID.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**URL Parameters:**

- `id` (required) - UUID biaya workorder

**Response Success (200):**

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "workorder_id": "uuid",
    "product_id": "uuid",
    "workorder": {
      "id": "uuid",
      "no_workorder": "WO-2025-001",
      "tanggal": "2025-12-28",
      "customer_id": "uuid",
      "status": "in_progress"
    },
    "product": {
      "id": "uuid",
      "nama": "Kompresor AC",
      "satuan": "unit"
    },
    "jumlah": 3,
    "harga": 250000,
    "total": 750000,
    "bukti": "uploads/biaya_wo_123.pdf",
    "created_at": "2025-12-29T10:00:00.000000Z",
    "updated_at": "2025-12-29T10:00:00.000000Z"
  }
}
```

---

### 8. Create Biaya Workorder

**Endpoint:** `POST /biaya-workorder`

**Description:** Membuat biaya workorder baru.

**Headers:**

```text
Authorization: Bearer {jwt_token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**

```text
workorder_id (required): "uuid"
product_id (required): "uuid"
jumlah (required): 3
harga (required): 250000
bukti (optional): [file upload - PDF/Image]
```

**Response Success (201):**

```json
{
  "success": true,
  "message": "Biaya workorder created successfully",
  "data": {
    "id": "uuid",
    "workorder_id": "uuid",
    "product_id": "uuid",
    "jumlah": 3,
    "harga": 250000,
    "total": 750000,
    "bukti": "uploads/biaya_wo_20251229_abc123.pdf",
    "created_at": "2025-12-29T10:00:00.000000Z",
    "updated_at": "2025-12-29T10:00:00.000000Z"
  }
}
```

---

### 9. Update Biaya Workorder

**Endpoint:** `PUT /biaya-workorder/{id}`

**Description:** Mengupdate biaya workorder.

**Headers:**

```text
Authorization: Bearer {jwt_token}
Content-Type: multipart/form-data
```

**URL Parameters:**

- `id` (required) - UUID biaya workorder

**Request Body:** Same as create

**Response Success (200):**

```json
{
  "success": true,
  "message": "Biaya workorder updated successfully",
  "data": {}
}
```

---

### 10. Delete Biaya Workorder

**Endpoint:** `DELETE /biaya-workorder/{id}`

**Description:** Menghapus biaya workorder.

**Headers:**

```text
Authorization: Bearer {jwt_token}
```

**URL Parameters:**

- `id` (required) - UUID biaya workorder

**Response Success (200):**

```json
{
  "success": true,
  "message": "Biaya workorder deleted successfully"
}
```

---

## Data Models

### Expense Model

```json
{
  "id": "uuid",
  "tanggal": "date",
  "keterangan": "text",
  "jumlah": "decimal(15,2)",
  "jenis": "string",
  "kategori": "string|null",
  "vendor_id": "uuid|null",
  "bukti": "string|null",
  "chart_of_account_id": "uuid",
  "status": "string",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

### Biaya Workorder Model

```json
{
  "id": "uuid",
  "workorder_id": "uuid",
  "product_id": "uuid",
  "jumlah": "integer",
  "harga": "integer",
  "total": "integer",
  "bukti": "string|null",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

---

## File Upload Specifications

### Supported File Types

- PDF (.pdf)
- Images (.jpg, .jpeg, .png)

### Maximum File Size

- 5 MB

### Storage Location

- Files stored in: `public/uploads/`
- Naming pattern: `expense_bukti_{timestamp}_{random}.{ext}`

### Upload Notes

- Original filename is preserved in database but stored with unique name
- Old files are automatically deleted when updating with new file
- Files are deleted when expense is deleted

---

## Integration with Accounting

### Create Expense Journal Entry

**Endpoint:** `POST /accounting/journals/journal-expense`

**Description:** Membuat journal entry untuk expense dengan pemilihan COA expense dan liability dari front-end.

**Headers:**

```text
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

**Request Payload:**

```json
{
  "expense_account_id": "uuid",
  "liability_account_id": "uuid",
  "amount": 500000,
  "entry_date": "2025-12-29",
  "description": "Pembelian alat tulis kantor",
  "reference_number": "EXP-2025-001",
  "vendor_id": "uuid",
  "created_by": "user-uuid"
}
```

**Field Details:**

- `expense_account_id` (required) - UUID dari Chart of Account tipe expense (dari front-end)
- `liability_account_id` (optional) - UUID dari Chart of Account tipe liability (Hutang). Jika tidak disediakan, sistem akan otomatis gunakan COA 2030/2010/2020
- `amount` (required) - Jumlah expense
- `entry_date` (required) - Tanggal transaksi
- `description` (optional) - Deskripsi journal entry
- `reference_number` (optional) - Nomor referensi (default: auto-generated)
- `vendor_id` (optional) - UUID vendor jika ada
- `created_by` (optional) - UUID user pembuat

**Response Success (201):**

```json
{
  "success": true,
  "message": "Expense journal created successfully",
  "data": {
    "id": "uuid",
    "entry_date": "2025-12-29",
    "reference_number": "EXP-2025-001",
    "description": "Pembelian alat tulis kantor",
    "status": "posted",
    "journal_lines": [
      {
        "id": "uuid",
        "chart_of_account_id": "uuid-expense",
        "chart_of_account": {
          "code": "5200",
          "name": "Operating Expenses",
          "type": "expense"
        },
        "description": "Expense: Pembelian alat tulis kantor",
        "debit": 500000,
        "credit": 0
      },
      {
        "id": "uuid",
        "chart_of_account_id": "uuid-liability",
        "chart_of_account": {
          "code": "2030",
          "name": "Hutang Perusahaan",
          "type": "liability"
        },
        "description": "Liability: Pembelian alat tulis kantor",
        "debit": 0,
        "credit": 500000,
        "vendor_id": "uuid"
      }
    ],
    "created_at": "2025-12-29T10:00:00.000000Z"
  }
}
```

**Journal Entry Created:**

- **Debit:** Expense Account (dari front-end, contoh: 5200 - Operating Expenses)
- **Credit:** Liability Account (dari front-end, contoh: 2030 - Hutang Perusahaan)

**Example using cURL:**

```bash
curl -X POST http://localhost:8080/accounting/journals/journal-expense \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "expense_account_id": "uuid-expense-account",
    "liability_account_id": "uuid-liability-account",
    "amount": 500000,
    "entry_date": "2025-12-29",
    "description": "Pembelian alat tulis kantor",
    "reference_number": "EXP-2025-001"
  }'
```

**Example using JavaScript/Axios:**

```javascript
const journalData = {
  expense_account_id: expenseAccountId,
  liability_account_id: liabilityAccountId,
  amount: 500000,
  entry_date: '2025-12-29',
  description: 'Pembelian alat tulis kantor',
  reference_number: 'EXP-2025-001'
};

const response = await axios.post(
  'http://localhost:8080/accounting/journals/journal-expense',
  journalData,
  {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  }
);
```

**Recommended COA untuk Expense:**

- 5100 - Cost of Goods Sold
- 5200 - Operating Expenses
- 5300 - Salaries Expense
- 5400 - Utilities Expense

**Recommended COA untuk Liability (Hutang):**

- 2030 - Hutang Perusahaan (preferred)
- 2010 - Hutang Usaha
- 2020 - Hutang Pihak Ketiga

**Note on Error Handling:**

- Jika `liability_account_id` tidak disediakan, sistem akan otomatis mencari COA dengan kode 2030 → 2010 → 2020
- Jika tidak ada satupun akun hutang yang ditemukan, akan menampilkan error dengan daftar akun hutang yang tersedia
- Pastikan minimal ada satu akun hutang (type='liability') di database sebelum membuat journal expense

**Note:** Front-end dapat menggunakan endpoint `GET /chart-of-accounts/expenses` untuk mendapatkan list COA expense.

---

## Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 400 | Validation failed | Request data tidak valid |
| 401 | Unauthorized | Token tidak valid atau expired |
| 404 | Not found | Data tidak ditemukan |
| 413 | File too large | File lebih dari 5MB |
| 415 | Unsupported file type | Format file tidak didukung |
| 500 | Internal server error | Server error |

---

## Example Usage

### Create Expense with File Upload (JavaScript/Axios)

```javascript
const formData = new FormData();
formData.append('tanggal', '2025-12-29');
formData.append('keterangan', 'Pembelian alat tulis');
formData.append('jumlah', 500000);
formData.append('chart_of_account_id', 'uuid-expense-account');
formData.append('bukti', fileInput.files[0]);

const response = await axios.post('http://localhost:8080/expenses', formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  }
});
```

### Create Expense (PHP)

```php
$ch = curl_init('http://localhost:8080/expenses');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'tanggal' => '2025-12-29',
    'keterangan' => 'Pembelian alat tulis',
    'jumlah' => 500000,
    'chart_of_account_id' => 'uuid-expense-account',
    'bukti' => new CURLFile('/path/to/receipt.pdf')
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

---

## Related Documentation

- [Accounting API](ACCOUNTING_API.md) - Integration dengan sistem akuntansi
- [Vendor API](API_DOCUMENTATION.md#vendors) - Manajemen vendor
- [Chart of Accounts](ACCOUNTING_API.md#chart-of-accounts) - COA untuk expense accounts
