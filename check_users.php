<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Checking Users in Database ===" . PHP_EOL;
echo "Total Users: " . User::count() . PHP_EOL . PHP_EOL;

if (User::count() > 0) {
    echo "Users List:" . PHP_EOL;
    foreach (User::all() as $user) {
        echo "ID: {$user->id} | Username: {$user->username} | Name: {$user->name} | Role: {$user->role}" . PHP_EOL;
    }
} else {
    echo "No users found in database. Please run: php artisan db:seed" . PHP_EOL;
}
