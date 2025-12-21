# Accounting Module Quick Reference

## Overview
The Accounting Module provides comprehensive double-entry bookkeeping, journal management, and financial reporting capabilities.

## Key Features
✅ Chart of Accounts (CoA) Management  
✅ Multiple Journal Entry Types  
✅ Financial Reports (Balance Sheet, P&L, Cash Book, Aged Ledger)  
✅ Automatic Debit/Credit Validation  
✅ Perpetual Inventory System Integration  

---

## Quick Links

📖 [Full API Documentation](./ACCOUNTING_API.md)

---

## API Endpoints Summary

### Chart of Accounts
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/chart-of-accounts` | List all accounts |
| GET | `/chart-of-accounts/{id}` | Get account by ID |
| POST | `/chart-of-accounts` | Create account 🔒 |
| PUT | `/chart-of-accounts/{id}` | Update account 🔒 |
| DELETE | `/chart-of-accounts/{id}` | Delete account 🔒 |

### Journal Entries
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/accounting/journals` | List all journals |
| GET | `/accounting/journals/{id}` | Get journal by ID |
| DELETE | `/accounting/journals/{id}` | Delete journal 🔒 |
| POST | `/accounting/journals/miscellaneous` | Manual entry 🔒 |
| POST | `/accounting/journals/sales-perpetual` | Record sale 🔒 |
| POST | `/accounting/journals/sales-payment` | Record payment received 🔒 |
| POST | `/accounting/journals/purchase` | Record purchase 🔒 |
| POST | `/accounting/journals/purchase-payment` | Record payment made 🔒 |
| POST | `/accounting/journals/expense` | Record expense 🔒 |
| POST | `/accounting/journals/expense-payment` | Pay expense 🔒 |
| POST | `/accounting/journals/internal-expenditure` | Internal use 🔒 |

### Financial Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/accounting/reports/balance-sheet` | Assets, Liabilities, Equity |
| GET | `/accounting/reports/profit-loss` | Revenue & Expenses |
| GET | `/accounting/reports/cash-book` | Cash transactions |
| GET | `/accounting/reports/aged-ledger` | Receivable/Payable aging |
| GET | `/accounting/reports/trial-balance` | All account balances |
| GET | `/accounting/reports/general-ledger` | Account transaction history |

🔒 = Requires JWT Authentication

---

## Quick Start Examples

### 1. Create a Chart of Account
```bash
curl -X POST http://localhost/api/chart-of-accounts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "1110",
    "name": "Cash in Bank",
    "description": "Main operating account",
    "type": "asset",
    "normal_balance": "debit",
    "is_active": true
  }'
```

### 2. Create a Miscellaneous Journal Entry
```bash
curl -X POST http://localhost/api/accounting/journals/miscellaneous \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "entry_date": "2025-12-21",
    "reference_number": "ADJ-001",
    "description": "Bank fee adjustment",
    "lines": [
      {
        "chart_of_account_id": "expense-account-uuid",
        "description": "Bank fees",
        "debit": 50,
        "credit": 0
      },
      {
        "chart_of_account_id": "cash-account-uuid",
        "description": "Bank fees",
        "debit": 0,
        "credit": 50
      }
    ]
  }'
```

### 3. Record a Sale
```bash
curl -X POST http://localhost/api/accounting/journals/sales-perpetual \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sale_order_id": "sale-uuid"
  }'
```

### 4. Record Customer Payment
```bash
curl -X POST http://localhost/api/accounting/journals/sales-payment \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_date": "2025-12-21",
    "amount": 5000.00,
    "description": "Payment from ABC Corp",
    "customer_id": "customer-uuid"
  }'
```

### 5. Get Balance Sheet
```bash
curl "http://localhost/api/accounting/reports/balance-sheet?as_of_date=2025-12-31"
```

### 6. Get P&L Statement
```bash
curl "http://localhost/api/accounting/reports/profit-loss?start_date=2025-01-01&end_date=2025-12-31"
```

### 7. Get Aged Receivables
```bash
curl "http://localhost/api/accounting/reports/aged-ledger?type=receivable"
```

### 8. Get Cash Book
```bash
curl "http://localhost/api/accounting/reports/cash-book?start_date=2025-12-01&end_date=2025-12-31"
```

---

## Account Types

| Type | Normal Balance | Examples |
|------|----------------|----------|
| **Asset** | Debit | Cash, Inventory, A/R, Equipment |
| **Liability** | Credit | A/P, Loans, Accrued Expenses |
| **Equity** | Credit | Owner's Capital, Retained Earnings |
| **Revenue** | Credit | Sales, Service Income |
| **Expense** | Debit | COGS, Salaries, Rent, Utilities |

---

## Standard Account Codes

### Assets (1xxx)
- 1110 - Cash in Bank
- 1120 - Accounts Receivable
- 1130 - Inventory
- 1500 - Fixed Assets

### Liabilities (2xxx)
- 2110 - Accounts Payable
- 2120 - Accrued Expenses

### Equity (3xxx)
- 3100 - Owner's Capital
- 3900 - Retained Earnings

### Revenue (4xxx)
- 4100 - Sales Revenue
- 4200 - Service Revenue

### Expenses (5xxx)
- 5100 - Cost of Goods Sold
- 5200 - Operating/Internal Expenses
- 5300 - Salaries Expense

---

## Journal Entry Types Explained

### 1. Miscellaneous Journal
**Use for:** Manual adjustments, corrections, accruals, deferrals
**Example:** Recording bank fees, depreciation, adjusting entries

### 2. Sales Perpetual Journal
**Use for:** Recording sales with automatic inventory reduction
**Creates:** 
- DR: A/R → CR: Sales Revenue
- DR: COGS → CR: Inventory

### 3. Sales Payment Journal
**Use for:** Recording customer payments
**Creates:** DR: Cash → CR: A/R

### 4. Purchase Journal
**Use for:** Recording purchases from vendors
**Creates:** DR: Inventory → CR: A/P

### 5. Purchase Payment Journal
**Use for:** Paying vendors
**Creates:** DR: A/P → CR: Cash

### 6. Expense Journal
**Use for:** Recording expenses incurred
**Creates:** DR: Expense → CR: A/P

### 7. Expense Payment Journal
**Use for:** Paying expenses
**Creates:** DR: A/P → CR: Cash

### 8. Internal Expenditure Journal
**Use for:** Goods used internally (not for sale)
**Creates:** DR: Internal Expense → CR: Inventory

---

## Report Descriptions

### 1. Balance Sheet
Shows financial position at a specific date:
- Assets (what you own)
- Liabilities (what you owe)
- Equity (owner's stake)

**Formula:** Assets = Liabilities + Equity

### 2. Profit & Loss (Income Statement)
Shows profitability for a period:
- Revenues (income earned)
- Expenses (costs incurred)
- Net Income/Loss

**Formula:** Net Income = Revenue - Expenses

### 3. Cash Book
Shows all cash/bank transactions with running balance.
Useful for daily cash monitoring and reconciliation.

### 4. Aged Ledger
Shows receivables/payables by age:
- Current (0-30 days)
- 1-30 days (31-60 days)
- 31-60 days (61-90 days)
- 61-90 days (91-120 days)
- Over 90 days (120+ days)

### 5. Trial Balance
Lists all accounts with debit/credit balances.
Used to verify that total debits = total credits.

### 6. General Ledger
Shows all transactions for a specific account.
Useful for account reconciliation and analysis.

---

## Important Rules

### Double-Entry Bookkeeping
✅ Every transaction has equal debits and credits  
✅ Debits must always equal credits  
✅ Assets and expenses increase with debits  
✅ Liabilities, equity, and revenue increase with credits  

### Best Practices
✅ Always create entries with status "draft" first  
✅ Review before marking as "posted"  
✅ Never delete posted entries (create reversing entries)  
✅ Use consistent account codes  
✅ Document each transaction clearly  

---

## Common Workflows

### Monthly Close Process
1. ✅ Generate Trial Balance
2. ✅ Create adjusting entries (accruals, deferrals, depreciation)
3. ✅ Review all accounts
4. ✅ Generate P&L Statement
5. ✅ Generate Balance Sheet
6. ✅ Archive reports

### Collections Management
1. ✅ Review Aged Receivables weekly
2. ✅ Follow up on overdue accounts
3. ✅ Record payments promptly
4. ✅ Reconcile A/R account monthly

### Cash Management
1. ✅ Review Cash Book daily
2. ✅ Reconcile bank accounts weekly
3. ✅ Monitor cash flow
4. ✅ Plan for upcoming payments

---

## Error Messages

| Error | Meaning | Solution |
|-------|---------|----------|
| "Debit and credit must be balanced" | Journal entry doesn't balance | Ensure total debits = total credits |
| "Required chart of accounts not found" | Missing standard account | Create required CoA entries |
| "Journal entry not found" | Invalid journal ID | Check the journal ID |
| "Account not found" | Invalid account ID | Verify the account exists |

---

## Support & Resources

📖 **Full Documentation:** [ACCOUNTING_API.md](./ACCOUNTING_API.md)  
💻 **Models:** `app/Models/ChartOfAccount.php`, `JournalEntry.php`, `JournalLine.php`  
🔧 **Services:** `app/Services/AccountingService.php`, `ReportService.php`  
🛣️ **Routes:** `routes/accounting.php`, `routes/chart_of_accounts.php`  

---

**Version:** 1.0  
**Last Updated:** December 21, 2025
