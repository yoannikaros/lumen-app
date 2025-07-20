# SB Farm API
php -S localhost:8000 -t public
php database_fresh.php
API untuk sistem manajemen kebun SB Farm yang dibangun dengan Lumen Framework.

## Fitur

- **Autentikasi JWT** - Sistem login dan registrasi dengan JSON Web Token
- **Pencatatan Pupuk** - Manajemen pencatatan penggunaan pupuk
- **Penjualan Sayur** - Tracking penjualan hasil panen
- **Belanja Modal** - Pencatatan pengeluaran operasional
- **Nutrisi Pupuk** - Monitoring nutrisi dan kondisi tanaman
- **Data Sayur** - Manajemen data penanaman dan panen
- **Area Kebun** - Manajemen area/lahan kebun
- **Jenis Pupuk** - Master data jenis pupuk
- **Activity Logging** - Pencatatan aktivitas pengguna
- **CORS Support** - Cross-Origin Resource Sharing

## Persyaratan Sistem

- PHP >= 8.1
- MySQL/MariaDB
- Composer

## Instalasi

1. Clone repository atau copy project
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy file environment:
   ```bash
   cp .env.example .env
   ```

4. Konfigurasi database di file `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sb_farm_bigdata
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate JWT Secret:
   ```bash
   php -r "echo base64_encode(random_bytes(32));"
   ```
   Masukkan hasil ke `JWT_SECRET` di file `.env`

6. Buat database dan import schema dari `database_complete_schema.sql`

7. Jalankan server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Endpoint API

### Autentikasi

- `POST /auth/register` - Registrasi pengguna baru
- `POST /auth/login` - Login pengguna
- `GET /auth/me` - Info pengguna yang sedang login
- `POST /auth/logout` - Logout pengguna

### Pencatatan Pupuk

- `GET /api/pencatatan-pupuk` - List pencatatan pupuk
- `POST /api/pencatatan-pupuk` - Tambah pencatatan pupuk
- `GET /api/pencatatan-pupuk/summary` - Summary pencatatan pupuk
- `GET /api/pencatatan-pupuk/{id}` - Detail pencatatan pupuk
- `PUT /api/pencatatan-pupuk/{id}` - Update pencatatan pupuk
- `DELETE /api/pencatatan-pupuk/{id}` - Hapus pencatatan pupuk

### Penjualan Sayur

- `GET /api/penjualan-sayur` - List penjualan sayur
- `POST /api/penjualan-sayur` - Tambah penjualan sayur
- `GET /api/penjualan-sayur/summary` - Summary penjualan sayur
- `GET /api/penjualan-sayur/{id}` - Detail penjualan sayur
- `PUT /api/penjualan-sayur/{id}` - Update penjualan sayur
- `DELETE /api/penjualan-sayur/{id}` - Hapus penjualan sayur

### Belanja Modal

- `GET /api/belanja-modal` - List belanja modal
- `POST /api/belanja-modal` - Tambah belanja modal
- `GET /api/belanja-modal/summary` - Summary belanja modal
- `GET /api/belanja-modal/kategori` - List kategori belanja
- `GET /api/belanja-modal/{id}` - Detail belanja modal
- `PUT /api/belanja-modal/{id}` - Update belanja modal
- `DELETE /api/belanja-modal/{id}` - Hapus belanja modal

### Nutrisi Pupuk

- `GET /api/nutrisi-pupuk` - List nutrisi pupuk
- `POST /api/nutrisi-pupuk` - Tambah nutrisi pupuk
- `GET /api/nutrisi-pupuk/summary` - Summary nutrisi pupuk
- `GET /api/nutrisi-pupuk/areas` - List area aktif
- `GET /api/nutrisi-pupuk/{id}` - Detail nutrisi pupuk
- `PUT /api/nutrisi-pupuk/{id}` - Update nutrisi pupuk
- `DELETE /api/nutrisi-pupuk/{id}` - Hapus nutrisi pupuk

### Data Sayur

- `GET /api/data-sayur` - List data sayur
- `POST /api/data-sayur` - Tambah data sayur
- `GET /api/data-sayur/summary` - Summary data sayur
- `GET /api/data-sayur/areas` - List area aktif
- `GET /api/data-sayur/{id}` - Detail data sayur
- `PUT /api/data-sayur/{id}` - Update data sayur
- `DELETE /api/data-sayur/{id}` - Hapus data sayur

### Area Kebun

- `GET /api/area-kebun` - List area kebun
- `POST /api/area-kebun` - Tambah area kebun
- `GET /api/area-kebun/summary` - Summary area kebun
- `GET /api/area-kebun/{id}` - Detail area kebun
- `PUT /api/area-kebun/{id}` - Update area kebun
- `DELETE /api/area-kebun/{id}` - Hapus area kebun

### Jenis Pupuk

- `GET /api/jenis-pupuk` - List jenis pupuk
- `POST /api/jenis-pupuk` - Tambah jenis pupuk
- `GET /api/jenis-pupuk/summary` - Summary jenis pupuk
- `GET /api/jenis-pupuk/active` - List jenis pupuk aktif
- `GET /api/jenis-pupuk/{id}` - Detail jenis pupuk
- `PUT /api/jenis-pupuk/{id}` - Update jenis pupuk
- `DELETE /api/jenis-pupuk/{id}` - Hapus jenis pupuk

## Autentikasi

Semua endpoint API (kecuali register dan login) memerlukan JWT token di header:

```
Authorization: Bearer <your-jwt-token>
```

## Response Format

Semua response menggunakan format JSON:

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

Untuk error:

```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error"
}
```

## Filter dan Pagination

Sebagian besar endpoint list mendukung:

- **Pagination**: `?page=1&per_page=15`
- **Filter tanggal**: `?tanggal_mulai=2024-01-01&tanggal_selesai=2024-01-31`
- **Search**: `?search=keyword`
- **Status**: `?status=aktif`

## Teknologi

- **Framework**: Lumen 10.x
- **Database**: MySQL/MariaDB
- **Authentication**: JWT (Firebase PHP-JWT)
- **CORS**: Laravel CORS
- **PHP**: 8.1+

## Struktur Database

Database schema lengkap tersedia di file `database_complete_schema.sql` yang mencakup:

- Tabel users dan sistem role-permission
- Tabel master data (area_kebun, jenis_pupuk)
- Tabel transaksi (pencatatan_pupuk, penjualan_sayur, belanja_modal, nutrisi_pupuk, data_sayur)
- Tabel activity_log untuk audit trail

## Development

Untuk development, pastikan:

1. Database sudah dibuat dan schema diimport
2. File `.env` sudah dikonfigurasi dengan benar
3. Dependencies sudah diinstall dengan `composer install`
4. Server berjalan di `http://localhost:8000`

## Kontribusi

Untuk berkontribusi pada project ini:

1. Fork repository
2. Buat feature branch
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

Project ini menggunakan lisensi MIT.
