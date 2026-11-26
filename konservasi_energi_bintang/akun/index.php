<?php
require_once '../config/koneksi.php';
wajib_login();

$halaman_aktif = 'akun';
$judul_halaman = 'Profil & Keamanan Akun';
$id_user = $_SESSION['user']['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("UPDATE users SET nama_lengkap=?, username=?, password=? WHERE id_user=?");
    $stmt->execute([$nama, $username, $password, $id_user]);

    $_SESSION['user']['nama_lengkap'] = $nama;
    $_SESSION['user']['username']     = $username;
    $_SESSION['user']['password']     = $password;

    header("Location: index.php?pesan=sukses");
    exit;
}

$profil = $_SESSION['user'];
require_once '../includes/header.php';
?>

<div class="bento-card" style="max-width: 560px; margin: 0 auto;">
  <?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success rounded-4">
      <i class="bi bi-check-circle-fill me-1"></i> Profil dan password berhasil diperbarui!
    </div>
  <?php endif; ?>

  <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-circle bg-success text-white fw-bold fs-4" style="width:58px;height:58px;display:grid;place-items:center;">
        <?= strtoupper(substr($profil['nama_lengkap'], 0, 1)) ?>
      </div>
      <div>
        <h5 class="fw-bold mb-0"><?= htmlspecialchars($profil['nama_lengkap']) ?></h5>
        <span class="badge bg-success-subtle text-success text-uppercase"><?= htmlspecialchars($profil['role']) ?></span>
        <?php if (!empty($profil['kelas'])): ?>
          <span class="badge bg-secondary-subtle text-secondary ms-1"><?= htmlspecialchars($profil['kelas']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Yakin ingin keluar dari akun?')">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
  </div>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label small fw-semibold">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($profil['nama_lengkap']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label small fw-semibold">Username Login</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($profil['username']) ?>" required>
    </div>
    <div class="mb-4">
      <label class="form-label small fw-semibold">Password Baru</label>
      <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($profil['password']) ?>" required>
    </div>
    <button type="submit" class="btn btn-eco w-100">Simpan Perubahan Profil</button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>