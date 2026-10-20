<?php
require 'koneksi.php';

$id = cek_id($_GET['id'] ?? '');
if (!$id) {
    set_pesan('error', 'ID tidak valid.');
    pindah('tugas.php');
}

$stmt = $conn->prepare('SELECT t.id, t.judul, t.instruksi, t.tenggat, t.status, k.nama AS nama_mk
                        FROM tugas t JOIN mata_kuliah k ON k.id = t.mata_kuliah_id
                        WHERE t.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();
if (!$t) {
    set_pesan('error', 'Tugas tidak ditemukan.');
    pindah('tugas.php');
}

$judul = 'Detail Tugas';
include 'header.php';
?>
<h1>Detail Tugas</h1>
<div class="kotak">
    <p>Mata kuliah: <strong><?= e($t['nama_mk']) ?></strong></p>
    <p>Judul: <?= e($t['judul']) ?></p>
    <p>Instruksi:<br><?= nl2br(e($t['instruksi'])) ?></p>
    <p>Tenggat: <?= e($t['tenggat']) ?></p>
    <p>Status: <?= e($t['status']) ?></p>
    <a class="tombol" href="tugas_form.php?id=<?= e($t['id']) ?>">Edit</a>
    <a class="tombol abu" href="tugas.php">Kembali</a>
</div>
<?php include 'footer.php'; ?>
