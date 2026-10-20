<?php
require 'koneksi.php';
$judul = 'Beranda';

// Hitung jumlah data dengan COUNT
$jml_mk = $conn->query('SELECT COUNT(*) AS jml FROM mata_kuliah')->fetch_assoc()['jml'];
$jml_materi = $conn->query('SELECT COUNT(*) AS jml FROM materi')->fetch_assoc()['jml'];
$jml_tugas = $conn->query('SELECT COUNT(*) AS jml FROM tugas')->fetch_assoc()['jml'];

include 'header.php';
?>
<h1>LMS Lab EAD</h1>
<p>Kelola mata kuliah, materi, dan tugas dalam satu aplikasi.</p>

<div class="kartu-wrap">
    <div class="kartu">
        <p>Mata Kuliah</p>
        <h2><?= $jml_mk ?></h2>
        <a class="tombol" href="mata_kuliah.php">Kelola</a>
    </div>
    <div class="kartu">
        <p>Materi</p>
        <h2><?= $jml_materi ?></h2>
        <a class="tombol" href="materi.php">Kelola</a>
    </div>
    <div class="kartu">
        <p>Tugas</p>
        <h2><?= $jml_tugas ?></h2>
        <a class="tombol" href="tugas.php">Kelola</a>
    </div>
</div>
<?php include 'footer.php'; ?>
