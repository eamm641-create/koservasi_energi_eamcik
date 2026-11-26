<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$title = 'Users';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role  = in_array($_POST['role'] ?? '', ['admin','guru','siswa'], true) ? $_POST['role'] : 'siswa';
    $kelas = trim($_POST['kelas'] ?? '') ?: '-';
    $uname = trim($_POST['username'] ?? '');
    if (isset($_POST['hapus'])) {
        if ((int)$_POST['hapus'] !== (int)$_SESSION['user']['id']) q('DELETE FROM users WHERE id=?', [(int)$_POST['hapus']], 'i');
    } else {
        $id = (int)($_POST['id'] ?? 0);
        if (row('SELECT id FROM users WHERE username=? AND id<>?', [$uname, $id], 'si')) {
            $err = 'Username sudah dipakai.';
        } else {
            if ($id) {
                q('UPDATE users SET username=?,nama=?,role=? WHERE id=?', [$uname, $_POST['nama'], $role, $id], 'sssi');
                if ($_POST['password'] !== '') q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['password'], PASSWORD_DEFAULT), $id], 'si');
            } else {
                q('INSERT INTO users (username,password,nama,role) VALUES (?,?,?,?)', [$uname, password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['nama'], $role]);
                $id = $conn->insert_id;
            }
            if ($role === 'siswa') {   // akun siswa otomatis terhubung ke data siswa
                $u = row('SELECT siswa_id FROM users WHERE id=?', [$id], 'i');
                if ($u['siswa_id']) q('UPDATE siswa SET nama=?,kelas=? WHERE id=?', [$_POST['nama'], $kelas, $u['siswa_id']], 'ssi');
                else {
                    q('INSERT INTO siswa (nama,kelas) VALUES (?,?)', [$_POST['nama'], $kelas]);
                    q('UPDATE users SET siswa_id=? WHERE id=?', [$conn->insert_id, $id], 'ii');
                }
            }
        }
    }
    if (!$err) { header('Location: users.php'); exit; }
}
require __DIR__ . '/includes/header.php';
$edit = isset($_GET['edit']) ? row('SELECT u.*, s.kelas FROM users u LEFT JOIN siswa s ON s.id=u.siswa_id WHERE u.id=?', [(int)$_GET['edit']], 'i') : null;
$data = rows('SELECT u.*, s.kelas FROM users u LEFT JOIN siswa s ON s.id=u.siswa_id ORDER BY FIELD(u.role,"admin","guru","siswa"), u.username');
?>
<?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif; ?>
<div class="two">
<div class="card tw"><table><thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Kelas</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data as $d): ?><tr><td><?= e($d['username']) ?></td><td><?= e($d['nama']) ?></td><td><span class="badge <?= $d['role']==='admin'?'on':'' ?>"><?= e($d['role']) ?></span></td><td><?= e($d['kelas'] ?? '-') ?></td>
<td class="row" style="margin:0"><a class="btn ghost sm" href="?edit=<?= $d['id'] ?>">Edit</a>
<?php if ($d['id'] != $_SESSION['user']['id']): ?><form method="post" data-confirm="Hapus user ini?"><input type="hidden" name="hapus" value="<?= $d['id'] ?>"><button class="btn ghost sm">Hapus</button></form><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<form class="card form" method="post" autocomplete="off"><h3><?= $edit ? 'Edit' : 'Tambah' ?> User</h3>
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  <label>Username</label><input name="username" value="<?= e($edit['username'] ?? '') ?>" required>
  <label>Nama</label><input name="nama" value="<?= e($edit['nama'] ?? '') ?>" required>
  <label>Password <?= $edit ? '(kosongkan jika tidak diubah)' : '' ?></label><input type="password" name="password" <?= $edit ? '' : 'required' ?>>
  <label>Role</label><select name="role"><?php foreach (['admin','guru','siswa'] as $r): ?><option <?= ($edit['role'] ?? 'siswa') === $r ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select>
  <label>Kelas (khusus role siswa)</label><input name="kelas" value="<?= e($edit['kelas'] ?? '') ?>" placeholder="contoh: XI RPL 2">
  <button class="btn">Simpan</button> <?php if ($edit): ?><a class="btn ghost" href="users.php">Batal</a><?php endif; ?>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
