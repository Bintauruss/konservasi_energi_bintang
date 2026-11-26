<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$error = '';
$akun_ditemukan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tahap 1: Cari & Verifikasi Akun berdasarkan Nama Lengkap + Username
    if (isset($_POST['cek_akun'])) {
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $username     = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(nama_lengkap) = LOWER(?) AND username = ?");
        $stmt->execute([$nama_lengkap, $username]);
        $user = $stmt->fetch();

        if ($user) {
            $akun_ditemukan = $user;
        } else {
            $error = "Data tidak cocok! Pastikan Nama Lengkap dan Username sesuai dengan yang terdaftar.";
        }
    }

    // Tahap 2: Simpan Password Baru
    if (isset($_POST['simpan_password'])) {
        $id_user       = (int) ($_POST['id_user'] ?? 0);
        $password_baru = trim($_POST['password_baru'] ?? '');

        if ($id_user > 0 && $password_baru !== '') {
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
            $upd->execute([$password_baru, $id_user]);

            header("Location: " . BASE_URL . "/login.php?pesan=reset_sukses");
            exit;
        } else {
            $error = "Password baru tidak boleh kosong!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Akun — Konservasi Energi Bintang</title>
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
        <i class="bi bi-key-fill"></i>
      </div>
      <h4 class="fw-bold mb-1">Pemulihan Akun</h4>
      <p class="text-muted small mb-0">
        <?= $akun_ditemukan ? 'Akun terverifikasi! Silakan buat password baru.' : 'Masukkan Nama Lengkap & Username untuk reset password' ?>
      </p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small rounded-3">
        <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!$akun_ditemukan): ?>
      <!-- Form Verifikasi Identitas -->
      <form method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Lengkap Terdaftar</label>
          <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: R.P. Bintang" required autofocus>
        </div>
        <div class="mb-4">
          <label class="form-label small fw-semibold">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Masukkan username akun" required>
        </div>
        <button type="submit" name="cek_akun" class="btn btn-eco w-100 py-2">
          <i class="bi bi-search me-1"></i> Verifikasi Akun
        </button>
      </form>
    <?php else: ?>
      <!-- Form Buat Password Baru -->
      <div class="alert alert-success py-2 small rounded-3 mb-3">
        <i class="bi bi-person-check-fill me-1"></i> Akun ditemukan: <b><?= htmlspecialchars($akun_ditemukan['nama_lengkap']) ?></b> (<code><?= htmlspecialchars($akun_ditemukan['username']) ?></code>)
      </div>
      <form method="POST">
        <input type="hidden" name="id_user" value="<?= $akun_ditemukan['id_user'] ?>">
        <div class="mb-4">
          <label class="form-label small fw-semibold">Password Baru</label>
          <input type="text" name="password_baru" class="form-control" placeholder="Masukkan password baru" required autofocus>
        </div>
        <button type="submit" name="simpan_password" class="btn btn-eco w-100 py-2">
          <i class="bi bi-check2-circle me-1"></i> Simpan Password Baru
        </button>
      </form>
    <?php endif; ?>

    <div class="text-center mt-4 pt-3 border-top">
      <small class="text-muted">Ingat password Anda? <a href="<?= BASE_URL ?>/login.php" class="text-success fw-bold text-decoration-none">Kembali ke Login</a></small>
    </div>
  </div>
</div>

</body>
</html>