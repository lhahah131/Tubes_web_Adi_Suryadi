<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi QR Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8faff 100%);
        }

        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .role-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-4px);
        }

        .role-card.active {
            border-color: #2563EB;
            background-color: #EFF6FF;
        }

        .logo-container {
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container {
            animation: fadeIn 0.8s ease-out 0.2s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="logo-container text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-qrcode text-white text-2xl"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Login Absensi</h1>
            <p class="text-gray-600 text-sm">Sistem Absensi QR Sekolah</p>
        </div>

        <!-- Form Card -->
        <div class="form-container bg-white rounded-2xl card-shadow p-8 mb-6">

            <!-- Error Message -->
            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                    <div>
                        <p class="text-red-800 font-medium text-sm">Login Gagal</p>
                        @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-xs mt-1">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <!-- Role Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold text-sm mb-3">Pilih Role</label>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Siswa Role -->
                        <label class="role-card border-2 border-gray-200 rounded-lg p-4 text-center {{ old('role') === 'siswa' ? 'active' : '' }}">
                            <input type="radio" name="role" value="siswa" class="hidden" {{ old('role') === 'siswa' ? 'checked' : '' }} required>
                            <i class="fas fa-user-graduate text-2xl text-blue-500 mb-2 block"></i>
                            <span class="text-sm font-medium text-gray-800 block">Siswa</span>
                        </label>

                        <!-- Guru Role -->
                        <label class="role-card border-2 border-gray-200 rounded-lg p-4 text-center {{ old('role') === 'guru' ? 'active' : '' }}">
                            <input type="radio" name="role" value="guru" class="hidden" {{ old('role') === 'guru' ? 'checked' : '' }} required>
                            <i class="fas fa-chalkboard-user text-2xl text-green-500 mb-2 block"></i>
                            <span class="text-sm font-medium text-gray-800 block">Guru</span>
                        </label>
                    </div>
                </div>

                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-gray-700 font-semibold text-sm mb-2">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                            class="input-focus w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 placeholder-gray-400"
                            required>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-gray-700 font-semibold text-sm mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            class="input-focus w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 placeholder-gray-400"
                            required>
                        <button
                            type="button"
                            class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600 toggle-password"
                            onclick="togglePassword()">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button
                    type="submit"
                    class="btn-login w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-lg mt-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            <!-- Info Note -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3 flex-shrink-0"></i>
                    <p class="text-blue-800 text-sm">
                        <strong>Catatan:</strong> Gunakan akun yang telah diberikan sekolah. Hubungi admin jika lupa password.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-gray-600 text-xs">
            <p>© 2025 Sistem Absensi QR Sekolah. Semua hak cipta dilindungi.</p>
        </div>
    </div>

    <script>
        // Validasi Form Login
        const loginForm = document.querySelector('form');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const submitButton = loginForm.querySelector('button[type="submit"]');

        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Role Selection Toggle
        const roleCards = document.querySelectorAll('.role-card');
        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                roleCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Set active role on page load
        const checkedRole = document.querySelector('input[name="role"]:checked');
        if (checkedRole) {
            checkedRole.closest('.role-card').classList.add('active');
        }

        // Validasi Form Sebelum Submit
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous error states
            usernameInput.classList.remove('border-red-500');
            passwordInput.classList.remove('border-red-500');

            let isValid = true;
            let errorMessages = [];

            // Validasi Username
            if (!usernameInput.value.trim()) {
                usernameInput.classList.add('border-red-500');
                errorMessages.push('Username harus diisi');
                isValid = false;
            } else if (usernameInput.value.trim().length < 3) {
                usernameInput.classList.add('border-red-500');
                errorMessages.push('Username minimal 3 karakter');
                isValid = false;
            }

            // Validasi Password
            if (!passwordInput.value) {
                passwordInput.classList.add('border-red-500');
                errorMessages.push('Password harus diisi');
                isValid = false;
            } else if (passwordInput.value.length < 6) {
                passwordInput.classList.add('border-red-500');
                errorMessages.push('Password minimal 6 karakter');
                isValid = false;
            }

            // Validasi Role
            const selectedRole = document.querySelector('input[name="role"]:checked');
            if (!selectedRole) {
                errorMessages.push('Silahkan pilih role (Siswa atau Guru)');
                isValid = false;
            }

            // Jika tidak valid, tampilkan error
            if (!isValid) {
                alert('⚠️ Validasi Gagal:\n\n' + errorMessages.join('\n'));
                return false;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            // Submit form setelah validasi berhasil
            setTimeout(() => {
                this.submit();
            }, 500);
        });

        // Real-time validation feedback
        usernameInput.addEventListener('input', function() {
            if (this.value.trim().length >= 3) {
                this.classList.remove('border-red-500');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (this.value.length >= 6) {
                this.classList.remove('border-red-500');
            }
        });

        // Auto-focus pada error
        window.addEventListener('load', function() {
            const errorDiv = document.querySelector('[class*="bg-red"]');
            if (errorDiv) {
                usernameInput.focus();
                usernameInput.select();
            }
        });

        // Reload page jika klik tombol back browser
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                location.reload();
            }
        });

        // Prevent form resubmit on reload
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>