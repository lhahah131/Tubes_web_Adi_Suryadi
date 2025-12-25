<?php
// Simple script to display all users and their passwords (for debugging/development only)
// SAVE AS: public/cek_akun.php

// Load Laravel Framework
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get all users
$users = \App\Models\User::all();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Semua Akun</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; color: #555; }
        tr:hover { background: #f1f1f1; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-guru { background: #e3f2fd; color: #1976d2; }
        .badge-siswa { background: #e8f5e9; color: #2e7d32; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffeeba; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Semua Akun</h1>
        
        <div class="warning">
            <strong>⚠️ Perhatian:</strong> Halaman ini hanya untuk keperluan testing/development. Password asli terenkripsi (Hash), tapi default password untuk tugas ini biasanya <strong>password123</strong>.
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username (Login)</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td>#<?= $user->id ?></td>
                    <td><strong><?= htmlspecialchars($user->name) ?></strong></td>
                    <td style="font-family: monospace; color: #d63384;"><?= htmlspecialchars($user->username) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td>
                        <span class="badge badge-<?= $user->role ?>">
                            <?= $user->role ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: center;">
            <a href="/login" style="display: inline-block; background: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ke Halaman Login</a>
        </div>
    </div>
</body>
</html>
