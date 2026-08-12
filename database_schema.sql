-- Tạo và dùng database
CREATE DATABASE IF NOT EXISTS `thongtindoanhnghiep`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `thongtindoanhnghiep`;

-- ============================================================
-- Bảng tỉnh / thành phố
-- ============================================================
CREATE TABLE IF NOT EXISTS `tinh_thanh` (
  `id`       INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `ten`      VARCHAR(150)    NOT NULL,
  `slug`     VARCHAR(180)    NOT NULL,
  `mien_tay` TINYINT(1)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed 13 tỉnh Miền Tây
INSERT INTO `tinh_thanh` (`ten`, `slug`, `mien_tay`) VALUES
  ('Long An',    'long-an',    1),
  ('Tiền Giang', 'tien-giang', 1),
  ('Bến Tre',    'ben-tre',    1),
  ('Trà Vinh',   'tra-vinh',   1),
  ('Vĩnh Long',  'vinh-long',  1),
  ('Đồng Tháp',  'dong-thap',  1),
  ('An Giang',   'an-giang',   1),
  ('Kiên Giang', 'kien-giang', 1),
  ('Cần Thơ',    'can-tho',    1),
  ('Hậu Giang',  'hau-giang',  1),
  ('Sóc Trăng',  'soc-trang',  1),
  ('Bạc Liêu',   'bac-lieu',   1),
  ('Cà Mau',     'ca-mau',     1)
ON DUPLICATE KEY UPDATE `mien_tay` = 1;

-- ============================================================
-- Bảng phường / xã / thị trấn
-- ============================================================
CREATE TABLE IF NOT EXISTS `phuong_xa` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten`           VARCHAR(200) NOT NULL,
  `slug`          VARCHAR(220) NOT NULL,
  `tinh_thanh_id` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug_tinh` (`slug`, `tinh_thanh_id`),
  KEY `fk_px_tinh` (`tinh_thanh_id`),
  CONSTRAINT `fk_px_tinh`
    FOREIGN KEY (`tinh_thanh_id`) REFERENCES `tinh_thanh`(`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng loại hình doanh nghiệp
-- ============================================================
CREATE TABLE IF NOT EXISTS `loai_hinh_doanh_nghiep` (
  `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten`  VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng ngành nghề
-- ============================================================
CREATE TABLE IF NOT EXISTS `nganh_nghe` (
  `id`       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `ten`      VARCHAR(300)  NOT NULL,
  `slug`     VARCHAR(350)  NOT NULL,
  `mo_ta`    TEXT          DEFAULT NULL,
  `hinh_anh` VARCHAR(255)  DEFAULT NULL,
  `ngay_tao` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng doanh nghiệp (bảng chính)
-- ============================================================
CREATE TABLE IF NOT EXISTS `doanh_nghiep` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mst`             VARCHAR(20)  NOT NULL,
  `ten_cong_ty`     VARCHAR(500) NOT NULL,
  `ten_quoc_te`     VARCHAR(500) DEFAULT NULL,
  `ten_viet_tat`    VARCHAR(200) DEFAULT NULL,
  `nguoi_dai_dien`  VARCHAR(255) DEFAULT NULL,
  `dia_chi`         TEXT         DEFAULT NULL,
  `dia_chi_thue`    TEXT         DEFAULT NULL,
  `dien_thoai`      VARCHAR(50)  DEFAULT NULL,
  `tinh_trang`      VARCHAR(100) DEFAULT NULL,
  `ngay_hoat_dong`  VARCHAR(50)  DEFAULT NULL,
  `quan_ly_boi`     VARCHAR(255) DEFAULT NULL,
  `loai_hinh_id`    INT UNSIGNED DEFAULT NULL,
  `nganh_nghe_id`   INT UNSIGNED DEFAULT NULL,
  `tinh_thanh_id`   INT UNSIGNED DEFAULT NULL,
  `phuong_xa_id`    INT UNSIGNED DEFAULT NULL,
  `url_nguon`       VARCHAR(600) NOT NULL,
  `ngay_cap_nhat`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                      ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mst` (`mst`),
  KEY `fk_dn_loai`  (`loai_hinh_id`),
  KEY `fk_dn_nganh` (`nganh_nghe_id`),
  KEY `fk_dn_tinh`  (`tinh_thanh_id`),
  KEY `fk_dn_phuong`(`phuong_xa_id`),
  CONSTRAINT `fk_dn_loai`   FOREIGN KEY (`loai_hinh_id`)   REFERENCES `loai_hinh_doanh_nghiep`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dn_nganh`  FOREIGN KEY (`nganh_nghe_id`)  REFERENCES `nganh_nghe`(`id`)              ON DELETE SET NULL,
  CONSTRAINT `fk_dn_tinh`   FOREIGN KEY (`tinh_thanh_id`)  REFERENCES `tinh_thanh`(`id`)              ON DELETE SET NULL,
  CONSTRAINT `fk_dn_phuong` FOREIGN KEY (`phuong_xa_id`)   REFERENCES `phuong_xa`(`id`)               ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng hàng chờ crawl
-- ============================================================
CREATE TABLE IF NOT EXISTS `crawl_queue` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`          VARCHAR(600) NOT NULL,
  `trang_thai`   ENUM('cho','dang_xu_ly','thanh_cong','that_bai') NOT NULL DEFAULT 'cho',
  `so_lan_thu`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `ngay_them`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat`TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_url` (`url`),
  KEY `idx_trang_thai` (`trang_thai`, `so_lan_thu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng log crawl
-- ============================================================
CREATE TABLE IF NOT EXISTS `crawl_log` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`      VARCHAR(600) NOT NULL,
  `ket_qua`  ENUM('thanh_cong','that_bai') NOT NULL,
  `ghi_chu`  TEXT DEFAULT NULL,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(191)),
  KEY `idx_ket_qua` (`ket_qua`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
