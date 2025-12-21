# Accounting Module - Example API Calls

Quick reference for common API operations with cURL commands.

---

## 🔑 Authentication

All write operations require JWT token:
```bash
export TOKEN="your_jwt_token_here"
export BASE_URL="http://localhost/api"
```

---

## 📊 Chart of Accounts (CoA)

### List All Accounts
```bash
curl "$BASE_URL/chart-of-accounts"
```

**Response:**
```json
{
  "success": true,
  "message": "Chart of accounts retrieved successfully",
  "data": [
    {
      "id": "uuid-1110",
      "code": "1110",
      "name": "Cash in Bank",
      "description": "Main operating bank account",
      "type": "asset",
      "normal_balance": "debit",
      "is_active": true
    }
  ]
}
```

### Get Account by ID
```bash
curl "$BASE_URL/chart-of-accounts/uuid-1110"
```

### Create New Account
```bash
curl -X POST "$BASE_URL/chart-of-accounts" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "1140",
    "name": "Prepaid Expenses",
    "description": "Expenses paid in advance",
    "type": "asset",
    "normal_balance": "debit",
    "is_active": true
  }'
```

### Update Account
```bash
curl -X PUT "$BASE_URL/chart-of-accounts/uuid-1140" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Prepaid insurance and other prepaid expenses"
  }'
```

### Delete Account
```bash
curl -X DELETE "$BASE_URL/chart-of-accounts/uuid-1140" \
  -H "Authorization: Bearer $TOKEN"
```

---

## 📖 Journal Entries

### List All Journal Entries
```bash
curl "$BASE_URL/accounting/journals"
```

### List Journal Entries with Filters
```bash
# By date range
curl "$BASE_URL/accounting/journals?start_date=2025-01-01&end_date=2025-12-31"

# By status
curl "$BASE_URL/accounting/journals?status=posted"

# By reference
curl "$BASE_URL/accounting/journals?reference_number=MISC"
```

### Get Journal Entry by ID
```bash
curl "$BASE_URL/accounting/journals/journal-uuid"
```

---

## 🔐 Create Journal Entries

### 1. Miscellaneous Journal Entry
```bash
curl -X POST "$BASE_URL/accounting/journals/miscellaneous" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "entry_date": "2025-12-21",
    "reference_number": "ADJ-001",
    "description": "Bank reconciliation adjustment",
    "status": "posted",
    "lines": [
      {
        "chart_of_account_id": "uuid-expense",
        "description": "Bank fees charged",
        "debit": 50,
        "credit": 0
      },
      {
        "chart_of_account_id": "uuid-cash",
        "description": "Bank fee reduction",
        "debit": 0,
        "credit": 50
      }
    ]
  }'
```

### 2. Sales Perpetual Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/sales-perpetual" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sale_order_id": "sale-order-uuid",
    "created_by": "user-uuid"
  }'
```

**This automatically creates:**
- Debit: Accounts Receivable
- Credit: Sales Revenue
- Debit: Cost of Goods Sold
- Credit: Inventory

### 3. Sales Payment Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/sales-payment" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_date": "2025-12-21",
    "amount": 5000.00,
    "reference_number": "PAY-001",
    "description": "Payment received from Customer ABC",
    "customer_id": "customer-uuid",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Cash
- Credit: Accounts Receivable

### 4. Purchase Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/purchase" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "purchase_order_id": "purchase-order-uuid",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Inventory
- Credit: Accounts Payable

### 5. Purchase Payment Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/purchase-payment" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_date": "2025-12-21",
    "amount": 3000.00,
    "reference_number": "PAY-PUR-001",
    "description": "Payment to Vendor XYZ",
    "vendor_id": "vendor-uuid",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Accounts Payable
- Credit: Cash

### 6. Expense Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/expense" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "expense_id": "expense-uuid",
    "expense_account_id": "expense-account-uuid",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Expense Account
- Credit: Accounts Payable

### 7. Expense Payment Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/expense-payment" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_date": "2025-12-21",
    "amount": 500.00,
    "reference_number": "PAY-EXP-001",
    "description": "Office supplies payment",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Accounts Payable
- Credit: Cash

### 8. Internal Goods Expenditure Journal
```bash
curl -X POST "$BASE_URL/accounting/journals/internal-expenditure" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "usage_date": "2025-12-21",
    "amount": 250.00,
    "reference_number": "INT-USE-001",
    "description": "Office supplies used from inventory",
    "created_by": "user-uuid"
  }'
```

**Creates:**
- Debit: Internal Expense
- Credit: Inventory

### Delete Journal Entry
```bash
curl -X DELETE "$BASE_URL/accounting/journals/journal-uuid" \
  -H "Authorization: Bearer $TOKEN"
```

---

## 📊 Financial Reports

### 1. Balance Sheet Report
```bash
# As of today
curl "$BASE_URL/accounting/reports/balance-sheet"

# As of specific date
curl "$BASE_URL/accounting/reports/balance-sheet?as_of_date=2025-12-31"

# Include zero balance accounts
curl "$BASE_URL/accounting/reports/balance-sheet?show_zero_balance=true"
```

**Response includes:**
```json
{
  "report_name": "Balance Sheet",
  "as_of_date": "2025-12-31",
  "assets": {
    "accounts": [...],
    "total": 105000.00
  },
  "liabilities": {
    "accounts": [...],
    "total": 15000.00
  },
  "equity": {
    "accounts": [...],
    "total": 90000.00
  },
  "balanced": true
}
```

### 2. Profit & Loss Report
```bash
# Current month
curl "$BASE_URL/accounting/reports/profit-loss"

# Custom period
curl "$BASE_URL/accounting/reports/profit-loss?start_date=2025-01-01&end_date=2025-12-31"
```

**Response includes:**
```json
{
  "report_name": "Profit & Loss Statement",
  "period": {
    "start_date": "2025-12-01",
    "end_date": "2025-12-31"
  },
  "revenues": {
    "accounts": [...],
    "total": 200000.00
  },
  "expenses": {
    "accounts": [...],
    "total": 180000.00
  },
  "net_income": 20000.00
}
```

### 3. Cash Book Report
```bash
# Current month
curl "$BASE_URL/accounting/reports/cash-book"

# Custom period
curl "$BASE_URL/accounting/reports/cash-book?start_date=2025-12-01&end_date=2025-12-31"
```

**Response includes:**
```json
{
  "report_name": "Cash Book",
  "period": {...},
  "opening_balance": 45000.00,
  "transactions": [
    {
      "date": "2025-12-05",
      "reference": "PAY-001",
      "debit": 5000,
      "credit": 0,
      "balance": 50000.00
    }
  ],
  "closing_balance": 47000.00
}
```

### 4. Aged Ledger Report
```bash
# Aged receivables
curl "$BASE_URL/accounting/reports/aged-ledger?type=receivable"

# Aged payables
curl "$BASE_URL/accounting/reports/aged-ledger?type=payable"

# As of specific date
curl "$BASE_URL/accounting/reports/aged-ledger?type=receivable&as_of_date=2025-12-31"
```

**Response includes:**
```json
{
  "report_name": "Aged Receivable Ledger",
  "type": "receivable",
  "details": [
    {
      "party_name": "Customer ABC",
      "total": 8000,
      "current": 3000,
      "1_30_days": 0,
      "31_60_days": 5000,
      "61_90_days": 0,
      "over_90_days": 0
    }
  ],
  "summary": {
    "total": 8000,
    "current": 3000,
    "31_60_days": 5000
  }
}
```

### 5. Trial Balance Report
```bash
# Current date
curl "$BASE_URL/accounting/reports/trial-balance"

# Specific date
curl "$BASE_URL/accounting/reports/trial-balance?as_of_date=2025-12-31"
```

**Response includes:**
```json
{
  "report_name": "Trial Balance",
  "accounts": [
    {
      "code": "1110",
      "name": "Cash in Bank",
      "debit": 50000,
      "credit": 0
    }
  ],
  "total_debit": 50000,
  "total_credit": 50000,
  "balanced": true
}
```

### 6. General Ledger Report
```bash
# For specific account
curl "$BASE_URL/accounting/reports/general-ledger?account_id=uuid-1110"

# With date range
curl "$BASE_URL/accounting/reports/general-ledger?account_id=uuid-1110&start_date=2025-12-01&end_date=2025-12-31"
```

**Response includes:**
```json
{
  "report_name": "General Ledger",
  "account": {
    "code": "1110",
    "name": "Cash in Bank"
  },
  "transactions": [
    {
      "date": "2025-12-05",
      "reference": "PAY-001",
      "debit": 5000,
      "credit": 0,
      "balance": 50000
    }
  ],
  "closing_balance": 50000
}
```

---

## 🔄 Complete Workflow Example

### Step 1: Create Required Accounts
```bash
# Create Cash account
curl -X POST "$BASE_URL/chart-of-accounts" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "1110",
    "name": "Cash in Bank",
    "type": "asset",
    "normal_balance": "debit"
  }'

# Create A/R account
curl -X POST "$BASE_URL/chart-of-accounts" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "1120",
    "name": "Accounts Receivable",
    "type": "asset",
    "normal_balance": "debit"
  }'

# Create Sales Revenue account
curl -X POST "$BASE_URL/chart-of-accounts" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "4100",
    "name": "Sales Revenue",
    "type": "revenue",
    "normal_balance": "credit"
  }'
```

### Step 2: Record a Sale
```bash
curl -X POST "$BASE_URL/accounting/journals/sales-perpetual" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sale_order_id": "sale-uuid-123"
  }'
```

### Step 3: Record Customer Payment
```bash
curl -X POST "$BASE_URL/accounting/journals/sales-payment" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_date": "2025-12-21",
    "amount": 5000,
    "customer_id": "customer-uuid"
  }'
```

### Step 4: Check Reports
```bash
# Balance Sheet
curl "$BASE_URL/accounting/reports/balance-sheet"

# Cash Book
curl "$BASE_URL/accounting/reports/cash-book"

# Aged Receivables
curl "$BASE_URL/accounting/reports/aged-ledger?type=receivable"
```

---

## 🐛 Error Examples

### Unbalanced Journal Entry
```bash
curl -X POST "$BASE_URL/accounting/journals/miscellaneous" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "entry_date": "2025-12-21",
    "lines": [
      {"chart_of_account_id": "uuid", "debit": 100, "credit": 0},
      {"chart_of_account_id": "uuid", "debit": 0, "credit": 50}
    ]
  }'
```

**Response:**
```json
{
  "success": false,
  "message": "Debit and credit must be balanced",
  "data": null
}
```

### Account Not Found
```bash
curl "$BASE_URL/chart-of-accounts/invalid-uuid"
```

**Response:**
```json
{
  "success": false,
  "message": "Chart of account not found",
  "data": null
}
```

### Missing Required Field
```bash
curl -X POST "$BASE_URL/accounting/journals/sales-payment" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 5000
  }'
```

**Response:**
```json
{
  "success": false,
  "message": "payment_date and amount are required",
  "data": null
}
```

---

## 💡 Tips & Tricks

### Using with Postman
1. Set environment variable: `base_url = http://localhost/api`
2. Set environment variable: `token = your_jwt_token`
3. Use `{{base_url}}` and `{{token}}` in requests

### Saving Responses to File
```bash
curl "$BASE_URL/accounting/reports/balance-sheet" > balance_sheet.json
```

### Pretty Print JSON
```bash
curl "$BASE_URL/accounting/reports/balance-sheet" | jq '.'
```

### Check Status Code
```bash
curl -w "\nStatus: %{http_code}\n" "$BASE_URL/chart-of-accounts"
```

### Debug Request
```bash
curl -v "$BASE_URL/chart-of-accounts"
```

---

## 📚 Related Documentation

- Full API Reference: `docs/ACCOUNTING_API.md`
- Quick Reference: `docs/ACCOUNTING_QUICK_REFERENCE.md`
- Implementation Guide: `docs/ACCOUNTING_IMPLEMENTATION.md`

---

**Generated:** December 21, 2025  
**Version:** 1.0  
**Status:** Ready to Use  
