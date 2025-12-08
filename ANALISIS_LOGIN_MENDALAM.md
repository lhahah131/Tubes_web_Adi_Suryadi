# 🔍 ANALISIS MENDALAM: MASALAH LOGIN LARAVEL

## 📋 RINGKASAN MASALAH
Login tidak masuk ke dashboard setelah menekan tombol login. User dikembalikan ke halaman login.

---

## ✅ ANALISIS KODE - FINDINGS

### 1️⃣ ROUTES (routes/web.php) - ✅ BENAR

```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});
```

**Status:** ✅ Benar
- Route POST ke `/login` mengarah ke `login.store`
- Middleware `guest` mencegah user login masuk ulang (cocok)
- Route nama sudah benar

---

### 2️⃣ BLADE FORM (login.blade.php) - ✅ BENAR

```blade
<form method="POST" action="{{ route('login.store') }}" class="space-y-5">
    @csrf
    
    <!-- Form fields -->
    <input type="radio" name="role" value="siswa" required>
    <input type="text" id="username" name="username" required>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Masuk</button>
</form>
```

**Status:** ✅ Benar
- Method POST ✓
- Action ke route('login.store') ✓
- @csrf token ada ✓
- Semua field (username, password, role) ada ✓
- Name attributes sesuai ✓

---

### 3️⃣ CONTROLLER (AuthController.php) - ⚠️ ADA MASALAH

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
        'role' => 'required|in:siswa,guru'
    ]);

    // ❌ PROBLEM: Manual query, bukan Auth::attempt()
    $user = User::where('username', $credentials['username'])
                ->where('role', $credentials['role'])
                ->first();

    if ($user && Hash::check($credentials['password'], $user->password)) {
        Auth::login($user);
        
        // Redirect berdasarkan role
        if ($user->role === 'guru') {
            return redirect()->route('guru.laporan')->with('success', 'Login berhasil');
        } else {
            return redirect()->route('siswa.riwayat')->with('success', 'Login berhasil');
        }
    }

    return back()->withErrors(['login' => 'Username, password, atau role tidak sesuai']);
}
```

**⚠️ MASALAH DITEMUKAN:**

#### Masalah #1: Menggunakan Manual Query daripada Auth::attempt()
**❌ Tidak ideal tapi masih bisa jalan**
- Anda menggunakan query manual instead of `Auth::attempt()`
- `Auth::attempt()` lebih aman dan built-in untuk Laravel
- Namun kode Anda masih seharusnya jalan dengan `Auth::login($user)`

#### Masalah #2: Redirect Route Mungkin Tidak Terdaftar
**⚠️ KEMUNGKINAN PENYEBAB UTAMA**
```php
// Anda me-redirect ke:
return redirect()->route('guru.laporan'); // atau siswa.riwayat
```

**TAPI:** Route ini berada di dalam middleware `auth`:
```php
Route::middleware('auth')->group(function () {
    Route::prefix('guru')->group(function () {
        Route::get('/laporan', [...])->name('guru.laporan'); // ✓ Ada
    });
    Route::prefix('siswa')->group(function () {
        Route::get('/riwayat', [...])->name('siswa.riwayat'); // ✓ Ada
    });
});
```

✅ Route ada, tapi ada permasalahan dengan middleware atau session!

#### Masalah #3: Session Tidak Persisten
**❌ KEMUNGKINAN PENYEBAB UTAMA**
- Jika session tidak tersimpan dengan baik, `Auth::login()` tidak bekerja
- User ter-login tapi session hilang sebelum redirect

---

## 🔧 DEBUGGING STEPS

### Step 1: Cek apakah login berhasil
```php
// Tambahkan di AuthController
public function login(Request $request)
{
    // ... validation ...
    
    if ($user && Hash::check($credentials['password'], $user->password)) {
        \Log::info('User ditemukan: ' . $user->username);
        \Log::info('Auth attempt...', ['user_id' => $user->id]);
        
        Auth::login($user);
        
        \Log::info('User berhasil login: ' . Auth::id());
        
        // Check session
        \Log::info('Session data: ', session()->all());
        
        // ... redirect ...
    }
}
```

### Step 2: Cek database user
```php
// Di tinker:
php artisan tinker
>>> DB::table('users')->get();
>>> DB::table('users')->where('username', 'siswa1')->first();
>>> Hash::check('password123', DB::table('users')->where('username', 'siswa1')->first()->password);
```

### Step 3: Cek session driver
```bash
# Check di .env
SESSION_DRIVER=file   # Harus file atau database, bukan cookie

# Check storage/framework/sessions
ls storage/framework/sessions/
```

---

## 🎯 ROOT CAUSE ANALYSIS

### KEMUNGKINAN #1: Session Driver Salah (PALING MUNGKIN)
```env
# ❌ Jika ini:
SESSION_DRIVER=cookie

# ✅ Ubah jadi:
SESSION_DRIVER=file
```
**Alasan:** Cookie session terbatas dan tidak cocok untuk autentikasi kompleks

### KEMUNGKINAN #2: Password Hash Tidak Match
**❌ JIKA:**
- Password di database tidak di-hash dengan benar
- Atau Password di-hash dengan hash yang berbeda (bcrypt vs other)

**Cek:**
```php
// Cek manual di tinker
$user = User::where('username', 'siswa1')->first();
$user->password; // Harus string hash (bukan plain text!)
Hash::check('password123', $user->password); // Harus return true
```

### KEMUNGKINAN #3: Role Tidak Cocok di Database
**❌ JIKA:**
- Database role ada typo
- Role di database berbeda dengan yang dikirim form

**Cek:**
```php
// Di database:
SELECT username, role FROM users;
// Harus ada siswa dan guru dengan role tepat
```

### KEMUNGKINAN #4: Auth Middleware Memblokir
**❌ JIKA:**
- Redirect terjadi tapi masih kembali ke login
- Berarti user ter-login tapi middleware auth menolak

**Cek:**
```php
// Di route guru/laporan
Route::get('/laporan', function() {
    dd(Auth::user()); // Check apakah user ada
})->name('guru.laporan');
```

### KEMUNGKINAN #5: Route Name Typo
**❌ JIKA:**
- Route name tidak cocok dengan redirect
- Throw RouteNotFoundException

---

## ✅ SOLUSI LENGKAP

### PERBAIKAN 1: Update AuthController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
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

        // Cari user berdasarkan username dan role
        $user = User::where('username', $credentials['username'])
                    ->where('role', $credentials['role'])
                    ->first();

        // Validasi user dan password
        if (!$user) {
            Log::warning('User tidak ditemukan', ['username' => $credentials['username'], 'role' => $credentials['role']]);
            return back()->withErrors([
                'login' => 'Username atau role tidak ditemukan'
            ])->onlyInput('username', 'role');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Password salah', ['username' => $credentials['username']]);
            return back()->withErrors([
                'login' => 'Password salah'
            ])->onlyInput('username', 'role');
        }

        // Login user
        Auth::login($user, remember: false);
        
        Log::info('User berhasil login', [
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role
        ]);

        // Regenerate session untuk security
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role === 'guru') {
            return redirect()->intended(route('guru.laporan'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        } else {
            return redirect()->intended(route('siswa.riwayat'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }
    }

    public function logout(Request $request)
    {
        Log::info('User logout', ['user_id' => Auth::id()]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
    }
}
```

### PERBAIKAN 2: Update Routes (web.php)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes - Hanya untuk guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Protected Routes - Hanya untuk authenticated users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard (redirect berdasarkan role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::get('/absensi', [DashboardController::class, 'guruAbsensi'])->name('guru.absensi');
        Route::get('/laporan', [DashboardController::class, 'guruLaporan'])->name('guru.laporan');
    });
    
    // Siswa Routes
    Route::prefix('siswa')->group(function () {
        Route::get('/absensi', [DashboardController::class, 'siswaAbsensi'])->name('siswa.absensi');
        Route::get('/riwayat', [DashboardController::class, 'siswaRiwayat'])->name('siswa.riwayat');
    });
});
```

### PERBAIKAN 3: Update .env

```env
APP_NAME="Sistem Absensi QR"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ✅ PENTING: Session Driver
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Driver
CACHE_DRIVER=file

# Database
DB_CONNECTION=sqlite
```

### PERBAIKAN 4: DashboardController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }

        if ($user->role === 'guru') {
            return redirect()->route('guru.laporan');
        } else {
            return redirect()->route('siswa.riwayat');
        }
    }

    // Guru Methods
    public function guruAbsensi()
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return view('guru.absensi');
    }

    public function guruLaporan()
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return view('guru.laporan');
    }

    // Siswa Methods
    public function siswaAbsensi()
    {
        $user = Auth::user();
        if ($user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return view('siswa.absensi');
    }

    public function siswaRiwayat()
    {
        $user = Auth::user();
        if ($user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return view('siswa.riwayat');
    }
}
```

---

## 🚀 LANGKAH-LANGKAH IMPLEMENTASI

### Step 1: Clear Cache & Config
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Step 2: Update Kode (gunakan perbaikan di atas)
- Ganti `AuthController.php`
- Ganti `DashboardController.php`
- Pastikan `.env` sudah benar

### Step 3: Restart Server
```bash
# Stop server Ctrl+C
# Jalankan ulang:
php artisan serve
```

### Step 4: Test Login
```
1. Buka http://localhost:8000/login
2. Pilih role: Siswa
3. Username: siswa1
4. Password: password123
5. Klik Masuk

Expected: Redirect ke /siswa/riwayat ✓
```

---

## 📊 TESTING CHECKLIST

- [ ] Login sebagai siswa1 → Redirect ke /siswa/riwayat
- [ ] Login sebagai siswa2 → Redirect ke /siswa/riwayat
- [ ] Login sebagai guru1 → Redirect ke /guru/laporan
- [ ] Login sebagai guru2 → Redirect ke /guru/laporan
- [ ] Akses /guru/laporan sebagai siswa → Error 403
- [ ] Akses /siswa/riwayat sebagai guru → Error 403
- [ ] Refresh page → Session persist
- [ ] Logout → Redirect ke login
- [ ] Akses /siswa/riwayat tanpa login → Redirect ke /login

---

## 📝 KESIMPULAN

### Kemungkinan Root Cause (Urutan Prioritas):
1. ⚠️ **Session Driver** - Paling sering terjadi
2. ⚠️ **Password Hash** - Cek DB
3. ⚠️ **Route Middleware** - Cek auth middleware
4. ⚠️ **Session Timeout** - Cek SESSION_LIFETIME

### Action Items:
✅ Update AuthController dengan logging
✅ Pastikan .env SESSION_DRIVER=file
✅ Cek database user password
✅ Restart server
✅ Test login ulang
