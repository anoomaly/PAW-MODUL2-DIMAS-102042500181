<?php
// Satu file untuk TAMBAH (tanpa ?id) dan EDIT (dengan ?id)
require 'koneksi.php';

$id = 0;
$lama = null;
$data = ['mata_kuliah_id' => '', 'judul' => '', 'isi' => '', 'tautan' => ''];

if (isset($_GET['id'])) {
    $id = cek_id($_GET['id']);
    if (!$id) {
        set_pesan('error', 'ID tidak valid.');
        pindah('materi.php');
    }
    $stmt = $conn->prepare('SELECT mata_kuliah_id, judul, isi, tautan FROM materi WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $lama = $stmt->get_result()->fetch_assoc();
    if (!$lama) {
        set_pesan('error', 'Materi tidak ditemukan.');
        pindah('materi.php');
    }
    $data = $lama;
}

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'mata_kuliah_id' => trim($_POST['mata_kuliah_id'] ?? ''),
        'judul'          => trim($_POST['judul'] ?? ''),
        'isi'            => trim($_POST['isi'] ?? ''),
        'tautan'         => trim($_POST['tautan'] ?? ''),
    ];

    // Mata kuliah harus dipilih dan benar-benar ada
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
    if (mb_strlen($data['isi']) < 3 || mb_strlen($data['isi']) > 2000) {
        $error[] = 'Isi harus 3-2000 karakter.';
    }
    if (mb_strlen($data['tautan']) > 255) {
        $error[] = 'Tautan maksimal 255 karakter.';
    } elseif (!preg_match('#^https?://.+#i', $data['tautan'])) {
        $error[] = 'Tautan harus diawali http:// atau https://.';
    }

    if (!$error) {
        if ($id == 0) {
            $stmt = $conn->prepare('INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $mk_id, $data['judul'], $data['isi'], $data['tautan']);
            $stmt->execute();
            set_pesan('sukses', 'Materi berhasil ditambahkan.');
            pindah('materi.php');
        } else {
            if ((int)$lama['mata_kuliah_id'] === $mk_id && $lama['judul'] === $data['judul']
                && $lama['isi'] === $data['isi'] && $lama['tautan'] === $data['tautan']) {
                set_pesan('info', 'Tidak ada perubahan data.');
                pindah('materi.php');
            }
            $stmt = $conn->prepare('UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?');
            $stmt->bind_param('isssi', $mk_id, $data['judul'], $data['isi'], $data['tautan'], $id);
            $stmt->execute();
            set_pesan('sukses', 'Materi berhasil diubah.');
            pindah('materi.php');
        }
    }
}

$daftar_mk = $conn->query('SELECT id, nama FROM mata_kuliah ORDER BY nama')->fetch_all(MYSQLI_ASSOC);

$judul = $id ? 'Edit Materi' : 'Tambah Materi';
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

    <label>Isi</label>
    <textarea name="isi"><?= e($data['isi']) ?></textarea>

    <label>Tautan (http:// atau https://)</label>
    <input type="text" name="tautan" value="<?= e($data['tautan']) ?>">

    <p>
        <button type="submit" class="tombol">Simpan</button>
        <a class="tombol abu" href="materi.php">Batal</a>
    </p>
</form>
<?php include 'footer.php'; ?>
