<?php
$halaman_aktif = $halaman_aktif ?? '';
$judul_halaman = $judul_halaman ?? 'Dashboard';
$user_login = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($judul_halaman) ?> — Konservasi Energi Bintang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/style.css" rel="stylesheet">
  <script>
    const temaTersimpan = localStorage.getItem('tema_eco') || 'light';
    document.documentElement.setAttribute('data-bs-theme', temaTersimpan);
  </script>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <a href="<?= BASE_URL ?>/index.php" class="brand-box">
      <div class="brand-icon"><i class="bi bi-lightning-charge-fill"></i></div>
      <div>
        <div class="fw-bold lh-1">EcoSchool</div>
        <small class="text-white-50" style="font-size: 0.72rem;">Konservasi Energi</small>
      </div>
    </a>

    <div class="nav-label">Menu Utama</div>
    <a href="<?= BASE_URL ?>/index.php" class="nav-item-link <?= $halaman_aktif === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>
    <a href="<?= BASE_URL ?>/checklist/index.php" class="nav-item-link <?= $halaman_aktif === 'checklist' ? 'active' : '' ?>">
      <i class="bi bi-check2-square"></i> Checklist Energi
    </a>

    <?php if ($user_login['role'] === 'admin'): ?>
      <div class="nav-label">Master Data</div>
      <a href="<?= BASE_URL ?>/perangkat/index.php" class="nav-item-link <?= $halaman_aktif === 'perangkat' ? 'active' : '' ?>">
        <i class="bi bi-plug-fill"></i> Data Perangkat
      </a>
      <a href="<?= BASE_URL ?>/siswa/index.php" class="nav-item-link <?= $halaman_aktif === 'siswa' ? 'active' : '' ?>">
        <i class="bi bi-mortarboard-fill"></i> Data Siswa
      </a>
      <a href="<?= BASE_URL ?>/users/index.php" class="nav-item-link <?= $halaman_aktif === 'users' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Kelola Akun
      </a>
    <?php endif; ?>

    <div class="nav-label">Pengaturan</div>
    <a href="<?= BASE_URL ?>/akun/index.php" class="nav-item-link <?= $halaman_aktif === 'akun' ? 'active' : '' ?>">
      <i class="bi bi-person-gear"></i> Profil Saya
    </a>
    <a href="<?= BASE_URL ?>/logout.php" class="nav-item-link text-warning mt-auto" onclick="return confirm('Yakin ingin keluar?')">
      <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
  </aside>

  <!-- Area Utama -->
  <div class="main-area">
    <header class="topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary btn-sm rounded-3" id="btnToggleSidebar" type="button">
          <i class="bi bi-list fs-5"></i>
        </button>
        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($judul_halaman) ?></h5>
      </div>

      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary btn-sm rounded-3" id="btnTema" type="button" title="Ganti Tema">
          <i class="bi bi-moon-stars-fill" id="ikonTema"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
          <div class="text-end d-none d-sm-block">
            <div class="fw-bold small lh-1"><?= htmlspecialchars($user_login['nama_lengkap']) ?></div>
            <small class="text-muted text-uppercase" style="font-size: 0.7rem;"><?= htmlspecialchars($user_login['role']) ?></small>
          </div>
          <div class="rounded-circle bg-success text-white d-grid place-items-center fw-bold" style="width:38px;height:38px;display:grid;place-items:center;">
            <?= strtoupper(substr($user_login['nama_lengkap'], 0, 1)) ?>
          </div>
        </div>
      </div>
    </header>

    <main class="content-body">