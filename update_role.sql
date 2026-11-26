-- Jalankan HANYA jika database lama sudah terlanjur di-import (tanpa sistem role baru).
USE konservasienergi_eamcik;
ALTER TABLE users ADD COLUMN siswa_id INT NULL AFTER role;
ALTER TABLE checklist
  ADD COLUMN verif_status ENUM('Belum','Disetujui','Perlu Perbaikan') NOT NULL DEFAULT 'Belum',
  ADD COLUMN verif_oleh INT NULL,
  ADD COLUMN verif_catatan TEXT NULL,
  ADD COLUMN verif_at DATETIME NULL;
