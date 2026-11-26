<?php
$title = 'Perangkat';
require __DIR__ . '/includes/header.php';
$staff = is_staff();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $staff) {
    if (isset($_POST['hapus'])) q('DELETE FROM perangkat WHERE id=?', [(int)$_POST['hapus']], 'i');
    elseif (!empty($_POST['id'])) q('UPDATE perangkat SET nama=?,lokasi=?,daya_watt=? WHERE id=?', [$_POST['nama'], $_POST['lokasi'], (int)$_POST['daya_watt'], (int)$_POST['id']], 'ssii');
    else q('INSERT INTO perangkat (nama,lokasi,daya_watt) VALUES (?,?,?)', [$_POST['nama'], $_POST['lokasi'], (int)$_POST['daya_watt']], 'ssi');
    header('Location: perangkat.php'); exit;
}
$edit = ($staff && isset($_GET['edit'])) ? row('SELECT * FROM perangkat WHERE id=?', [(int)$_GET['edit']], 'i') : null;
$data = rows('SELECT * FROM perangkat ORDER BY nama');
?>
<div class="<?= $staff ? 'two' : '' ?>">
<div class="card tw"><table><thead><tr><th>Nama</th><th>Lokasi</th><th>Daya (W)</th><?php if ($staff): ?><th>Aksi</th><?php endif; ?></tr></thead><tbody>
<?php foreach ($data as $d): ?><tr><td><?= e($d['nama']) ?></td><td><?= e($d['lokasi']) ?></td><td><?= (int)$d['daya_watt'] ?></td>
<?php if ($staff): ?><td class="row" style="margin:0"><a class="btn ghost sm" href="?edit=<?= $d['id'] ?>">Edit</a>
<form method="post" data-confirm="Hapus perangkat ini? Checklist terkait ikut terhapus."><input type="hidden" name="hapus" value="<?= $d['id'] ?>"><button class="btn ghost sm">Hapus</button></form></td><?php endif; ?></tr>
<?php endforeach; ?></tbody></table></div>
<?php if ($staff): ?><form class="card form" method="post"><h3><?= $edit ? 'Edit' : 'Tambah' ?> Perangkat</h3>
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  <label>Nama</label><input name="nama" value="<?= e($edit['nama'] ?? '') ?>" required>
  <label>Lokasi</label><input name="lokasi" value="<?= e($edit['lokasi'] ?? '') ?>" required>
  <label>Daya (Watt)</label><input type="number" min="0" name="daya_watt" value="<?= e($edit['daya_watt'] ?? 0) ?>" required>
  <button class="btn">Simpan</button> <?php if ($edit): ?><a class="btn ghost" href="perangkat.php">Batal</a><?php endif; ?>
</form><?php endif; ?></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
