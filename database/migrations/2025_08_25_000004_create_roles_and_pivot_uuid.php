
<?php

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'openpg',
    'password'  => 'openpgpwd',
    'charset'   => 'utf8',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$capsule->getConnection()->statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

Capsule::schema()->dropIfExists('role_user');
Capsule::schema()->dropIfExists('roles');

Capsule::schema()->create('roles', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name')->unique();
    $table->string('label')->nullable();
    $table->timestamps();
});

Capsule::schema()->create('role_user', function (Blueprint $table) {
    $table->uuid('user_id');
    $table->uuid('role_id');

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

    $table->primary(['user_id', 'role_id']);
});

echo "Roles and role_user tables with UUID primary keys created!\n";

use Illuminate\Database\Schema\Blueprint;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->dropIfExists('role_user');
Capsule::schema()->dropIfExists('roles');

Capsule::schema()->create('roles', function (Blueprint $table) {
    $table->char('id', 36)->primary();
    $table->string('name')->unique();
    $table->string('label')->nullable();
    $table->timestamps();
});

Capsule::schema()->create('role_user', function (Blueprint $table) {
    $table->char('user_id', 36);
    $table->char('role_id', 36);

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

    $table->primary(['user_id', 'role_id']);
});

echo "Roles and role_user tables with UUID primary keys created!\n";
