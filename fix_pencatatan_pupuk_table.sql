-- Script untuk memperbaiki tabel pencatatan_pupuk
-- Menambahkan kolom yang hilang: area_id, tandon_id, bentuk, volume_liter

USE sb_farm;

-- Cek apakah kolom area_id sudah ada
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.columns 
WHERE table_schema = 'sb_farm' 
AND table_name = 'pencatatan_pupuk' 
AND column_name = 'area_id';

-- Tambahkan kolom jika belum ada
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE pencatatan_pupuk 
     ADD COLUMN area_id INT NULL AFTER tanggal,
     ADD COLUMN tandon_id INT NULL AFTER area_id,
     ADD COLUMN bentuk ENUM("padat","larutan","lainnya") DEFAULT "padat" AFTER satuan,
     ADD COLUMN volume_liter DECIMAL(10,2) NULL AFTER bentuk', 
    'SELECT "Kolom sudah ada" as status');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambahkan foreign key constraints jika belum ada
SET @fk_area_exists = 0;
SELECT COUNT(*) INTO @fk_area_exists 
FROM information_schema.table_constraints 
WHERE table_schema = 'sb_farm' 
AND table_name = 'pencatatan_pupuk' 
AND constraint_name = 'fk_pp_area';

SET @sql_fk = IF(@fk_area_exists = 0, 
    'ALTER TABLE pencatatan_pupuk 
     ADD CONSTRAINT fk_pp_area FOREIGN KEY (area_id) REFERENCES area_kebun(id) ON UPDATE CASCADE ON DELETE SET NULL,
     ADD CONSTRAINT fk_pp_tandon FOREIGN KEY (tandon_id) REFERENCES tandon(id) ON UPDATE CASCADE ON DELETE SET NULL', 
    'SELECT "Foreign keys sudah ada" as status');

PREPARE stmt2 FROM @sql_fk;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

SELECT 'Tabel pencatatan_pupuk berhasil diperbaiki!' as result;