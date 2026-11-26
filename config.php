<?php
session_start();
ob_start(); // agar redirect header() tetap jalan setelah header.php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli('localhost', 'root', '', 'konservasienergi_eamcik');
if ($conn->connect_error) {
    die('Koneksi gagal. Import file konservasi_energi_eamcik.sql lewat phpMyAdmin dulu. (' . $conn->connect_error . ')');
}
$conn->set_charset('utf8mb4');

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function q($sql, $params = [], $types = '') {
    global $conn;
    $st = $conn->prepare($sql);
    if (!$st) die('SQL error: ' . $conn->error);
    if ($params) {
        if (!$types) $types = str_repeat('s', count($params));
        $st->bind_param($types, ...$params);
    }
    $st->execute();
    $r = $st->get_result();
    return $r ?: $st;
}
function rows($sql, $p = [], $t = '') {
    $r = q($sql, $p, $t);
    return $r instanceof mysqli_result ? $r->fetch_all(MYSQLI_ASSOC) : [];
}
function row($sql, $p = [], $t = '') { $r = rows($sql, $p, $t); return $r ? $r[0] : null; }

function vbadge($s) {
    $c = $s === 'Disetujui' ? 'on' : ($s === 'Perlu Perbaikan' ? 'dash' : '');
    return '<span class="badge ' . $c . '">' . e($s) . '</span>';
}

// Akun awal (dibuat otomatis jika tabel users kosong):
// admin eamm/eamm123, guru guru/guru123, siswa siswa/siswa123
$cek = row('SELECT COUNT(*) c FROM users');
if ($cek && (int)$cek['c'] === 0) {
    $s1 = row('SELECT id FROM siswa ORDER BY id LIMIT 1');
    q('INSERT INTO users (username,password,nama,role) VALUES (?,?,?,?)', ['eamm', password_hash('eamm123', PASSWORD_DEFAULT), 'Administrator EAMM', 'admin']);
    q('INSERT INTO users (username,password,nama,role) VALUES (?,?,?,?)', ['guru', password_hash('guru123', PASSWORD_DEFAULT), 'Guru Pemantau', 'guru']);
    q('INSERT INTO users (username,password,nama,role,siswa_id) VALUES (?,?,?,?,?)', ['siswa', password_hash('siswa123', PASSWORD_DEFAULT), 'Contoh Siswa 1', 'siswa', $s1['id'] ?? null], 'ssssi');
}
