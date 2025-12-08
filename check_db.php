<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Database Users ===\n\n";

$users = \App\Models\User::all(['id', 'username', 'email', 'role']);

echo "Total Users: " . $users->count() . "\n\n";

if ($users->count() > 0) {
    echo "User List:\n";
    echo str_repeat("-", 60) . "\n";
    foreach ($users as $user) {
        echo sprintf("ID: %d | Username: %s | Role: %s\n", 
            $user->id, 
            $user->username, 
            $user->role
        );
    }
    echo str_repeat("-", 60) . "\n";
} else {
    echo "⚠️ NO USERS FOUND! Database might not be seeded.\n";
}

echo "\n=== Checking Password Hash for siswa1 ===\n";
$siswa1 = \App\Models\User::where('username', 'siswa1')->first();
if ($siswa1) {
    echo "Username: {$siswa1->username}\n";
    echo "Password Hash: {$siswa1->password}\n";
    echo "Hash Length: " . strlen($siswa1->password) . "\n";
    
    // Test password
    $testPassword = 'password123';
    $isValid = \Illuminate\Support\Facades\Hash::check($testPassword, $siswa1->password);
    echo "Password 'password123' valid: " . ($isValid ? '✅ YES' : '❌ NO') . "\n";
} else {
    echo "❌ User siswa1 NOT FOUND\n";
}
