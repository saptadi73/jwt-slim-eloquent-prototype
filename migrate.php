<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

// Load environment
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'openpg',
    'password'  => 'openpgpwd',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
    'port'      => 5432,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set($_ENV['APP_TZ'] ?? 'Asia/Jakarta');

// List of migration files to run
$migrations = [
    __DIR__ . '/database/migrations/2025_01_01_000000_create_chart_of_accounts_table.php',
    __DIR__ . '/database/migrations/2025_01_01_000001_create_journal_entries_table.php',
    __DIR__ . '/database/migrations/2025_01_01_000002_create_journal_lines_table.php',
    __DIR__ . '/database/migrations/2025_01_01_000003_add_normal_balance_to_chart_of_accounts_table.php',
    __DIR__ . '/database/migrations/2025_01_01_000004_add_vendor_customer_to_journal_lines_table.php',
    __DIR__ . '/database/migrations/2025_12_20_100000_create_product_order_lines_table.php',
    __DIR__ . '/database/migrations/2025_12_20_100100_create_service_order_lines_table.php',
    __DIR__ . '/database/migrations/2025_12_20_111900_add_customer_fields_to_sale_orders.php',
    __DIR__ . '/database/migrations/2025_12_20_120000_add_hpp_to_product_order_lines.php',
];

foreach ($migrations as $migrationFile) {
    if (file_exists($migrationFile)) {
        echo "Running migration: " . basename($migrationFile) . "\n";
        require_once $migrationFile;
        // Assuming the file returns a class, but since it's anonymous, we need to handle differently
        // For anonymous classes, we can include and assume it's run, but since it's a class, we need to instantiate.
        // Actually, for Laravel migrations, they are meant to be run by the migrator.
        // Since we don't have migrator, let's modify the approach.

        // Instead, let's use Schema directly in the script.
        // But to keep it simple, let's include the file and call the up method if possible.

        // Since the migration files are anonymous classes, we can do:
        $migrationClass = require $migrationFile;
        if ($migrationClass instanceof Closure) {
            // It's a closure returning the class
            $instance = $migrationClass();
            $instance->up();
        } else {
            // Assume it's the class
            $instance = new $migrationClass();
            $instance->up();
        }
        echo "Migration completed.\n";
    } else {
        echo "Migration file not found: " . $migrationFile . "\n";
    }
}

echo "All migrations run.\n";
