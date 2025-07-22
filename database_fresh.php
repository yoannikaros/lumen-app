<?php

/**
 * Database Fresh Script
 * Script untuk drop dan recreate database seperti php artisan migrate --fresh
 * 
 * Usage: php database_fresh.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: 'sb_farm_bigdata';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

echo "=== SB Farm Database Fresh Script ===\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n";
echo "Username: {$username}\n\n";

try {
    // Connect to MySQL server (without selecting database)
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "✓ Connected to MySQL server\n";
    
    // Drop database if exists
    echo "Dropping database '{$database}' if exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `{$database}`");
    echo "✓ Database dropped\n";
    
    // Create database
    echo "Creating database '{$database}'...\n";
    $pdo->exec("CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database created\n";
    
    // Select the database
    $pdo->exec("USE `{$database}`");
    
    // Create tables
    echo "\nCreating tables...\n";
    
    // 1. Users table
    echo "Creating users table...\n";
    $pdo->exec("
        CREATE TABLE `users` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `username` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `nama` varchar(255) NOT NULL,
            `telepon` varchar(20) DEFAULT NULL,
            `alamat` text DEFAULT NULL,
            `tanggal_lahir` date DEFAULT NULL,
            `jenis_kelamin` enum('L','P') DEFAULT 'L',
            `status` enum('aktif','nonaktif') DEFAULT 'aktif',
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `users_username_unique` (`username`),
            UNIQUE KEY `users_email_unique` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 2. Roles table
    echo "Creating roles table...\n";
    $pdo->exec("
        CREATE TABLE `roles` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama` varchar(255) NOT NULL,
            `deskripsi` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `roles_nama_unique` (`nama`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 3. Permissions table
    echo "Creating permissions table...\n";
    $pdo->exec("
        CREATE TABLE `permissions` (
            `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama` varchar(255) NOT NULL,
            `deskripsi` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`permission_id`),
            UNIQUE KEY `permissions_nama_unique` (`nama`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 4. User Roles table
    echo "Creating user_roles table...\n";
    $pdo->exec("
        CREATE TABLE `user_roles` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) unsigned NOT NULL,
            `role_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `user_roles_user_id_foreign` (`user_id`),
            KEY `user_roles_role_id_foreign` (`role_id`),
            CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 5. Role Permissions table
    echo "Creating role_permissions table...\n";
    $pdo->exec("
        CREATE TABLE `role_permissions` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `role_id` bigint(20) unsigned NOT NULL,
            `permission_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `role_permissions_role_id_foreign` (`role_id`),
            KEY `role_permissions_permission_id_foreign` (`permission_id`),
            CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
            CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 6. Area Kebun table
    echo "Creating area_kebun table...\n";
    $pdo->exec("
        CREATE TABLE `area_kebun` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama_area` varchar(255) NOT NULL,
            `deskripsi` text DEFAULT NULL,
            `luas_m2` decimal(8,2) DEFAULT NULL,
            `kapasitas_tanaman` int(11) DEFAULT NULL,
            `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 7. Jenis Pupuk table
    echo "Creating jenis_pupuk table...\n";
    $pdo->exec("
        CREATE TABLE `jenis_pupuk` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama_pupuk` varchar(255) NOT NULL,
            `deskripsi` text DEFAULT NULL,
            `satuan` varchar(50) DEFAULT NULL,
            `harga_per_satuan` decimal(10,2) DEFAULT NULL,
            `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 8. Pencatatan Pupuk table
    echo "Creating pencatatan_pupuk table...\n";
    $pdo->exec("
        CREATE TABLE `pencatatan_pupuk` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal` date NOT NULL,
            `jenis_pupuk_id` bigint(20) unsigned NOT NULL,
            `jumlah_pupuk` decimal(8,2) NOT NULL,
            `satuan` varchar(50) DEFAULT NULL,
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `pencatatan_pupuk_jenis_pupuk_id_foreign` (`jenis_pupuk_id`),
            KEY `pencatatan_pupuk_user_id_foreign` (`user_id`),
            CONSTRAINT `pencatatan_pupuk_jenis_pupuk_id_foreign` FOREIGN KEY (`jenis_pupuk_id`) REFERENCES `jenis_pupuk` (`id`) ON DELETE CASCADE,
            CONSTRAINT `pencatatan_pupuk_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 9. Penjualan Sayur table
    echo "Creating penjualan_sayur table...\n";
    $pdo->exec("
        CREATE TABLE `penjualan_sayur` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal_penjualan` date NOT NULL,
            `nama_pembeli` varchar(255) NOT NULL,
            `tipe_pembeli` enum('individu','toko','pasar','online') DEFAULT 'individu',
            `alamat_pembeli` text DEFAULT NULL,
            `jenis_sayur` varchar(255) NOT NULL,
            `jumlah_kg` decimal(8,2) NOT NULL,
            `harga_per_kg` decimal(10,2) NOT NULL,
            `total_harga` decimal(12,2) NOT NULL,
            `metode_pembayaran` enum('tunai','transfer','kredit') DEFAULT 'tunai',
            `status_pembayaran` enum('lunas','belum_lunas','cicilan') DEFAULT 'lunas',
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `penjualan_sayur_user_id_foreign` (`user_id`),
            CONSTRAINT `penjualan_sayur_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 10. Belanja Modal table
    echo "Creating belanja_modal table...\n";
    $pdo->exec("
        CREATE TABLE `belanja_modal` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal_belanja` date NOT NULL,
            `kategori` enum('benih','pupuk','pestisida','alat','lainnya') NOT NULL,
            `deskripsi` varchar(255) NOT NULL,
            `jumlah` decimal(10,2) NOT NULL,
            `satuan` varchar(50) DEFAULT NULL,
            `nama_toko` varchar(255) DEFAULT NULL,
            `alamat_toko` text DEFAULT NULL,
            `metode_pembayaran` enum('tunai','transfer','kredit') DEFAULT 'tunai',
            `bukti_pembayaran` varchar(255) DEFAULT NULL,
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `belanja_modal_user_id_foreign` (`user_id`),
            CONSTRAINT `belanja_modal_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 11. Nutrisi Pupuk table
    echo "Creating nutrisi_pupuk table...\n";
    $pdo->exec("
        CREATE TABLE `nutrisi_pupuk` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal_pencatatan` date NOT NULL,
            `area_id` bigint(20) unsigned NOT NULL,
            `jumlah_tanda_air` decimal(8,2) DEFAULT NULL,
            `jumlah_pupuk_ml` decimal(8,2) DEFAULT NULL,
            `jumlah_air_liter` decimal(8,2) DEFAULT NULL,
            `ppm_sebelum` decimal(8,2) DEFAULT NULL,
            `ppm_sesudah` decimal(8,2) DEFAULT NULL,
            `ph_sebelum` decimal(4,2) DEFAULT NULL,
            `ph_sesudah` decimal(4,2) DEFAULT NULL,
            `suhu_air` decimal(5,2) DEFAULT NULL,
            `kondisi_cuaca` varchar(100) DEFAULT NULL,
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `nutrisi_pupuk_area_id_foreign` (`area_id`),
            KEY `nutrisi_pupuk_user_id_foreign` (`user_id`),
            CONSTRAINT `nutrisi_pupuk_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area_kebun` (`id`) ON DELETE CASCADE,
            CONSTRAINT `nutrisi_pupuk_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 12. Data Sayur table
    echo "Creating data_sayur table...\n";
    $pdo->exec("
        CREATE TABLE `data_sayur` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal_tanam` date NOT NULL,
            `jenis_sayur` varchar(255) NOT NULL,
            `varietas` varchar(255) DEFAULT NULL,
            `area_id` bigint(20) unsigned NOT NULL,
            `jumlah_bibit` int(11) NOT NULL,
            `metode_tanam` varchar(100) DEFAULT NULL,
            `jenis_media` varchar(100) DEFAULT NULL,
            `tanggal_panen_target` date DEFAULT NULL,
            `tanggal_panen_aktual` date DEFAULT NULL,
            `status_panen` enum('belum_panen','panen_sukses','gagal_panen') DEFAULT 'belum_panen',
            `jumlah_panen_kg` decimal(8,2) DEFAULT NULL,
            `penyebab_gagal` varchar(255) DEFAULT NULL,
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `data_sayur_area_id_foreign` (`area_id`),
            KEY `data_sayur_user_id_foreign` (`user_id`),
            CONSTRAINT `data_sayur_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area_kebun` (`id`) ON DELETE CASCADE,
            CONSTRAINT `data_sayur_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 13. Tandon table
    echo "Creating tandon table...\n";
    $pdo->exec("
        CREATE TABLE `tandon` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama_tandon` varchar(255) NOT NULL,
            `kapasitas_liter` decimal(10,2) NOT NULL,
            `lokasi` varchar(255) DEFAULT NULL,
            `status` enum('aktif','tidak_aktif','maintenance') DEFAULT 'aktif',
            `keterangan` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 14. Perlakuan Master table
    echo "Creating perlakuan_master table...\n";
    $pdo->exec("
        CREATE TABLE `perlakuan_master` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `nama_perlakuan` varchar(255) NOT NULL,
            `tipe` enum('pemupukan','penyiraman','pestisida','perawatan','lainnya') NOT NULL,
            `deskripsi` text DEFAULT NULL,
            `satuan_default` varchar(50) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 15. Jadwal Perlakuan table
    echo "Creating jadwal_perlakuan table...\n";
    $pdo->exec("
        CREATE TABLE `jadwal_perlakuan` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tanggal` date NOT NULL,
            `minggu_ke` int(11) NOT NULL,
            `hari_dalam_minggu` int(11) NOT NULL,
            `area_id` bigint(20) unsigned NOT NULL,
            `tandon_id` bigint(20) unsigned NOT NULL,
            `perlakuan_id` bigint(20) unsigned NOT NULL,
            `dosis` decimal(8,2) NOT NULL,
            `satuan` varchar(50) NOT NULL,
            `keterangan` text DEFAULT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `jadwal_perlakuan_area_id_foreign` (`area_id`),
            KEY `jadwal_perlakuan_tandon_id_foreign` (`tandon_id`),
            KEY `jadwal_perlakuan_perlakuan_id_foreign` (`perlakuan_id`),
            KEY `jadwal_perlakuan_user_id_foreign` (`user_id`),
            CONSTRAINT `jadwal_perlakuan_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area_kebun` (`id`) ON DELETE CASCADE,
            CONSTRAINT `jadwal_perlakuan_tandon_id_foreign` FOREIGN KEY (`tandon_id`) REFERENCES `tandon` (`id`) ON DELETE CASCADE,
            CONSTRAINT `jadwal_perlakuan_perlakuan_id_foreign` FOREIGN KEY (`perlakuan_id`) REFERENCES `perlakuan_master` (`id`) ON DELETE CASCADE,
            CONSTRAINT `jadwal_perlakuan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 16. Pembelian Benih Detail table
    echo "Creating pembelian_benih_detail table...\n";
    $pdo->exec("
        CREATE TABLE `pembelian_benih_detail` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `belanja_modal_id` bigint(20) unsigned NOT NULL,
            `nama_benih` varchar(255) NOT NULL,
            `varietas` varchar(255) DEFAULT NULL,
            `qty` decimal(8,2) NOT NULL,
            `unit` varchar(50) NOT NULL,
            `harga_per_unit` decimal(10,2) NOT NULL,
            `keterangan` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `pembelian_benih_detail_belanja_modal_id_foreign` (`belanja_modal_id`),
            CONSTRAINT `pembelian_benih_detail_belanja_modal_id_foreign` FOREIGN KEY (`belanja_modal_id`) REFERENCES `belanja_modal` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 17. Penjualan Detail Batch table
    echo "Creating penjualan_detail_batch table...\n";
    $pdo->exec("
        CREATE TABLE `penjualan_detail_batch` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `penjualan_id` bigint(20) unsigned NOT NULL,
            `data_sayur_id` bigint(20) unsigned NOT NULL,
            `qty_kg` decimal(8,2) NOT NULL,
            `keterangan` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `penjualan_detail_batch_penjualan_id_foreign` (`penjualan_id`),
            KEY `penjualan_detail_batch_data_sayur_id_foreign` (`data_sayur_id`),
            CONSTRAINT `penjualan_detail_batch_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_sayur` (`id`) ON DELETE CASCADE,
            CONSTRAINT `penjualan_detail_batch_data_sayur_id_foreign` FOREIGN KEY (`data_sayur_id`) REFERENCES `data_sayur` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // 18. Activity Logs table
    echo "Creating activity_logs table...\n";
    $pdo->exec("
        CREATE TABLE `activity_logs` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `action` varchar(255) NOT NULL,
            `table_name` varchar(255) DEFAULT NULL,
            `record_id` bigint(20) unsigned DEFAULT NULL,
            `details` text DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `activity_logs_user_id_foreign` (`user_id`),
            CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ All tables created successfully\n";
    
    // Insert default data
    echo "\nInserting default data...\n";
    
    // Insert default roles
    echo "Inserting default roles...\n";
    $pdo->exec("
        INSERT INTO `roles` (`nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
        ('admin', 'Administrator dengan akses penuh', NOW(), NOW()),
        ('user', 'User biasa dengan akses terbatas', NOW(), NOW())
    ");
    
    // Insert default permissions
    echo "Inserting default permissions...\n";
    $pdo->exec("
        INSERT INTO `permissions` (`nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
        ('manage_users', 'Mengelola data pengguna', NOW(), NOW()),
        ('manage_areas', 'Mengelola area kebun', NOW(), NOW()),
        ('manage_fertilizers', 'Mengelola data pupuk', NOW(), NOW()),
        ('manage_sales', 'Mengelola penjualan sayur', NOW(), NOW()),
        ('manage_expenses', 'Mengelola belanja modal', NOW(), NOW()),
        ('manage_nutrition', 'Mengelola nutrisi pupuk', NOW(), NOW()),
        ('manage_vegetables', 'Mengelola data sayur', NOW(), NOW()),
        ('view_reports', 'Melihat laporan', NOW(), NOW())
    ");
    
    // Assign all permissions to admin role
    echo "Assigning permissions to admin role...\n";
    $pdo->exec("
        INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
        SELECT r.id, p.permission_id, NOW(), NOW()
        FROM `roles` r, `permissions` p
        WHERE r.nama = 'admin'
    ");
    
    // Assign limited permissions to user role
    echo "Assigning permissions to user role...\n";
    $pdo->exec("
        INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
        SELECT r.id, p.permission_id, NOW(), NOW()
        FROM `roles` r, `permissions` p
        WHERE r.nama = 'user' AND p.nama IN ('manage_areas', 'manage_fertilizers', 'manage_sales', 'manage_expenses', 'manage_nutrition', 'manage_vegetables', 'view_reports')
    ");
    
    // Insert sample area kebun
    echo "Inserting sample area kebun...\n";
    $pdo->exec("
        INSERT INTO `area_kebun` (`nama_area`, `deskripsi`, `luas_m2`, `kapasitas_tanaman`, `status`, `created_at`, `updated_at`) VALUES
        ('Area A', 'Area utama untuk sayuran hijau di bagian depan kebun', 100.00, 500, 'aktif', NOW(), NOW()),
        ('Area B', 'Area untuk sayuran buah di bagian belakang kebun', 150.00, 750, 'aktif', NOW(), NOW())
    ");
    
    // Insert sample jenis pupuk
    echo "Inserting sample jenis pupuk...\n";
    $pdo->exec("
        INSERT INTO `jenis_pupuk` (`nama_pupuk`, `deskripsi`, `satuan`, `harga_per_satuan`, `status`, `created_at`, `updated_at`) VALUES
        ('NPK 16-16-16', 'Pupuk NPK dengan kandungan nitrogen, fosfor, dan kalium seimbang untuk pertumbuhan optimal tanaman', 'kg', 15000.00, 'aktif', NOW(), NOW()),
        ('Pupuk Organik Cair', 'Pupuk organik cair untuk nutrisi tambahan dan meningkatkan kualitas tanah', 'liter', 25000.00, 'aktif', NOW(), NOW())
    ");
    
    // Insert sample tandon
    echo "Inserting sample tandon...\n";
    $pdo->exec("
        INSERT INTO `tandon` (`nama_tandon`, `kapasitas_liter`, `lokasi`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
        ('Tandon Utama', 1000.00, 'Bagian tengah kebun', 'aktif', 'Tandon utama untuk penyiraman area A dan B', NOW(), NOW()),
        ('Tandon Cadangan', 500.00, 'Bagian belakang kebun', 'aktif', 'Tandon cadangan untuk area B', NOW(), NOW())
    ");
    
    // Insert sample perlakuan master
    echo "Inserting sample perlakuan master...\n";
    $pdo->exec("
        INSERT INTO `perlakuan_master` (`nama_perlakuan`, `tipe`, `deskripsi`, `satuan_default`, `created_at`, `updated_at`) VALUES
        ('Penyiraman Rutin', 'penyiraman', 'Penyiraman rutin untuk menjaga kelembaban tanah', 'liter', NOW(), NOW()),
        ('Pemupukan NPK', 'pemupukan', 'Pemberian pupuk NPK untuk nutrisi tanaman', 'gram', NOW(), NOW()),
        ('Penyemprotan Pestisida', 'pestisida', 'Penyemprotan pestisida untuk mengendalikan hama', 'ml', NOW(), NOW()),
        ('Pembersihan Gulma', 'perawatan', 'Pembersihan gulma di sekitar tanaman', 'area', NOW(), NOW()),
        ('Pemupukan Organik', 'pemupukan', 'Pemberian pupuk organik cair', 'ml', NOW(), NOW())
    ");
    
    echo "✓ Default data inserted successfully\n";
    
    echo "\n=== Database Fresh Completed Successfully! ===\n";
    echo "Database '{$database}' has been recreated with all tables and default data.\n";
    echo "\nYou can now:\n";
    echo "1. Register a new user via API: POST /api/register\n";
    echo "2. Login to get JWT token: POST /api/login\n";
    echo "3. Start using the API endpoints\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
?>