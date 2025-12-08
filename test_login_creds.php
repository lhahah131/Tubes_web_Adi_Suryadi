<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LOGIN TEST ===\n\n";

// Test credentials
$testCreds = [
    ['username' => 'siswa1', 'password' => 'password123', 'role' => 'siswa'],
    ['username' => 'guru1', 'password' => 'password123', 'role' => 'guru'],
];

foreach ($testCreds as $cred) {
    echo "Testing: {$cred['username']} (role: {$cred['role']})\n";
    
    $user = \App\Models\User::where('username', $cred['username'])
        ->where('role', $cred['role'])
        ->first();
    
    if (!$user) {
        echo "  ❌ User NOT FOUND in database\n\n";
        continue;
    }
    
    echo "  ✅ User found (ID: {$user->id})\n";
    
    $passwordCheck = \Illuminate\Support\Facades\Hash::check($cred['password'], $user->password);
    
    if ($passwordCheck) {
        echo "  ✅ Password CORRECT\n";
    } else {
        echo "  ❌ Password WRONG\n";
    }
    
    echo "\n";
}

echo "=== ALL USERS IN DATABASE ===\n";
$allUsers = \App\Models\User::all(['username', 'role']);
foreach ($allUsers as $u) {
    echo "- {$u->username} ({$u->role})\n";
}
