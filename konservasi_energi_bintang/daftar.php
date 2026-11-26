<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $kelas        = trim($_POST['kelas'] ?? '');

    // Cek apakah username sudah dipakai
    $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $cek->execute([$username]);

    if ($cek->fetchColumn() > 0) {
        $error = "Username sudah digunakan, silakan pilih username lain!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, username, password, role, kelas) VALUES (?, ?, ?, 'siswa', ?)");
        $stmt->execute([$nama_lengkap, $username, $password, $kelas]);

        header("Location: " . BASE_URL . "/login.php?pesan=terdaftar");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun — Konservasi Energi Bintang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/style.css" rel="stylesheet">
  <script>
    const tema = localStorage.getItem('tema_eco') || 'light';
    document.documentElement.setAttribute('data-bs-theme', tema);
  </script>
</head>
<body>

<div class="login-wrapper">
  <div class="bento-card login-box p-4 p-md-5">
    <div class="text-center mb-4">
      <div class="brand-icon mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.6rem;">
        <i class="bi bi-person-plus-fill"></i>
      </div>
      <h4 class="fw-bold mb-1">Buat Akun Baru</h4>
      <p class="text-muted small mb-0">Daftar untuk mulai mengisi checklist energi</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small rounded-3">
        <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label small fw-semibold">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: R.P. Bintang" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Kelas</label>
        <input type="text" name="kelas" class="form-control" placeholder="Contoh: X RPL 2" required>
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Buat username unik" required>
      </div>
      <div class="mb-4">
        <label class="form-label small fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Buat password" required>
      </div>
      <button type="submit" class="btn btn-eco w-100 py-2">
        <i class="bi bi-check2-circle me-1"></i> Daftar Sekarang
      </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
      <small class="text-muted">Sudah punya akun? <a href="<?= BASE_URL ?>/login.php" class="text-success fw-bold text-decoration-none">Masuk di sini</a></small>
    </div>
  </div>
</div>

</body>
</html>