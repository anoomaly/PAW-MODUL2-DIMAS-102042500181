<?php
// Satu file untuk TAMBAH (tanpa ?id) dan EDIT (dengan ?id)
require 'koneksi.php';

$daftar_status = ['Draf', 'Aktif', 'Selesai'];

$id = 0;
$lama = null;
$data = ['mata_kuliah_id' => '', 'judul' => '', 'instruksi' => '', 'tenggat' => '', 'status' => 'Draf'];

if (isset($_GET['id'])) {
    $id = cek_id($_GET['id']);
    if (!$id) {
        set_pesan('error', 'ID tidak valid.');
        pindah('tugas.php');
    }
    $stmt = $conn->prepare('SELECT mata_kuliah_id, judul, instruksi, tenggat, status FROM tugas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $lama = $stmt->get_result()->fetch_assoc();
    if (!$lama) {
        set_pesan('error', 'Tugas tidak ditemukan.');
        pindah('tugas.php');
    }
    $data = $lama;
}

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'mata_kuliah_id' => trim($_POST['mata_kuliah_id'] ?? ''),
        'judul'          => trim($_POST['judul'] ?? ''),
        'instruksi'      => trim($_POST['instruksi'] ?? ''),
        'tenggat'        => trim($_POST['tenggat'] ?? ''),
        'status'         => trim($_POST['status'] ?? ''),
    ];

    $mk_id = cek_id($data['mata_kuliah_id']);
    if (!$mk_id) {
        $error[] = 'Pilih mata kuliah.';
    } else {
        $cek = $conn->prepare('SELECT id FROM mata_kuliah WHERE id = ?');
        $cek->bind_param('i', $mk_id);
        $cek->execute();
        if ($cek->get_result()->num_rows == 0) {
            $error[] = 'Mata kuliah tidak ditemukan.';
        }
    }

    if (mb_strlen($data['judul']) < 3 || mb_strlen($data['judul']) > 150) {
        $error[] = 'Judul harus 3-150 karakter.';
    }
    if (mb_strlen($data['instruksi']) < 3 || mb_strlen($data['instruksi']) > 2000) {
        $error[] = 'Instruksi harus 3-2000 karakter.';
    }
    if (!tanggal_valid($data['tenggat'])) {
        $error[] = 'Tenggat harus tanggal yang valid (format YYYY-MM-DD).';
    }
    if (!in_array($data['status'], $daftar_status, true)) {
        $error[] = 'Status hanya boleh Draf, Aktif, atau Selesai.';
    }

    if (!$error) {
        if ($id == 0) {
            $stmt = $conn->prepare('INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issss', $mk_id, $data['judul'], $data['instruksi'], $data['tenggat'], $data['status']);
            $stmt->execute();
            set_pesan('sukses', 'Tugas berhasil ditambahkan.');
            pindah('tugas.php');
        } else {
            if ((int)$lama['mata_kuliah_id'] === $mk_id && $lama['judul'] === $data['judul']
                && $lama['instruksi'] === $data['instruksi'] && $lama['tenggat'] === $data['tenggat']
                && $lama['status'] === $data['status']) {
                set_pesan('info', 'Tidak ada perubahan data.');
                pindah('tugas.php');
            }
            $stmt = $conn->prepare('UPDATE tugas SET mata_kuliah_id = ?, judul = ?, instruksi = ?, tenggat = ?, status = ? WHERE id = ?');
            $stmt->bind_param('issssi', $mk_id, $data['judul'], $data['instruksi'], $data['tenggat'], $data['status'], $id);
            $stmt->execute();
            set_pesan('sukses', 'Tugas berhasil diubah.');
            pindah('tugas.php');
        }
    }
}

$daftar_mk = $conn->query('SELECT id, nama FROM mata_kuliah ORDER BY nama')->fetch_all(MYSQLI_ASSOC);

$judul = $id ? 'Edit Tugas' : 'Tambah Tugas';
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
    <label>Mata Kuliah</label>
    <select name="mata_kuliah_id">
        <option value="">-- Pilih mata kuliah --</option>
        <?php foreach ($daftar_mk as $k): ?>
            <option value="<?= e($k['id']) ?>" <?= ((string)$data['mata_kuliah_id'] === (string)$k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Judul</label>
    <input type="text" name="judul" value="<?= e($data['judul']) ?>">

    <label>Instruksi</label>
    <textarea name="instruksi"><?= e($data['instruksi']) ?></textarea>

    <label>Tenggat (YYYY-MM-DD)</label>
    <input type="text" name="tenggat" placeholder="2026-12-31" value="<?= e($data['tenggat']) ?>">

    <label>Status</label>
    <select name="status">
        <?php foreach ($daftar_status as $s): ?>
            <option value="<?= e($s) ?>" <?= ($data['status'] === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>

    <p>
        <button type="submit" class="tombol">Simpan</button>
        <a class="tombol abu" href="tugas.php">Batal</a>
    </p>
</form>
<?php include 'footer.php'; ?>
