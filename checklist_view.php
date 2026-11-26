<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$staff = is_staff(); $sid = my_siswa_id();
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $staff) {
    $vs = in_array($_POST['verif_status'], ['Belum','Disetujui','Perlu Perbaikan'], true) ? $_POST['verif_status'] : 'Belum';
    q('UPDATE checklist SET verif_status=?, verif_catatan=?, verif_oleh=?, verif_at=NOW() WHERE id=?', [$vs, $_POST['verif_catatan'], (int)$_SESSION['user']['id'], $id], 'ssii');
    header('Location: checklist_view.php?id=' . $id . '&ok=1'); exit;
}
$title = 'Detail Checklist';
require __DIR__ . '/includes/header.php';
$c = row('SELECT c.*, s.nama sn, s.kelas, p.nama pn, p.lokasi, p.daya_watt, u.nama vn FROM checklist c JOIN siswa s ON s.id=c.siswa_id JOIN perangkat p ON p.id=c.perangkat_id LEFT JOIN users u ON u.id=c.verif_oleh WHERE c.id=?', [$id], 'i');
if (!$c || (!$staff && $c['siswa_id'] != $sid)) { echo '<div class="alert">Data tidak ditemukan.</div>'; require __DIR__ . '/includes/footer.php'; exit; }
?>
<?php if (isset($_GET['ok'])): ?><div class="alert">Verifikasi tersimpan.</div><?php endif; ?>
<div class="two">
<div class="card"><table>
<tr><th>Tanggal</th><td><?= e($c['tanggal']) ?></td></tr>
<tr><th>Siswa</th><td><?= e($c['sn']) ?> (<?= e($c['kelas']) ?>)</td></tr>
<tr><th>Perangkat</th><td><?= e($c['pn']) ?> · <?= e($c['lokasi']) ?></td></tr>
<tr><th>Daya</th><td><?= (int)$c['daya_watt'] ?> Watt</td></tr>
<tr><th>Status</th><td><span class="badge <?= $c['status']==='Mati'?'on':'' ?>"><?= $c['status'] ?></span></td></tr>
<tr><th>Catatan</th><td><?= nl2br(e($c['catatan'])) ?: '-' ?></td></tr>
<tr><th>Verifikasi</th><td><?= vbadge($c['verif_status']) ?></td></tr>
<?php if ($c['verif_at']): ?>
<tr><th>Diperiksa oleh</th><td><?= e($c['vn'] ?? '-') ?> · <?= e($c['verif_at']) ?></td></tr>
<tr><th>Catatan pemeriksa</th><td><?= nl2br(e($c['verif_catatan'])) ?: '-' ?></td></tr>
<?php endif; ?>
</table>
<p style="margin-top:16px"><a class="btn ghost" href="checklist.php">Kembali</a></p></div>
<?php if ($staff): ?>
<form class="card form" method="post"><h3>Periksa checklist</h3>
  <label>Hasil pemeriksaan</label><select name="verif_status">
    <?php foreach (['Belum','Disetujui','Perlu Perbaikan'] as $o): ?><option <?= $c['verif_status'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select>
  <label>Catatan pemeriksa</label><textarea name="verif_catatan" rows="3"><?= e($c['verif_catatan']) ?></textarea>
  <button class="btn">Simpan Pemeriksaan</button>
</form>
<?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
