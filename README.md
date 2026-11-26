# konservasienergi_eamcik

Web Konservasi Energi (PHP + MySQL, XAMPP) bertema hitam putih dengan animasi setelah login dan logout.

## Cara pakai
1. Salin folder `konservasienergi_eamcik` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL di XAMPP.
3. Buka phpMyAdmin, lalu Import `konservasi_energi_eamcik.sql`.
   (Jika database lama sudah terlanjur di-import, jalankan `update_role.sql` saja.)
4. Buka http://localhost/konservasienergi_eamcik/

## Akun awal (dibuat otomatis)
| Role  | Username | Password  |
|-------|----------|-----------|
| admin | eamm     | eamm123   |
| guru  | guru     | guru123   |
| siswa | siswa    | siswa123  |

## Hak akses
- **Siswa**: mengisi checklist (tambah/edit/hapus milik sendiri selama belum disetujui), melihat status verifikasi, melihat daftar perangkat.
- **Guru**: memantau semua checklist, memeriksa dan memverifikasi (Disetujui / Perlu Perbaikan), menambah/mengedit checklist, mengelola perangkat dan siswa.
- **Admin**: semua akses guru, ditambah menghapus checklist apa pun dan mengelola akun users (admin/guru/siswa).

Akun siswa baru dibuat admin lewat menu Users; data siswa terhubung otomatis.
Jika MySQL memakai password root, ubah di `config.php`.
