<?php
require 'koneksi.php';
$judul = 'Mata Kuliah';

// Pencarian dari kode atau nama
$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';

$stmt = $conn->prepare('SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE kode LIKE ? OR nama LIKE ? ORDER BY nama');
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$hasil = $stmt->get_result();

include 'header.php';
?>
<h1>Daftar Mata Kuliah</h1>

<form method="get" class="cari">
    <input type="text" name="q" placeholder="Cari kode / nama" value="<?= e($q) ?>">
    <button type="submit" class="tombol">Cari</button>
    <a class="tombol abu" href="mata_kuliah.php">Reset</a>
</form>

<a class="tombol" href="mata_kuliah_form.php">+ Tambah Mata Kuliah</a>

<?php if ($hasil->num_rows == 0): ?>
    <p class="pesan info">Tidak ada mata kuliah yang cocok.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Kode</th><th>Nama</th><th>Dosen</th><th>Semester</th><th>Aksi</th>
        </tr>
        <?php while ($r = $hasil->fetch_assoc()): ?>
        <tr>
            <td><?= e($r['kode']) ?></td>
            <td><?= e($r['nama']) ?></td>
            <td><?= e($r['dosen']) ?></td>
            <td><?= e($r['semester']) ?></td>
            <td>
                <a href="mata_kuliah_detail.php?id=<?= e($r['id']) ?>">Detail</a> |
                <a href="mata_kuliah_form.php?id=<?= e($r['id']) ?>">Edit</a> |
                <a href="hapus.php?tabel=mata_kuliah&id=<?= e($r['id']) ?>">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
