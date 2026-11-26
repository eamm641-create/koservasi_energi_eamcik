<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Keluar · EAM</title><link rel="stylesheet" href="assets/css/style.css">
<meta http-equiv="refresh" content="3;url=login.php?bye=1"></head>
<body class="auth">
<div class="bye"><div>
  <svg viewBox="0 0 64 64" width="84" height="84" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:1;animation:pulse 1.6s infinite"><path d="M22 44c0-6-8-9-8-19a18 18 0 0 1 36 0c0 10-8 13-8 19z"/><path d="M24 52h16M27 58h10"/></svg>
  <h1>Sampai jumpa!</h1>
  <p>Anda sedang keluar. Jangan lupa hemat energi.</p>
  <div class="bar"><span></span></div>
</div></div>
</body></html>
