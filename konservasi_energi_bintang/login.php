<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        $_SESSION['user'] = $user;
        header("Location: " . BASE_URL . "/index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Konservasi Energi Bintang</title>
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
        <i class="bi bi-lightning-charge-fill"></i>
      </div>
      <h4 class="fw-bold mb-1">EcoSchool Energy</h4>
      <p class="text-muted small mb-0">Masuk dengan username & password akun Anda</p>
    </div>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'terdaftar'): ?>
      <div class="alert alert-success py-2 small rounded-3">
        <i class="bi bi-check-circle-fill me-1"></i> Pendaftaran berhasil! Silakan login dengan akun baru Anda.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'reset_sukses'): ?>
      <div class="alert alert-success py-2 small rounded-3">
        <i class="bi bi-check-circle-fill me-1"></i> Password berhasil diperbarui! Silakan login dengan password baru.
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small rounded-3">
        <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label small fw-semibold">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Contoh: Aris" required autofocus>
      </div>
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label class="form-label small fw-semibold mb-0">Password</label>
          <a href="<?= BASE_URL ?>/lupa_akun.php" class="small text-success text-decoration-none">Lupa akun?</a>
        </div>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-eco w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
      </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
      <small class="text-muted">Belum punya akun? <a href="<?= BASE_URL ?>/daftar.php" class="text-success fw-bold text-decoration-none">Daftar di sini</a></small>
    </div>
  </div>
</div>

</body>
</html>