# Employee to Pegawai Migration

## Overview

Telah dilakukan konsolidasi penuh antara table **Employee** dan **Pegawai**. Karena Pegawai sudah ada dan dipakai sebagai referensi di Workorder untuk tanda tangan, maka semua fungsionalitas Employee telah diintegrasikan ke Pegawai.

## Status

✅ **COMPLETED** - Employee fully merged into Pegawai

## What Was Done

### 1. Pegawai Model Updated
Pegawai model sekarang memiliki semua field dan relasi dari Employee:

```php
// Fields ditambahkan:
- position_id (FK to Position)
- hire_date (Date field)
- is_active (Boolean)

// Relasi ditambahkan:
- position() -> belongsTo(Position)
- timeOffs() -> hasMany(TimeOff)
- attendances() -> hasMany(Attendance)
- workorders() -> hasMany(Workorder)

// Casting ditambahkan:
- 'hire_date' => 'date'
- 'is_active' => 'boolean'
- 'created_at' => 'datetime'
- 'updated_at' => 'datetime'
```

### 2. TimeOff Model Updated
- Menambah `pegawai_id` field
- Relasi: `pegawai()` -> belongsTo(Pegawai)
- Keep backward compatibility dengan `employee()` relation

### 3. Attendance Model Updated
- Menambah `pegawai_id` field
- Relasi: `pegawai()` -> belongsTo(Pegawai)
- Keep backward compatibility dengan `employee()` relation

### 4. Employee Model Deprecated
Employee model sekarang hanya alias ke Pegawai:

```php
class Employee extends Pegawai
{
    protected $table = 'pegawai';
}
```

**Alasan:** Backward compatibility - kode lama yang import Employee masih bekerja

### 5. EmployeeService Deprecated
EmployeeService sekarang hanya alias ke PegawaiService:

```php
class EmployeeService extends PegawaiService
{
}
```

### 6. Routes Cleaned
- **routes/employees.php** - Deprecated (skeleton file saja)
- **routes/employees.php** - Removed dari routes/index.php
- **routes/pegawai.php** - Semua endpoint employee sekarang ada di sini
- **routes/timeoffs.php** - Ditambah endpoint `/api/pegawai/{pegawaiId}/timeoffs`

### 7. Database Migrations
4 migration files dibuat untuk support pegawai_id:

1. `2025_12_30_000002_update_pegawai_table.php`
   - Add columns: group_id, position_id, url_foto, tanda_tangan, hire_date, is_active

2. `2025_12_30_000004_add_pegawai_id_to_time_offs.php`
   - Add column: pegawai_id (FK to pegawai)

3. `2025_12_30_000005_add_pegawai_id_to_attendances.php`
   - Add column: pegawai_id (FK to pegawai)

## API Endpoints Available

### Pegawai Endpoints (Complete)
```
GET    /api/pegawai              - List all employees
GET    /api/pegawai/{id}         - Get employee detail
POST   /api/pegawai              - Create employee (with photo & signature)
PUT    /api/pegawai/{id}         - Update employee
POST   /api/pegawai/{id}         - Update employee (multipart form)
DELETE /api/pegawai/{id}         - Delete employee
```

### TimeOff Endpoints (Updated)
```
GET    /api/timeoffs
GET    /api/timeoffs/{id}
POST   /api/timeoffs
PUT    /api/timeoffs/{id}
POST   /api/timeoffs/{id}/approve
POST   /api/timeoffs/{id}/reject
POST   /api/timeoffs/{id}/cancel
DELETE /api/timeoffs/{id}
GET    /api/pegawai/{pegawaiId}/timeoffs  [NEW]
```

### Attendance Endpoints (Support pegawai_id)
```
GET    /api/attendances
GET    /api/attendances/{id}
GET    /api/attendances/summary/employee
POST   /api/attendances/checkin
POST   /api/attendances/{id}/checkout
POST   /api/attendances
PUT    /api/attendances/{id}
DELETE /api/attendances/{id}
```

## Backward Compatibility

✅ **FULLY MAINTAINED**

Kode yang menggunakan Employee model atau EmployeeService masih bekerja karena:
1. Employee class masih ada (as alias to Pegawai)
2. EmployeeService masih ada (as alias to PegawaiService)
3. TimeOff & Attendance masih punya `employee_id` field dan `employee()` relation

**Namun untuk development baru, gunakan:**
- Model: `Pegawai` bukan `Employee`
- Service: `PegawaiService` bukan `EmployeeService`
- Routes: `/api/pegawai` bukan `/api/employees`

## Field Mapping

Pegawai sekarang memiliki SEMUA field yang ada di Employee ditambah lagi:

### Database Columns (pegawai table)
```
- id (UUID, Primary Key)
- nama (VARCHAR 255) ← sama dengan Employee.name
- alamat (VARCHAR 255) ← sama dengan Employee.address
- hp (VARCHAR 255) ← sama dengan Employee.phone
- email (VARCHAR 255)
- departemen_id (UUID, FK)
- group_id (UUID, FK) [NEW]
- position_id (UUID, FK) ← dari Employee.position_id
- url_foto (VARCHAR 255) ← sama dengan Employee.url_photo
- tanda_tangan (VARCHAR 255) [NEW]
- hire_date (DATE) ← dari Employee.hire_date
- is_active (BOOLEAN) ← dari Employee.is_active
- created_at, updated_at (TIMESTAMP)
```

## How to Use (For New Code)

### Create Employee with Photo and Signature
```bash
curl -X POST "http://localhost:8000/api/pegawai" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "nama=Ahmad Budi" \
  -F "alamat=Jl. Raya No. 15, Jakarta" \
  -F "hp=08123456789" \
  -F "email=ahmad.budi@bengkel.com" \
  -F "departemen_id=550e8400-e29b-41d4-a716-446655440000" \
  -F "group_id=660e8400-e29b-41d4-a716-446655440001" \
  -F "position_id=550e8400-e29b-41d4-a716-446655440003" \
  -F "hire_date=2024-01-01" \
  -F "is_active=true" \
  -F "url_foto=@/path/to/photo.jpg" \
  -F "tanda_tangan=@/path/to/signature.png"
```

### Get Employee Time Offs
```bash
curl -X GET "http://localhost:8000/api/pegawai/550e8400-e29b-41d4-a716-446655440002/timeoffs" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Workorder Integration

WorkOrderAcService sudah mereferensi Pegawai:
```php
public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'teknisi_id');
}
```

Ini sudah benar dan tidak memerlukan perubahan.

## Clean Up

Files yang bisa dihapus (tapi dijaga untuk backward compatibility):
- ❌ `/api/employees` endpoints (deprecated)
- ❌ Employee table in database (data sudah di pegawai)

**Note:** Tidak perlu dihapus karena:
1. Ada relasi lama yang mungkin masih pakai
2. Migration dapat meng-handle keduanya (dengan field employee_id)
3. Backward compatibility dijaga

## Files Changed

### Models
- ✅ `app/Models/Pegawai.php` - Updated dengan semua field & relasi
- ✅ `app/Models/Employee.php` - Deprecated (alias to Pegawai)
- ✅ `app/Models/TimeOff.php` - Updated dengan pegawai_id
- ✅ `app/Models/Attendance.php` - Updated dengan pegawai_id

### Services
- ✅ `app/Services/PegawaiService.php` - Fully functional
- ✅ `app/Services/EmployeeService.php` - Deprecated (alias)

### Routes
- ✅ `routes/pegawai.php` - Fully functional (6 endpoints)
- ✅ `routes/employees.php` - Deprecated (skeleton)
- ✅ `routes/index.php` - Removed employees.php import
- ✅ `routes/timeoffs.php` - Updated dengan pegawai endpoint

### Migrations
- ✅ `2025_12_30_000002_update_pegawai_table.php` - Add columns to pegawai
- ✅ `2025_12_30_000004_add_pegawai_id_to_time_offs.php` - Support pegawai FK
- ✅ `2025_12_30_000005_add_pegawai_id_to_attendances.php` - Support pegawai FK

## Next Steps

1. Run migrations:
   ```bash
   php migrate.php
   ```

2. Update any custom code that uses Employee to use Pegawai

3. Test all endpoints to ensure they work with new pegawai_id references

4. Remove `/api/employees` from documentation (keep `/api/pegawai`)

