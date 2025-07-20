# SB Farm API - Dokumentasi Postman

Dokumentasi lengkap untuk testing API SB Farm menggunakan Postman.

## Base URL
```
http://localhost:8000
```

## Authentication

API ini menggunakan JWT (JSON Web Token) untuk autentikasi. Setelah login, gunakan token yang diterima dalam header `Authorization: Bearer {token}` untuk semua endpoint yang dilindungi.

---

## 1. Authentication Endpoints

### 1.1 Register User
**POST** `/api/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "username": "testuser",
    "nama": "Test User",
    "email": "test@example.com",
    "password": "password123"
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

### 1.2 Login
**POST** `/api/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "username": "testuser",
    "password": "password123"
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

### 1.3 Get Current User
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

### 1.4 Logout
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

## 2. Area Kebun Management

### 2.1 Get All Area Kebun
**GET** `/api/area-kebun`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Query Parameters:**
- `page` (optional): Nomor halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 10)
- `search` (optional): Kata kunci pencarian

**Example:** `/api/area-kebun?page=1&per_page=5&search=test`

### 2.2 Create Area Kebun
**POST** `/api/area-kebun`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "nama_area": "Area Kebun Utama",
    "deskripsi": "Area kebun untuk tanaman sayuran",
    "luas_m2": 100.5,
    "kapasitas_tanaman": 500,
    "status": "aktif"
}
```

### 2.3 Get Area Kebun by ID
**GET** `/api/area-kebun/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### 2.4 Update Area Kebun
**PUT** `/api/area-kebun/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "nama_area": "Area Kebun Updated",
    "deskripsi": "Area kebun updated",
    "luas_m2": 120.0,
    "kapasitas_tanaman": 600,
    "status": "aktif"
}
```

### 2.5 Delete Area Kebun
**DELETE** `/api/area-kebun/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### 2.6 Get Area Kebun Summary
**GET** `/api/area-kebun/summary`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## 3. Jenis Pupuk Management

### 3.1 Get All Jenis Pupuk
**GET** `/api/jenis-pupuk`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### 3.2 Create Jenis Pupuk
**POST** `/api/jenis-pupuk`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "nama_pupuk": "NPK 16-16-16",
    "deskripsi": "Pupuk NPK untuk tanaman sayuran",
    "satuan": "kg",
    "harga_per_satuan": 25000,
    "status": "aktif"
}
```

### 3.3 Get Jenis Pupuk by ID
**GET** `/api/jenis-pupuk/{id}`

### 3.4 Update Jenis Pupuk
**PUT** `/api/jenis-pupuk/{id}`

**Body (JSON):**
```json
{
    "nama_pupuk": "NPK 16-16-16 Updated",
    "deskripsi": "Pupuk NPK updated",
    "satuan": "kg",
    "harga_per_satuan": 30000,
    "status": "aktif"
}
```

### 3.5 Delete Jenis Pupuk
**DELETE** `/api/jenis-pupuk/{id}`

### 3.6 Get Active Jenis Pupuk
**GET** `/api/jenis-pupuk/active`

### 3.7 Get Jenis Pupuk Summary
**GET** `/api/jenis-pupuk/summary`

---

## 4. Pencatatan Pupuk Management

### 4.1 Get All Pencatatan Pupuk
**GET** `/api/pencatatan-pupuk`

**Query Parameters:**
- `tanggal_dari` (optional): Filter tanggal mulai (YYYY-MM-DD)
- `tanggal_sampai` (optional): Filter tanggal akhir (YYYY-MM-DD)
- `page`, `per_page`, `search` (optional)

**Example:** `/api/pencatatan-pupuk?tanggal_dari=2024-01-01&tanggal_sampai=2024-01-31`

### 4.2 Create Pencatatan Pupuk
**POST** `/api/pencatatan-pupuk`

**Body (JSON):**
```json
{
    "tanggal": "2024-01-15",
    "jenis_pupuk_id": 1,
    "jumlah_pupuk": 5.5,
    "satuan": "kg",
    "keterangan": "Pemupukan rutin area kebun utama"
}
```

### 4.3 Get Pencatatan Pupuk by ID
**GET** `/api/pencatatan-pupuk/{id}`

### 4.4 Update Pencatatan Pupuk
**PUT** `/api/pencatatan-pupuk/{id}`

### 4.5 Delete Pencatatan Pupuk
**DELETE** `/api/pencatatan-pupuk/{id}`

### 4.6 Get Pencatatan Pupuk Summary
**GET** `/api/pencatatan-pupuk/summary`

---

## 5. Penjualan Sayur Management

### 5.1 Get All Penjualan Sayur
**GET** `/api/penjualan-sayur`

### 5.2 Create Penjualan Sayur
**POST** `/api/penjualan-sayur`

**Body (JSON):**
```json
{
    "tanggal_penjualan": "2024-01-15",
    "nama_pembeli": "Toko Sayur Segar",
    "tipe_pembeli": "pasar",
    "alamat_pembeli": "Jl. Pasar Raya No. 123",
    "jenis_sayur": "Selada Hijau",
    "jumlah_kg": 5.5,
    "harga_per_kg": 15000,
    "metode_pembayaran": "tunai",
    "status_pembayaran": "lunas",
    "keterangan": "Penjualan rutin ke pasar"
}
```

**Nilai yang valid:**
- `tipe_pembeli`: "pasar", "hotel", "restoran", "individu"
- `metode_pembayaran`: "tunai", "transfer", "kredit"
- `status_pembayaran`: "lunas", "belum_lunas", "sebagian"

### 5.3 Get Penjualan Sayur by ID
**GET** `/api/penjualan-sayur/{id}`

### 5.4 Update Penjualan Sayur
**PUT** `/api/penjualan-sayur/{id}`

### 5.5 Delete Penjualan Sayur
**DELETE** `/api/penjualan-sayur/{id}`

### 5.6 Get Penjualan Sayur Summary
**GET** `/api/penjualan-sayur/summary`

---

## 6. Belanja Modal Management

### 6.1 Get All Belanja Modal
**GET** `/api/belanja-modal`

### 6.2 Create Belanja Modal
**POST** `/api/belanja-modal`

**Body (JSON):**
```json
{
    "tanggal_belanja": "2024-01-15",
    "kategori": "benih",
    "deskripsi": "Benih selada varietas unggul",
    "jumlah": 1000,
    "satuan": "biji",
    "nama_toko": "Toko Pertanian Maju",
    "alamat_toko": "Jl. Pertanian No. 456",
    "metode_pembayaran": "tunai",
    "bukti_pembayaran": "Nota pembelian #001",
    "keterangan": "Pembelian benih untuk musim tanam baru"
}
```

**Kategori yang valid:**
- "benih"
- "pupuk"
- "pestisida"
- "alat"
- "lainnya"

### 6.3 Get Belanja Modal by ID
**GET** `/api/belanja-modal/{id}`

### 6.4 Update Belanja Modal
**PUT** `/api/belanja-modal/{id}`

### 6.5 Delete Belanja Modal
**DELETE** `/api/belanja-modal/{id}`

### 6.6 Get Kategori Belanja Modal
**GET** `/api/belanja-modal/kategori`

### 6.7 Get Belanja Modal Summary
**GET** `/api/belanja-modal/summary`

---

## 7. Nutrisi Pupuk Management

### 7.1 Get All Nutrisi Pupuk
**GET** `/api/nutrisi-pupuk`

### 7.2 Create Nutrisi Pupuk
**POST** `/api/nutrisi-pupuk`

**Body (JSON):**
```json
{
    "tanggal_pencatatan": "2024-01-15",
    "area_id": 1,
    "jumlah_tanda_air": 100,
    "jumlah_pupuk": 5.5,
    "jumlah_air": 200
}
```

### 7.3 Get Nutrisi Pupuk by ID
**GET** `/api/nutrisi-pupuk/{id}`

### 7.4 Update Nutrisi Pupuk
**PUT** `/api/nutrisi-pupuk/{id}`

### 7.5 Delete Nutrisi Pupuk
**DELETE** `/api/nutrisi-pupuk/{id}`

### 7.6 Get Areas for Nutrisi Pupuk
**GET** `/api/nutrisi-pupuk/areas`

### 7.7 Get Nutrisi Pupuk Summary
**GET** `/api/nutrisi-pupuk/summary`

---

## 8. Data Sayur Management

### 8.1 Get All Data Sayur
**GET** `/api/data-sayur`

### 8.2 Create Data Sayur
**POST** `/api/data-sayur`

**Body (JSON):**
```json
{
    "tanggal_tanam": "2024-01-15",
    "jenis_sayur": "Selada Hijau",
    "area_id": 1,
    "jumlah_bibit": 500
}
```

### 8.3 Get Data Sayur by ID
**GET** `/api/data-sayur/{id}`

### 8.4 Update Data Sayur
**PUT** `/api/data-sayur/{id}`

### 8.5 Delete Data Sayur
**DELETE** `/api/data-sayur/{id}`

### 8.6 Get Areas for Data Sayur
**GET** `/api/data-sayur/areas`

### 8.7 Get Data Sayur Summary
**GET** `/api/data-sayur/summary`

---

## 9. Common Response Format

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

## 10. Status Codes

- **200**: OK - Request berhasil
- **201**: Created - Resource berhasil dibuat
- **400**: Bad Request - Request tidak valid
- **401**: Unauthorized - Token tidak valid atau tidak ada
- **404**: Not Found - Resource tidak ditemukan
- **422**: Unprocessable Entity - Validation error
- **500**: Internal Server Error - Error server

---

## 11. Testing dengan Postman

### Setup Environment
1. Buat environment baru di Postman
2. Tambahkan variable:
   - `base_url`: `http://localhost:8000`
   - `token`: (akan diisi setelah login)

### Workflow Testing
1. **Register** user baru
2. **Login** untuk mendapatkan token
3. Copy token ke environment variable `token`
4. Test endpoint lainnya dengan token yang sudah didapat

### Pre-request Script untuk Auto Token
Tambahkan script ini di Collection level untuk auto-set token:

```javascript
if (pm.environment.get("token")) {
    pm.request.headers.add({
        key: "Authorization",
        value: "Bearer " + pm.environment.get("token")
    });
}
```

### Test Script untuk Save Token
Tambahkan script ini di request Login:

```javascript
if (pm.response.code === 200) {
    const response = pm.response.json();
    if (response.success && response.data.token) {
        pm.environment.set("token", response.data.token);
    }
}
```

---

## 12. Tips Penggunaan

1. **Selalu gunakan Content-Type: application/json** untuk semua request
2. **Gunakan Bearer Token** untuk semua endpoint yang dilindungi
3. **Perhatikan format tanggal** menggunakan YYYY-MM-DD
4. **Gunakan pagination** untuk endpoint yang mengembalikan banyak data
5. **Manfaatkan filter tanggal** untuk pencarian data berdasarkan periode
6. **Periksa validation rules** jika mendapat error 422

Dokumentasi ini mencakup semua endpoint yang tersedia dalam SB Farm API. Untuk informasi lebih detail tentang validation rules atau response format, silakan merujuk ke source code controller masing-masing endpoint.