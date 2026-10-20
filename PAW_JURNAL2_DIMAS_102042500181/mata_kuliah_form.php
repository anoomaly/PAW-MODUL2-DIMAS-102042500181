<?php
// Satu file untuk TAMBAH (tanpa ?id) dan EDIT (dengan ?id)
require 'koneksi.php';

$id = 0;
$lama = null;
$data = ['kode' => '', 'nama' => '', 'dosen' => '', 'semester' => ''];

// Mode edit: ambil data lama dulu
if (isset($_GET['id'])) {
    $id = cek_id($_GET['id']);
    if (!$id) {
        set_pesan('error', 'ID tidak valid.');
        pindah('mata_kuliah.php');
    }
    $stmt = $conn->prepare('SELECT kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $lama = $stmt->get_result()->fetch_assoc();
    if (!$lama) {
        set_pesan('error', 'Mata kuliah tidak ditemukan.');
        pindah('mata_kuliah.php');
    }
    $data = $lama;
}

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'kode'     => trim($_POST['kode'] ?? ''),
        'nama'     => trim($_POST['nama'] ?? ''),
        'dosen'    => trim($_POST['dosen'] ?? ''),
        'semester' => trim($_POST['semester'] ?? ''),
    ];

    // Periksa isian
    if (!preg_match('/^[A-Z0-9-]{3,20}$/', $data['kode'])) {
        $error[] = 'Kode harus 3-20 karakter: huruf kapital, angka, atau tanda -.';
    }
    if (mb_strlen($data['nama']) < 3 || mb_strlen($data['nama']) > 100) {
        $error[] = 'Nama harus 3-100 karakter.';
    }
    if (mb_strlen($data['dosen']) < 3 || mb_strlen($data['dosen']) > 100) {
        $error[] = 'Dosen harus 3-100 karakter.';
    }
    $semester = filter_var($data['semester'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 14]]);
    if ($semester === false) {
        $error[] = 'Semester harus bilangan bulat 1-14.';
    }

    // Kode harus unik (data yang sedang diedit tidak dihitung)
    if (!$error) {
        $cek = $conn->prepare('SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?');
        $cek->bind_param('si', $data['kode'], $id);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $error[] = 'Kode mata kuliah sudah digunakan.';
        }
    }

    if (!$error) {
        if ($id == 0) {
            // TAMBAH
            $stmt = $conn->prepare('INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('sssi', $data['kode'], $data['nama'], $data['dosen'], $semester);
            $stmt->execute();
            set_pesan('sukses', 'Mata kuliah berhasil ditambahkan.');
            pindah('mata_kuliah.php');
        } else {
            // EDIT: cek dulu apakah ada yang berubah
            if ($lama['kode'] === $data['kode'] && $lama['nama'] === $data['nama']
                && $lama['dosen'] === $data['dosen'] && (int)$lama['semester'] === $semester) {
                set_pesan('info', 'Tidak ada perubahan data.');
                pindah('mata_kuliah.php');
            }
            $stmt = $conn->prepare('UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?');
            $stmt->bind_param('sssii', $data['kode'], $data['nama'], $data['dosen'], $semester, $id);
            $stmt->execute();
            set_pesan('sukses', 'Mata kuliah berhasil diubah.');
            pindah('mata_kuliah.php');
        }
    }
}

$judul = $id ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah';
include 'header.php';
?>
<h1><?= e($judul) ?></h1>

<?php if ($error): ?>
    <div class="pesan error">
        <?php foreach ($error as $pesan): ?>
            <div><?= e($pesan) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Kode</label>
    <input type="text" name="kode" value="<?= e($data['kode']) ?>">

    <label>Nama</label>
    <input type="text" name="nama" value="<?= e($data['nama']) ?>">

    <label>Dosen</label>
    <input type="text" name="dosen" value="<?= e($data['dosen']) ?>">

    <label>Semester (1-14)</label>
    <input type="text" name="semester" value="<?= e($data['semester']) ?>">

    <p>
        <button type="submit" class="tombol">Simpan</button>
        <a class="tombol abu" href="mata_kuliah.php">Batal</a>
    </p>
</form>
<?php include 'footer.php'; ?>
