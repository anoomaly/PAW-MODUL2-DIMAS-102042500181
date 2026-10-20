<?php
require 'koneksi.php';

// Nama tabel HANYA boleh dari daftar ini (bukan dari isian pengguna)
$boleh = [
    'mata_kuliah' => ['balik' => 'mata_kuliah.php', 'kolom' => 'nama'],
    'materi'      => ['balik' => 'materi.php',      'kolom' => 'judul'],
    'tugas'       => ['balik' => 'tugas.php',       'kolom' => 'judul'],
];

$sumber = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET;
$tabel = $sumber['tabel'] ?? '';
if (!isset($boleh[$tabel])) {
    set_pesan('error', 'Data yang akan dihapus tidak dikenal.');
    pindah('index.php');
}
$balik = $boleh[$tabel]['balik'];
$kolom = $boleh[$tabel]['kolom'];

$id = cek_id($sumber['id'] ?? '');
if (!$id) {
    set_pesan('error', 'ID tidak valid.');
    pindah($balik);
}

// Pastikan datanya ada
$stmt = $conn->prepare("SELECT $kolom AS label FROM $tabel WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$baris = $stmt->get_result()->fetch_assoc();
if (!$baris) {
    set_pesan('error', 'Data tidak ditemukan.');
    pindah($balik);
}

// Proses hapus (hanya lewat POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_ok()) {
        set_pesan('error', 'Token tidak cocok. Data tidak dihapus.');
        pindah($balik);
    }

    // Mata kuliah yang masih dipakai tidak boleh dihapus
    if ($tabel === 'mata_kuliah') {
        $s1 = $conn->prepare('SELECT COUNT(*) AS jml FROM materi WHERE mata_kuliah_id = ?');
        $s1->bind_param('i', $id);
        $s1->execute();
        $jml_materi = $s1->get_result()->fetch_assoc()['jml'];

        $s2 = $conn->prepare('SELECT COUNT(*) AS jml FROM tugas WHERE mata_kuliah_id = ?');
        $s2->bind_param('i', $id);
        $s2->execute();
        $jml_tugas = $s2->get_result()->fetch_assoc()['jml'];

        if ($jml_materi > 0 || $jml_tugas > 0) {
            set_pesan('error', "Mata kuliah tidak bisa dihapus karena masih punya $jml_materi materi dan $jml_tugas tugas. Hapus dulu materi dan tugasnya.");
            pindah($balik);
        }
    }

    try {
        $hapus = $conn->prepare("DELETE FROM $tabel WHERE id = ?");
        $hapus->bind_param('i', $id);
        $hapus->execute();
        set_pesan('sukses', 'Data berhasil dihapus.');
    } catch (mysqli_sql_exception $ex) {
        set_pesan('error', 'Data gagal dihapus karena masih digunakan data lain.');
    }
    pindah($balik);
}

$judul = 'Konfirmasi Hapus';
include 'header.php';
?>
<h1>Konfirmasi Hapus</h1>
<div class="kotak">
    <p>Yakin ingin menghapus <strong><?= e($baris['label']) ?></strong>?</p>
    <form method="post">
        <input type="hidden" name="token" value="<?= e($_SESSION['token']) ?>">
        <input type="hidden" name="tabel" value="<?= e($tabel) ?>">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <button type="submit" class="tombol merah">Ya, Hapus</button>
        <a class="tombol abu" href="<?= e($balik) ?>">Batal</a>
    </form>
</div>
<?php include 'footer.php'; ?>
