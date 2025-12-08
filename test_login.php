<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Login Credentials ===" . PHP_EOL . PHP_EOL;

// Test credentials
$testCredentials = [
    ['username' => 'siswa1', 'password' => 'password123', 'role' => 'siswa'],
    ['username' => 'guru1', 'password' => 'password123', 'role' => 'guru'],
];

foreach ($testCredentials as $cred) {
    echo "Testing: Username={$cred['username']}, Role={$cred['role']}" . PHP_EOL;
    
    $user = User::where('username', $cred['username'])
                ->where('role', $cred['role'])
                ->first();
    
    if ($user) {
        echo " ✓ User found: {$user->name}" . PHP_EOL;
        
        if (Hash::check($cred['password'], $user->password)) {
            echo " ✓ Password is CORRECT" . PHP_EOL;
        } else {
            echo " ✗ Password is WRONG" . PHP_EOL;
        }
    } else {
        echo " ✗ User NOT FOUND" . PHP_EOL;
    }
    echo PHP_EOL;
}
