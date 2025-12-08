<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DETAILED USER CHECK ===\n\n";

// Cek user siswa1 secara detail
$username = 'siswa1';
$role = 'siswa';

echo "Looking for:\n";
echo "  Username: '{$username}' (length: " . strlen($username) . ")\n";
echo "  Role: '{$role}' (length: " . strlen($role) . ")\n\n";

// Cari dengan where username
echo "1. Searching by username only:\n";
$userByUsername = \App\Models\User::where('username', $username)->first();
if ($userByUsername) {
    echo "  ✅ Found user!\n";
    echo "  - ID: {$userByUsername->id}\n";
    echo "  - Name: {$userByUsername->name}\n";
    echo "  - Username: '{$userByUsername->username}' (length: " . strlen($userByUsername->username) . ")\n";
    echo "  - Role: '{$userByUsername->role}' (length: " . strlen($userByUsername->role) . ")\n";
    echo "  - Email: {$userByUsername->email}\n";
} else {
    echo "  ❌ NOT FOUND\n";
}

echo "\n2. Searching by username AND role:\n";
$userByBoth = \App\Models\User::where('username', $username)
    ->where('role', $role)
    ->first();
    
if ($userByBoth) {
    echo "  ✅ Found user!\n";
    echo "  - ID: {$userByBoth->id}\n";
    echo "  - Name: {$userByBoth->name}\n";
} else {
    echo "  ❌ NOT FOUND\n";
}

echo "\n3. All users in database:\n";
$allUsers = \App\Models\User::all(['id', 'username', 'role']);
echo "Total: " . $allUsers->count() . " users\n";
foreach ($allUsers as $u) {
    $usernameHex = bin2hex($u->username);
    $roleHex = bin2hex($u->role);
    echo "  - ID {$u->id}: username='{$u->username}' (hex: {$usernameHex}), role='{$u->role}' (hex: {$roleHex})\n";
}

echo "\n4. Direct SQL query test:\n";
$result = \Illuminate\Support\Facades\DB::select(
    "SELECT * FROM users WHERE username = ? AND role = ?", 
    [$username, $role]
);
echo "  Results: " . count($result) . "\n";
if (count($result) > 0) {
    foreach ($result as $r) {
        echo "  - Found: {$r->id} | {$r->username} | {$r->role}\n";
    }
}
