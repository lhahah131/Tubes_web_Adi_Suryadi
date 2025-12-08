# Dokumentasi Database Sistem Absensi QR Sekolah

## Struktur Tabel Database

### 1. **Tabel Users** (users)
```
id (Primary Key)
name (string) - Nama lengkap
username (string, unique) - Username untuk login
email (string, unique) - Email
password (string) - Password ter-hash
role (enum: 'siswa', 'guru') - Role pengguna
remember_token (nullable)
created_at, updated_at
```

### 2. **Tabel Siswa** (siswa)
```
id (Primary Key)
user_id (Foreign Key) → users.id
nis (string, unique) - Nomor Induk Siswa
nomor_induk (string) - Nomor Induk
kelas_id (Foreign Key) → kelas.id
tanggal_lahir (date, nullable)
alamat (string, nullable)
nomor_telepon (string, nullable)
created_at, updated_at
```

### 3. **Tabel Guru** (guru)
```
id (Primary Key)
user_id (Foreign Key) → users.id
nip (string, unique) - Nomor Induk Pegawai
mata_pelajaran (string, nullable)
tanggal_lahir (date, nullable)
alamat (string, nullable)
nomor_telepon (string, nullable)
created_at, updated_at
```

### 4. **Tabel Kelas** (kelas)
```
id (Primary Key)
nama_kelas (string, unique) - Nama kelas (X-A, X-B, dll)
wali_kelas (string, nullable) - Nama wali kelas
created_at, updated_at
```

### 5. **Tabel QR Code** (qr_codes)
```
id (Primary Key)
kelas_id (Foreign Key) → kelas.id
kode_qr (string, unique) - Kode QR
waktu_dibuat (timestamp) - Waktu pembuatan
waktu_berlaku_sampai (timestamp, nullable) - Batas waktu berlaku
aktif (boolean) - Status aktif/tidak aktif
created_at, updated_at
```

### 6. **Tabel Absensi** (absensi)
```
id (Primary Key)
siswa_id (Foreign Key) → siswa.id
kelas_id (Foreign Key) → kelas.id
tanggal (date) - Tanggal absensi
jam_masuk (time, nullable) - Jam masuk siswa
status (enum: 'hadir', 'absen', 'sakit', 'izin') - Status kehadiran
keterangan (string, nullable) - Keterangan tambahan
qr_code_id (Foreign Key, nullable) → qr_codes.id
created_at, updated_at
```

### 7. **Tabel Jam Pelajaran** (jam_pelajaran)
```
id (Primary Key)
jam_ke (integer) - Jam ke berapa
jam_mulai (time) - Jam mulai pelajaran
jam_selesai (time) - Jam selesai pelajaran
created_at, updated_at
```

### 8. **Tabel Jadwal Pelajaran** (jadwal_pelajaran)
```
id (Primary Key)
guru_id (Foreign Key) → guru.id
kelas_id (Foreign Key) → kelas.id
jam_pelajaran_id (Foreign Key) → jam_pelajaran.id
hari (enum: 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', nullable)
created_at, updated_at
```

### 9. **Tabel Laporan Absensi** (laporan_absensi)
```
id (Primary Key)
kelas_id (Foreign Key) → kelas.id
bulan (integer) - Bulan (1-12)
tahun (integer) - Tahun
total_hari_efektif (integer) - Total hari efektif sekolah
created_at, updated_at
```

### 10. **Tabel Detail Laporan Absensi** (detail_laporan_absensi)
```
id (Primary Key)
laporan_absensi_id (Foreign Key) → laporan_absensi.id
siswa_id (Foreign Key) → siswa.id
hadir (integer) - Jumlah hari hadir
absen (integer) - Jumlah hari absen
sakit (integer) - Jumlah hari sakit
izin (integer) - Jumlah hari izin
persentase_kehadiran (decimal) - Persentase kehadiran
created_at, updated_at
```

## Akun Test

### Siswa
- **Username:** siswa1 | **Password:** password123
- **Username:** siswa2 | **Password:** password123
- **Username:** siswa3 | **Password:** password123

### Guru
- **Username:** guru1 | **Password:** password123
- **Username:** guru2 | **Password:** password123

## Data Sample

### Kelas
- X-A (Wali: Ibu Ratna Dewi)
- X-B (Wali: Pak Ahmad Pratama)

### Jam Pelajaran
- Jam 1: 07:00 - 07:45
- Jam 2: 07:45 - 08:30
- Jam 3: 08:30 - 09:15

### Absensi Sample (Hari Ini)
- Budi Santoso: Hadir (07:15)
- Siti Nurhaliza: Hadir (07:10)
- Ahmad Ridho: Sakit (demam)

## Relasi Model

```
User (1) ←→ (1) Siswa
User (1) ←→ (1) Guru
Kelas (1) ←→ (Many) Siswa
Kelas (1) ←→ (Many) Absensi
Kelas (1) ←→ (Many) QrCode
Kelas (1) ←→ (Many) JadwalPelajaran
Siswa (1) ←→ (Many) Absensi
Guru (1) ←→ (Many) JadwalPelajaran
JamPelajaran (1) ←→ (Many) JadwalPelajaran
QrCode (1) ←→ (Many) Absensi
LaporanAbsensi (1) ←→ (Many) DetailLaporanAbsensi
```
