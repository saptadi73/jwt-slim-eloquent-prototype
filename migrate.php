<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Schema;
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

// Set Schema Facade
Illuminate\Support\Facades\Facade::setFacadeApplication($capsule->getContainer());

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
    __DIR__ . '/database/migrations/2025_12_26_add_tax_to_purchase_order_line.php',
];

foreach ($migrations as $migrationFile) {
    if (file_exists($migrationFile)) {
        echo "Running migration: " . basename($migrationFile) . "\n";
        
        try {
            // The migration file returns an anonymous class
            $migrationClass = require $migrationFile;
            
            if ($migrationClass instanceof \Closure) {
                // It's a closure returning the class
                $instance = $migrationClass();
            } else {
                // Assume it's the class
                $instance = $migrationClass;
            }
            
            // Call the up() method
            if (method_exists($instance, 'up')) {
                $instance->up();
                echo "Migration completed: " . basename($migrationFile) . "\n";
            } else {
                echo "Warning: Migration class does not have up() method: " . basename($migrationFile) . "\n";
            }
        } catch (\Exception $e) {
            echo "Error running migration " . basename($migrationFile) . ": " . $e->getMessage() . "\n";
        }
    } else {
        echo "Migration file not found: " . $migrationFile . "\n";
    }
}

echo "All migrations completed.\n";
