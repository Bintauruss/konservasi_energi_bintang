<?php
require_once '../config/koneksi.php';
wajib_login();

// Query dasar ambil checklist beserta role petugas
$sql_dasar = "
    SELECT c.*, u.nama_lengkap, u.role, u.kelas,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Sudah Dimatikan') AS jml_mati,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Masih Menyala') AS jml_nyala
    FROM checklist c
    JOIN users u ON c.id_user = u.id_user
";

$daftar_siswa = $pdo->query($sql_dasar . " WHERE u.role = 'siswa' ORDER BY c.tanggal DESC, c.waktu_cek DESC")->fetchAll();
$daftar_guru  = $pdo->query($sql_dasar . " WHERE u.role IN ('guru', 'admin') ORDER BY c.tanggal DESC, c.waktu_cek DESC")->fetchAll();

// 1. MODE EXPORT EXCEL (?aksi=excel&kategori=siswa|guru|semua)
if (isset($_GET['aksi']) && $_GET['aksi'] === 'excel') {
    $kat = $_GET['kategori'] ?? 'semua';
    if ($kat === 'siswa') {
        $data_export = $daftar_siswa;
        $judul_file  = "Rekap_Checklist_Siswa_";
        $judul_lap   = "REKAP LAPORAN CHECKLIST ENERGI — SISWA";
    } elseif ($kat === 'guru') {
        $data_export = $daftar_guru;
        $judul_file  = "Rekap_Checklist_Guru_";
        $judul_lap   = "REKAP LAPORAN CHECKLIST ENERGI — GURU";
    } else {
        $data_export = array_merge($daftar_guru, $daftar_siswa);
        $judul_file  = "Rekap_Checklist_Semua_";
        $judul_lap   = "REKAP LAPORAN CHECKLIST KONSERVASI ENERGI (SEMUA)";
    }

    $filename = $judul_file . date('Y-m-d') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    ?>
    <table border="1">
      <thead>
        <tr>
          <th colspan="8" style="font-size:14pt; font-weight:bold;"><?= $judul_lap ?></th>
        </tr>
        <tr style="background-color:#d1fae5; font-weight:bold;">
          <th>No</th>
          <th>Tanggal</th>
          <th>Waktu (WIB)</th>
          <th>Ruangan</th>
          <th>Petugas</th>
          <th>Status</th>
          <th>Sudah Dimatikan</th>
          <th>Masih Menyala</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($data_export as $row): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td><?= substr($row['waktu_cek'], 0, 5) ?></td>
            <td><?= htmlspecialchars($row['ruangan']) ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= strtoupper($row['role']) ?></td>
            <td><?= $row['jml_mati'] ?> Unit</td>
            <td><?= $row['jml_nyala'] ?> Unit</td>
            <td><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php
    exit;
}

// 2. MODE CETAK PDF (?aksi=pdf&kategori=siswa|guru|semua)
if (isset($_GET['aksi']) && $_GET['aksi'] === 'pdf') {
    $kat = $_GET['kategori'] ?? 'semua';
    if ($kat === 'siswa') {
        $data_pdf  = $daftar_siswa;
        $judul_pdf = "LAPORAN CHECKLIST KONSERVASI ENERGI — SISWA";
    } elseif ($kat === 'guru') {
        $data_pdf  = $daftar_guru;
        $judul_pdf = "LAPORAN CHECKLIST KONSERVASI ENERGI — GURU";
    } else {
        $data_pdf  = array_merge($daftar_guru, $daftar_siswa);
        $judul_pdf = "LAPORAN REKAP CHECKLIST KONSERVASI ENERGI";
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <title><?= $judul_pdf ?></title>
      <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 25px; }
        .kop { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; color: #065f46; }
        .kop p { margin: 4px 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #ecfdf5; color: #065f46; }
        .ttd { margin-top: 40px; text-align: right; }
        @media print { .no-print { display: none; } }
      </style>
    </head>
    <body onload="window.print()">
      <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #10b981; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Simpan / Cetak PDF</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
      </div>
      <div class="kop">
        <h2><?= $judul_pdf ?></h2>
        <p>EcoSchool Energy • Dicetak pada <?= date('d M Y, H:i') ?> WIB</p>
      </div>
      <table>
        <thead>
          <tr>
            <th style="width: 35px;">No</th>
            <th>Tanggal & Jam</th>
            <th>Ruangan</th>
            <th>Petugas</th>
            <th>Status Perangkat</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($data_pdf as $row): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= date('d M Y', strtotime($row['tanggal'])) ?> (<?= substr($row['waktu_cek'], 0, 5) ?>)</td>
              <td><?= htmlspecialchars($row['ruangan']) ?></td>
              <td><?= htmlspecialchars($row['nama_lengkap']) ?> (<?= strtoupper($row['role']) ?>)</td>
              <td><?= $row['jml_mati'] ?> Dimatikan, <?= $row['jml_nyala'] ?> Menyala</td>
              <td><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="ttd">
        <p>Mengetahui,<br>Penanggung Jawab Konservasi Energi</p>
        <br><br><br>
        <p><b>( _______________________ )</b></p>
      </div>
    </body>
    </html>
    <?php
    exit;
}

$halaman_aktif = 'checklist';
$judul_halaman = 'Checklist Konservasi Energi';

// Hapus checklist (khusus admin)
if (isset($_GET['hapus']) && $_SESSION['user']['role'] === 'admin') {
    $id = (int) $_GET['hapus'];
    $pdo->prepare("DELETE FROM detail_checklist WHERE id_checklist = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM checklist WHERE id_checklist = ?")->execute([$id]);
    header("Location: index.php?pesan=dihapus");
    exit;
}

require_once '../includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?= $_GET['pesan'] === 'tersimpan' ? 'Data checklist energi berhasil disimpan!' : 'Data checklist berhasil dihapus.' ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Header Aksi Utama -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h5 class="fw-bold mb-0">Riwayat Pengecekan Energi</h5>
    <p class="text-muted small mb-0">Dipisahkan berdasarkan pengecekan oleh Siswa dan Guru</p>
  </div>
  <a href="tambah.php" class="btn btn-eco">
    <i class="bi bi-plus-lg me-1"></i> Isi Checklist Baru
  </a>
</div>

<!-- 1. KARTU RIWAYAT PENGECEKAN GURU -->
<div class="bento-card mb-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h6 class="fw-bold mb-1">
        <i class="bi bi-person-badge-fill text-primary me-1"></i> Riwayat Pengecekan Guru
      </h6>
      <p class="text-muted small mb-0">Laporan monitoring perangkat listrik oleh Guru / Pembina</p>
    </div>
    <div class="d-flex gap-2">
      <a href="?aksi=excel&kategori=guru" class="btn btn-sm btn-outline-success rounded-3">
        <i class="bi bi-file-earmark-excel-fill me-1"></i> Export
      </a>
      <a href="?aksi=pdf&kategori=guru" target="_blank" class="btn btn-sm btn-outline-danger rounded-3">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Tanggal & Jam</th>
          <th>Ruangan</th>
          <th>Nama Guru</th>
          <th>Status Perangkat</th>
          <th>Catatan</th>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <th class="text-end">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($daftar_guru)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pengecekan dari Guru.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($daftar_guru as $row): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                <small class="text-muted"><?= substr($row['waktu_cek'], 0, 5) ?> WIB</small>
              </td>
              <td class="fw-bold"><?= htmlspecialchars($row['ruangan']) ?></td>
              <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
              <td>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                  <i class="bi bi-power me-1"></i><?= $row['jml_mati'] ?> Dimatikan
                </span>
                <?php if ($row['jml_nyala'] > 0): ?>
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 ms-1">
                    <i class="bi bi-lightning-fill me-1"></i><?= $row['jml_nyala'] ?> Menyala
                  </span>
                <?php endif; ?>
              </td>
              <td class="text-muted small"><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
              <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <td class="text-end">
                  <a href="?hapus=<?= $row['id_checklist'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Hapus laporan checklist ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 2. KARTU RIWAYAT PENGECEKAN SISWA -->
<div class="bento-card">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h6 class="fw-bold mb-1">
        <i class="bi bi-mortarboard-fill text-success me-1"></i> Riwayat Pengecekan Siswa
      </h6>
      <p class="text-muted small mb-0">Laporan pengecekan perangkat listrik oleh perwakilan kelas / siswa</p>
    </div>
    <div class="d-flex gap-2">
      <a href="?aksi=excel&kategori=siswa" class="btn btn-sm btn-outline-success rounded-3">
        <i class="bi bi-file-earmark-excel-fill me-1"></i> Export
      </a>
      <a href="?aksi=pdf&kategori=siswa" target="_blank" class="btn btn-sm btn-outline-danger rounded-3">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Tanggal & Jam</th>
          <th>Ruangan</th>
          <th>Nama Siswa</th>
          <th>Status Perangkat</th>
          <th>Catatan</th>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <th class="text-end">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($daftar_siswa)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pengecekan dari Siswa.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($daftar_siswa as $row): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                <small class="text-muted"><?= substr($row['waktu_cek'], 0, 5) ?> WIB</small>
              </td>
              <td class="fw-bold"><?= htmlspecialchars($row['ruangan']) ?></td>
              <td>
                <?= htmlspecialchars($row['nama_lengkap']) ?>
                <?php if (!empty($row['kelas'])): ?>
                  <small class="text-muted d-block"><?= htmlspecialchars($row['kelas']) ?></small>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                  <i class="bi bi-power me-1"></i><?= $row['jml_mati'] ?> Dimatikan
                </span>
                <?php if ($row['jml_nyala'] > 0): ?>
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 ms-1">
                    <i class="bi bi-lightning-fill me-1"></i><?= $row['jml_nyala'] ?> Menyala
                  </span>
                <?php endif; ?>
              </td>
              <td class="text-muted small"><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
              <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <td class="text-end">
                  <a href="?hapus=<?= $row['id_checklist'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Hapus laporan checklist ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>