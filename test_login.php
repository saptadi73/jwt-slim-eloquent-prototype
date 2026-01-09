<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;
use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Koneksi ke PostgreSQL
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'] ?? 'pgsql',
    'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database'  => $_ENV['DB_NAME'] ?? 'erpmini',
    'username'  => $_ENV['DB_USERNAME'] ?? 'openpg',
    'password'  => $_ENV['DB_PASSWORD'] ?? 'openpgpwd',
    'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8',
    'prefix'    => $_ENV['DB_PREFIX'] ?? '',
    'schema'    => $_ENV['DB_SCHEMA'] ?? 'public',
    'port'      => (int)($_ENV['DB_PORT'] ?? 5432),
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Test credentials
$email = 'saptadi@yahoo.com';
$password = '123456';

echo "Testing login for: $email\n";
echo "Password: $password\n\n";

// Check if user exists
$user = User::where('email', $email)->with('roles')->first();

if (!$user) {
    echo "❌ User not found in database!\n";
    echo "Available users:\n";
    $users = User::all();
    foreach ($users as $u) {
        echo "  - {$u->email}\n";
    }
    exit(1);
}

echo "✓ User found: {$user->name} ({$user->email})\n";
echo "User ID: {$user->id}\n";
echo "Password hash in DB: {$user->password}\n\n";

// Test password verification
if (password_verify($password, $user->password)) {
    echo "✓ Password verification: SUCCESS\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
} else {
    echo "❌ Password verification: FAILED\n";
    echo "The password does not match the hash in database.\n\n";
    
    // Generate correct hash
    $correctHash = password_hash($password, PASSWORD_DEFAULT);
    echo "Correct hash for '$password' would be:\n";
    echo "$correctHash\n\n";
    
    echo "To fix this, run:\n";
    echo "UPDATE users SET password = '$correctHash' WHERE email = '$email';\n";
}
