<?php
require_once 'config/koneksi.php';
wajib_login();

$halaman_aktif = 'dashboard';
$judul_halaman = 'Dashboard Konservasi Energi';

// Ambil statistik utama
$total_perangkat = $pdo->query("SELECT COUNT(*) FROM perangkat")->fetchColumn();
$total_daya      = $pdo->query("SELECT COALESCE(SUM(daya_watt), 0) FROM perangkat WHERE status_aktif = 'Aktif'")->fetchColumn();
$total_checklist = $pdo->query("SELECT COUNT(*) FROM checklist")->fetchColumn();
$total_dimatikan = $pdo->query("SELECT COUNT(*) FROM detail_checklist WHERE kondisi = 'Sudah Dimatikan'")->fetchColumn();
$total_detail    = $pdo->query("SELECT COUNT(*) FROM detail_checklist")->fetchColumn();

$persen_hemat = $total_detail > 0 ? round(($total_dimatikan / $total_detail) * 100) : 100;

// Data Grafik 7 Tanggal Terakhir
$grafik_rows = $pdo->query("
    SELECT c.tanggal,
        SUM(CASE WHEN dc.kondisi = 'Sudah Dimatikan' THEN 1 ELSE 0 END) AS jml_mati,
        SUM(CASE WHEN dc.kondisi = 'Masih Menyala' THEN 1 ELSE 0 END) AS jml_nyala
    FROM checklist c
    LEFT JOIN detail_checklist dc ON c.id_checklist = dc.id_checklist
    GROUP BY c.tanggal
    ORDER BY c.tanggal DESC
    LIMIT 7
")->fetchAll();

// Balik urutan agar kronologis dari kiri ke kanan
$grafik_rows = array_reverse($grafik_rows);

$label_tgl = [];
$data_mati = [];
$data_nyala = [];

foreach ($grafik_rows as $g) {
    $label_tgl[]  = date('d M', strtotime($g['tanggal']));
    $data_mati[]  = (int) $g['jml_mati'];
    $data_nyala[] = (int) $g['jml_nyala'];
}

// Jika data checklist masih kosong, tampilkan contoh placeholder tanggal hari ini agar grafik tetap rapi
if (empty($label_tgl)) {
    $label_tgl  = [date('d M')];
    $data_mati  = [0];
    $data_nyala = [0];
}

// Riwayat checklist terbaru
$riwayat = $pdo->query("
    SELECT c.*, u.nama_lengkap 
    FROM checklist c 
    JOIN users u ON c.id_user = u.id_user 
    ORDER BY c.tanggal DESC, c.waktu_cek DESC 
    LIMIT 5
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="hero-eco mb-4">
  <div class="row align-items-center g-4">
    <div class="col-lg-8">
      <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-3 fw-bold">
        <i class="bi bi-leaf-fill me-1"></i> Gerakan Sekolah Hemat Energi
      </span>
      <h2 class="fw-extrabold mb-2">Halo, <?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?>!</h2>
      <p class="mb-3 opacity-75">Pantau penggunaan perangkat listrik ruangan dan isi checklist harian sebelum meninggalkan kelas atau laboratorium.</p>
      <a href="<?= BASE_URL ?>/checklist/tambah.php" class="btn btn-light text-success fw-bold rounded-3 px-4">
        <i class="bi bi-plus-circle-fill me-1"></i> Isi Checklist Sekarang
      </a>
    </div>
    <div class="col-lg-4 text-lg-end">
      <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.14);">
        <div class="small opacity-75">Tingkat Kepatuhan Matikan Listrik</div>
        <div class="display-5 fw-bold my-1"><?= $persen_hemat ?>%</div>
        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
          <div class="progress-bar bg-warning" style="width: <?= $persen_hemat ?>%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Kartu Statistik Bento -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="bento-card h-100">
      <div class="stat-badge mb-3"><i class="bi bi-plug-fill"></i></div>
      <div class="text-muted small">Total Perangkat Terdata</div>
      <div class="fs-3 fw-bold"><?= $total_perangkat ?> <span class="fs-6 fw-normal text-muted">Unit</span></div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="bento-card h-100">
      <div class="stat-badge mb-3"><i class="bi bi-lightning-fill"></i></div>
      <div class="text-muted small">Beban Daya Aktif</div>
      <div class="fs-3 fw-bold"><?= number_format($total_daya) ?> <span class="fs-6 fw-normal text-muted">Watt</span></div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="bento-card h-100">
      <div class="stat-badge mb-3"><i class="bi bi-clipboard2-check-fill"></i></div>
      <div class="text-muted small">Laporan Checklist</div>
      <div class="fs-3 fw-bold"><?= $total_checklist ?> <span class="fs-6 fw-normal text-muted">Kali</span></div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="bento-card h-100">
      <div class="stat-badge mb-3"><i class="bi bi-power"></i></div>
      <div class="text-muted small">Perangkat Sudah Dimatikan</div>
      <div class="fs-3 fw-bold"><?= $total_dimatikan ?> <span class="fs-6 fw-normal text-muted">Item</span></div>
    </div>
  </div>
</div>

<!-- Grafik Tren Kepatuhan & Tabel Riwayat -->
<div class="row g-4">
  <div class="col-lg-5">
    <div class="bento-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Tren Kepatuhan Energi</h6>
          <small class="text-muted">7 hari pengecekan terakhir</small>
        </div>
      </div>
      <div style="position: relative; height: 240px;">
        <canvas id="grafikEnergi"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="bento-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-success"></i>Aktivitas Checklist Terbaru</h6>
        <a href="<?= BASE_URL ?>/checklist/index.php" class="btn btn-sm btn-outline-success rounded-3">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Tanggal & Waktu</th>
              <th>Ruangan</th>
              <th>Petugas Cek</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($riwayat)): ?>
              <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data checklist energi.</td></tr>
            <?php else: ?>
              <?php foreach ($riwayat as $row): ?>
                <tr>
                  <td><?= date('d M Y', strtotime($row['tanggal'])) ?> • <span class="text-muted"><?= substr($row['waktu_cek'], 0, 5) ?></span></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['ruangan']) ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  const ctxEnergi = document.getElementById('grafikEnergi');
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const warnaTeks = isDark ? '#8fb3a5' : '#5c756c';
  const warnaGrid = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';

  const chartEnergi = new Chart(ctxEnergi, {
    type: 'bar',
    data: {
      labels: <?= json_encode($label_tgl) ?>,
      datasets: [
        {
          label: 'Sudah Dimatikan',
          data: <?= json_encode($data_mati) ?>,
          backgroundColor: '#10b981',
          borderRadius: 6
        },
        {
          label: 'Masih Menyala',
          data: <?= json_encode($data_nyala) ?>,
          backgroundColor: '#f59e0b',
          borderRadius: 6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: warnaTeks, boxWidth: 12, usePointStyle: true }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: warnaTeks }
        },
        y: {    
          beginAtZero: true,
          ticks: { stepSize: 1, color: warnaTeks },
          grid: { color: warnaGrid }
        }
      }
    }
  });
</script>

<?php require_once 'includes/footer.php'; ?>