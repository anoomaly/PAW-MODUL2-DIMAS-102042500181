<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul) ?> - LMS Lab EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Beranda</a>
    <a href="mata_kuliah.php">Mata Kuliah</a>
    <a href="materi.php">Materi</a>
    <a href="tugas.php">Tugas</a>
</nav>
<div class="isi">
<?php if (isset($_SESSION['pesan'])): ?>
    <div class="pesan <?= e($_SESSION['pesan']['jenis']) ?>"><?= e($_SESSION['pesan']['teks']) ?></div>
    <?php unset($_SESSION['pesan']); ?>
<?php endif; ?>
