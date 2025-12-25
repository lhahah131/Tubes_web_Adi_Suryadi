<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input dengan pesan yang detail
        $credentials = $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:6',
            'role' => 'required|in:siswa,guru'
        ], [
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 3 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role harus siswa atau guru'
        ]);

        Log::info('Login attempt', [
            'username' => $credentials['username'],
            'role' => $credentials['role']
        ]);

        // Cari user berdasarkan username dan role
        $user = User::where('username', $credentials['username'])
            ->where('role', $credentials['role'])
            ->first();

        // Jika user tidak ditemukan
        if (!$user) {
            Log::warning('User tidak ditemukan', [
                'username' => $credentials['username'],
                'role' => $credentials['role']
            ]);
            return back()->withErrors([
                'login' => 'Username atau role tidak sesuai'
            ])->onlyInput('username', 'role');
        }

        // Cek password
        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Password salah', [
                'username' => $credentials['username'],
                'user_id' => $user->id
            ]);
            return back()->withErrors([
                'login' => 'Password salah'
            ])->onlyInput('username', 'role');
        }

        // Login user
        Auth::login($user, remember: false);

        Log::info('User berhasil login', [
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'session_id' => session()->getId()
        ]);

        // Regenerate session untuk security
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role === 'guru') {
            return redirect()->intended(route('guru.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        } else {
            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }
    }

    public function logout(Request $request)
    {
        Log::info('User logout', [
            'user_id' => Auth::id(),
            'username' => Auth::user()?->username
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
    }
}
