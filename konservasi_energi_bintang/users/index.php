<?php
require_once '../config/koneksi.php';
wajib_admin();

$halaman_aktif = 'users';
$judul_halaman = 'Kelola Akun Pengguna';

if (isset($_POST['tambah'])) {
    $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, username, password, role, kelas) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([trim($_POST['nama_lengkap']), trim($_POST['username']), trim($_POST['password']), $_POST['role'], trim($_POST['kelas'])]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE users SET nama_lengkap=?, username=?, password=?, role=?, kelas=? WHERE id_user=?");
    $stmt->execute([trim($_POST['nama_lengkap']), trim($_POST['username']), trim($_POST['password']), $_POST['role'], trim($_POST['kelas']), (int) $_POST['id_user']]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    if ($id !== (int) $_SESSION['user']['id_user']) {
        $pdo->prepare("DELETE FROM users WHERE id_user = ?")->execute([$id]);
    }
    header("Location: index.php?pesan=dihapus");
    exit;
}

$data = $pdo->query("SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC")->fetchAll();
require_once '../includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4">
    Data akun berhasil diperbarui!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="bento-card">
      <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock-fill text-success me-1"></i> Buat Akun Baru</h6>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Username Login</label>
          <input type="text" name="username" class="form-control" placeholder="Bebas (bukan NIS juga bisa)" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Password</label>
          <input type="text" name="password" class="form-control" value="12345" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Role</label>
          <select name="role" class="form-select">
            <option value="siswa">Siswa</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Kelas (Opsional)</label>
          <input type="text" name="kelas" class="form-control" placeholder="Contoh: XI RPL 2">
        </div>
        <button type="submit" name="tambah" class="btn btn-eco w-100">Buat Akun</button>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="bento-card">
      <h6 class="fw-bold mb-3">Daftar Akun Terdaftar</h6>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Kelas</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $row): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                <td>
                  <span class="badge <?= $row['role'] === 'admin' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' ?> rounded-pill text-uppercase">
                    <?= $row['role'] ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($row['kelas'] ?: '-') ?></td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#editUser<?= $row['id_user'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <?php if ($row['id_user'] !== $_SESSION['user']['id_user']): ?>
                    <a href="?hapus=<?= $row['id_user'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Hapus akun ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit diletakkan di luar tabel -->
<?php foreach ($data as $row): ?>
  <div class="modal fade" id="editUser<?= $row['id_user'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content rounded-4">
        <div class="modal-header">
          <h6 class="modal-title fw-bold">Edit Akun</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
          <div class="mb-3">
            <label class="form-label small">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Password</label>
            <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($row['password']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Role</label>
            <select name="role" class="form-select">
              <option value="siswa" <?= $row['role'] === 'siswa' ? 'selected' : '' ?>>Siswa</option>
              <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small">Kelas</label>
            <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($row['kelas'] ?? '') ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="edit" class="btn btn-eco">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>