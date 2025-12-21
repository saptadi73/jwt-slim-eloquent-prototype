# Accounting Module API Documentation

This document describes the Accounting Module API endpoints for managing Chart of Accounts, Journal Entries, and Financial Reports.

## Base URL
```
/api
```

## Authentication
Most endpoints require JWT authentication. Include the JWT token in the Authorization header:
```
Authorization: Bearer <your_jwt_token>
```

---

## Table of Contents
1. [Chart of Accounts (CoA)](#chart-of-accounts-coa)
2. [Journal Entries](#journal-entries)
3. [Financial Reports](#financial-reports)

---

## Chart of Accounts (CoA)

### 1. Get All Chart of Accounts
**Endpoint:** `GET /chart-of-accounts`

**Response:**
```json
{
  "success": true,
  "message": "Chart of accounts retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "code": "1110",
      "name": "Cash in Bank",
      "description": "Main operating bank account",
      "type": "asset",
      "category": "current_asset",
      "normal_balance": "debit",
      "is_active": true,
      "created_at": "2025-12-21 10:00:00",
      "updated_at": "2025-12-21 10:00:00"
    }
  ]
}
```

### 2. Get Chart of Account by ID
**Endpoint:** `GET /chart-of-accounts/{id}`

**Parameters:**
- `id` (path) - UUID of the chart of account

**Response:**
```json
{
  "success": true,
  "message": "Chart of account retrieved successfully",
  "data": {
    "id": "uuid",
    "code": "1110",
    "name": "Cash in Bank",
    "description": "Main operating bank account",
    "type": "asset",
    "category": "current_asset",
    "normal_balance": "debit",
    "is_active": true
  }
}
```

### 3. Create Chart of Account
**Endpoint:** `POST /chart-of-accounts`

**Authentication:** Required

**Request Body:**
```json
{
  "code": "1110",
  "name": "Cash in Bank",
  "description": "Main operating bank account",
  "type": "asset",
  "category": "current_asset",
  "normal_balance": "debit",
  "is_active": true
}
```

**Field Descriptions:**
- `code` (required) - Account code (e.g., "1110")
- `name` (required) - Account name
- `description` (optional) - Detailed description
- `type` (required) - Account type: "asset", "liability", "equity", "revenue", "expense"
- `category` (optional) - Sub-category of the type
- `normal_balance` (required) - "debit" or "credit"
- `is_active` (optional) - Boolean, default true

**Response:**
```json
{
  "success": true,
  "message": "Chart of account created successfully",
  "data": {
    "id": "uuid",
    "code": "1110",
    "name": "Cash in Bank",
    ...
  }
}
```

### 4. Update Chart of Account
**Endpoint:** `PUT /chart-of-accounts/{id}`

**Authentication:** Required

**Request Body:** Same as create

### 5. Delete Chart of Account
**Endpoint:** `DELETE /chart-of-accounts/{id}`

**Authentication:** Required

---

## Journal Entries

### 1. Get All Journal Entries
**Endpoint:** `GET /accounting/journals`

**Query Parameters:**
- `start_date` (optional) - Filter by start date (YYYY-MM-DD)
- `end_date` (optional) - Filter by end date (YYYY-MM-DD)
- `status` (optional) - Filter by status ("draft", "posted")
- `reference_number` (optional) - Search by reference number

**Example:**
```
GET /accounting/journals?start_date=2025-01-01&end_date=2025-12-31&status=posted
```

**Response:**
```json
{
  "success": true,
  "message": "Journal entries retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "entry_date": "2025-12-21",
      "reference_number": "MISC-001",
      "description": "Miscellaneous adjustment",
      "status": "posted",
      "created_by": "user_id",
      "journal_lines": [
        {
          "id": "uuid",
          "chart_of_account_id": "uuid",
          "description": "Debit entry",
          "debit": "1000.00",
          "credit": "0.00"
        }
      ]
    }
  ]
}
```

### 2. Get Journal Entry by ID
**Endpoint:** `GET /accounting/journals/{id}`

**Response:** Single journal entry with all related lines

### 3. Delete Journal Entry
**Endpoint:** `DELETE /accounting/journals/{id}`

**Authentication:** Required

### 4. Create Miscellaneous Journal Entry
**Endpoint:** `POST /accounting/journals/miscellaneous`

**Authentication:** Required

**Description:** Create a manual journal entry for adjustments, corrections, or other non-standard transactions.

**Request Body:**
```json
{
  "entry_date": "2025-12-21",
  "reference_number": "MISC-001",
  "description": "Bank reconciliation adjustment",
  "status": "posted",
  "created_by": "user_id",
  "lines": [
    {
      "chart_of_account_id": "uuid-cash-account",
      "description": "Bank fees",
      "debit": 0,
      "credit": 50
    },
    {
      "chart_of_account_id": "uuid-expense-account",
      "description": "Bank charges",
      "debit": 50,
      "credit": 0
    }
  ]
}
```

**Important:** Debits must equal credits. The system validates this automatically.

### 5. Create Sales Perpetual Journal
**Endpoint:** `POST /accounting/journals/sales-perpetual`

**Authentication:** Required

**Description:** Records a sale with automatic inventory reduction (perpetual inventory system).

**Request Body:**
```json
{
  "sale_order_id": "uuid",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
1. Debit: Accounts Receivable / Credit: Sales Revenue (for sale amount)
2. Debit: Cost of Goods Sold / Credit: Inventory (for cost amount)

**Required Accounts:**
- 1120 - Accounts Receivable
- 4100 - Sales Revenue
- 5100 - Cost of Goods Sold
- 1130 - Inventory

### 6. Create Sales Payment Journal
**Endpoint:** `POST /accounting/journals/sales-payment`

**Authentication:** Required

**Description:** Records payment received from a customer.

**Request Body:**
```json
{
  "payment_date": "2025-12-21",
  "reference_number": "PAY-001",
  "amount": 1000.00,
  "description": "Payment from Customer ABC",
  "customer_id": "uuid",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Cash/Bank
- Credit: Accounts Receivable

### 7. Create Purchase Journal
**Endpoint:** `POST /accounting/journals/purchase`

**Authentication:** Required

**Description:** Records a purchase of goods or services.

**Request Body:**
```json
{
  "purchase_order_id": "uuid",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Inventory
- Credit: Accounts Payable

**Required Accounts:**
- 1130 - Inventory
- 2110 - Accounts Payable

### 8. Create Purchase Payment Journal
**Endpoint:** `POST /accounting/journals/purchase-payment`

**Authentication:** Required

**Description:** Records payment to a vendor.

**Request Body:**
```json
{
  "payment_date": "2025-12-21",
  "reference_number": "PAY-PUR-001",
  "amount": 5000.00,
  "description": "Payment to Vendor XYZ",
  "vendor_id": "uuid",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Accounts Payable
- Credit: Cash/Bank

### 9. Create Expense Journal
**Endpoint:** `POST /accounting/journals/expense`

**Authentication:** Required

**Description:** Records an expense incurred.

**Request Body:**
```json
{
  "expense_id": "uuid",
  "expense_account_id": "uuid",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Expense Account (specified)
- Credit: Accounts Payable

### 10. Create Expense Payment Journal
**Endpoint:** `POST /accounting/journals/expense-payment`

**Authentication:** Required

**Description:** Records payment of an expense.

**Request Body:**
```json
{
  "payment_date": "2025-12-21",
  "reference_number": "PAY-EXP-001",
  "amount": 500.00,
  "description": "Office supplies payment",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Accounts Payable
- Credit: Cash/Bank

### 11. Create Internal Goods Expenditure Journal
**Endpoint:** `POST /accounting/journals/internal-expenditure`

**Authentication:** Required

**Description:** Records goods used internally (not for sale), such as supplies used in operations.

**Request Body:**
```json
{
  "usage_date": "2025-12-21",
  "reference_number": "INT-USE-001",
  "amount": 250.00,
  "description": "Office supplies used from inventory",
  "created_by": "user_id"
}
```

**Journal Entries Created:**
- Debit: Internal Expense (5200)
- Credit: Inventory (1130)

---

## Financial Reports

### 1. Balance Sheet Report
**Endpoint:** `GET /accounting/reports/balance-sheet`

**Description:** Shows the financial position (assets, liabilities, equity) at a specific date.

**Query Parameters:**
- `as_of_date` (optional) - Date for the report (YYYY-MM-DD), defaults to today
- `show_zero_balance` (optional) - Include accounts with zero balance

**Example:**
```
GET /accounting/reports/balance-sheet?as_of_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "Balance sheet generated successfully",
  "data": {
    "report_name": "Balance Sheet",
    "as_of_date": "2025-12-31",
    "assets": {
      "accounts": [
        {
          "code": "1110",
          "name": "Cash in Bank",
          "balance": 50000.00
        },
        {
          "code": "1120",
          "name": "Accounts Receivable",
          "balance": 25000.00
        },
        {
          "code": "1130",
          "name": "Inventory",
          "balance": 30000.00
        }
      ],
      "total": 105000.00
    },
    "liabilities": {
      "accounts": [
        {
          "code": "2110",
          "name": "Accounts Payable",
          "balance": 15000.00
        }
      ],
      "total": 15000.00
    },
    "equity": {
      "accounts": [
        {
          "code": "3100",
          "name": "Owner's Capital",
          "balance": 75000.00
        },
        {
          "code": "3900",
          "name": "Retained Earnings",
          "balance": 15000.00
        }
      ],
      "total": 90000.00
    },
    "total_liabilities_and_equity": 105000.00,
    "balanced": true
  }
}
```

### 2. Profit & Loss Report
**Endpoint:** `GET /accounting/reports/profit-loss`

**Description:** Shows revenues and expenses for a period, resulting in net income or loss.

**Query Parameters:**
- `start_date` (optional) - Period start date (YYYY-MM-DD), defaults to first day of current month
- `end_date` (optional) - Period end date (YYYY-MM-DD), defaults to today
- `show_zero_balance` (optional) - Include accounts with zero balance

**Example:**
```
GET /accounting/reports/profit-loss?start_date=2025-01-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "P&L statement generated successfully",
  "data": {
    "report_name": "Profit & Loss Statement",
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    },
    "revenues": {
      "accounts": [
        {
          "code": "4100",
          "name": "Sales Revenue",
          "amount": 150000.00
        },
        {
          "code": "4200",
          "name": "Service Revenue",
          "amount": 50000.00
        }
      ],
      "total": 200000.00
    },
    "expenses": {
      "accounts": [
        {
          "code": "5100",
          "name": "Cost of Goods Sold",
          "amount": 90000.00
        },
        {
          "code": "5200",
          "name": "Operating Expenses",
          "amount": 40000.00
        },
        {
          "code": "5300",
          "name": "Salaries Expense",
          "amount": 50000.00
        }
      ],
      "total": 180000.00
    },
    "net_income": 20000.00
  }
}
```

### 3. Cash Book Report
**Endpoint:** `GET /accounting/reports/cash-book`

**Description:** Shows all cash/bank transactions for a period with running balance.

**Query Parameters:**
- `start_date` (optional) - Period start date (YYYY-MM-DD)
- `end_date` (optional) - Period end date (YYYY-MM-DD)

**Example:**
```
GET /accounting/reports/cash-book?start_date=2025-12-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "Cash book generated successfully",
  "data": {
    "report_name": "Cash Book",
    "period": {
      "start_date": "2025-12-01",
      "end_date": "2025-12-31"
    },
    "opening_balance": 45000.00,
    "transactions": [
      {
        "date": "2025-12-05",
        "reference": "PAY-SALE-001",
        "description": "Payment from customer",
        "account": "Cash in Bank",
        "debit": 5000.00,
        "credit": 0.00,
        "balance": 50000.00
      },
      {
        "date": "2025-12-10",
        "reference": "PAY-PUR-001",
        "description": "Payment to vendor",
        "account": "Cash in Bank",
        "debit": 0.00,
        "credit": 3000.00,
        "balance": 47000.00
      }
    ],
    "total_debit": 5000.00,
    "total_credit": 3000.00,
    "closing_balance": 47000.00
  }
}
```

### 4. Aged Ledger Report (Receivable/Payable Aging)
**Endpoint:** `GET /accounting/reports/aged-ledger`

**Description:** Shows outstanding receivables or payables grouped by age (how long they've been outstanding).

**Query Parameters:**
- `type` (required) - "receivable" or "payable"
- `as_of_date` (optional) - Date for the report (YYYY-MM-DD), defaults to today

**Example:**
```
GET /accounting/reports/aged-ledger?type=receivable&as_of_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "Aged ledger generated successfully",
  "data": {
    "report_name": "Aged Receivable Ledger",
    "as_of_date": "2025-12-31",
    "type": "receivable",
    "details": [
      {
        "party_id": "customer-uuid-1",
        "party_name": "ABC Company",
        "transactions": [
          {
            "date": "2025-11-15",
            "reference": "SALE-001",
            "description": "Invoice for goods",
            "amount": 5000.00,
            "days_old": 46
          }
        ],
        "total": 5000.00,
        "current": 0.00,
        "1_30_days": 0.00,
        "31_60_days": 5000.00,
        "61_90_days": 0.00,
        "over_90_days": 0.00
      },
      {
        "party_id": "customer-uuid-2",
        "party_name": "XYZ Corporation",
        "transactions": [
          {
            "date": "2025-12-20",
            "reference": "SALE-002",
            "description": "Invoice for services",
            "amount": 3000.00,
            "days_old": 11
          }
        ],
        "total": 3000.00,
        "current": 3000.00,
        "1_30_days": 0.00,
        "31_60_days": 0.00,
        "61_90_days": 0.00,
        "over_90_days": 0.00
      }
    ],
    "summary": {
      "total": 8000.00,
      "current": 3000.00,
      "1_30_days": 0.00,
      "31_60_days": 5000.00,
      "61_90_days": 0.00,
      "over_90_days": 0.00
    }
  }
}
```

**Age Categories:**
- **Current**: 0-30 days old
- **1-30 days**: 31-60 days old
- **31-60 days**: 61-90 days old
- **61-90 days**: 91-120 days old
- **Over 90 days**: More than 120 days old

### 5. Trial Balance Report
**Endpoint:** `GET /accounting/reports/trial-balance`

**Description:** Lists all accounts with their debit and credit balances. Used to verify that debits equal credits.

**Query Parameters:**
- `as_of_date` (optional) - Date for the report (YYYY-MM-DD)
- `show_zero_balance` (optional) - Include accounts with zero balance

**Example:**
```
GET /accounting/reports/trial-balance?as_of_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "Trial balance generated successfully",
  "data": {
    "report_name": "Trial Balance",
    "as_of_date": "2025-12-31",
    "accounts": [
      {
        "code": "1110",
        "name": "Cash in Bank",
        "type": "asset",
        "debit": 50000.00,
        "credit": 0.00
      },
      {
        "code": "2110",
        "name": "Accounts Payable",
        "type": "liability",
        "debit": 0.00,
        "credit": 15000.00
      }
    ],
    "total_debit": 50000.00,
    "total_credit": 50000.00,
    "balanced": true
  }
}
```

### 6. General Ledger Report
**Endpoint:** `GET /accounting/reports/general-ledger`

**Description:** Shows all transactions for a specific account over a period.

**Query Parameters:**
- `account_id` (required) - UUID of the chart of account
- `start_date` (optional) - Period start date (YYYY-MM-DD)
- `end_date` (optional) - Period end date (YYYY-MM-DD)

**Example:**
```
GET /accounting/reports/general-ledger?account_id=uuid-1110&start_date=2025-12-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "message": "General ledger generated successfully",
  "data": {
    "report_name": "General Ledger",
    "account": {
      "code": "1110",
      "name": "Cash in Bank",
      "type": "asset"
    },
    "period": {
      "start_date": "2025-12-01",
      "end_date": "2025-12-31"
    },
    "opening_balance": 45000.00,
    "transactions": [
      {
        "date": "2025-12-05",
        "reference": "PAY-SALE-001",
        "description": "Payment from customer",
        "debit": 5000.00,
        "credit": 0.00,
        "balance": 50000.00
      }
    ],
    "total_debit": 5000.00,
    "total_credit": 0.00,
    "closing_balance": 50000.00
  }
}
```

---

## Chart of Accounts Structure

### Standard Account Codes

#### Assets (1xxx)
- **1110** - Cash in Bank
- **1120** - Accounts Receivable
- **1130** - Inventory
- **1140** - Prepaid Expenses
- **1500** - Fixed Assets
- **1510** - Accumulated Depreciation

#### Liabilities (2xxx)
- **2110** - Accounts Payable
- **2120** - Accrued Expenses
- **2130** - Unearned Revenue
- **2500** - Long-term Debt

#### Equity (3xxx)
- **3100** - Owner's Capital
- **3200** - Owner's Drawings
- **3900** - Retained Earnings (auto-calculated)

#### Revenue (4xxx)
- **4100** - Sales Revenue
- **4200** - Service Revenue
- **4900** - Other Income

#### Expenses (5xxx)
- **5100** - Cost of Goods Sold
- **5200** - Operating Expenses / Internal Expense
- **5300** - Salaries Expense
- **5400** - Rent Expense
- **5500** - Utilities Expense
- **5600** - Depreciation Expense

---

## Error Responses

All endpoints may return error responses in the following format:

```json
{
  "success": false,
  "message": "Error description",
  "data": null
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized (missing or invalid token)
- `404` - Not Found
- `500` - Internal Server Error

---

## Best Practices

### 1. Journal Entry Workflow
1. Create journal entry with status "draft" for review
2. Verify all debits equal credits
3. Update status to "posted" when finalized
4. Never delete posted entries (create reversing entries instead)

### 2. Account Setup
- Set up all required accounts before creating transactions
- Use consistent account codes across the system
- Define normal_balance correctly for each account

### 3. Period Close
1. Generate Trial Balance to verify accounts balance
2. Create adjusting entries as needed
3. Generate P&L to see net income
4. Generate Balance Sheet to verify financial position
5. Archive reports for the period

### 4. Reporting Best Practices
- Run Aged Ledger weekly to monitor collections/payments
- Review Cash Book daily for cash flow monitoring
- Generate monthly P&L and Balance Sheet
- Use General Ledger for account reconciliation

---

## Example Workflow: Complete Sales Transaction

### Step 1: Create a Sale Order
First create a sale order through the sales module (not shown here).

### Step 2: Record the Sale
```bash
POST /accounting/journals/sales-perpetual
Content-Type: application/json
Authorization: Bearer <token>

{
  "sale_order_id": "uuid-of-sale-order",
  "created_by": "user-uuid"
}
```

This creates two journal entries:
- A/R Debit, Sales Revenue Credit (for sale amount)
- COGS Debit, Inventory Credit (for cost amount)

### Step 3: Record Payment
When customer pays:
```bash
POST /accounting/journals/sales-payment
Content-Type: application/json
Authorization: Bearer <token>

{
  "payment_date": "2025-12-21",
  "amount": 5000.00,
  "description": "Payment from Customer ABC - Invoice INV-001",
  "customer_id": "customer-uuid",
  "created_by": "user-uuid"
}
```

This creates:
- Cash Debit, A/R Credit

### Step 4: Verify with Reports
Check Cash Book:
```bash
GET /accounting/reports/cash-book?start_date=2025-12-01&end_date=2025-12-31
```

Check Receivables:
```bash
GET /accounting/reports/aged-ledger?type=receivable
```

---

## Notes

- All monetary amounts are stored with 2 decimal places
- Dates use ISO 8601 format (YYYY-MM-DD)
- UUIDs are used for all primary keys
- Timestamps are in format: YYYY-MM-DD HH:mm:ss
- All reports can be exported by saving the JSON response

---

## Support

For issues or questions about the Accounting API, please contact the development team.

**Version:** 1.0  
**Last Updated:** December 21, 2025
