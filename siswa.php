<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['admin','guru']);
$title = 'Siswa';
require __DIR__ . '/includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus'])) q('DELETE FROM siswa WHERE id=?', [(int)$_POST['hapus']], 'i');
    elseif (!empty($_POST['id'])) q('UPDATE siswa SET nama=?,kelas=? WHERE id=?', [$_POST['nama'], $_POST['kelas'], (int)$_POST['id']], 'ssi');
    else q('INSERT INTO siswa (nama,kelas) VALUES (?,?)', [$_POST['nama'], $_POST['kelas']]);
    header('Location: siswa.php'); exit;
}
$edit = isset($_GET['edit']) ? row('SELECT * FROM siswa WHERE id=?', [(int)$_GET['edit']], 'i') : null;
$data = rows('SELECT * FROM siswa ORDER BY nama');
?>
<div class="two">
<div class="card tw"><table><thead><tr><th>Nama</th><th>Kelas</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data as $d): ?><tr><td><?= e($d['nama']) ?></td><td><?= e($d['kelas']) ?></td>
<td class="row" style="margin:0"><a class="btn ghost sm" href="?edit=<?= $d['id'] ?>">Edit</a>
<form method="post" data-confirm="Hapus siswa ini? Checklist terkait ikut terhapus."><input type="hidden" name="hapus" value="<?= $d['id'] ?>"><button class="btn ghost sm">Hapus</button></form></td></tr>
<?php endforeach; ?></tbody></table></div>
<form class="card form" method="post"><h3><?= $edit ? 'Edit' : 'Tambah' ?> Siswa</h3>
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  <label>Nama</label><input name="nama" value="<?= e($edit['nama'] ?? '') ?>" required>
  <label>Kelas</label><input name="kelas" value="<?= e($edit['kelas'] ?? '') ?>" required>
  <button class="btn">Simpan</button> <?php if ($edit): ?><a class="btn ghost" href="siswa.php">Batal</a><?php endif; ?>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
