<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/test/scan-qr', function (Request $request) {
    // 1. Simulasi Login Siswa (Manual Login by ID)
    // ID 2 biasanya siswa1 (cek database Anda)
    $userId = $request->input('user_id', 2); 
    $user = User::find($userId);
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    // Login paksa untuk request ini saja
    Auth::login($user);
    
    // 2. Panggil Controller Method dengan Try-Catch Debugging
    try {
        $controller = new DashboardController();
        return $controller->siswaScanQRSubmit($request);
    } catch (\Throwable $e) {
        return response()->json([
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
