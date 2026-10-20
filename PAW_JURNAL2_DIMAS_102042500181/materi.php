<?php
require 'koneksi.php';
$judul = 'Materi';

// Pencarian judul + filter mata kuliah
$q = trim($_GET['q'] ?? '');
$mk = cek_id($_GET['mk'] ?? '');
$like = '%' . $q . '%';

$sql = 'SELECT m.id, m.judul, k.nama AS nama_mk
        FROM materi m JOIN mata_kuliah k ON k.id = m.mata_kuliah_id
        WHERE m.judul LIKE ?';
$tipe = 's';
$nilai = [$like];
if ($mk) {
    $sql .= ' AND m.mata_kuliah_id = ?';
    $tipe .= 'i';
    $nilai[] = $mk;
}
$sql .= ' ORDER BY m.id DESC';

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipe, ...$nilai);
$stmt->execute();
$hasil = $stmt->get_result();

$daftar_mk = $conn->query('SELECT id, nama FROM mata_kuliah ORDER BY nama')->fetch_all(MYSQLI_ASSOC);

include 'header.php';
?>
<h1>Daftar Materi</h1>

<form method="get" class="cari">
    <input type="text" name="q" placeholder="Cari judul materi" value="<?= e($q) ?>">
    <select name="mk">
        <option value="">Semua mata kuliah</option>
        <?php foreach ($daftar_mk as $k): ?>
            <option value="<?= e($k['id']) ?>" <?= ($mk == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="tombol">Cari</button>
    <a class="tombol abu" href="materi.php">Reset</a>
</form>

<a class="tombol" href="materi_form.php">+ Tambah Materi</a>

<?php if ($hasil->num_rows == 0): ?>
    <p class="pesan info">Tidak ada materi yang cocok.</p>
<?php else: ?>
    <table>
        <tr><th>Judul</th><th>Mata Kuliah</th><th>Aksi</th></tr>
        <?php while ($r = $hasil->fetch_assoc()): ?>
        <tr>
            <td><?= e($r['judul']) ?></td>
            <td><?= e($r['nama_mk']) ?></td>
            <td>
                <a href="materi_detail.php?id=<?= e($r['id']) ?>">Detail</a> |
                <a href="materi_form.php?id=<?= e($r['id']) ?>">Edit</a> |
                <a href="hapus.php?tabel=materi&id=<?= e($r['id']) ?>">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
