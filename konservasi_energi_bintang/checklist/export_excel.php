<?php
require_once '../config/koneksi.php';
wajib_login();

$filename = "Rekap_Checklist_Energi_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

$daftar = $pdo->query("
    SELECT c.*, u.nama_lengkap,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Sudah Dimatikan') AS jml_mati,
        (SELECT COUNT(*) FROM detail_checklist dc WHERE dc.id_checklist = c.id_checklist AND dc.kondisi = 'Masih Menyala') AS jml_nyala
    FROM checklist c
    JOIN users u ON c.id_user = u.id_user
    ORDER BY c.tanggal DESC, c.waktu_cek DESC
")->fetchAll();
?>
<table border="1">
  <thead>
    <tr>
      <th colspan="8" style="font-size:14pt; font-weight:bold;">REKAP LAPORAN CHECKLIST KONSERVASI ENERGI</th>
    </tr>
    <tr style="background-color:#d1fae5; font-weight:bold;">
      <th>No</th>
      <th>Tanggal</th>
      <th>Waktu (WIB)</th>
      <th>Ruangan</th>
      <th>Petugas</th>
      <th>Sudah Dimatikan</th>
      <th>Masih Menyala</th>
      <th>Catatan</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; foreach ($daftar as $row): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
        <td><?= substr($row['waktu_cek'], 0, 5) ?></td>
        <td><?= htmlspecialchars($row['ruangan']) ?></td>
        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
        <td><?= $row['jml_mati'] ?> Unit</td>
        <td><?= $row['jml_nyala'] ?> Unit</td>
        <td><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>