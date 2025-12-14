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

Capsule::schema()->dropIfExists('users');

Capsule::schema()->create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});

echo "Users table with UUID primary key created!\n";
