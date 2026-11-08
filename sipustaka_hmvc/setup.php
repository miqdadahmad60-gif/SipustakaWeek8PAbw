<?php
/**
 * Setup SiPustaka (jalankan SEKALI):  php setup.php
 *
 * Yang dilakukan:
 *  1. Mengunduh kerangka Laravel baru (composer create-project) lalu menggabungkannya
 *     dengan kode SiPustaka di folder ini (file SiPustaka tidak ditimpa).
 *  2. Memasang nwidart/laravel-modules + wikimedia/composer-merge-plugin
 *     dan mengatur merge-plugin pada composer.json.
 *  3. Mengatur .env (database db_sipustaka), membuat database, migrate + seed, storage:link.
 *
 * Kebutuhan: PHP >= 8.2, Composer, MySQL/MariaDB berjalan (XAMPP/Laragon), koneksi internet.
 * Opsional: php setup.php <db_user> <db_password>   (default: root, kosong)
 */

chdir(__DIR__);

$dbUser = $argv[1] ?? 'root';
$dbPass = $argv[2] ?? '';
$dbName = 'db_sipustaka';

function run(string $cmd): void
{
    echo "\n> $cmd\n";
    passthru($cmd, $code);
    if ($code !== 0) {
        echo "\n[GAGAL] perintah di atas berhenti dengan kode $code.\n";
        exit($code);
    }
}

function copyMissing(string $src, string $dst): void
{
    foreach (scandir($src) as $f) {
        if ($f === '.' || $f === '..') continue;
        $s = "$src/$f";
        $d = "$dst/$f";
        if (is_dir($s)) {
            if (! is_dir($d)) mkdir($d, 0777, true);
            copyMissing($s, $d);
        } elseif (! file_exists($d)) {
            copy($s, $d);
        }
    }
}

function rrmdir(string $dir): void
{
    if (! is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = "$dir/$f";
        is_dir($p) && ! is_link($p) ? rrmdir($p) : @unlink($p);
    }
    @rmdir($dir);
}

function setEnv(string $key, string $value): void
{
    $env = file_get_contents('.env');
    $line = $key.'='.$value;
    if (preg_match('/^#?\s*'.preg_quote($key, '/').'=.*$/m', $env)) {
        $env = preg_replace('/^#?\s*'.preg_quote($key, '/').'=.*$/m', str_replace('\\', '\\\\', $line), $env, 1);
    } else {
        $env .= "\n".$line;
    }
    file_put_contents('.env', $env);
}

// 1. Kerangka Laravel
if (! file_exists('artisan')) {
    run('composer create-project laravel/laravel _skeleton --prefer-dist --no-interaction');
    copyMissing('_skeleton', '.');
    rrmdir('_skeleton');
}

// 2. Atur composer.json DULU (autoload modul + merge-plugin), baru pasang paket.
//    Urutan ini penting: saat paket dipasang, Laravel langsung memuat provider modul,
//    jadi autoload "Modules\..." harus sudah terdaftar.
$composer = json_decode(file_get_contents('composer.json'), true);
$composer['config']['allow-plugins']['wikimedia/composer-merge-plugin'] = true;
$composer['extra']['merge-plugin'] = [
    'include' => ['Modules/*/composer.json'],
];
$composer['autoload']['psr-4']['Modules\\'] = 'Modules/';
foreach (['Book', 'Loan', 'UserManagement'] as $m) {
    $composer['autoload']['psr-4']["Modules\\$m\\"] = "Modules/$m/app/";
}
file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

run('composer dump-autoload --no-scripts');
run('composer require nwidart/laravel-modules wikimedia/composer-merge-plugin --no-interaction');
run('composer dump-autoload');

// 3. .env
if (! file_exists('.env')) copy('.env.example', '.env');
setEnv('APP_NAME', 'SiPustaka');
setEnv('APP_LOCALE', 'id');
setEnv('DB_CONNECTION', 'mysql');
setEnv('DB_HOST', '127.0.0.1');
setEnv('DB_PORT', '3306');
setEnv('DB_DATABASE', $dbName);
setEnv('DB_USERNAME', $dbUser);
setEnv('DB_PASSWORD', $dbPass);
if (! preg_match('/^APP_KEY=base64:/m', file_get_contents('.env'))) {
    run('php artisan key:generate --force');
}

// Buat database bila belum ada
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', $dbUser, $dbPass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "\nDatabase $dbName siap.\n";
} catch (Throwable $e) {
    echo "\n[PERHATIAN] Tidak bisa membuat database otomatis: ".$e->getMessage()."\n";
    echo "Buat database '$dbName' lewat phpMyAdmin, lalu jalankan: php artisan migrate --seed\n";
    exit(1);
}

run('php artisan config:clear');
run('php artisan migrate --seed --force');
run('php artisan storage:link');

echo "\n=====================================================\n";
echo " SiPustaka siap!  Jalankan:  php artisan serve\n";
echo " Buka http://127.0.0.1:8000\n";
echo " Admin : admin@sipustaka.test / admin12345\n";
echo " User  : user@sipustaka.test  / user12345\n";
echo "=====================================================\n";