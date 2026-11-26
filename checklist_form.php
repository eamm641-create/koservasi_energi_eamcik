<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$staff = is_staff(); $sid = my_siswa_id();
$id = (int)($_GET['id'] ?? 0);
$d = $id ? row('SELECT * FROM checklist WHERE id=?', [$id], 'i') : null;
if ($id && !$d) { header('Location: checklist.php'); exit; }
if ($d && !$staff && ($d['siswa_id'] != $sid || $d['verif_status'] === 'Disetujui')) { header('Location: checklist.php'); exit; }
if (!$staff && !$sid) {
    $title = 'Tambah Checklist'; require __DIR__ . '/includes/header.php';
    echo '<div class="alert">Akun Anda belum terhubung ke data siswa. Hubungi admin.</div>';
    require __DIR__ . '/includes/footer.php'; exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = $staff ? (int)$_POST['siswa_id'] : $sid;
    $v = [$_POST['tanggal'], $siswa_id, (int)$_POST['perangkat_id'], $_POST['status'] === 'Hidup' ? 'Hidup' : 'Mati', $_POST['catatan']];
    if ($d && $staff)  q('UPDATE checklist SET tanggal=?,siswa_id=?,perangkat_id=?,status=?,catatan=? WHERE id=?', [...$v, $id], 'siissi');
    elseif ($d)        q("UPDATE checklist SET tanggal=?,siswa_id=?,perangkat_id=?,status=?,catatan=?,verif_status='Belum',verif_oleh=NULL,verif_catatan=NULL,verif_at=NULL WHERE id=?", [...$v, $id], 'siissi');
    else               q('INSERT INTO checklist (tanggal,siswa_id,perangkat_id,status,catatan) VALUES (?,?,?,?,?)', $v, 'siiss');
    header('Location: checklist.php?ok=1'); exit;
}
$title = $d ? 'Edit Checklist' : 'Tambah Checklist';
require __DIR__ . '/includes/header.php';
$siswa = rows('SELECT * FROM siswa ORDER BY nama'); $per = rows('SELECT * FROM perangkat ORDER BY nama');
?>
<form class="card form" method="post">
  <label>Tanggal</label><input type="date" name="tanggal" value="<?= e($d['tanggal'] ?? date('Y-m-d')) ?>" required>
  <?php if ($staff): ?>
  <label>Siswa</label><select name="siswa_id" required>
    <?php foreach ($siswa as $s): ?><option value="<?= $s['id'] ?>" <?= ($d['siswa_id'] ?? 0) == $s['id'] ? 'selected' : '' ?>><?= e($s['nama']) ?> (<?= e($s['kelas']) ?>)</option><?php endforeach; ?></select>
  <?php endif; ?>
  <label>Perangkat</label><select name="perangkat_id" required>
    <?php foreach ($per as $p): ?><option value="<?= $p['id'] ?>" <?= ($d['perangkat_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>><?= e($p['nama']) ?> - <?= e($p['lokasi']) ?></option><?php endforeach; ?></select>
  <label>Status</label><select name="status">
    <option <?= ($d['status'] ?? '') === 'Mati' ? 'selected' : '' ?>>Mati</option><option <?= ($d['status'] ?? '') === 'Hidup' ? 'selected' : '' ?>>Hidup</option></select>
  <label>Catatan</label><textarea name="catatan" rows="3"><?= e($d['catatan'] ?? '') ?></textarea>
  <button class="btn">Simpan</button> <a class="btn ghost" href="checklist.php">Batal</a>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
