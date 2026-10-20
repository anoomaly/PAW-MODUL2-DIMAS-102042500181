<?php
require 'koneksi.php';

$id = cek_id($_GET['id'] ?? '');
if (!$id) {
    set_pesan('error', 'ID tidak valid.');
    pindah('materi.php');
}

$stmt = $conn->prepare('SELECT m.id, m.judul, m.isi, m.tautan, k.nama AS nama_mk
                        FROM materi m JOIN mata_kuliah k ON k.id = m.mata_kuliah_id
                        WHERE m.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
if (!$m) {
    set_pesan('error', 'Materi tidak ditemukan.');
    pindah('materi.php');
}

$judul = 'Detail Materi';
include 'header.php';
?>
<h1>Detail Materi</h1>
<div class="kotak">
    <p>Mata kuliah: <strong><?= e($m['nama_mk']) ?></strong></p>
    <p>Judul: <?= e($m['judul']) ?></p>
    <p>Isi:<br><?= nl2br(e($m['isi'])) ?></p>
    <p>Tautan: <a href="<?= e($m['tautan']) ?>" target="_blank" rel="noopener"><?= e($m['tautan']) ?></a></p>
    <a class="tombol" href="materi_form.php?id=<?= e($m['id']) ?>">Edit</a>
    <a class="tombol abu" href="materi.php">Kembali</a>
</div>
<?php include 'footer.php'; ?>
