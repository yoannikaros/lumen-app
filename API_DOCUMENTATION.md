# SB Farm API - Dokumentasi Lengkap

Dokumentasi API lengkap untuk sistem manajemen kebun SB Farm yang dibangun dengan Lumen Framework.

## 📋 Informasi Umum

### Base URL
```
http://localhost:8000
```

### Teknologi
- **Framework**: Lumen 10.x
- **Database**: MySQL/MariaDB
- **Authentication**: JWT (Firebase PHP-JWT)
- **CORS**: Laravel CORS
- **PHP**: 8.1+

### Authentication
API ini menggunakan JWT (JSON Web Token) untuk autentikasi. Setelah login, gunakan token yang diterima dalam header `Authorization: Bearer {token}` untuk semua endpoint yang dilindungi.

---

## 🔐 Authentication Endpoints

### 1. Register User
**POST** `/api/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "username": "string (required, unique)",
    "nama": "string (required)",
    "email": "string (required, email, unique)",
    "password": "string (required, min:6)"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "id": 1,
        "username": "testuser",
        "nama": "Test User",
        "email": "test@example.com"
    }
}
```

### 2. Login
**POST** `/api/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "username": "string (required)",
    "password": "string (required)"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 1,
            "username": "testuser",
            "nama": "Test User",
            "email": "test@example.com"
        }
    }
}
```

### 3. Get Current User
**GET** `/api/me`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "username": "testuser",
        "nama": "Test User",
        "email": "test@example.com"
    }
}
```

### 4. Logout
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

---

## 🌱 Area Kebun Management

### Data Structure
```json
{
    "id": "integer",
    "nama_area": "string",
    "deskripsi": "string",
    "luas_m2": "decimal(8,2)",
    "kapasitas_tanaman": "integer",
    "status": "enum(aktif, tidak_aktif)",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Area Kebun
**GET** `/api/area-kebun`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `search` (optional): Kata kunci pencarian

**Example:** `/api/area-kebun?page=1&per_page=5&search=utama`

### 2. Create Area Kebun
**POST** `/api/area-kebun`

**Request Body:**
```json
{
    "nama_area": "string (required)",
    "deskripsi": "string (optional)",
    "luas_m2": "decimal (required)",
    "kapasitas_tanaman": "integer (required)",
    "status": "enum (required): aktif, tidak_aktif"
}
```

### 3. Get Area Kebun by ID
**GET** `/api/area-kebun/{id}`

### 4. Update Area Kebun
**PUT** `/api/area-kebun/{id}`

### 5. Delete Area Kebun
**DELETE** `/api/area-kebun/{id}`

### 6. Get Area Kebun Summary
**GET** `/api/area-kebun/summary`

---

## 🧪 Jenis Pupuk Management

### Data Structure
```json
{
    "id": "integer",
    "nama_pupuk": "string",
    "deskripsi": "string",
    "satuan": "string",
    "harga_per_satuan": "decimal(10,2)",
    "status": "enum(aktif, tidak_aktif)",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Jenis Pupuk
**GET** `/api/jenis-pupuk`

### 2. Create Jenis Pupuk
**POST** `/api/jenis-pupuk`

**Request Body:**
```json
{
    "nama_pupuk": "string (required)",
    "deskripsi": "string (optional)",
    "satuan": "string (required)",
    "harga_per_satuan": "decimal (required)",
    "status": "enum (required): aktif, tidak_aktif"
}
```

### 3. Get Jenis Pupuk by ID
**GET** `/api/jenis-pupuk/{id}`

### 4. Update Jenis Pupuk
**PUT** `/api/jenis-pupuk/{id}`

### 5. Delete Jenis Pupuk
**DELETE** `/api/jenis-pupuk/{id}`

### 6. Get Active Jenis Pupuk
**GET** `/api/jenis-pupuk/active`

### 7. Get Jenis Pupuk Summary
**GET** `/api/jenis-pupuk/summary`

---

## 📝 Pencatatan Pupuk Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal": "date",
    "jenis_pupuk_id": "integer",
    "jumlah_pupuk": "decimal(8,2)",
    "satuan": "string",
    "keterangan": "text",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Pencatatan Pupuk
**GET** `/api/pencatatan-pupuk`

**Query Parameters:**
- `tanggal_dari` (optional): Filter tanggal mulai (YYYY-MM-DD)
- `tanggal_sampai` (optional): Filter tanggal akhir (YYYY-MM-DD)
- `page`, `per_page`, `search` (optional)

### 2. Create Pencatatan Pupuk
**POST** `/api/pencatatan-pupuk`

**Request Body:**
```json
{
    "tanggal": "date (required, format: YYYY-MM-DD)",
    "jenis_pupuk_id": "integer (required)",
    "jumlah_pupuk": "decimal (required)",
    "satuan": "string (required)",
    "keterangan": "text (optional)"
}
```

### 3. Get Pencatatan Pupuk by ID
**GET** `/api/pencatatan-pupuk/{id}`

### 4. Update Pencatatan Pupuk
**PUT** `/api/pencatatan-pupuk/{id}`

### 5. Delete Pencatatan Pupuk
**DELETE** `/api/pencatatan-pupuk/{id}`

### 6. Get Pencatatan Pupuk Summary
**GET** `/api/pencatatan-pupuk/summary`

---

## 💰 Penjualan Sayur Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal_penjualan": "date",
    "nama_pembeli": "string",
    "tipe_pembeli": "enum(pasar, hotel, restoran, individu)",
    "alamat_pembeli": "string",
    "jenis_sayur": "string",
    "jumlah_kg": "decimal(8,2)",
    "harga_per_kg": "decimal(10,2)",
    "total_harga": "decimal(12,2)",
    "metode_pembayaran": "enum(tunai, transfer, kredit)",
    "status_pembayaran": "enum(lunas, belum_lunas, sebagian)",
    "keterangan": "text",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Penjualan Sayur
**GET** `/api/penjualan-sayur`

### 2. Create Penjualan Sayur
**POST** `/api/penjualan-sayur`

**Request Body:**
```json
{
    "tanggal_penjualan": "date (required, format: YYYY-MM-DD)",
    "nama_pembeli": "string (required)",
    "tipe_pembeli": "enum (required): pasar, hotel, restoran, individu",
    "alamat_pembeli": "string (optional)",
    "jenis_sayur": "string (required)",
    "jumlah_kg": "decimal (required)",
    "harga_per_kg": "decimal (required)",
    "metode_pembayaran": "enum (required): tunai, transfer, kredit",
    "status_pembayaran": "enum (required): lunas, belum_lunas, sebagian",
    "keterangan": "text (optional)"
}
```

### 3. Get Penjualan Sayur by ID
**GET** `/api/penjualan-sayur/{id}`

### 4. Update Penjualan Sayur
**PUT** `/api/penjualan-sayur/{id}`

### 5. Delete Penjualan Sayur
**DELETE** `/api/penjualan-sayur/{id}`

### 6. Get Penjualan Sayur Summary
**GET** `/api/penjualan-sayur/summary`

---

## 🛒 Belanja Modal Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal_belanja": "date",
    "kategori": "enum(benih, pupuk, pestisida, alat, lainnya)",
    "deskripsi": "string",
    "jumlah": "decimal(10,2)",
    "satuan": "string",
    "nama_toko": "string",
    "alamat_toko": "string",
    "metode_pembayaran": "enum(tunai, transfer, kredit)",
    "bukti_pembayaran": "string",
    "keterangan": "text",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Belanja Modal
**GET** `/api/belanja-modal`

### 2. Create Belanja Modal
**POST** `/api/belanja-modal`

**Request Body:**
```json
{
    "tanggal_belanja": "date (required, format: YYYY-MM-DD)",
    "kategori": "enum (required): benih, pupuk, pestisida, alat, lainnya",
    "deskripsi": "string (required)",
    "jumlah": "decimal (required)",
    "satuan": "string (required)",
    "nama_toko": "string (optional)",
    "alamat_toko": "string (optional)",
    "metode_pembayaran": "enum (required): tunai, transfer, kredit",
    "bukti_pembayaran": "string (optional)",
    "keterangan": "text (optional)"
}
```

### 3. Get Belanja Modal by ID
**GET** `/api/belanja-modal/{id}`

### 4. Update Belanja Modal
**PUT** `/api/belanja-modal/{id}`

### 5. Delete Belanja Modal
**DELETE** `/api/belanja-modal/{id}`

### 6. Get Kategori Belanja Modal
**GET** `/api/belanja-modal/kategori`

### 7. Get Belanja Modal Summary
**GET** `/api/belanja-modal/summary`

---

## 🧪 Nutrisi Pupuk Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal_pencatatan": "date",
    "area_id": "integer",
    "jumlah_tanda_air": "decimal(8,2)",
    "jumlah_pupuk": "decimal(8,2)",
    "jumlah_air": "decimal(8,2)",
    "ppm_sebelum": "decimal(6,2)",
    "ppm_sesudah": "decimal(6,2)",
    "ph_sebelum": "decimal(4,2)",
    "ph_sesudah": "decimal(4,2)",
    "suhu_air": "decimal(5,2)",
    "kondisi_cuaca": "string",
    "keterangan": "text",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime",
    "area": {
        "id": "integer",
        "nama_area": "string",
        "deskripsi": "string"
    }
}
```

### 1. Get All Nutrisi Pupuk
**GET** `/api/nutrisi-pupuk`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10, max: 100)
- `tanggal_mulai` (optional): Filter tanggal mulai (format: YYYY-MM-DD)
- `tanggal_selesai` (optional): Filter tanggal akhir (format: YYYY-MM-DD)
- `area_id` (optional): Filter berdasarkan area kebun
- `kondisi_cuaca` (optional): Filter berdasarkan kondisi cuaca

**Example:** `/api/nutrisi-pupuk?tanggal_mulai=2024-01-01&tanggal_selesai=2024-12-31&area_id=1&kondisi_cuaca=cerah&per_page=15`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "tanggal_pencatatan": "2024-01-15",
                "area_id": 1,
                "jumlah_tanda_air": 50.5,
                "jumlah_pupuk": 120.0,
                "jumlah_air": 100.0,
                "ppm_sebelum": 800,
                "ppm_sesudah": 1200,
                "ph_sebelum": 6.0,
                "ph_sesudah": 6.5,
                "suhu_air": 25.5,
                "kondisi_cuaca": "cerah",
                "keterangan": "Pemberian nutrisi rutin",
                "area": {
                    "id": 1,
                    "nama_area": "Area Utama",
                    "deskripsi": "Area kebun utama"
                }
            }
        ],
        "per_page": 10,
        "total": 25
    }
}
```

### 2. Create Nutrisi Pupuk
**POST** `/api/nutrisi-pupuk`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "tanggal_pencatatan": "date (required, format: YYYY-MM-DD)",
    "area_id": "integer (required)",
    "jumlah_tanda_air": "decimal (optional)",
    "jumlah_pupuk": "decimal (optional)",
    "jumlah_air": "decimal (optional)",
    "ppm_sebelum": "decimal (optional)",
    "ppm_sesudah": "decimal (optional)",
    "ph_sebelum": "decimal (optional)",
    "ph_sesudah": "decimal (optional)",
    "suhu_air": "decimal (optional)",
    "kondisi_cuaca": "string (optional)",
    "keterangan": "text (optional)"
}
```

**Example Request:**
```json
{
    "tanggal_pencatatan": "2024-01-15",
    "area_id": 1,
    "jumlah_tanda_air": 50.5,
    "jumlah_pupuk": 120.0,
    "jumlah_air": 100.0,
    "ppm_sebelum": 800,
    "ppm_sesudah": 1200,
    "ph_sebelum": 6.0,
    "ph_sesudah": 6.5,
    "suhu_air": 25.5,
    "kondisi_cuaca": "cerah",
    "keterangan": "Pemberian nutrisi rutin untuk area kebun utama"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Nutrisi pupuk berhasil ditambahkan",
    "data": {
        "id": 1,
        "tanggal_pencatatan": "2024-01-15",
        "area_id": 1,
        "jumlah_tanda_air": 50.5,
        "jumlah_pupuk": 120.0,
        "jumlah_air": 100.0,
        "ppm_sebelum": 800,
        "ppm_sesudah": 1200,
        "ph_sebelum": 6.0,
        "ph_sesudah": 6.5,
        "suhu_air": 25.5,
        "kondisi_cuaca": "cerah",
        "keterangan": "Pemberian nutrisi rutin untuk area kebun utama",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 3. Get Nutrisi Pupuk by ID
**GET** `/api/nutrisi-pupuk/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "tanggal_pencatatan": "2024-01-15",
        "area_id": 1,
        "jumlah_tanda_air": 50.5,
        "jumlah_pupuk": 120.0,
        "jumlah_air": 100.0,
        "ppm_sebelum": 800,
        "ppm_sesudah": 1200,
        "ph_sebelum": 6.0,
        "ph_sesudah": 6.5,
        "suhu_air": 25.5,
        "kondisi_cuaca": "cerah",
        "keterangan": "Pemberian nutrisi rutin",
        "area": {
            "id": 1,
            "nama_area": "Area Utama",
            "deskripsi": "Area kebun utama"
        }
    }
}
```

### 4. Update Nutrisi Pupuk
**PUT** `/api/nutrisi-pupuk/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:** (sama seperti Create, semua field optional)

### 5. Delete Nutrisi Pupuk
**DELETE** `/api/nutrisi-pupuk/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "message": "Nutrisi pupuk berhasil dihapus"
}
```

### 6. Get Active Areas for Nutrisi Pupuk
**GET** `/api/nutrisi-pupuk/areas`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_area": "Area Utama",
            "deskripsi": "Area kebun utama",
            "status": "aktif"
        },
        {
            "id": 2,
            "nama_area": "Area Percobaan",
            "deskripsi": "Area untuk percobaan varietas baru",
            "status": "aktif"
        }
    ]
}
```

### 7. Get Nutrisi Pupuk Summary
**GET** `/api/nutrisi-pupuk/summary`

**Query Parameters:**
- `tanggal_mulai` (optional): Filter tanggal mulai (format: YYYY-MM-DD)
- `tanggal_selesai` (optional): Filter tanggal akhir (format: YYYY-MM-DD)

**Example:** `/api/nutrisi-pupuk/summary?tanggal_mulai=2024-01-01&tanggal_selesai=2024-12-31`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_records": 25,
        "total_pupuk_used": 3000.5,
        "total_air_used": 2500.0,
        "avg_ppm_before": 750.5,
        "avg_ppm_after": 1150.2,
        "avg_ph_before": 6.1,
        "avg_ph_after": 6.6,
        "avg_suhu_air": 25.8,
        "cuaca_distribution": {
            "cerah": 15,
            "berawan": 8,
            "hujan": 2
        },
        "area_distribution": {
            "Area Utama": 20,
            "Area Percobaan": 5
        }
    }
}
```

---

## 🥬 Data Sayur Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal_tanam": "date",
    "jenis_sayur": "string",
    "varietas": "string",
    "area_id": "integer",
    "jumlah_bibit": "integer",
    "metode_tanam": "string",
    "jenis_media": "string",
    "tanggal_panen_target": "date",
    "tanggal_panen_aktual": "date",
    "status_panen": "enum(belum_panen, panen_sukses, gagal_panen)",
    "jumlah_panen_kg": "decimal(8,2)",
    "penyebab_gagal": "string",
    "keterangan": "text",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime",
    "area": {
        "id": "integer",
        "nama_area": "string",
        "deskripsi": "string"
    }
}
```

### 1. Get All Data Sayur
**GET** `/api/data-sayur`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10, max: 100)
- `tanggal_tanam_mulai` (optional): Filter tanggal tanam mulai (format: YYYY-MM-DD)
- `tanggal_tanam_selesai` (optional): Filter tanggal tanam akhir (format: YYYY-MM-DD)
- `jenis_sayur` (optional): Filter berdasarkan jenis sayur
- `area_id` (optional): Filter berdasarkan area kebun
- `status_panen` (optional): Filter berdasarkan status panen (belum_panen, panen_sukses, gagal_panen)

**Example:** `/api/data-sayur?tanggal_tanam_mulai=2024-01-01&tanggal_tanam_selesai=2024-12-31&jenis_sayur=selada&area_id=1&status_panen=belum_panen&per_page=15`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "tanggal_tanam": "2024-01-15",
                "jenis_sayur": "Selada Hijau",
                "varietas": "Grand Rapids",
                "area_id": 1,
                "jumlah_bibit": 100,
                "metode_tanam": "hidroponik",
                "jenis_media": "rockwool",
                "tanggal_panen_target": "2024-02-15",
                "tanggal_panen_aktual": null,
                "status_panen": "belum_panen",
                "jumlah_panen_kg": null,
                "penyebab_gagal": null,
                "keterangan": "Penanaman selada varietas unggul",
                "area": {
                    "id": 1,
                    "nama_area": "Area Utama",
                    "deskripsi": "Area kebun utama"
                }
            }
        ],
        "per_page": 10,
        "total": 15
    }
}
```

### 2. Create Data Sayur
**POST** `/api/data-sayur`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "tanggal_tanam": "date (required, format: YYYY-MM-DD)",
    "jenis_sayur": "string (required)",
    "varietas": "string (optional)",
    "area_id": "integer (required)",
    "jumlah_bibit": "integer (required)",
    "metode_tanam": "string (optional)",
    "jenis_media": "string (optional)",
    "tanggal_panen_target": "date (optional, format: YYYY-MM-DD)",
    "tanggal_panen_aktual": "date (optional, format: YYYY-MM-DD)",
    "status_panen": "enum (optional): belum_panen, panen_sukses, gagal_panen",
    "jumlah_panen_kg": "decimal (optional)",
    "penyebab_gagal": "string (optional)",
    "keterangan": "text (optional)"
}
```

**Example Request:**
```json
{
    "tanggal_tanam": "2024-01-15",
    "jenis_sayur": "Selada Hijau",
    "varietas": "Grand Rapids",
    "area_id": 1,
    "jumlah_bibit": 100,
    "metode_tanam": "hidroponik",
    "jenis_media": "rockwool",
    "tanggal_panen_target": "2024-02-15",
    "status_panen": "belum_panen",
    "keterangan": "Penanaman selada varietas unggul untuk musim ini"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Data sayur berhasil ditambahkan",
    "data": {
        "id": 1,
        "tanggal_tanam": "2024-01-15",
        "jenis_sayur": "Selada Hijau",
        "varietas": "Grand Rapids",
        "area_id": 1,
        "jumlah_bibit": 100,
        "metode_tanam": "hidroponik",
        "jenis_media": "rockwool",
        "tanggal_panen_target": "2024-02-15",
        "tanggal_panen_aktual": null,
        "status_panen": "belum_panen",
        "jumlah_panen_kg": null,
        "penyebab_gagal": null,
        "keterangan": "Penanaman selada varietas unggul untuk musim ini",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 3. Get Data Sayur by ID
**GET** `/api/data-sayur/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "tanggal_tanam": "2024-01-15",
        "jenis_sayur": "Selada Hijau",
        "varietas": "Grand Rapids",
        "area_id": 1,
        "jumlah_bibit": 100,
        "metode_tanam": "hidroponik",
        "jenis_media": "rockwool",
        "tanggal_panen_target": "2024-02-15",
        "tanggal_panen_aktual": "2024-02-14",
        "status_panen": "panen_sukses",
        "jumlah_panen_kg": 15.5,
        "penyebab_gagal": null,
        "keterangan": "Panen berhasil dengan hasil memuaskan",
        "area": {
            "id": 1,
            "nama_area": "Area Utama",
            "deskripsi": "Area kebun utama"
        }
    }
}
```

### 4. Update Data Sayur
**PUT** `/api/data-sayur/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:** (sama seperti Create, semua field optional)

**Example Update Request (untuk update hasil panen):**
```json
{
    "tanggal_panen_aktual": "2024-02-14",
    "status_panen": "panen_sukses",
    "jumlah_panen_kg": 15.5,
    "keterangan": "Panen berhasil dengan hasil memuaskan"
}
```

### 5. Delete Data Sayur
**DELETE** `/api/data-sayur/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "message": "Data sayur berhasil dihapus"
}
```

### 6. Get Active Areas for Data Sayur
**GET** `/api/data-sayur/areas`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_area": "Area Utama",
            "deskripsi": "Area kebun utama",
            "status": "aktif"
        },
        {
            "id": 2,
            "nama_area": "Area Percobaan",
            "deskripsi": "Area untuk percobaan varietas baru",
            "status": "aktif"
        }
    ]
}
```

### 7. Get Data Sayur Summary
**GET** `/api/data-sayur/summary`

**Query Parameters:**
- `tanggal_tanam_mulai` (optional): Filter tanggal tanam mulai (format: YYYY-MM-DD)
- `tanggal_tanam_selesai` (optional): Filter tanggal tanam akhir (format: YYYY-MM-DD)

**Example:** `/api/data-sayur/summary?tanggal_tanam_mulai=2024-01-01&tanggal_tanam_selesai=2024-12-31`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_records": 15,
        "total_bibit_planted": 1500,
        "total_harvest_kg": 225.5,
        "success_rate": 80.0,
        "status_distribution": {
            "belum_panen": 5,
            "panen_sukses": 8,
            "gagal_panen": 2
        },
        "jenis_sayur_distribution": {
            "Selada Hijau": 8,
            "Kangkung": 4,
            "Bayam": 3
        },
        "area_distribution": {
            "Area Utama": 10,
            "Area Percobaan": 5
        },
        "metode_tanam_distribution": {
            "hidroponik": 12,
            "konvensional": 3
        }
    }
}
```

---

## 🌾 Pembelian Benih Detail Management

### Data Structure
```json
{
    "id": "integer",
    "belanja_modal_id": "integer",
    "nama_benih": "string",
    "varietas": "string",
    "qty": "decimal(8,2)",
    "unit": "string",
    "harga_per_unit": "decimal(10,2)",
    "keterangan": "string",
    "created_at": "datetime",
    "updated_at": "datetime",
    "belanja_modal": {
        "id": "integer",
        "tanggal": "date",
        "kategori": "string",
        "total_harga": "decimal(10,2)"
    }
}
```

### 1. Get All Pembelian Benih Detail
**GET** `/api/pembelian-benih-detail`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `belanja_modal_id` (optional): Filter berdasarkan ID belanja modal
- `nama_benih` (optional): Filter berdasarkan nama benih
- `varietas` (optional): Filter berdasarkan varietas
- `unit` (optional): Filter berdasarkan unit
- `qty_min` (optional): Filter quantity minimum
- `qty_max` (optional): Filter quantity maksimum
- `harga_min` (optional): Filter harga minimum
- `harga_max` (optional): Filter harga maksimum
- `date_from` (optional): Filter tanggal mulai (YYYY-MM-DD)
- `date_to` (optional): Filter tanggal akhir (YYYY-MM-DD)

### 2. Create Pembelian Benih Detail
**POST** `/api/pembelian-benih-detail`

**Request Body:**
```json
{
    "belanja_modal_id": "integer (required)",
    "nama_benih": "string (required)",
    "varietas": "string (optional)",
    "qty": "decimal (required, min:0.01)",
    "unit": "string (required)",
    "harga_per_unit": "decimal (required, min:0.01)",
    "keterangan": "string (optional)"
}
```

### 3. Get Pembelian Benih Detail by ID
**GET** `/api/pembelian-benih-detail/{id}`

### 4. Update Pembelian Benih Detail
**PUT** `/api/pembelian-benih-detail/{id}`

### 5. Delete Pembelian Benih Detail
**DELETE** `/api/pembelian-benih-detail/{id}`

### 6. Get by Belanja Modal
**GET** `/api/pembelian-benih-detail/belanja-modal/{belanja_modal_id}`

### 7. Get Summary
**GET** `/api/pembelian-benih-detail/summary`

### 8. Get Price Analysis
**GET** `/api/pembelian-benih-detail/price-analysis`

---

## 📦 Penjualan Detail Batch Management

### Data Structure
```json
{
    "id": "integer",
    "penjualan_id": "integer",
    "data_sayur_id": "integer",
    "qty_kg": "decimal(8,2)",
    "keterangan": "string",
    "created_at": "datetime",
    "updated_at": "datetime",
    "penjualan": {
        "id": "integer",
        "tanggal": "date",
        "nama_pembeli": "string",
        "total_harga": "decimal(10,2)"
    },
    "data_sayur": {
        "id": "integer",
        "jenis_sayur": "string",
        "varietas": "string",
        "area_id": "integer"
    }
}
```

### 1. Get All Penjualan Detail Batch
**GET** `/api/penjualan-detail-batch`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `penjualan_id` (optional): Filter berdasarkan ID penjualan
- `data_sayur_id` (optional): Filter berdasarkan ID data sayur
- `qty_min` (optional): Filter quantity minimum
- `qty_max` (optional): Filter quantity maksimum
- `date_from` (optional): Filter tanggal mulai (YYYY-MM-DD)
- `date_to` (optional): Filter tanggal akhir (YYYY-MM-DD)
- `jenis_sayur` (optional): Filter berdasarkan jenis sayur

### 2. Create Penjualan Detail Batch
**POST** `/api/penjualan-detail-batch`

**Request Body:**
```json
{
    "penjualan_id": "integer (required)",
    "data_sayur_id": "integer (required)",
    "qty_kg": "decimal (required, min:0.01)",
    "keterangan": "string (optional)"
}
```

### 3. Get Penjualan Detail Batch by ID
**GET** `/api/penjualan-detail-batch/{id}`

### 4. Update Penjualan Detail Batch
**PUT** `/api/penjualan-detail-batch/{id}`

### 5. Delete Penjualan Detail Batch
**DELETE** `/api/penjualan-detail-batch/{id}`

### 6. Get by Penjualan
**GET** `/api/penjualan-detail-batch/penjualan/{penjualan_id}`

### 7. Get by Data Sayur
**GET** `/api/penjualan-detail-batch/data-sayur/{data_sayur_id}`

### 8. Get Summary
**GET** `/api/penjualan-detail-batch/summary`

### 9. Get Batch Performance
**GET** `/api/penjualan-detail-batch/batch-performance`

---

## 💧 Tandon Management

### Data Structure
```json
{
    "id": "integer",
    "nama_tandon": "string",
    "kapasitas_liter": "decimal(10,2)",
    "lokasi": "string",
    "status": "enum(aktif, tidak_aktif, maintenance)",
    "keterangan": "string",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Tandon
**GET** `/api/tandon`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `search` (optional): Kata kunci pencarian
- `status` (optional): Filter berdasarkan status
- `kapasitas_min` (optional): Filter kapasitas minimum
- `kapasitas_max` (optional): Filter kapasitas maksimum

### 2. Create Tandon
**POST** `/api/tandon`

**Request Body:**
```json
{
    "nama_tandon": "string (required)",
    "kapasitas_liter": "decimal (required, min:0.01)",
    "lokasi": "string (optional)",
    "status": "enum (required): aktif, tidak_aktif, maintenance",
    "keterangan": "string (optional)"
}
```

### 3. Get Tandon by ID
**GET** `/api/tandon/{id}`

### 4. Update Tandon
**PUT** `/api/tandon/{id}`

### 5. Delete Tandon
**DELETE** `/api/tandon/{id}`

### 6. Get Summary
**GET** `/api/tandon/summary`

### 7. Get Active Tandon
**GET** `/api/tandon/active`

---

## 🔧 Perlakuan Master Management

### Data Structure
```json
{
    "id": "integer",
    "nama_perlakuan": "string",
    "tipe": "enum(pemupukan, penyiraman, pestisida, perawatan, lainnya)",
    "deskripsi": "string",
    "satuan_default": "string",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 1. Get All Perlakuan Master
**GET** `/api/perlakuan-master`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `search` (optional): Kata kunci pencarian
- `tipe` (optional): Filter berdasarkan tipe

### 2. Create Perlakuan Master
**POST** `/api/perlakuan-master`

**Request Body:**
```json
{
    "nama_perlakuan": "string (required)",
    "tipe": "enum (required): pemupukan, penyiraman, pestisida, perawatan, lainnya",
    "deskripsi": "string (optional)",
    "satuan_default": "string (optional)"
}
```

### 3. Get Perlakuan Master by ID
**GET** `/api/perlakuan-master/{id}`

### 4. Update Perlakuan Master
**PUT** `/api/perlakuan-master/{id}`

### 5. Delete Perlakuan Master
**DELETE** `/api/perlakuan-master/{id}`

### 6. Get Summary
**GET** `/api/perlakuan-master/summary`

### 7. Get by Tipe
**GET** `/api/perlakuan-master/tipe/{tipe}`

---

## 📅 Jadwal Perlakuan Management

### Data Structure
```json
{
    "id": "integer",
    "tanggal": "date",
    "minggu_ke": "integer",
    "hari_dalam_minggu": "integer",
    "area_id": "integer",
    "tandon_id": "integer",
    "perlakuan_id": "integer",
    "dosis": "decimal(8,2)",
    "satuan": "string",
    "keterangan": "string",
    "user_id": "integer",
    "created_at": "datetime",
    "updated_at": "datetime",
    "area": {
        "id": "integer",
        "nama_area": "string"
    },
    "tandon": {
        "id": "integer",
        "nama_tandon": "string"
    },
    "perlakuan": {
        "id": "integer",
        "nama_perlakuan": "string",
        "tipe": "string"
    },
    "user": {
        "id": "integer",
        "nama": "string"
    }
}
```

### 1. Get All Jadwal Perlakuan
**GET** `/api/jadwal-perlakuan`

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `tanggal` (optional): Filter berdasarkan tanggal (YYYY-MM-DD)
- `minggu_ke` (optional): Filter berdasarkan minggu ke
- `area_id` (optional): Filter berdasarkan area
- `tandon_id` (optional): Filter berdasarkan tandon
- `perlakuan_id` (optional): Filter berdasarkan perlakuan
- `user_id` (optional): Filter berdasarkan user
- `date_from` (optional): Filter tanggal mulai (YYYY-MM-DD)
- `date_to` (optional): Filter tanggal akhir (YYYY-MM-DD)

### 2. Create Jadwal Perlakuan
**POST** `/api/jadwal-perlakuan`

**Request Body:**
```json
{
    "tanggal": "date (required, format: YYYY-MM-DD)",
    "minggu_ke": "integer (required, min:1)",
    "hari_dalam_minggu": "integer (required, min:1, max:7)",
    "area_id": "integer (required)",
    "tandon_id": "integer (required)",
    "perlakuan_id": "integer (required)",
    "dosis": "decimal (required, min:0.01)",
    "satuan": "string (required)",
    "keterangan": "string (optional)"
}
```

### 3. Get Jadwal Perlakuan by ID
**GET** `/api/jadwal-perlakuan/{id}`

### 4. Update Jadwal Perlakuan
**PUT** `/api/jadwal-perlakuan/{id}`

### 5. Delete Jadwal Perlakuan
**DELETE** `/api/jadwal-perlakuan/{id}`

### 6. Get Summary
**GET** `/api/jadwal-perlakuan/summary`

### 7. Get by Month
**GET** `/api/jadwal-perlakuan/month/{year}/{month}`

### 8. Get by Area
**GET** `/api/jadwal-perlakuan/area/{area_id}`

### 9. Get by Perlakuan
**GET** `/api/jadwal-perlakuan/perlakuan/{perlakuan_id}`

### 10. Get Rotation Schedule
**GET** `/api/jadwal-perlakuan/rotation-schedule`

---

## 📊 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

### Pagination Response
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            // Array of items
        ],
        "first_page_url": "http://localhost:8000/api/endpoint?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://localhost:8000/api/endpoint?page=5",
        "next_page_url": "http://localhost:8000/api/endpoint?page=2",
        "path": "http://localhost:8000/api/endpoint",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 50
    }
}
```

---

## 🔢 HTTP Status Codes

- **200**: OK - Request berhasil
- **201**: Created - Resource berhasil dibuat
- **400**: Bad Request - Request tidak valid
- **401**: Unauthorized - Token tidak valid atau tidak ada
- **404**: Not Found - Resource tidak ditemukan
- **422**: Unprocessable Entity - Validation error
- **500**: Internal Server Error - Error server

---

## 📱 Panduan untuk Pengembangan Mobile

### 1. Authentication Flow
```
1. POST /api/register (untuk registrasi user baru)
2. POST /api/login (untuk mendapatkan JWT token)
3. Simpan token di local storage/secure storage
4. Gunakan token di header Authorization untuk semua request selanjutnya
5. POST /api/logout (untuk logout dan invalidate token)
```

### 2. Data Types untuk Mobile Development

#### Enum Values
- **Status**: `aktif`, `tidak_aktif`
- **Status Tandon**: `aktif`, `tidak_aktif`, `maintenance`
- **Tipe Pembeli**: `pasar`, `hotel`, `restoran`, `individu`
- **Metode Pembayaran**: `tunai`, `transfer`, `kredit`
- **Status Pembayaran**: `lunas`, `belum_lunas`, `sebagian`
- **Kategori Belanja**: `benih`, `pupuk`, `pestisida`, `alat`, `lainnya`
- **Status Panen**: `belum_panen`, `panen_sukses`, `gagal_panen`
- **Tipe Perlakuan**: `pemupukan`, `penyiraman`, `pestisida`, `perawatan`, `lainnya`

#### Date Format
- Semua tanggal menggunakan format: `YYYY-MM-DD`
- Contoh: `2024-01-15`

#### Decimal Fields
- Gunakan tipe data decimal/float untuk field seperti:
  - `luas_m2`, `jumlah_kg`, `harga_per_kg`, `total_harga`
  - `jumlah_pupuk`, `ppm_sebelum`, `ph_sebelum`, dll

### 3. Pagination Handling
```json
{
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50,
    "data": []
}
```

### 4. Error Handling
```javascript
// Contoh handling error di mobile app
if (response.status === 401) {
    // Token expired, redirect to login
    redirectToLogin();
} else if (response.status === 422) {
    // Validation error, show field errors
    showValidationErrors(response.data.errors);
} else if (!response.data.success) {
    // General error
    showErrorMessage(response.data.message);
}
```

### 5. Recommended Request Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {jwt_token}
```

### 6. Offline Capability
- Simpan data penting di local database (SQLite)
- Sync data ketika koneksi tersedia
- Implementasi queue untuk request yang gagal

### 7. Security Best Practices
- Gunakan HTTPS di production
- Simpan JWT token di secure storage
- Implement token refresh mechanism
- Validate semua input di client side

---

## 🧪 Testing

### Postman Collection
Gunakan file `SB_Farm_API.postman_collection.json` yang tersedia di root project untuk testing dengan Postman.

### Environment Variables
- `base_url`: `http://localhost:8000`
- `token`: (akan diisi otomatis setelah login)

### Test Workflow
1. Register user baru
2. Login untuk mendapatkan token
3. Test semua endpoint dengan token yang valid
4. Test error scenarios (invalid token, validation errors, dll)

---

## 📞 Support

Untuk pertanyaan atau bantuan pengembangan, silakan merujuk ke:
- Source code di repository
- File `TESTING.md` untuk panduan testing
- File `README.md` untuk setup dan instalasi

---

*Dokumentasi ini dibuat untuk memudahkan pengembangan aplikasi mobile yang terintegrasi dengan SB Farm API.*