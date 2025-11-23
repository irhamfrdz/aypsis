CREATE TABLE IF NOT EXISTS `invoice_tagihan_sewas` (
  `id` bigint unsigned AUTO_INCREMENT PRIMARY KEY,
  `nomor_invoice` varchar(255) UNIQUE,
  `vendor` varchar(255),
  `tanggal_invoice` date,
  `tanggal_jatuh_tempo` date NULL,
  `subtotal` decimal(15,2) DEFAULT 0,
  `ppn` decimal(15,2) DEFAULT 0,
  `pph` decimal(15,2) DEFAULT 0,
  `total` decimal(15,2) DEFAULT 0,
  `status` enum('draft','approved','paid','cancelled') DEFAULT 'draft',
  `keterangan` text NULL,
  `created_by` bigint unsigned NULL,
  `approved_by` bigint unsigned NULL,
  `approved_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `deleted_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoice_tagihan_sewa_items` (
  `id` bigint unsigned AUTO_INCREMENT PRIMARY KEY,
  `invoice_tagihan_sewa_id` bigint unsigned NOT NULL,
  `daftar_tagihan_kontainer_sewa_id` bigint unsigned NOT NULL,
  `jumlah` decimal(15,2) DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `inv_sewa_id_idx` (`invoice_tagihan_sewa_id`),
  KEY `tagihan_sewa_id_idx` (`daftar_tagihan_kontainer_sewa_id`),
  CONSTRAINT `invoice_tagihan_sewa_items_invoice_id_fk` FOREIGN KEY (`invoice_tagihan_sewa_id`) REFERENCES `invoice_tagihan_sewas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inv_sewa_tagihan_fk` FOREIGN KEY (`daftar_tagihan_kontainer_sewa_id`) REFERENCES `daftar_tagihan_kontainer_sewa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration, batch) VALUES 
('2025_11_23_100109_create_invoice_tagihan_sewas_table', 100),
('2025_11_23_100133_create_invoice_tagihan_sewa_items_table', 100);
