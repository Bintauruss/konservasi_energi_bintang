<?php
require_once '../config/koneksi.php';
wajib_login();

$halaman_aktif = 'checklist';
$judul_halaman = 'Isi Checklist Energi';

// Ambil daftar perangkat aktif
$perangkat_list = $pdo->query("SELECT * FROM perangkat WHERE status_aktif = 'Aktif' ORDER BY lokasi ASC, nama_perangkat ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user   = $_SESSION['user']['id_user'];
    $tanggal   = $_POST['tanggal'];
    $waktu_cek = $_POST['waktu_cek'];
    $ruangan   = trim($_POST['ruangan']);
    $catatan   = trim($_POST['catatan']);
    $kondisi   = $_POST['kondisi'] ?? [];

    $stmt = $pdo->prepare("INSERT INTO checklist (id_user, tanggal, waktu_cek, ruangan, catatan) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_user, $tanggal, $waktu_cek, $ruangan, $catatan]);
    $id_checklist = $pdo->lastInsertId();

    $stmt_detail = $pdo->prepare("INSERT INTO detail_checklist (id_checklist, id_perangkat, kondisi) VALUES (?, ?, ?)");
    foreach ($kondisi as $id_perangkat => $status_kondisi) {
        $stmt_detail->execute([$id_checklist, (int) $id_perangkat, $status_kondisi]);
    }

    header("Location: index.php?pesan=tersimpan");
    exit;
}

require_once '../includes/header.php';
?>

<div class="bento-card" style="max-width: 820px; margin: 0 auto;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h5 class="fw-bold mb-1">Form Checklist Hemat Energi</h5>
      <p class="text-muted small mb-0">Pastikan perangkat listrik yang tidak terpakai sudah dimatikan</p>
    </div>
    <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <form method="POST">
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tanggal Cek</label>
        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Waktu Cek</label>
        <input type="time" name="waktu_cek" class="form-control" value="<?= date('H:i') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Nama Ruangan</label>
        <input type="text" name="ruangan" class="form-control" placeholder="Contoh: Lab RPL 1" required>
      </div>
    </div>

    <h6 class="fw-bold mb-3"><i class="bi bi-plug-fill text-success me-1"></i> Kondisi Perangkat Listrik</h6>
    <div class="table-responsive mb-4">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Perangkat</th>
            <th>Lokasi / Daya</th>
            <th style="width: 240px;">Status Saat Dicek</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($perangkat_list as $p): ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($p['nama_perangkat']) ?></td>
              <td class="small text-muted"><?= htmlspecialchars($p['lokasi']) ?> • <?= $p['daya_watt'] ?>W</td>
              <td>
                <select name="kondisi[<?= $p['id_perangkat'] ?>]" class="form-select form-select-sm">
                  <option value="Sudah Dimatikan" selected>✅ Sudah Dimatikan</option>
                  <option value="Masih Menyala">⚡ Masih Menyala</option>
                </select>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mb-4">
      <label class="form-label small fw-semibold">Catatan Tambahan (Opsional)</label>
      <textarea name="catatan" class="form-control" rows="2" placeholder="Misal: AC Lab RPL 1 masih dipakai jam tambahan..."></textarea>
    </div>

    <button type="submit" class="btn btn-eco px-4">
      <i class="bi bi-check2-circle me-1"></i> Simpan Laporan Checklist
    </button>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>