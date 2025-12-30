# Database Migration Execution Guide

Panduan lengkap untuk menjalankan semua database migrations pada aplikasi Slim-Eloquent AcService.

## Prerequisites

Sebelum menjalankan migrations, pastikan:

1. **Database Connection**: Database sudah dibuat dan accessible
   - Driver: PostgreSQL (sesuai konfigurasi di `bootstrap/app.php`)
   - Credentials: Sesuaikan dengan `.env` atau konfigurasi app

2. **Dependencies**: Install required packages
   ```bash
   composer require illuminate/database
   composer require illuminate/migrations
   ```

3. **Directory Structure**: Pastikan folder `database/migrations/` ada
   ```
   database/
   ├── migrations/       (sudah ada)
   ├── seeders/         (optional)
   └── factories/       (optional)
   ```

## Migration Setup untuk Slim Framework

Karena menggunakan Slim Framework (bukan Laravel), butuh setup khusus. Ikuti langkah berikut:

### Option 1: Menggunakan Illuminate Database CLI (Recommended)

#### Step 1: Buat config file untuk migrations
Buat file `config/database.php`:

```php
<?php

return [
    'default' => 'pgsql',
    
    'connections' => [
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', 5432),
            'database' => env('DB_NAME', 'erpmini'),
            'username' => env('DB_USER', 'openpg'),
            'password' => env('DB_PASS', 'openpgpwd'),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],
    ],
    
    'migrations' => 'database/migrations',
];
```

#### Step 2: Buat bootstrap file untuk migrations
Buat file `database/connection.php`:

```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;

// Load configuration
$config = require __DIR__ . '/../config/database.php';

// Setup Capsule (Eloquent without Laravel)
$capsule = new Capsule;
$capsule->addConnection($config['connections'][$config['default']]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;
```

#### Step 3: Buat Artisan Migration Command
Buat file `bin/migrate.php`:

```php
#!/usr/bin/env php
<?php

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Filesystem\Filesystem;

// Setup database
$capsule = require BASE_PATH . '/database/connection.php';
$connection = $capsule->connection();
$resolver = $capsule->getDatabaseManager();

// Setup migration repository
$repository = new DatabaseMigrationRepository($resolver, 'migrations');

// Create migrations table if not exists
if (!$connection->getSchemaBuilder()->hasTable('migrations')) {
    $repository->createRepository();
}

// Setup migrator
$files = new Filesystem;
$migrator = new Migrator($repository, $resolver, $files);
$migrator->run([BASE_PATH . '/database/migrations'], []);

echo "Migrations executed successfully!\n";
```

#### Step 4: Jalankan migrations
```bash
php bin/migrate.php
```

---

### Option 2: Menggunakan Script Manual

Jika Option 1 kompleks, gunakan script manual. Buat file `migrate.php` di root:

```php
<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;

// Get Capsule instance dari bootstrap
global $capsule;

// Get migration files
$files = new Filesystem;
$path = __DIR__ . '/database/migrations';
$migrations = $files->files($path);

// Connection
$connection = $capsule->connection();
$schema = $connection->getSchemaBuilder();

// Create migrations tracking table
if (!$schema->hasTable('migrations')) {
    $connection->statement("
        CREATE TABLE migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INTEGER NOT NULL
        )
    ");
    echo "[OK] Created migrations table\n";
}

// Get already ran migrations
$ranMigrations = $connection->table('migrations')->pluck('migration')->toArray();

// Sort files alphabetically (they are named with timestamps)
sort($migrations);

// Track batch
$currentBatch = $connection->table('migrations')->max('batch') + 1;

// Run migrations
$count = 0;
foreach ($migrations as $file) {
    $filename = $file->getFilename();
    
    // Skip non-php files
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    
    // Skip already ran migrations
    if (in_array($filename, $ranMigrations)) {
        echo "[SKIP] $filename (already ran)\n";
        continue;
    }
    
    try {
        // Require migration file
        require $file->getRealPath();
        
        // Get migration class name
        $class = 'App\\Database\\Migrations\\' . str_replace('.php', '', $filename);
        $classShort = str_replace('2025_12_30_', '', str_replace('.php', '', $filename));
        
        // Try to find and execute the migration
        foreach (get_declared_classes() as $cls) {
            if (strpos($cls, 'extends Migration') !== false || 
                method_exists($cls, 'up')) {
                
                $migration = new $cls;
                
                if (method_exists($migration, 'up')) {
                    $migration->up();
                    
                    // Record migration
                    $connection->table('migrations')->insert([
                        'migration' => $filename,
                        'batch' => $currentBatch,
                    ]);
                    
                    echo "[RUN] $filename\n";
                    $count++;
                }
                break;
            }
        }
    } catch (Exception $e) {
        echo "[ERROR] $filename: " . $e->getMessage() . "\n";
    }
}

echo "\n[COMPLETE] $count migrations executed\n";
```

Jalankan dengan:
```bash
php migrate.php
```

---

### Option 3: Gunakan Existing `migrate.php`

Proyek sudah memiliki file `migrate.php` di root. Gunakan:

```bash
php migrate.php
```

---

## Verification

Setelah menjalankan migrations, verifikasi dengan:

### Check PostgreSQL Directly
```bash
# Connect ke database
psql -h 127.0.0.1 -U openpg -d erpmini

# List tables
\dt

# Check migrations table
SELECT * FROM migrations;

# Check specific table
\d pegawai
\d users
```

### Check via PHP
Buat file `check_migrations.php`:

```php
<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

global $capsule;

$tables = [
    'pegawai',
    'departemen',
    'positions',
    'groups',
    'tanda_tangan',
    'time_offs',
    'attendances',
    'absen',
    'cuti',
    'ijin',
    'lembur',
    'gaji',
    'jatah_cuti',
    'customers',
    'vendors',
    'kategoris',
    'satuans',
    'brands',
    'tipes',
    'services',
    'products',
    'customer_assets',
    'purchase_orders',
    'purchase_order_lines',
    'sale_orders',
    'product_order_lines',
    'service_order_lines',
    'workorders',
    'workorder_ac_services',
    'workorder_penjualans',
    'workorder_penyewaans',
    'chart_of_accounts',
    'journal_entries',
    'journal_lines',
    'rental_assets',
    'product_move_histories',
    'stock_histories',
    'manual_transfers',
    'manual_transfer_details',
    'roles',
    'users',
];

$schema = $capsule->connection()->getSchemaBuilder();

echo "=== Database Tables Status ===\n";
$created = 0;
$missing = 0;

foreach ($tables as $table) {
    if ($schema->hasTable($table)) {
        echo "✓ $table\n";
        $created++;
    } else {
        echo "✗ $table (MISSING)\n";
        $missing++;
    }
}

echo "\n=== Summary ===\n";
echo "Created: $created\n";
echo "Missing: $missing\n";
echo "Total: " . count($tables) . "\n";

if ($missing === 0) {
    echo "\n✓ All tables created successfully!\n";
} else {
    echo "\n⚠ $missing tables are missing. Check migrations execution.\n";
}
```

Jalankan dengan:
```bash
php check_migrations.php
```

---

## Troubleshooting

### Error: "SQLSTATE[42P07]: Duplicate table"
**Penyebab**: Migration sudah dijalankan sebelumnya
**Solusi**: 
- Cek `migrations` table
- Hapus entry dari migrations table untuk menjalankan ulang
- Atau gunakan conditional checks di migration (`if (!Schema::hasTable(...)`)

### Error: "Class not found"
**Penyebab**: Namespace atau require path salah
**Solusi**:
- Pastikan migration file menggunakan `return new class extends Migration { ... }`
- Bukan class dengan nama spesifik

### Error: "Foreign key constraint fails"
**Penyebab**: Parent table belum dibuat saat child table dibuat
**Solusi**:
- Ikuti urutan execution yang benar
- Atau jalankan migrations secara berurutan: `php artisan migrate --step`

### Error: "Table already exists"
**Penyebab**: Manual check `if (!Schema::hasTable(...))` sudah ada
**Solusi**: Normal - migrations sudah memiliki protective checks

---

## Rollback (Downgrade)

Untuk rollback migrations (jika perlu):

```php
// Di dalam script
foreach ($migrations as $migration) {
    require $migration;
    $class = ... // get class
    $migration = new $class;
    $migration->down(); // Call down() method
}
```

Atau gunakan existing `migrate.php` dengan option `--rollback`

---

## Best Practices

1. **Jangan edit migration yang sudah dijalankan**
   - Buat migration baru untuk perubahan
   - Gunakan untuk audit trail

2. **Selalu include kondisional checks**
   - `if (!Schema::hasTable(...))` saat create
   - `if (Schema::hasColumn(...))` saat menambah kolom
   - `dropIfExists()` saat rollback

3. **Test di development terlebih dahulu**
   - Jangan langsung ke production
   - Backup database sebelum migration

4. **Jalankan dalam transaction**
   ```php
   $connection->transaction(function() {
       // Run migrations
   });
   ```

5. **Log migration activities**
   ```php
   echo "[RUN] Migration file loaded\n";
   $migration->up();
   echo "[SUCCESS] Migration completed\n";
   ```

---

## Next Steps

Setelah migrations berhasil:

1. **Seed database** (jika ada seeders)
   ```bash
   php artisan db:seed
   ```

2. **Verify dengan Tinker**
   ```php
   // Test model relationships
   $pegawai = \App\Models\Pegawai::with('departemen', 'position')->first();
   ```

3. **Run tests**
   ```bash
   php vendor/bin/phpunit
   ```

4. **Start application**
   ```bash
   php -S localhost:8000 -t public
   ```

---

**Created**: 30 Desember 2025
**Total Migrations**: 16 files
**Tables Created**: 40+
**Estimated Execution Time**: 5-10 detik
