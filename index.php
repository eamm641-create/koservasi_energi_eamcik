<?php
$title = 'Dashboard';
require __DIR__ . '/includes/header.php';
$staff = is_staff(); $sid = my_siswa_id();
$base  = $staff ? '1=1' : 'siswa_id=' . $sid;
$basec = $staff ? '1=1' : 'c.siswa_id=' . $sid;
$cnt   = fn($x = '1=1') => (int)row("SELECT COUNT(*) c FROM checklist WHERE $base AND $x")['c'];
$hidup = $cnt("status='Hidup'"); $mati = $cnt("status='Mati'"); $total = max($hidup + $mati, 1);
$per  = rows("SELECT p.nama, COUNT(c.id) j FROM perangkat p LEFT JOIN checklist c ON c.perangkat_id=p.id AND $basec GROUP BY p.id ORDER BY j DESC LIMIT 5");
$maxj = max(array_column($per, 'j') ?: [1]) ?: 1;
$last = rows("SELECT c.*, s.nama sn, p.nama pn FROM checklist c JOIN siswa s ON s.id=c.siswa_id JOIN perangkat p ON p.id=c.perangkat_id WHERE $basec ORDER BY c.tanggal DESC, c.id DESC LIMIT 5");
$role = $_SESSION['user']['role'];
$note = ['admin'=>'Admin: Anda dapat memeriksa dan mengelola semua checklist.','guru'=>'Guru: pantau checklist siswa dan verifikasi hasilnya.','siswa'=>'Siswa: isi checklist penggunaan perangkat Anda.'][$role];
?>
<p class="note rise"><?= $note ?></p>
<div class="grid">
<?php if ($staff): ?>
  <div class="card stat"><b data-count="<?= (int)row('SELECT COUNT(*) c FROM siswa')['c'] ?>">0</b><span>Siswa</span></div>
  <div class="card stat"><b data-count="<?= (int)row('SELECT COUNT(*) c FROM perangkat')['c'] ?>">0</b><span>Perangkat</span></div>
  <div class="card stat"><b data-count="<?= $cnt() ?>">0</b><span>Total Checklist</span></div>
  <div class="card stat"><b data-count="<?= $cnt("verif_status='Belum'") ?>">0</b><span>Menunggu Verifikasi</span></div>
<?php else: ?>
  <div class="card stat"><b data-count="<?= $cnt() ?>">0</b><span>Checklist Saya</span></div>
  <div class="card stat"><b data-count="<?= $cnt("verif_status='Disetujui'") ?>">0</b><span>Disetujui</span></div>
  <div class="card stat"><b data-count="<?= $cnt("verif_status='Belum'") ?>">0</b><span>Menunggu Verifikasi</span></div>
  <div class="card stat"><b data-count="<?= $cnt("verif_status='Perlu Perbaikan'") ?>">0</b><span>Perlu Perbaikan</span></div>
<?php endif; ?>
</div>
<div class="two">
  <div class="card bars">
    <h3>Perangkat paling sering dicek</h3>
    <?php foreach ($per as $p): ?>
      <div class="b"><div class="t"><span><?= e($p['nama']) ?></span><span><?= $p['j'] ?>x</span></div>
      <div class="tr"><i style="width:<?= round($p['j'] / $maxj * 100) ?>%"></i></div></div>
    <?php endforeach; if (!$per) echo '<p>Belum ada data.</p>'; ?>
    <h3 style="margin-top:22px">Status perangkat</h3>
    <div class="b"><div class="t"><span>Mati (hemat)</span><span><?= $mati ?></span></div><div class="tr"><i style="width:<?= round($mati/$total*100) ?>%"></i></div></div>
    <div class="b"><div class="t"><span>Hidup</span><span><?= $hidup ?></span></div><div class="tr"><i style="width:<?= round($hidup/$total*100) ?>%"></i></div></div>
  </div>
  <div class="card">
    <h3 style="margin-bottom:10px">Checklist terbaru</h3>
    <?php foreach ($last as $c): ?>
      <p style="padding:8px 0;border-bottom:1px solid var(--g3)">
        <a href="checklist_view.php?id=<?= $c['id'] ?>"><b><?= e($c['pn']) ?></b></a>
        <span class="badge <?= $c['status']==='Mati'?'on':'' ?>"><?= $c['status'] ?></span> <?= vbadge($c['verif_status']) ?><br>
        <small style="color:var(--g4)"><?= e($c['sn']) ?> · <?= e($c['tanggal']) ?></small></p>
    <?php endforeach; if (!$last) echo '<p>Belum ada checklist.</p>'; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
