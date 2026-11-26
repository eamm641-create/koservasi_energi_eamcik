<?php
$title = 'Checklist';
require __DIR__ . '/includes/header.php';
$staff = is_staff(); $sid = my_siswa_id();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = (int)$_POST['hapus'];
    if (is_admin()) q('DELETE FROM checklist WHERE id=?', [$id], 'i');
    elseif (!$staff) q("DELETE FROM checklist WHERE id=? AND siswa_id=? AND verif_status<>'Disetujui'", [$id, $sid], 'ii');
    header('Location: checklist.php?ok=1'); exit;
}
$v = $_GET['v'] ?? '';
$where = $staff ? '1=1' : 'c.siswa_id=' . $sid; $par = [];
if (in_array($v, ['Belum','Disetujui','Perlu Perbaikan'], true)) { $where .= ' AND c.verif_status=?'; $par[] = $v; }
$data = rows("SELECT c.*, s.nama sn, p.nama pn FROM checklist c JOIN siswa s ON s.id=c.siswa_id JOIN perangkat p ON p.id=c.perangkat_id WHERE $where ORDER BY c.tanggal DESC, c.id DESC", $par);
?>
<?php if (isset($_GET['ok'])): ?><div class="alert">Data berhasil disimpan.</div><?php endif; ?>
<div class="row">
  <?php if ($staff || $sid): ?><a class="btn" href="checklist_form.php">+ Tambah Checklist</a><?php endif; ?>
  <?php if ($staff): ?>
    <a class="btn ghost" href="checklist.php">Semua</a>
    <a class="btn ghost" href="?v=Belum">Menunggu</a>
    <a class="btn ghost" href="?v=Disetujui">Disetujui</a>
    <a class="btn ghost" href="?v=Perlu Perbaikan">Perlu Perbaikan</a>
  <?php endif; ?>
</div>
<div class="card tw"><table>
<thead><tr><th>Tanggal</th><th>Siswa</th><th>Perangkat</th><th>Status</th><th>Verifikasi</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data as $c):
  $own = $c['siswa_id'] == $sid && $c['verif_status'] !== 'Disetujui';
  $canEdit = $staff || $own; $canDel = is_admin() || (!$staff && $own); ?>
<tr><td><?= e($c['tanggal']) ?></td><td><?= e($c['sn']) ?></td><td><?= e($c['pn']) ?></td>
<td><span class="badge <?= $c['status']==='Mati'?'on':'' ?>"><?= $c['status'] ?></span></td>
<td><?= vbadge($c['verif_status']) ?></td>
<td class="row" style="margin:0">
  <a class="btn ghost sm" href="checklist_view.php?id=<?= $c['id'] ?>"><?= $staff ? 'Periksa' : 'Detail' ?></a>
  <?php if ($canEdit): ?><a class="btn ghost sm" href="checklist_form.php?id=<?= $c['id'] ?>">Edit</a><?php endif; ?>
  <?php if ($canDel): ?><form method="post" data-confirm="Hapus checklist ini?"><input type="hidden" name="hapus" value="<?= $c['id'] ?>"><button class="btn ghost sm">Hapus</button></form><?php endif; ?>
</td></tr>
<?php endforeach; if (!$data): ?><tr><td colspan="6">Belum ada data.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
