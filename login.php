<?php
require_once __DIR__ . '/config.php';
if (!empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = row('SELECT * FROM users WHERE username=?', [trim($_POST['username'] ?? '')]);
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'nama'=>$u['nama'],'role'=>$u['role'],'siswa_id'=>$u['siswa_id']];
        $_SESSION['just_login'] = 1;
        header('Location: index.php'); exit;
    }
    $err = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk · EAM Konservasi Energi</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth">
<form class="box" method="post" autocomplete="off">
  <svg class="bulb" viewBox="0 0 64 64" width="64" height="64" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 44c0-6-8-9-8-19a18 18 0 0 1 36 0c0 10-8 13-8 19z"/><path d="M24 52h16M27 58h10"/></svg>
  <h1>EAM Konservasi Energi</h1>
  <p class="sub"><?= isset($_GET['bye']) ? 'Anda sudah keluar. Sampai jumpa!' : 'Silakan masuk untuk melanjutkan' ?></p>
  <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
  <label>Username</label><input name="username" required autofocus>
  <label>Password</label><input name="password" type="password" required>
  <button class="btn">Masuk</button>
</form>
</body></html>
