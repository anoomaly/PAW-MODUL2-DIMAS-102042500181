<?php
require 'koneksi.php';

$id = cek_id($_GET['id'] ?? '');
if (!$id) {
    set_pesan('error', 'ID tidak valid.');
    pindah('mata_kuliah.php');
}

$stmt = $conn->prepare('SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$mk = $stmt->get_result()->fetch_assoc();
if (!$mk) {
    set_pesan('error', 'Mata kuliah tidak ditemukan.');
    pindah('mata_kuliah.php');
}

// Materi dan tugas milik mata kuliah ini
$s1 = $conn->prepare('SELECT id, judul FROM materi WHERE mata_kuliah_id = ? ORDER BY id');
$s1->bind_param('i', $id);
$s1->execute();
$daftar_materi = $s1->get_result();

$s2 = $conn->prepare('SELECT id, judul, tenggat, status FROM tugas WHERE mata_kuliah_id = ? ORDER BY tenggat');
$s2->bind_param('i', $id);
$s2->execute();
$daftar_tugas = $s2->get_result();

$judul = 'Detail Mata Kuliah';
include 'header.php';
?>
<h1>Detail Mata Kuliah</h1>
<div class="kotak">
    <p>Kode: <strong><?= e($mk['kode']) ?></strong></p>
    <p>Nama: <?= e($mk['nama']) ?></p>
    <p>Dosen: <?= e($mk['dosen']) ?></p>
    <p>Semester: <?= e($mk['semester']) ?></p>
    <a class="tombol" href="mata_kuliah_form.php?id=<?= e($mk['id']) ?>">Edit</a>
    <a class="tombol abu" href="mata_kuliah.php">Kembali</a>
</div>

<h2>Materi</h2>
<?php if ($daftar_materi->num_rows == 0): ?>
    <p class="pesan info">Belum ada materi.</p>
<?php else: ?>
    <ul>
    <?php while ($m = $daftar_materi->fetch_assoc()): ?>
        <li><a href="materi_detail.php?id=<?= e($m['id']) ?>"><?= e($m['judul']) ?></a></li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>

<h2>Tugas</h2>
<?php if ($daftar_tugas->num_rows == 0): ?>
    <p class="pesan info">Belum ada tugas.</p>
<?php else: ?>
    <ul>
    <?php while ($t = $daftar_tugas->fetch_assoc()): ?>
        <li><a href="tugas_detail.php?id=<?= e($t['id']) ?>"><?= e($t['judul']) ?></a>
            (tenggat <?= e($t['tenggat']) ?>, <?= e($t['status']) ?>)</li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>
<?php include 'footer.php'; ?>
