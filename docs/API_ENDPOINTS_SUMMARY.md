# API Endpoints Summary

**Last Updated:** January 6, 2026

Quick reference for all available API endpoints in the system.

---

## Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/auth/register` | Register new user | No |
| POST | `/auth/login` | Login and get JWT token | No |

---

## User Management Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/users` | Get all users with roles | Yes (JWT) |
| GET | `/users/{id}` | Get single user by ID | Yes (JWT) |
| PUT | `/users/{id}` | Update user | Yes (JWT) |
| DELETE | `/users/{id}` | Delete user | Yes (JWT) |
| POST | `/users/update/role` | Update user role | Yes (JWT) |

---

## Role Management Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/roles` | Get all available roles | Yes (JWT) |
| GET | `/roles/users` | Get users with their roles | Yes (JWT) |
| POST | `/roles/users/{userId}/roles` | Assign multiple roles to user | Yes (JWT) |
| POST | `/roles/users/{userId}/roles/{roleId}` | Add single role to user | Yes (JWT) |
| DELETE | `/roles/users/{userId}/roles/{roleId}` | Remove role from user | Yes (JWT) |

---

## Dashboard Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/dashboard` | Get dashboard statistics | Yes (JWT) |

---

## Employee (Pegawai) Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/pegawai` | List all pegawai | Yes (JWT) |
| POST | `/pegawai` | Create new pegawai | Yes (JWT) |
| GET | `/pegawai/{id}` | Get pegawai by ID | Yes (JWT) |
| PUT | `/pegawai/{id}` | Update pegawai | Yes (JWT) |
| DELETE | `/pegawai/{id}` | Delete pegawai | Yes (JWT) |

---

## Position & Department Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/positions` | List all positions | Yes (JWT) |
| POST | `/positions` | Create new position | Yes (JWT) |
| GET | `/positions/{id}` | Get position by ID | Yes (JWT) |
| PUT | `/positions/{id}` | Update position | Yes (JWT) |
| DELETE | `/positions/{id}` | Delete position | Yes (JWT) |
| GET | `/departments` | List all departments | Yes (JWT) |
| POST | `/departments` | Create new department | Yes (JWT) |
| GET | `/departments/{id}` | Get department by ID | Yes (JWT) |
| PUT | `/departments/{id}` | Update department | Yes (JWT) |
| DELETE | `/departments/{id}` | Delete department | Yes (JWT) |

---

## Group Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/groups` | List all groups | Yes (JWT) |
| POST | `/groups` | Create new group | Yes (JWT) |
| GET | `/groups/{id}` | Get group by ID | Yes (JWT) |
| PUT | `/groups/{id}` | Update group | Yes (JWT) |
| DELETE | `/groups/{id}` | Delete group | Yes (JWT) |

---

## Customer Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/customers` | List all customers | Yes (JWT) |
| POST | `/customers` | Create new customer | Yes (JWT) |
| GET | `/customers/{id}` | Get customer by ID | Yes (JWT) |
| PUT | `/customers/{id}` | Update customer | Yes (JWT) |
| DELETE | `/customers/{id}` | Delete customer | Yes (JWT) |

---

## Product Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/products` | List all products | Yes (JWT) |
| POST | `/products` | Create new product | Yes (JWT) |
| GET | `/products/{id}` | Get product by ID | Yes (JWT) |
| PUT | `/products/{id}` | Update product | Yes (JWT) |
| DELETE | `/products/{id}` | Delete product | Yes (JWT) |

---

## Vendor Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/vendors` | List all vendors | Yes (JWT) |
| POST | `/vendors` | Create new vendor | Yes (JWT) |
| GET | `/vendors/{id}` | Get vendor by ID | Yes (JWT) |
| PUT | `/vendors/{id}` | Update vendor | Yes (JWT) |
| DELETE | `/vendors/{id}` | Delete vendor | Yes (JWT) |

---

## Service Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/services` | List all services | Yes (JWT) |
| POST | `/services` | Create new service | Yes (JWT) |
| GET | `/services/{id}` | Get service by ID | Yes (JWT) |
| PUT | `/services/{id}` | Update service | Yes (JWT) |
| DELETE | `/services/{id}` | Delete service | Yes (JWT) |

---

## Order Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/orders/sale` | List all sale orders | Yes (JWT) |
| POST | `/orders/sale` | Create sale order | Yes (JWT) |
| GET | `/orders/sale/{id}` | Get sale order by ID | Yes (JWT) |
| PUT | `/orders/sale/{id}` | Update sale order | Yes (JWT) |
| DELETE | `/orders/sale/{id}` | Delete sale order | Yes (JWT) |
| GET | `/orders/purchase` | List all purchase orders | Yes (JWT) |
| POST | `/orders/purchase` | Create purchase order | Yes (JWT) |
| GET | `/orders/purchase/{id}` | Get purchase order by ID | Yes (JWT) |
| PUT | `/orders/purchase/{id}` | Update purchase order | Yes (JWT) |
| DELETE | `/orders/purchase/{id}` | Delete purchase order | Yes (JWT) |

---

## Accounting Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/accounting/chart-of-accounts` | List chart of accounts | Yes (JWT) |
| POST | `/accounting/chart-of-accounts` | Create COA | Yes (JWT) |
| GET | `/accounting/journals` | List journal entries | Yes (JWT) |
| POST | `/accounting/journals` | Create journal entry | Yes (JWT) |
| GET | `/accounting/bank-accounts` | List bank accounts | Yes (JWT) |
| POST | `/accounting/bank-accounts` | Create bank account | Yes (JWT) |

---

## Expense Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/expenses` | List all expenses | Yes (JWT) |
| POST | `/expenses` | Create new expense | Yes (JWT) |
| GET | `/expenses/{id}` | Get expense by ID | Yes (JWT) |
| PUT | `/expenses/{id}` | Update expense | Yes (JWT) |
| DELETE | `/expenses/{id}` | Delete expense | Yes (JWT) |

---

## Reports

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/reports/cash-book` | Cash book report | Yes (JWT) |
| GET | `/reports/profit-loss` | Profit & loss statement | Yes (JWT) |
| GET | `/reports/balance-sheet` | Balance sheet | Yes (JWT) |
| GET | `/reports/general-ledger` | General ledger | Yes (JWT) |
| GET | `/reports/trial-balance` | Trial balance | Yes (JWT) |

---

## Attendance & Time Off

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/attendances` | List attendance records | Yes (JWT) |
| POST | `/attendances` | Create attendance | Yes (JWT) |
| GET | `/timeoffs` | List time off requests | Yes (JWT) |
| POST | `/timeoffs` | Create time off request | Yes (JWT) |

---

## Signature Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/tanda-tangan` | List all signatures | Yes (JWT) |
| POST | `/tanda-tangan` | Create signature | Yes (JWT) |
| GET | `/tanda-tangan/{id}` | Get signature by ID | Yes (JWT) |
| PUT | `/tanda-tangan/{id}` | Update signature | Yes (JWT) |
| DELETE | `/tanda-tangan/{id}` | Delete signature | Yes (JWT) |

---

## Notes

### Authentication
- All endpoints marked "Yes (JWT)" require a valid JWT token in the Authorization header
- Format: `Authorization: Bearer {your-jwt-token}`
- Token is obtained from `/auth/login` endpoint

### Base URL
- Local development: `http://localhost:8080`
- Production: Update according to your deployment

### Recent Changes (v1.1.0 - January 6, 2026)
- ✅ Changed `/role-management/*` to `/roles/*`
- ✅ Added `GET /users` endpoint
- ✅ Added `GET /users/{id}` endpoint
- ✅ Registered `UserService` in container

### Documentation Links
- Full authentication & role docs: [AUTH_AND_ROLE_MANAGEMENT.md](AUTH_AND_ROLE_MANAGEMENT.md)
- Employee API docs: [EMPLOYEE_API.md](EMPLOYEE_API.md)
- Accounting API docs: [ACCOUNTING_API.md](ACCOUNTING_API.md)
- Reports API docs: [REPORTS_API.md](REPORTS_API.md)
- Main documentation index: [INDEX.md](INDEX.md)
