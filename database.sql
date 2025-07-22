/* =========================================================
   START MIGRATION
   ========================================================= */
START TRANSACTION;

-- ---------------------------------------------------------
-- 1. MASTER: TANDON (untuk kode P1, P2, R1, dst)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tandon (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  area_id          INT NOT NULL,
  kode_tandon      VARCHAR(20) NOT NULL UNIQUE,  -- contoh: P1, R2, S3
  nama_tandon      VARCHAR(100) DEFAULT NULL,
  kapasitas_liter  DECIMAL(10,2) DEFAULT NULL,
  status           ENUM('aktif','nonaktif') DEFAULT 'aktif',
  keterangan       TEXT DEFAULT NULL,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tandon_area FOREIGN KEY (area_id) REFERENCES area_kebun(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 2. PERBAIKI TABEL nutrisi_pupuk
--    - Rename kolom jumlah_tanda_air -> jumlah_tandon_air
--    - Tambah total_tandon INT (opsional agregat)
--    - Tambah flag_data_detail apakah ada detail per tandon
-- ---------------------------------------------------------
ALTER TABLE nutrisi_pupuk
  CHANGE COLUMN jumlah_tanda_air jumlah_tandon_air DECIMAL(10,2) NOT NULL COMMENT 'Jumlah tandon air (total volume terpakai / terisi)'
;

ALTER TABLE nutrisi_pupuk
  ADD COLUMN total_tandon INT NULL AFTER area_id,
  ADD COLUMN flag_data_detail TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=jika detail per tandon tercatat' AFTER total_tandon;

-- ---------------------------------------------------------
-- 3. DETAIL PPM / NUTRISI PER TANDON (multi entri P1..R3..)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS nutrisi_pupuk_detail (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  nutrisi_pupuk_id   INT NOT NULL,
  tandon_id          INT NOT NULL,
  ppm                DECIMAL(8,2) DEFAULT NULL,
  nutrisi_ditambah_ml DECIMAL(10,2) DEFAULT NULL,
  air_ditambah_liter DECIMAL(10,2) DEFAULT NULL,
  ph                 DECIMAL(4,2) DEFAULT NULL,
  suhu_air           DECIMAL(5,2) DEFAULT NULL,
  keterangan         TEXT DEFAULT NULL,
  created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_npdetail_np FOREIGN KEY (nutrisi_pupuk_id) REFERENCES nutrisi_pupuk(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_npdetail_tandon FOREIGN KEY (tandon_id) REFERENCES tandon(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uk_npdetail_np_tandon (nutrisi_pupuk_id, tandon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 4. EXTEND pencatatan_pupuk
--    - Tambah area_id (nullable)
--    - Tambah tandon_id (nullable)
--    - Tambah bentuk (padat/larutan/dll)
--    - Tambah volume_liter (jika larutan)
-- ---------------------------------------------------------
ALTER TABLE pencatatan_pupuk
  ADD COLUMN area_id INT NULL AFTER tanggal,
  ADD COLUMN tandon_id INT NULL AFTER area_id,
  ADD COLUMN bentuk ENUM('padat','larutan','lainnya') DEFAULT 'padat' AFTER satuan,
  ADD COLUMN volume_liter DECIMAL(10,2) NULL AFTER bentuk,
  ADD CONSTRAINT fk_pp_area FOREIGN KEY (area_id) REFERENCES area_kebun(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  ADD CONSTRAINT fk_pp_tandon FOREIGN KEY (tandon_id) REFERENCES tandon(id)
    ON UPDATE CASCADE ON DELETE SET NULL;

-- ---------------------------------------------------------
-- 5. SEED LOG (pembenihan mingguan: tray/hampan)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS seed_log (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  tanggal_semai     DATE NOT NULL,
  hari              VARCHAR(20) DEFAULT NULL, -- opsional: Senin, Selasa
  nama_benih        VARCHAR(100) NOT NULL,
  varietas          VARCHAR(100) DEFAULT NULL,
  satuan            ENUM('tray','hampan','pak','biji','gram','lainnya') NOT NULL,
  jumlah            DECIMAL(10,2) NOT NULL,
  sumber_benih      VARCHAR(255) DEFAULT NULL, -- referensi pembelian / batch
  data_sayur_id     INT NULL,                  -- link ke batch tanam jika sudah dipindah
  keterangan        TEXT DEFAULT NULL,
  user_id           INT NOT NULL,
  created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_seed_user FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_seed_datasayur FOREIGN KEY (data_sayur_id) REFERENCES data_sayur(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_seed_tanggal (tanggal_semai),
  INDEX idx_seed_nama (nama_benih)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 6. PLANT HEALTH LOG (kebusukan / gagal panen harian)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS plant_health_log (
  id                     INT AUTO_INCREMENT PRIMARY KEY,
  tanggal                DATE NOT NULL,
  data_sayur_id          INT NOT NULL,
  gejala                 ENUM('busuk','layu','jamur','serangga','nutrisi','lainnya') NOT NULL,
  jumlah_tanaman_terdampak INT NOT NULL,
  tindakan               TEXT DEFAULT NULL,
  keterangan             TEXT DEFAULT NULL,
  user_id                INT NOT NULL,
  created_at             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_phl_datasayur FOREIGN KEY (data_sayur_id) REFERENCES data_sayur(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_phl_user FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_phl_tanggal (tanggal),
  INDEX idx_phl_gejala (gejala)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 7. MASTER PERLAKUAN TANAMAN (CEF, Coklat, Putih, PTh, HIRACOL, ANTRACOL, Bawang Putih, dsb)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS perlakuan_master (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama_perlakuan VARCHAR(100) NOT NULL UNIQUE,
  tipe          ENUM('pupuk','fungisida','insektisida','biopestisida','kultur','lainnya') DEFAULT 'lainnya',
  deskripsi     TEXT DEFAULT NULL,
  satuan_default VARCHAR(20) DEFAULT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Preload common values (idempotent insert ignore)
INSERT IGNORE INTO perlakuan_master (nama_perlakuan,tipe)
VALUES ('Pupuk CEF','pupuk'),
       ('Pupuk Coklat','pupuk'),
       ('Pupuk Putih','pupuk'),
       ('PTh','lainnya'),
       ('HIRACOL','fungisida'),
       ('ANTRACOL','fungisida'),
       ('Bawang Putih','biopestisida');

-- ---------------------------------------------------------
-- 8. JADWAL PERLAKUAN BULANAN
--    (memetakan perlakuan_master ke hari/minggu/area/tandon)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS jadwal_perlakuan (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  tanggal         DATE NOT NULL,
  minggu_ke       TINYINT NULL, -- 1-5
  hari_dalam_minggu ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NULL,
  area_id         INT NULL,
  tandon_id       INT NULL,
  perlakuan_id    INT NOT NULL,
  dosis           DECIMAL(10,2) NULL,
  satuan          VARCHAR(20) DEFAULT NULL,
  keterangan      TEXT DEFAULT NULL,
  user_id         INT NOT NULL,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_jp_area FOREIGN KEY (area_id) REFERENCES area_kebun(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_jp_tandon FOREIGN KEY (tandon_id) REFERENCES tandon(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_jp_perlakuan FOREIGN KEY (perlakuan_id) REFERENCES perlakuan_master(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_jp_user FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_jp_tanggal (tanggal),
  INDEX idx_jp_area (area_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 9. DETAIL PEMBELIAN BENIH (mengaitkan ke belanja_modal kategori=benih)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS pembelian_benih_detail (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  belanja_modal_id   INT NOT NULL,
  nama_benih         VARCHAR(100) NOT NULL,
  varietas           VARCHAR(100) DEFAULT NULL,
  qty                DECIMAL(10,2) NOT NULL,
  unit               ENUM('gram','biji','pak','lainnya') NOT NULL,
  harga_per_unit     DECIMAL(12,2) DEFAULT NULL,
  keterangan         TEXT DEFAULT NULL,
  created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pb_belanja FOREIGN KEY (belanja_modal_id) REFERENCES belanja_modal(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_pb_nama (nama_benih)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 10. DETAIL PENJUALAN ↔ BATCH TANAM
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS penjualan_detail_batch (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  penjualan_id      INT NOT NULL,
  data_sayur_id     INT NOT NULL,
  qty_kg            DECIMAL(10,2) NOT NULL,
  keterangan        TEXT DEFAULT NULL,
  created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pdb_penjualan FOREIGN KEY (penjualan_id) REFERENCES penjualan_sayur(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pdb_datasayur FOREIGN KEY (data_sayur_id) REFERENCES data_sayur(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uk_pdb_penjualan_datasayur (penjualan_id, data_sayur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- 11. OPSIONAL: VIEW ringkas PPM harian gabungan
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW view_ppm_harian AS
SELECT
  np.tanggal_pencatatan AS tanggal,
  a.nama_area,
  t.kode_tandon,
  nd.ppm,
  nd.nutrisi_ditambah_ml,
  nd.air_ditambah_liter,
  nd.ph,
  nd.suhu_air
FROM nutrisi_pupuk np
JOIN nutrisi_pupuk_detail nd ON nd.nutrisi_pupuk_id = np.id
JOIN tandon t ON t.id = nd.tandon_id
JOIN area_kebun a ON a.id = np.area_id;

-- ---------------------------------------------------------
-- 12. OPSIONAL: VIEW persentase kebusukan (berdasarkan plant_health_log)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW view_kebusukan_harian AS
SELECT
  phl.tanggal,
  ds.jenis_sayur,
  ds.varietas,
  phl.gejala,
  SUM(phl.jumlah_tanaman_terdampak) AS total_terdampak
FROM plant_health_log phl
JOIN data_sayur ds ON ds.id = phl.data_sayur_id
GROUP BY phl.tanggal, ds.jenis_sayur, ds.varietas, phl.gejala;

COMMIT;
/* =========================================================
   END MIGRATION
   ========================================================= */
