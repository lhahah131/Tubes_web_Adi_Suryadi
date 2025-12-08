# Validasi & Kondisi Form Login

## ✅ Fitur Validasi yang Ditambahkan

### 1. **Validasi Client-Side (JavaScript)**

#### Username Validation
- ✓ Tidak boleh kosong
- ✓ Minimal 3 karakter
- ✓ Real-time feedback saat mengetik

#### Password Validation  
- ✓ Tidak boleh kosong
- ✓ Minimal 6 karakter
- ✓ Real-time feedback saat mengetik

#### Role Selection
- ✓ Harus memilih role (Siswa atau Guru)
- ✓ Visual feedback dengan card aktif

### 2. **Validasi Server-Side (PHP/Laravel)**

Di `AuthController@login()`:
- ✓ Username required & string
- ✓ Password required & string  
- ✓ Role required & in:siswa,guru
- ✓ Username & Role harus cocok dengan database
- ✓ Password harus match dengan hash

### 3. **Kondisi Reload & Session**

- ✓ Prevent form resubmit on reload
- ✓ Auto-reload jika back button di-click
- ✓ Session file driver untuk persistensi
- ✓ Error auto-focus ke input username
- ✓ Error auto-select text di username

### 4. **UX Improvements**

- ✓ Loading state pada button submit
- ✓ Button disabled saat proses
- ✓ Error styling (border merah)
- ✓ Clear error messages
- ✓ Toggle password visibility
- ✓ Real-time input feedback

## 🧪 Cara Testing

### Test 1: Valid Siswa Login
```
Role: Siswa
Username: siswa1
Password: password123
Expected: Redirect ke /siswa/riwayat ✓
```

### Test 2: Valid Guru Login
```
Role: Guru
Username: guru1
Password: password123
Expected: Redirect ke /guru/laporan ✓
```

### Test 3: Empty Username
```
Username: (kosong)
Password: password123
Role: Siswa
Expected: Alert "Username harus diisi" ✓
```

### Test 4: Username < 3 karakter
```
Username: ab
Password: password123
Role: Siswa
Expected: Alert "Username minimal 3 karakter" ✓
```

### Test 5: Empty Password
```
Username: siswa1
Password: (kosong)
Role: Siswa
Expected: Alert "Password harus diisi" ✓
```

### Test 6: Password < 6 karakter
```
Username: siswa1
Password: pass
Role: Siswa
Expected: Alert "Password minimal 6 karakter" ✓
```

### Test 7: No Role Selected
```
Username: siswa1
Password: password123
Role: (tidak dipilih)
Expected: Alert "Silahkan pilih role" ✓
```

### Test 8: Wrong Password
```
Username: siswa1
Password: wrongpassword
Role: Siswa
Expected: "Username, password, atau role tidak sesuai" ✓
```

### Test 9: Wrong Role Selection
```
Username: siswa1
Password: password123
Role: Guru (dipilih, tapi username adalah siswa)
Expected: "Username, password, atau role tidak sesuai" ✓
```

### Test 10: Reload After Login
```
1. Login sebagai siswa1
2. Masuk ke /siswa/riwayat
3. Tekan F5 (reload)
Expected: Tetap di /siswa/riwayat (session persist) ✓
```

### Test 11: Back Button After Login
```
1. Login sebagai siswa1
2. Masuk ke /siswa/riwayat
3. Tekan back button browser
4. Tekan forward button
Expected: Kembali ke /siswa/riwayat (session valid) ✓
```

### Test 12: Direct URL Access
```
1. Buka browser baru
2. Akses langsung /siswa/riwayat
Expected: Redirect ke /login ✓
```

### Test 13: Toggle Password
```
1. Fokus di password field
2. Klik icon mata
Expected: Password visible ✓
3. Klik icon mata lagi
Expected: Password hidden ✓
```

### Test 14: Role Card Selection
```
1. Klik "Siswa" card
Expected: Card active (border biru, bg biru muda) ✓
2. Klik "Guru" card  
Expected: Guru card active, Siswa tidak active ✓
```

## 📋 Status Validasi

| Kondisi | Client | Server | Status |
|---------|--------|--------|--------|
| Username empty | ✓ | ✓ | ✅ |
| Username < 3 char | ✓ | - | ✅ |
| Password empty | ✓ | ✓ | ✅ |
| Password < 6 char | ✓ | - | ✅ |
| Role not selected | ✓ | ✓ | ✅ |
| Valid login | ✓ | ✓ | ✅ |
| Wrong password | - | ✓ | ✅ |
| Wrong role | - | ✓ | ✅ |
| Session persist | ✓ | ✓ | ✅ |
| Prevent resubmit | ✓ | - | ✅ |

## 🔄 Reload Behavior

### Scenario 1: After Successful Login
- ✓ Session tersimpan di file (`storage/framework/sessions/`)
- ✓ User tetap login meski browser di-refresh
- ✓ User tetap login meski klik back button

### Scenario 2: After Failed Login  
- ✓ Error ditampilkan
- ✓ Input preserved (old values)
- ✓ Username auto-focused
- ✓ Username auto-selected

### Scenario 3: Direct URL Access (Not Logged In)
- ✓ Redirect ke /login
- ✓ Session tidak ada

## 🎯 Error Messages

```
⚠️ Validasi Gagal:
- Username harus diisi
- Username minimal 3 karakter
- Password harus diisi
- Password minimal 6 karakter
- Silahkan pilih role (Siswa atau Guru)
```

## 📝 Notes

- Semua validasi berjalan real-time
- Error styling otomatis hilang saat user mulai mengetik
- Loading state mencegah double-submit
- Session driver: FILE (untuk reliabilitas lebih baik)
- SESSION_LIFETIME: 120 menit (bisa diubah di .env)
