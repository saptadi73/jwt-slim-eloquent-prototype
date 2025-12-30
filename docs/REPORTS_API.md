# Reports API Documentation

Dokumentasi lengkap untuk API Laporan Keuangan pada sistem AC Service.

## Daftar Isi

- [Balance Sheet](#balance-sheet)
- [Profit & Loss](#profit--loss)
- [Cash Book](#cash-book)
- [Aged Ledger](#aged-ledger)
- [General Ledger](#general-ledger)
- [Trial Balance](#trial-balance)

---

## Balance Sheet

Laporan Neraca yang menampilkan posisi aset, kewajiban, dan ekuitas perusahaan pada tanggal tertentu.

### Balance Sheet Endpoint

```text
GET /api/reports/balance-sheet
```

### Balance Sheet Query Parameters

| Parameter         | Type          | Required | Default | Description                      |
|-------------------|---------------|----------|---------|----------------------------------|
| as_of_date        | string (date) | No       | Today   | Tanggal laporan (YYYY-MM-DD)     |
| show_zero_balance | boolean       | No       | false   | Tampilkan akun dengan saldo 0    |

### Balance Sheet Request Example

```bash
curl -X GET "http://localhost:8000/api/reports/balance-sheet?as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Balance Sheet Response Example

```json
{
  "status": "success",
  "message": "Balance sheet generated successfully",
  "data": {
    "report_name": "Balance Sheet",
    "as_of_date": "2024-12-31",
    "assets": {
      "accounts": [
        {
          "code": "1110",
          "name": "Cash on Hand",
          "balance": 5000000
        },
        {
          "code": "1120",
          "name": "Accounts Receivable",
          "balance": 15000000
        },
        {
          "code": "1210",
          "name": "Inventory",
          "balance": 25000000
        }
      ],
      "total": 45000000
    },
    "liabilities": {
      "accounts": [
        {
          "code": "2110",
          "name": "Accounts Payable",
          "balance": 8000000
        },
        {
          "code": "2210",
          "name": "Long-term Debt",
          "balance": 12000000
        }
      ],
      "total": 20000000
    },
    "equity": {
      "accounts": [
        {
          "code": "3110",
          "name": "Owner's Capital",
          "balance": 20000000
        },
        {
          "code": "3900",
          "name": "Retained Earnings",
          "balance": 5000000
        }
      ],
      "total": 25000000
    },
    "total_liabilities_and_equity": 45000000,
    "balanced": true
  }
}
```

### Balance Sheet Response Fields

- `report_name`: Nama laporan
- `as_of_date`: Tanggal laporan
- `assets`: Daftar aset dan total
- `liabilities`: Daftar kewajiban dan total
- `equity`: Daftar ekuitas (termasuk retained earnings) dan total
- `total_liabilities_and_equity`: Total kewajiban + ekuitas
- `balanced`: Status keseimbangan (Assets = Liabilities + Equity)

---

## Profit & Loss

Laporan Laba Rugi yang menampilkan pendapatan dan beban untuk periode tertentu.

### Profit & Loss Endpoint

```text
GET /api/reports/profit-loss
```

### Profit & Loss Query Parameters

| Parameter         | Type          | Required | Default            | Description                        |
|-------------------|---------------|----------|--------------------|------------------------------------|
| start_date        | string (date) | No       | First day of month | Tanggal awal periode (YYYY-MM-DD)  |
| end_date          | string (date) | No       | Today              | Tanggal akhir periode (YYYY-MM-DD) |
| show_zero_balance | boolean       | No       | false              | Tampilkan akun dengan saldo 0      |

### Profit & Loss Request Example

```bash
curl -X GET "http://localhost:8000/api/reports/profit-loss?start_date=2024-01-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Profit & Loss Response Example

```json
{
  "status": "success",
  "message": "P&L statement generated successfully",
  "data": {
    "report_name": "Profit & Loss Statement",
    "period": {
      "start_date": "2024-01-01",
      "end_date": "2024-12-31"
    },
    "revenues": {
      "accounts": [
        {
          "code": "4110",
          "name": "AC Service Revenue",
          "amount": 50000000
        },
        {
          "code": "4120",
          "name": "Product Sales",
          "amount": 30000000
        },
        {
          "code": "4130",
          "name": "Rental Income",
          "amount": 10000000
        }
      ],
      "total": 90000000
    },
    "expenses": {
      "accounts": [
        {
          "code": "5110",
          "name": "Cost of Goods Sold",
          "amount": 20000000
        },
        {
          "code": "5210",
          "name": "Salaries Expense",
          "amount": 25000000
        },
        {
          "code": "5310",
          "name": "Utilities Expense",
          "amount": 5000000
        },
        {
          "code": "5410",
          "name": "Transportation Expense",
          "amount": 3000000
        }
      ],
      "total": 53000000
    },
    "net_income": 37000000
  }
}
```

### Profit & Loss Response Fields

- `report_name`: Nama laporan
- `period`: Periode laporan (start_date, end_date)
- `revenues`: Daftar pendapatan dan total
- `expenses`: Daftar beban dan total
- `net_income`: Laba bersih (Total Revenue - Total Expense)

---

## Cash Book

Laporan Buku Kas yang menampilkan semua transaksi kas/bank untuk periode tertentu.

### Cash Book Endpoint

```text
GET /api/reports/cash-book
```

### Cash Book Query Parameters

| Parameter  | Type          | Required | Default            | Description                       |
|------------|---------------|----------|--------------------|---------------------------------  |
| start_date | string (date) | No       | First day of month | Tanggal awal periode (YYYY-MM-DD) |
| end_date   | string (date) | No       | Today              | Tanggal akhir periode (YYYY-MM-DD)|

### Cash Book Request Example

```bash
curl -X GET "http://localhost:8000/api/reports/cash-book?start_date=2024-12-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Cash Book Response Example

```json
{
  "status": "success",
  "message": "Cash book generated successfully",
  "data": {
    "report_name": "Cash Book",
    "period": {
      "start_date": "2024-12-01",
      "end_date": "2024-12-31"
    },
    "opening_balance": 10000000,
    "transactions": [
      {
        "date": "2024-12-05",
        "reference": "JE-001",
        "description": "Payment from customer ABC",
        "account": "Cash on Hand",
        "debit": 5000000,
        "credit": 0,
        "balance": 15000000
      },
      {
        "date": "2024-12-10",
        "reference": "JE-002",
        "description": "Payment to vendor XYZ",
        "account": "Cash on Hand",
        "debit": 0,
        "credit": 2000000,
        "balance": 13000000
      },
      {
        "date": "2024-12-15",
        "reference": "JE-003",
        "description": "Service income cash",
        "account": "Cash on Hand",
        "debit": 3000000,
        "credit": 0,
        "balance": 16000000
      }
    ],
    "total_debit": 8000000,
    "total_credit": 2000000,
    "closing_balance": 16000000
  }
}
```

### Cash Book Response Fields

- `report_name`: Nama laporan
- `period`: Periode laporan
- `opening_balance`: Saldo awal kas
- `transactions`: Daftar transaksi kas
  - `date`: Tanggal transaksi
  - `reference`: Nomor referensi jurnal
  - `description`: Deskripsi transaksi
  - `account`: Nama akun kas/bank
  - `debit`: Jumlah debit
  - `credit`: Jumlah kredit
  - `balance`: Saldo berjalan
- `total_debit`: Total debit periode
- `total_credit`: Total kredit periode
- `closing_balance`: Saldo akhir kas

---

## Aged Ledger

Laporan Umur Piutang/Hutang yang menampilkan outstanding receivables atau payables berdasarkan umur.

### Aged Ledger Endpoint

```text
GET /api/reports/aged-ledger
```

### Aged Ledger Query Parameters

| Parameter  | Type          | Required | Default      | Description                                          |
|------------|---------------|----------|--------------|------------------------------------------------------|
| as_of_date | string (date) | No       | Today        | Tanggal laporan (YYYY-MM-DD)                         |
| type       | string        | No       | receivable   | Tipe: `receivable` (piutang) atau `payable` (hutang) |

### Aged Ledger Request Example

**Aged Receivables (Piutang):**

```bash
curl -X GET "http://localhost:8000/api/reports/aged-ledger?type=receivable&as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Aged Payables (Hutang):**

```bash
curl -X GET "http://localhost:8000/api/reports/aged-ledger?type=payable&as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Aged Ledger Response Example (Receivables)

```json
{
  "status": "success",
  "message": "Aged ledger generated successfully",
  "data": {
    "report_name": "Aged Receivable Ledger",
    "as_of_date": "2024-12-31",
    "type": "receivable",
    "details": [
      {
        "party_id": 1,
        "party_name": "PT ABC Indonesia",
        "transactions": [
          {
            "date": "2024-12-01",
            "reference": "INV-001",
            "description": "AC Service Invoice",
            "amount": 5000000,
            "days_old": 30
          },
          {
            "date": "2024-10-15",
            "reference": "INV-002",
            "description": "Product Sales",
            "amount": 8000000,
            "days_old": 77
          }
        ],
        "current": 5000000,
        "1_30_days": 0,
        "31_60_days": 0,
        "61_90_days": 8000000,
        "over_90_days": 0,
        "total": 13000000
      },
      {
        "party_id": 2,
        "party_name": "CV XYZ Sejahtera",
        "transactions": [
          {
            "date": "2024-09-01",
            "reference": "INV-003",
            "description": "Rental Service",
            "amount": 10000000,
            "days_old": 121
          }
        ],
        "current": 0,
        "1_30_days": 0,
        "31_60_days": 0,
        "61_90_days": 0,
        "over_90_days": 10000000,
        "total": 10000000
      }
    ],
    "summary": {
      "total": 23000000,
      "current": 5000000,
      "1_30_days": 0,
      "31_60_days": 0,
      "61_90_days": 8000000,
      "over_90_days": 10000000
    }
  }
}
```

### Aged Ledger Response Fields

- `report_name`: Nama laporan
- `as_of_date`: Tanggal laporan
- `type`: Tipe laporan (receivable/payable)
- `details`: Detail per customer/vendor
  - `party_id`: ID customer/vendor
  - `party_name`: Nama customer/vendor
  - `transactions`: Daftar transaksi
  - `current`: Outstanding 0-30 hari
  - `1_30_days`: Outstanding 31-60 hari
  - `31_60_days`: Outstanding 61-90 hari
  - `61_90_days`: Outstanding 91-120 hari
  - `over_90_days`: Outstanding > 120 hari
  - `total`: Total outstanding
- `summary`: Ringkasan total semua customer/vendor

---

## General Ledger

Laporan Buku Besar untuk akun tertentu yang menampilkan semua transaksi dan saldo berjalan.

### General Ledger Endpoint

```text
GET /api/reports/general-ledger
```

### General Ledger Query Parameters

| Parameter  | Type          | Required | Default            | Description                       |
|------------|---------------|----------|--------------------|---------------------------------  |
| account_id | integer       | **Yes**  | -                  | ID akun yang akan ditampilkan     |
| start_date | string (date) | No       | First day of month | Tanggal awal periode (YYYY-MM-DD) |
| end_date   | string (date) | No       | Today              | Tanggal akhir periode (YYYY-MM-DD)|

### General Ledger Request Example

```bash
curl -X GET "http://localhost:8000/api/reports/general-ledger?account_id=1&start_date=2024-01-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### General Ledger Response Example

```json
{
  "status": "success",
  "message": "General ledger generated successfully",
  "data": {
    "report_name": "General Ledger",
    "account": {
      "code": "1110",
      "name": "Cash on Hand",
      "type": "asset"
    },
    "period": {
      "start_date": "2024-01-01",
      "end_date": "2024-12-31"
    },
    "opening_balance": 10000000,
    "transactions": [
      {
        "date": "2024-01-05",
        "reference": "JE-001",
        "description": "Opening balance",
        "debit": 10000000,
        "credit": 0,
        "balance": 10000000
      },
      {
        "date": "2024-01-10",
        "reference": "JE-002",
        "description": "Service income",
        "debit": 5000000,
        "credit": 0,
        "balance": 15000000
      },
      {
        "date": "2024-01-15",
        "reference": "JE-003",
        "description": "Payment to vendor",
        "debit": 0,
        "credit": 3000000,
        "balance": 12000000
      },
      {
        "date": "2024-01-20",
        "reference": "JE-004",
        "description": "Customer payment",
        "debit": 8000000,
        "credit": 0,
        "balance": 20000000
      }
    ],
    "total_debit": 23000000,
    "total_credit": 3000000,
    "closing_balance": 20000000
  }
}
```

### General Ledger Response Fields

- `report_name`: Nama laporan
- `account`: Informasi akun
  - `code`: Kode akun
  - `name`: Nama akun
  - `type`: Tipe akun
- `period`: Periode laporan
- `opening_balance`: Saldo awal periode
- `transactions`: Daftar transaksi
  - `date`: Tanggal transaksi
  - `reference`: Nomor referensi jurnal
  - `description`: Deskripsi transaksi
  - `debit`: Jumlah debit
  - `credit`: Jumlah kredit
  - `balance`: Saldo berjalan
- `total_debit`: Total debit periode
- `total_credit`: Total kredit periode
- `closing_balance`: Saldo akhir periode

### General Ledger Error Response

```json
{
  "status": "error",
  "message": "Account not found",
  "data": null
}
```

---

## Trial Balance

Laporan Neraca Saldo yang menampilkan semua akun dengan saldo debit dan kredit.

### Trial Balance Endpoint

```text
GET /api/reports/trial-balance
```

### Trial Balance Query Parameters

| Parameter         | Type          | Required | Default | Description                      |
|-------------------|---------------|----------|---------|----------------------------------|
| as_of_date        | string (date) | No       | Today   | Tanggal laporan (YYYY-MM-DD)     |
| show_zero_balance | boolean       | No       | false   | Tampilkan akun dengan saldo 0    |

### Trial Balance Request Example

```bash
curl -X GET "http://localhost:8000/api/reports/trial-balance?as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Trial Balance Response Example

```json
{
  "status": "success",
  "message": "Trial balance generated successfully",
  "data": {
    "report_name": "Trial Balance",
    "as_of_date": "2024-12-31",
    "accounts": [
      {
        "code": "1110",
        "name": "Cash on Hand",
        "type": "asset",
        "debit": 15000000,
        "credit": 0
      },
      {
        "code": "1120",
        "name": "Accounts Receivable",
        "type": "asset",
        "debit": 10000000,
        "credit": 0
      },
      {
        "code": "2110",
        "name": "Accounts Payable",
        "type": "liability",
        "debit": 0,
        "credit": 8000000
      },
      {
        "code": "3110",
        "name": "Owner's Capital",
        "type": "equity",
        "debit": 0,
        "credit": 20000000
      },
      {
        "code": "4110",
        "name": "Service Revenue",
        "type": "revenue",
        "debit": 0,
        "credit": 50000000
      },
      {
        "code": "5110",
        "name": "Salaries Expense",
        "type": "expense",
        "debit": 25000000,
        "credit": 0
      }
    ],
    "total_debit": 50000000,
    "total_credit": 50000000,
    "balanced": true
  }
}
```

### Trial Balance Response Fields

- `report_name`: Nama laporan
- `as_of_date`: Tanggal laporan
- `accounts`: Daftar akun dengan saldo
  - `code`: Kode akun
  - `name`: Nama akun
  - `type`: Tipe akun
  - `debit`: Saldo debit
  - `credit`: Saldo kredit
- `total_debit`: Total semua debit
- `total_credit`: Total semua kredit
- `balanced`: Status keseimbangan (Total Debit = Total Credit)

---

## General Notes

### Authentication

Semua endpoint memerlukan JWT token yang valid. Sertakan token di header:

```text
Authorization: Bearer YOUR_JWT_TOKEN
```

### Date Format

Semua tanggal menggunakan format ISO 8601: `YYYY-MM-DD`

### Response Format

Semua response menggunakan format JSON standar:

```json
{
  "status": "success" | "error",
  "message": "Response message",
  "data": { }
}
```

### Error Handling

Error responses akan mengembalikan HTTP status code yang sesuai:

- `400 Bad Request` - Parameter tidak valid
- `401 Unauthorized` - Token tidak valid atau expired
- `404 Not Found` - Resource tidak ditemukan
- `500 Internal Server Error` - Error server

### Currency Format

Semua nilai mata uang dalam satuan Rupiah (IDR) tanpa desimal.

### Performance

Untuk laporan dengan periode yang panjang atau banyak transaksi, response time mungkin memerlukan waktu lebih lama. Disarankan untuk:

- Membatasi periode laporan
- Menggunakan pagination untuk data yang besar
- Mengaktifkan cache jika diperlukan

---

## Quick Reference

| Report         | Endpoint                         | Key Parameters                          |
|----------------|----------------------------------|-----------------------------------------|
| Balance Sheet  | `/api/reports/balance-sheet`     | `as_of_date`                            |
| Profit & Loss  | `/api/reports/profit-loss`       | `start_date`, `end_date`                |
| Cash Book      | `/api/reports/cash-book`         | `start_date`, `end_date`                |
| Aged Ledger    | `/api/reports/aged-ledger`       | `type`, `as_of_date`                    |
| General Ledger | `/api/reports/general-ledger`    | `account_id`, `start_date`, `end_date`  |
| Trial Balance  | `/api/reports/trial-balance`     | `as_of_date`                            |

---

## Examples

### Generate Monthly P&L Report

```bash
curl -X GET "http://localhost:8000/api/reports/profit-loss?start_date=2024-12-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Check Year-End Balance Sheet

```bash
curl -X GET "http://localhost:8000/api/reports/balance-sheet?as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Review Customer Outstanding Receivables

```bash
curl -X GET "http://localhost:8000/api/reports/aged-ledger?type=receivable&as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Audit Specific Account Transactions

```bash
curl -X GET "http://localhost:8000/api/reports/general-ledger?account_id=1&start_date=2024-01-01&end_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Verify Books Balance

```bash
curl -X GET "http://localhost:8000/api/reports/trial-balance?as_of_date=2024-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## Support

Untuk pertanyaan atau bantuan teknis, silakan hubungi tim developer atau buka issue di repository project.
