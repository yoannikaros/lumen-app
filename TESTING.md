# SB Farm API Testing Guide

Panduan lengkap untuk menjalankan testing API SB Farm dengan berbagai metode.

## 📋 Daftar Isi

- [Persiapan Testing](#persiapan-testing)
- [Metode Testing](#metode-testing)
- [File Testing](#file-testing)
- [Cara Menjalankan Test](#cara-menjalankan-test)
- [Troubleshooting](#troubleshooting)

## 🚀 Persiapan Testing

### 1. Pastikan Server Berjalan
```bash
php -S localhost:8000 -t public
```

### 2. Pastikan Database Tersedia
- Database: `sb_farm_bigdata`
- Import schema dari `database_complete_schema.sql`

### 3. Konfigurasi Environment
Pastikan file `.env` sudah dikonfigurasi dengan benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sb_farm_bigdata
DB_USERNAME=root
DB_PASSWORD=
```

## 🧪 Metode Testing

### 1. PHPUnit Testing (Recommended)
Menggunakan framework PHPUnit standar untuk testing yang lebih terstruktur.

**Keunggulan:**
- Testing framework standar
- Assertion yang lengkap
- Report yang detail
- Integrasi dengan IDE

### 2. Custom Test Suite
Script custom yang memberikan output visual yang lebih menarik.

**Keunggulan:**
- Output yang colorful dan informatif
- Progress tracking real-time
- Summary yang detail
- Mudah dipahami

## 📁 File Testing

### 1. `tests/ApiCrudTest.php`
**PHPUnit Test Class** - Testing menggunakan framework PHPUnit

**Fitur:**
- ✅ Authentication testing
- ✅ CRUD operations untuk semua endpoint
- ✅ Validation testing
- ✅ Response format validation
- ✅ Filter dan pagination testing
- ✅ Dependency management

**Test Methods:**
- `testAreaKebunCrud()` - Test CRUD Area Kebun
- `testJenisPupukCrud()` - Test CRUD Jenis Pupuk
- `testPencatatanPupukCrud()` - Test CRUD Pencatatan Pupuk
- `testPenjualanSayurCrud()` - Test CRUD Penjualan Sayur
- `testBelanjaModalCrud()` - Test CRUD Belanja Modal
- `testAuthenticationEndpoints()` - Test Authentication
- `testFilterAndPagination()` - Test Filter & Pagination

### 2. `tests/ApiTestSuite.php`
**Custom Test Suite** - Testing dengan output yang lebih visual

**Fitur:**
- 🎨 Colorful output
- 📊 Real-time progress
- 📈 Detailed summary
- 🔄 Comprehensive CRUD testing

### 3. `run_api_tests.php`
**Test Runner Script** - Script untuk menjalankan custom test suite

**Usage:**
```bash
# Full test suite
php run_api_tests.php

# Quick test only
php run_api_tests.php quick
```

### 4. `run_tests.bat`
**Windows Batch Script** - Menu interaktif untuk menjalankan test

**Fitur:**
- 🖱️ Interactive menu
- ✅ Server status check
- 📋 Multiple test options
- 🪟 Windows optimized

## 🏃‍♂️ Cara Menjalankan Test

### Metode 1: Windows Batch Script (Termudah)
```bash
# Double click atau jalankan di command prompt
run_tests.bat
```

**Menu Options:**
1. **PHPUnit Tests** - Recommended untuk development
2. **Custom Test Suite** - Visual output yang menarik
3. **Quick Test** - Test cepat untuk validasi dasar
4. **All Tests** - Menjalankan semua jenis test

### Metode 2: PHPUnit Command Line
```bash
# Test specific class
vendor/bin/phpunit tests/ApiCrudTest.php

# Test with verbose output
vendor/bin/phpunit tests/ApiCrudTest.php --verbose

# Test specific method
vendor/bin/phpunit tests/ApiCrudTest.php --filter testAreaKebunCrud

# Test all files
vendor/bin/phpunit
```

### Metode 3: Custom Test Runner
```bash
# Full comprehensive test
php run_api_tests.php

# Quick validation test
php run_api_tests.php quick
```

### Metode 4: Manual Testing dengan Postman/Insomnia
Import collection dari dokumentasi API di `README.md`

## 📊 Output Testing

### PHPUnit Output
```
PHPUnit 9.x.x by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

Time: 00:02.345, Memory: 18.00 MB

OK (7 tests, 42 assertions)
```

### Custom Test Suite Output
```
============================================================
           SB FARM API COMPREHENSIVE TEST SUITE
============================================================
Starting comprehensive API testing...
Test started at: 2025-01-20 15:30:00
------------------------------------------------------------

🧪 Running: Complete CRUD Operations
----------------------------------------
--- Testing Area Kebun CRUD ---
✓ Area Kebun Created: ID 1
✓ Area Kebun Index Retrieved
✓ Area Kebun Detail Retrieved
✓ Area Kebun Updated
✓ Area Kebun Summary Retrieved

✅ Complete CRUD Operations PASSED (2.34s)

============================================================
                    TEST SUMMARY
============================================================
Total Tests: 4
✅ Passed: 4
❌ Failed: 0
⏱️  Total Duration: 8.45s
📅 Completed at: 2025-01-20 15:30:08
------------------------------------------------------------
🎉 ALL TESTS PASSED! API is working correctly.
============================================================
```

## 🔧 Troubleshooting

### Error: "Server is not running"
**Solusi:**
```bash
php -S localhost:8000 -t public
```

### Error: "Database connection failed"
**Solusi:**
1. Pastikan MySQL/MariaDB berjalan
2. Cek konfigurasi `.env`
3. Pastikan database `sb_farm_bigdata` sudah dibuat
4. Import schema dari `database_complete_schema.sql`

### Error: "Token authentication failed"
**Solusi:**
1. Pastikan JWT secret key dikonfigurasi
2. Cek middleware authentication
3. Pastikan user registration/login berfungsi

### Error: "Class not found"
**Solusi:**
```bash
composer dump-autoload
```

### Error: "Foreign key constraint"
**Solusi:**
1. Pastikan urutan testing benar (dependencies dulu)
2. Gunakan database transactions
3. Cleanup test data dengan benar

## 📝 Test Coverage

### Endpoint Coverage
- ✅ Authentication (register, login, me, logout)
- ✅ Area Kebun (CRUD + summary)
- ✅ Jenis Pupuk (CRUD + summary + active)
- ✅ Pencatatan Pupuk (CRUD + summary)
- ✅ Nutrisi Pupuk (CRUD + summary + areas)
- ✅ Data Sayur (CRUD + summary + areas)
- ✅ Penjualan Sayur (CRUD + summary)
- ✅ Belanja Modal (CRUD + summary + kategori)

### Feature Coverage
- ✅ CRUD Operations
- ✅ Authentication & Authorization
- ✅ Input Validation
- ✅ Response Format
- ✅ Error Handling
- ✅ Pagination
- ✅ Filtering
- ✅ Search
- ✅ Summary/Analytics
- ✅ Relationships

## 🎯 Best Practices

1. **Selalu jalankan test sebelum deploy**
2. **Gunakan database terpisah untuk testing**
3. **Cleanup test data setelah testing**
4. **Test dengan data yang realistis**
5. **Validasi semua response format**
6. **Test error scenarios**
7. **Monitor performance testing**

## 📞 Support

Jika mengalami masalah dengan testing:
1. Cek log error di `storage/logs/`
2. Pastikan semua dependencies terinstall
3. Validasi konfigurasi environment
4. Cek dokumentasi API di `README.md`

---

**Happy Testing! 🚀**