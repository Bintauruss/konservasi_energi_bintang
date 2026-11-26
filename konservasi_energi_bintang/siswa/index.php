<?php
require_once '../config/koneksi.php';
wajib_admin();

$halaman_aktif = 'siswa';
$judul_halaman = 'Data Siswa';

if (isset($_POST['tambah'])) {
    $stmt = $pdo->prepare("INSERT INTO siswa (nis, nama, kelas) VALUES (?, ?, ?)");
    $stmt->execute([trim($_POST['nis']), trim($_POST['nama']), trim($_POST['kelas'])]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE siswa SET nis=?, nama=?, kelas=? WHERE id_siswa=?");
    $stmt->execute([trim($_POST['nis']), trim($_POST['nama']), trim($_POST['kelas']), (int) $_POST['id_siswa']]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?")->execute([(int) $_GET['hapus']]);
    header("Location: index.php?pesan=dihapus");
    exit;
}

$data = $pdo->query("SELECT * FROM siswa ORDER BY kelas ASC, nama ASC")->fetchAll();
require_once '../includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4">
    Data siswa berhasil diperbarui!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="bento-card">
      <h6 class="fw-bold mb-3"><i class="bi bi-person-plus-fill text-success me-1"></i> Tambah Siswa</h6>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold">NIS</label>
          <input type="text" name="nis" class="form-control" placeholder="Contoh: 1004" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" placeholder="Nama siswa" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Kelas</label>
          <input type="text" name="kelas" class="form-control" placeholder="Contoh: XI RPL 2" required>
        </div>
        <button type="submit" name="tambah" class="btn btn-eco w-100">Simpan Siswa</button>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="bento-card">
      <h6 class="fw-bold mb-3">Daftar Siswa</h6>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>NIS</th>
              <th>Nama Siswa</th>
              <th>Kelas</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $row): ?>
              <tr>
                <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($row['nis']) ?></span></td>
                <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['kelas']) ?></td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#editSiswa<?= $row['id_siswa'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <a href="?hapus=<?= $row['id_siswa'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Hapus data siswa ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
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
  <div class="modal fade" id="editSiswa<?= $row['id_siswa'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content rounded-4">
        <div class="modal-header">
          <h6 class="modal-title fw-bold">Edit Data Siswa</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_siswa" value="<?= $row['id_siswa'] ?>">
          <div class="mb-3">
            <label class="form-label small">NIS</label>
            <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($row['nis']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Kelas</label>
            <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($row['kelas']) ?>" required>
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