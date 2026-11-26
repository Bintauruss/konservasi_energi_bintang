<?php
require_once '../config/koneksi.php';
wajib_login();

$daftar = $pdo->query("
    SELECT c.*, u.nama_lengkap,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Sudah Dimatikan') AS jml_mati,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Masih Menyala') AS jml_nyala
    FROM checklist c
    JOIN users u ON c.id_user = u.id_user
    ORDER BY c.tanggal DESC, c.waktu_cek DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan PDF — Konservasi Energi</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 25px; }
    .kop { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px; }
    .kop h2 { margin: 0; color: #065f46; }
    .kop p { margin: 4px 0 0; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #ecfdf5; color: #065f46; }
    .ttd { margin-top: 40px; text-align: right; }
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom: 15px;">
  <button onclick="window.print()" style="padding: 8px 16px; background: #10b981; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
    Simpan / Cetak PDF
  </button>
  <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
    Tutup
  </button>
</div>

<div class="kop">
  <h2>LAPORAN REKAP CHECKLIST KONSERVASI ENERGI</h2>
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
    <?php $no = 1; foreach ($daftar as $row): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= date('d M Y', strtotime($row['tanggal'])) ?> (<?= substr($row['waktu_cek'], 0, 5) ?>)</td>
        <td><?= htmlspecialchars($row['ruangan']) ?></td>
        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
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