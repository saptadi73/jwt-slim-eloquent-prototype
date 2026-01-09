<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;
use App\Models\Role;
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

$email = 'saptadi@yahoo.com';
$password = '123456';
$name = 'Saptadi';

// Check if user already exists
$existingUser = User::where('email', $email)->first();
if ($existingUser) {
    echo "User already exists. Updating password...\n";
    $existingUser->password = password_hash($password, PASSWORD_DEFAULT);
    $existingUser->save();
    echo "✓ Password updated for: $email\n";
} else {
    // Get admin role
    $role = Role::where('name', 'admin')->first();
    if (!$role) {
        echo "Creating admin role...\n";
        $role = Role::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'admin',
            'label' => 'Administrator'
        ]);
    }
    
    // Create user
    $user = User::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    
    // Attach admin role
    $user->roles()->attach($role->id);
    
    echo "✓ User created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Role: admin\n";
}
