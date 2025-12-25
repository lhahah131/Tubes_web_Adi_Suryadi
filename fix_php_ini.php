<?php
$phpDir = dirname(PHP_BINARY);
$iniFile = $phpDir . DIRECTORY_SEPARATOR . 'php.ini';

echo "Memeriksa folder PHP di: $phpDir\n";

if (!file_exists($iniFile)) {
    echo "php.ini tidak ditemukan. Mencoba membuat dari template...\n";
    $template = $phpDir . DIRECTORY_SEPARATOR . 'php.ini-development';
    if (!file_exists($template)) {
        $template = $phpDir . DIRECTORY_SEPARATOR . 'php.ini-production';
    }
    
    if (file_exists($template)) {
        copy($template, $iniFile);
        echo "Berhasil membuat php.ini dari template.\n";
    } else {
        die("GAGAL: Tidak menemukan file template php.ini-development atau php.ini-production di $phpDir\n");
    }
} else {
    echo "php.ini ditemukan.\n";
}

$content = file_get_contents($iniFile);
$modified = false;

// 1. Fix extension_dir
$extDir = str_replace('\\', '/', $phpDir . '/ext');
// Regex to find extension_dir (commented or not)
if (preg_match('/^;?extension_dir\s*=\s*("ext"|"\.\/"|ext)/m', $content)) {
    $content = preg_replace('/^;?extension_dir\s*=\s*("ext"|"\.\/"|ext)/m', 'extension_dir = "' . $extDir . '"', $content);
    echo "Mengatur extension_dir ke: $extDir\n";
    $modified = true;
} else {
    // If not found, append it
    $content .= "\nextension_dir = \"$extDir\"\n";
    echo "Menambahkan extension_dir.\n";
    $modified = true;
}

// 2. Fix extensions
$extensions = ['pdo_mysql', 'mysqli', 'mbstring', 'openssl', 'fileinfo', 'curl'];
foreach ($extensions as $ext) {
    if (preg_match('/^;extension=' . $ext . '/m', $content)) {
        $content = preg_replace('/^;extension=' . $ext . '/m', 'extension=' . $ext, $content);
        echo "Mengaktifkan extension: $ext\n";
        $modified = true;
    } elseif (!preg_match('/^extension=' . $ext . '/m', $content)) {
        $content .= "\nextension=$ext\n";
        echo "Menambahkan extension: $ext\n";
        $modified = true;
    } else {
        echo "Extension $ext sudah aktif.\n";
    }
}

if ($modified) {
    file_put_contents($iniFile, $content);
    echo "\nSUKSES! php.ini telah diperbarui.\n";
    echo "Silakan jalankan 'php artisan migrate:fresh --seed' sekarang.\n";
} else {
    echo "\nphp.ini sudah benar, tidak ada perubahan yang diperlukan.\n";
}
