<?php
require_once '../config/koneksi.php';
wajib_admin();

$halaman_aktif = 'perangkat';$judul_halaman = 'Data Perangkat Listrik';

if (isset($_POST['tambah'])) {
    $stmt =$pdo->prepare("INSERT INTO perangkat (nama_perangkat, lokasi, daya_watt, status_aktif) VALUES (?, ?, ?, ?)");
    $stmt->execute([trim($_POST['nama_perangkat']), trim($_POST['lokasi']), (int) $_POST['daya_watt'],$_POST['status_aktif']]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_POST['edit'])) {
    $stmt =$pdo->prepare("UPDATE perangkat SET nama_perangkat=?, lokasi=?, daya_watt=?, status_aktif=? WHERE id_perangkat=?");
    $stmt->execute([trim($_POST['nama_perangkat']), trim($_POST['lokasi']), (int)$_POST['daya_watt'], $_POST['status_aktif'], (int)$_POST['id_perangkat']]);
    header("Location: index.php?pesan=berhasil");
    exit;
}

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM perangkat WHERE id_perangkat = ?")->execute([(int) $_GET['hapus']]);
    header("Location: index.php?pesan=dihapus");
    exit;
}

$data =$pdo->query("SELECT * FROM perangkat ORDER BY lokasi ASC, nama_perangkat ASC")->fetchAll();
require_once '../includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4">
    Data perangkat berhasil diperbarui!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="bento-card">
      <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-success me-1"></i> Tambah Perangkat</h6>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Perangkat</label>
          <input type="text" name="nama_perangkat" class="form-control" placeholder="Contoh: AC Split 1 PK" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Lokasi / Ruangan</label>
          <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lab RPL 1" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Daya (Watt)</label>
          <input type="number" name="daya_watt" class="form-control" placeholder="750" min="0" required>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Status</label>
          <select name="status_aktif" class="form-select">
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Nonaktif</option>
          </select>
        </div>
        <button type="submit" name="tambah" class="btn btn-eco w-100">Simpan Perangkat</button>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="bento-card">
      <h6 class="fw-bold mb-3">Daftar Perangkat Terdaftar</h6>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Perangkat</th>
              <th>Lokasi</th>
              <th>Daya</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as$row): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($row['nama_perangkat']) ?></td>
                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                <td><?= number_format($row['daya_watt']) ?> W</td>
                <td>
                  <span class="badge <?= $row['status_aktif'] === 'Aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill">
                    <?= $row['status_aktif'] ?>
                  </span>
                </td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id_perangkat'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <a href="?hapus=<?= $row['id_perangkat'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Hapus perangkat ini?')">
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
<?php foreach ($data as$row): ?>
  <div class="modal fade" id="edit<?= $row['id_perangkat'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content rounded-4">
        <div class="modal-header">
          <h6 class="modal-title fw-bold">Edit Perangkat</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_perangkat" value="<?= $row['id_perangkat'] ?>">
          <div class="mb-3">
            <label class="form-label small">Nama Perangkat</label>
            <input type="text" name="nama_perangkat" class="form-control" value="<?= htmlspecialchars($row['nama_perangkat']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($row['lokasi']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Daya (Watt)</label>
            <input type="number" name="daya_watt" class="form-control" value="<?= $row['daya_watt'] ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small">Status</label>
            <select name="status_aktif" class="form-select">
              <option value="Aktif" <?= $row['status_aktif'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
              <option value="Nonaktif" <?= $row['status_aktif'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
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