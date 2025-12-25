<?php
echo "=== DIAGNOSIS KONEKSI DATABASE ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n";
echo "Extension Dir: " . ini_get('extension_dir') . "\n";

echo "\nMemeriksa Driver PDO...\n";
$drivers = PDO::getAvailableDrivers();
echo "Driver yang aktif: " . implode(', ', $drivers) . "\n";

if (!in_array('mysql', $drivers)) {
    echo "\n[BAHAYA] Driver 'mysql' TIDAK DITEMUKAN!\n";
    echo "Ini penyebab error 'could not find driver'.\n";
    echo "Pastikan extension=pdo_mysql aktif di php.ini.\n";
} else {
    echo "\n[OK] Driver 'mysql' ditemukan.\n";
    
    echo "\nMencoba koneksi ke database...\n";
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=absensi_qr_sekolah', 'root', '');
        echo "[SUKSES] Berhasil terhubung ke database MySQL!\n";
    } catch (PDOException $e) {
        echo "[GAGAL] Koneksi ditolak: " . $e->getMessage() . "\n";
    }
}
echo "==================================\n";
