# Authentication and Role Management API Documentation

## Overview
Dokumentasi lengkap untuk sistem authentication (login/register) dan role management di aplikasi Slim Eloquent.

---

## Table of Contents
1. [Authentication](#authentication)
   - [Register](#register)
   - [Login](#login)
2. [Role Management](#role-management)
   - [Get Users](#get-users)
   - [Get Roles](#get-roles)
   - [Assign Roles](#assign-roles)
   - [Add Role](#add-role)
   - [Remove Role](#remove-role)

---

## Authentication

### Register
Endpoint untuk mendaftarkan user baru.

**Endpoint:**
```
POST /auth/register
```

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "role_id": "optional-uuid-role-id"
}
```

**Parameters:**
- `name` (string, required): Nama pengguna
- `email` (string, required): Email pengguna (harus unik)
- `password` (string, required): Password (akan di-hash)
- `role_id` (string, optional): ID role untuk user. Jika tidak diberikan, akan menggunakan role default "user"

**Success Response (200):**
```json
{
  "success": true,
  "user": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": [
      {
        "id": "role-uuid-1",
        "name": "user",
        "label": "User"
      }
    ]
  }
}
```

**Error Response (400/422):**
```json
{
  "success": false,
  "message": "Email already exists" | "Default role not found"
}
```

---

### Login
Endpoint untuk login dan mendapatkan JWT token.

**Endpoint:**
```
POST /auth/login
```

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123"
}
```

**Parameters:**
- `email` (string, required): Email pengguna
- `password` (string, required): Password pengguna

**Success Response (200):**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI1NTBlODQwMC1lMjliLTQxZDQtYTcxNi00NDY2NTU0NDAwMDAiLCJuYW1lIjoiSm9obiBEb2UiLCJlbWFpbCI6ImpvaG5AZXhhbXBsZS5jb20iLCJyb2xlcyI6WyJ1c2VyIiwiYWRtaW4iXSwiaWF0IjoxNjM2MzM4NDAwLCJleHAiOjE2MzYzNzQ0MDB9.signature",
  "user": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": [
      {
        "id": "role-uuid-1",
        "name": "user",
        "label": "User"
      },
      {
        "id": "role-uuid-2",
        "name": "admin",
        "label": "Administrator"
      }
    ]
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

**JWT Payload:**
```json
{
  "sub": "user-id",
  "name": "John Doe",
  "email": "john@example.com",
  "roles": ["user", "admin"],
  "iat": 1636338400,
  "exp": 1636374400
}
```

**Token Usage:**
Gunakan token di header `Authorization` untuk request yang memerlukan authentication:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

## Role Management

### Get Users
Mengambil daftar semua user dengan roles mereka.

**Endpoint:**
```
GET /role-management/users
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "john@example.com",
      "roles": [
        {
          "id": "role-uuid-1",
          "name": "user",
          "label": "User"
        },
        {
          "id": "role-uuid-2",
          "name": "admin",
          "label": "Administrator"
        }
      ]
    },
    {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "name": "Jane Smith",
      "email": "jane@example.com",
      "roles": [
        {
          "id": "role-uuid-1",
          "name": "user",
          "label": "User"
        }
      ]
    }
  ],
  "message": "Users retrieved with roles"
}
```

**Use Case:**
- Frontend: Populate dropdown/list user untuk pengaturan roles
- Admin panel: Tampilkan semua user beserta roles mereka

---

### Get Roles
Mengambil daftar semua role yang tersedia.

**Endpoint:**
```
GET /role-management/roles
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": "role-uuid-1",
      "name": "user",
      "label": "User"
    },
    {
      "id": "role-uuid-2",
      "name": "admin",
      "label": "Administrator"
    },
    {
      "id": "role-uuid-3",
      "name": "moderator",
      "label": "Moderator"
    }
  ],
  "message": "Roles retrieved"
}
```

**Use Case:**
- Frontend: Populate dropdown role untuk assign/unassign
- Admin panel: Pilihan role saat membuat atau edit user

---

### Assign Roles
Memberikan multiple roles ke user (mengganti roles yang ada sebelumnya).

**Endpoint:**
```
POST /role-management/users/{userId}/roles
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "role_ids": [
    "role-uuid-1",
    "role-uuid-2"
  ]
}
```

**Parameters:**
- `userId` (path parameter): UUID dari user
- `role_ids` (array, required): Array dari role IDs yang akan diberikan

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": [
      {
        "id": "role-uuid-1",
        "name": "user",
        "label": "User"
      },
      {
        "id": "role-uuid-2",
        "name": "admin",
        "label": "Administrator"
      }
    ]
  },
  "message": "Roles assigned successfully"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "role_ids is required"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "User not found" | "Some roles not found"
}
```

**Note:**
- Endpoint ini **mengganti** semua roles yang ada sebelumnya
- Jika ingin menambah role tanpa menghapus yang lain, gunakan endpoint [Add Role](#add-role)

---

### Add Role
Menambahkan single role ke user tanpa menghapus roles yang sudah ada.

**Endpoint:**
```
POST /role-management/users/{userId}/roles/{roleId}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Parameters:**
- `userId` (path parameter): UUID dari user
- `roleId` (path parameter): UUID dari role yang akan ditambahkan

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": [
      {
        "id": "role-uuid-1",
        "name": "user",
        "label": "User"
      },
      {
        "id": "role-uuid-3",
        "name": "moderator",
        "label": "Moderator"
      }
    ]
  },
  "message": "Role added successfully"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "User not found" | "Role not found"
}
```

**Note:**
- Jika role sudah ada, endpoint ini tidak akan duplikat
- Untuk menambah multiple roles sekaligus, gunakan endpoint [Assign Roles](#assign-roles)

---

### Remove Role
Menghapus single role dari user.

**Endpoint:**
```
DELETE /role-management/users/{userId}/roles/{roleId}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Parameters:**
- `userId` (path parameter): UUID dari user
- `roleId` (path parameter): UUID dari role yang akan dihapus

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": [
      {
        "id": "role-uuid-1",
        "name": "user",
        "label": "User"
      }
    ]
  },
  "message": "Role removed successfully"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "User not found" | "Role not found"
}
```

---

## Frontend Integration Examples

### Login Form
```javascript
// Contoh fetch login
async function login(email, password) {
  const response = await fetch('/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });

  const result = await response.json();
  if (result.success) {
    // Simpan token di localStorage
    localStorage.setItem('token', result.token);
    // Simpan user data
    localStorage.setItem('user', JSON.stringify(result.user));
    // Redirect ke dashboard
    window.location.href = '/dashboard';
  }
}
```

### Using Token in Requests
```javascript
// Contoh fetch dengan token
async function getUsers() {
  const token = localStorage.getItem('token');
  const response = await fetch('/role-management/users', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
}
```

### Role Management UI
```javascript
// Contoh assign roles ke user
async function assignRolesToUser(userId, roleIds) {
  const token = localStorage.getItem('token');
  const response = await fetch(`/role-management/users/${userId}/roles`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ role_ids: roleIds })
  });
  
  const result = await response.json();
  if (result.success) {
    alert('Roles assigned successfully');
    // Refresh data
    loadUsers();
  }
}
```

### Populate Dropdowns
```javascript
// Populate user dropdown
async function loadUsersDropdown() {
  const token = localStorage.getItem('token');
  const response = await fetch('/role-management/users', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const result = await response.json();
  
  const dropdown = document.getElementById('userSelect');
  result.data.forEach(user => {
    const option = document.createElement('option');
    option.value = user.id;
    option.textContent = user.name;
    dropdown.appendChild(option);
  });
}

// Populate role dropdown
async function loadRolesDropdown() {
  const token = localStorage.getItem('token');
  const response = await fetch('/role-management/roles', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const result = await response.json();
  
  const dropdown = document.getElementById('roleSelect');
  result.data.forEach(role => {
    const option = document.createElement('option');
    option.value = role.id;
    option.textContent = role.label;
    dropdown.appendChild(option);
  });
}
```

---

## Security Notes

1. **Token Management:**
   - JWT token valid selama 12 jam
   - Simpan token di secure storage (httpOnly cookie lebih aman dari localStorage)
   - Jangan expose token di URL atau log

2. **CORS:**
   - Endpoint dilindungi oleh CORS middleware
   - Request harus dari domain yang sudah dikonfigurasi

3. **JWT Middleware:**
   - Semua endpoint role-management dilindungi JWT middleware
   - Request tanpa token atau token invalid akan ditolak

4. **Role-Based Access Control:**
   - Implementasi RBAC di frontend berdasarkan `roles` array dari JWT payload
   - Validasi di backend juga sangat penting

---

## Common Error Codes

| Status | Message | Cause |
|--------|---------|-------|
| 400 | Bad Request | Request body tidak valid atau parameter kurang |
| 401 | Unauthorized | Token tidak ada, expired, atau invalid |
| 404 | Not Found | User atau role tidak ditemukan |
| 422 | Unprocessable Entity | Email sudah terdaftar atau data tidak valid |
| 500 | Internal Server Error | Error di server |

---

## Testing with cURL

### Register User
```bash
curl -X POST http://localhost:8080/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePassword123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8080/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePassword123"
  }'
```

### Get Users (dengan token)
```bash
curl -X GET http://localhost:8080/role-management/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Roles
```bash
curl -X GET http://localhost:8080/role-management/roles \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Assign Roles
```bash
curl -X POST http://localhost:8080/role-management/users/USER_ID/roles \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "role_ids": ["ROLE_ID_1", "ROLE_ID_2"]
  }'
```

---

## Database Schema

### Users Table
```sql
CREATE TABLE users (
  id VARCHAR(36) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Roles Table
```sql
CREATE TABLE roles (
  id VARCHAR(36) PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL,
  label VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Pivot Table (User-Role Relationship)
```sql
CREATE TABLE role_users (
  user_id VARCHAR(36) NOT NULL,
  role_id VARCHAR(36) NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY (user_id, role_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

---

## Last Updated
- January 5, 2026

## Version
- 1.0.0
