<?php
require 'config.php';
require 'functions.php';
require 'classes/UserAuth.php';

// cek id
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}
$id = intval($_GET['id']);
$data = getTersangkaById($id);

if (!$data) {
    die("Data tidak ditemukan.");
}

$pelaksanaan = strtolower(trim($data['pelaksanaan_rekomendasi']));
$anggaran = strtolower(trim($data['anggaran']));

  // jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = $_POST;

    // default gambar lama
    $updateData['gambar'] = $data['gambar'];

    // cek upload file baru
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $fileName;

        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // hapus gambar lama
            if (!empty($data['gambar']) && file_exists("uploads/" . $data['gambar'])) {
                unlink("uploads/" . $data['gambar']);
            }
            $updateData['gambar'] = $fileName;
        }
    }

    if (updateTersangka($id, $updateData)) {
        header("Location: detail.php?id=$id&status=success");
        exit();
    } else {
        echo "Gagal update data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/edit.css">
    
</head>
<body>

<div class="container">
    <h1>Edit Data Tersangka</h1>

    <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <label for="nomor_register">NOMOR REGISTER</label>
        <input type="number" name="nomor_register" id="nomor_register" value="<?php echo $data['nomor_register']; ?>" required>

        <label for="nama_tersangka">NAMA TERSANGKA</label>
        <input type="text" name="nama_tersangka" id="nama_tersangka" value="<?php echo $data['nama_tersangka']; ?>" required>

        <label for="instansi_pengirim">INSTANSI PENGIRIM</label>
        <input type="text" name="instansi_pengirim" id="instansi_pengirim" value="<?php echo $data['instansi_pengirim']; ?>" required>

        <label for="tanggal_penangkapan">TANGGAL PENANGKAPAN</label>
        <input type="datetime-local" name="tanggal_penangkapan" id="tanggal_penangkapan" value="<?php echo $data['tanggal_penangkapan']; ?>" required>

        <label for="tanggal_permohonan_tat">TANGGAL PERMOHONAN TAT</label>
        <input type="date" name="tanggal_permohonan_tat" id="tanggal_permohonan_tat" value="<?php echo $data['tanggal_permohonan_tat']; ?>" required>

        <label for="tanggal_pelaksanaan_asesmen_terpadu">TANGGAL PELAKSANAAN ASESMENT TERPADU</label>
        <input type="date" name="tanggal_pelaksanaan_asesmen_terpadu" id="tanggal_pelaksanaan_asesmen_terpadu" value="<?php echo $data['tanggal_pelaksanaan_asesmen_terpadu']; ?>" required>

        <label for="tanggal_rekomendasi">TANGGAL REKOMENDASI</label>
        <input type="date" name="tanggal_rekomendasi" id="tanggal_rekomendasi" value="<?php echo $data['tanggal_rekomendasi']; ?>" required>

        <label for="jenis_narkotika">JENIS NARKOTIKA</label>
        <select name="jenis_narkotika" id="jenis_narkotika" onchange="toggleCustomInput('jenis_narkotika', 'custom_jenis_narkotika')" required>
            <option value="Ganja" <?php if ($data['jenis_narkotika'] === 'Ganja') echo 'selected'; ?>>Ganja</option>
            <option value="Sabu" <?php if ($data['jenis_narkotika'] === 'Sabu') echo 'selected'; ?>>Sabu</option>
            <option value="Heroin" <?php if ($data['jenis_narkotika'] === 'Heroin') echo 'selected'; ?>>Heroin</option>
            <option value="Linting Ganja" <?php if ($data['jenis_narkotika'] === 'Linting Ganja') echo 'selected'; ?>>Linting Ganja</option>
            <option value="Tembakau Sintetis" <?php if ($data['jenis_narkotika'] === 'Tembakau SIntetis') echo 'selected'; ?>>Tembakau Sintetis</option>
            <option value="other" <?php if (!in_array($data['jenis_narkotika'], ['Ganja', 'Sabu', 'Heroin', 'Linting Ganja', 'Tembakau SIntetis'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        <input type="text" id="custom_jenis_narkotika" name="custom_jenis_narkotika" value="<?php echo $data['jenis_narkotika']; ?>" style="display: <?php echo (!in_array($jenis_narkotika, ['ganja', 'sabu', 'heroin', 'tembakau sintetis', 'linting ganja'])) ? 'block' : 'none'; ?>;">

        <label for="berat_barang_bukti">BERAT BARANG BUKTI (Gram)</label>
        <input type="text" name="berat_barang_bukti" id="berat_barang_bukti" value="<?php echo $data['berat_barang_bukti']; ?>" required>

        <label for="pasal_yang_disangkakan">PASAL YANG DISANGKAKAN</label>
        <select name="pasal_yang_disangkakan" id="pasal_yang_disangkakan" onchange="toggleCustomInput('pasal_yang_disangkakan', 'custom_pasal')" required>
            <option value="Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika') echo 'selected'; ?>>Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika') echo 'selected'; ?>>Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika" <?php if ($data['pasal_yang_disangkakan'] === 'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="other" <?php if (!in_array($data['pasal_yang_disangkakan'], ['Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika', 'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika', 
            'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika', 
            'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika', 
            'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika', 'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        <input type="text" id="custom_pasal" name="custom_pasal" value="<?php echo $data['pasal_yang_disangkakan']; ?>" style="display: <?php echo (!in_array($pasal_yang_disangkakan, ['Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika', 
        'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika',
        'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika',
        'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika',
        'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika',
        'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika'])) ? 'block' : 'none'; ?>;">

        <label for="hasil_rekomendasi_tat">HASIL REKOMENDASI TAT</label>
        <input type="text" name="hasil_rekomendasi_tat" id="hasil_rekomendasi_tat" value="<?php echo $data['hasil_rekomendasi_tat']; ?>" required>

        <label for="rekomendasi">REKOMENDASI</label>
        <select name="rekomendasi" id="rekomendasi" class="capitalize">
                <option value="Rehabilitasi" <?php if ($data['rekomendasi'] === 'Rehabilitasi') echo 'selected'; ?>>Rehabilitasi</option>
                <option value="Lanjut" <?php if ($data['rekomendasi'] === 'Lanjut') echo 'selected'; ?>>Proses Hukum Lanjut</option>
        </select>

        <div id="rehab-options" class="<?php echo $data['rekomendasi'] !== 'Rehabilitasi' ? 'hidden' : ''; ?>">
            <label for="jenis_rehab">JENIS REHAB</label>
            <select name="jenis_rehab" id="jenis_rehab" class="capitalize">
                <option value="Rawat Inap" <?php if ($data['jenis_rehab'] === 'Rawat Inap') echo 'selected'; ?>>Rawat Inap</option>
                <option value="Rawat Jalan" <?php if ($data['jenis_rehab'] === 'Rawat Jalan') echo 'selected'; ?>>Rawat Jalan</option>
                <option value="lainnya" <?php if ($data['jenis_rehab'] === 'lainnya') echo 'selected'; ?>>Lainnya</option>
            </select>
        </div>

        <label for="pelaksanaan_rekomendasi">PELAKSANAAN REKOMENDASI APAKAH DILAKSANAKAN</label>
        <select name="pelaksanaan_rekomendasi" id="pelaksanaan_rekomendasi" onchange="toggleCustomInput('pelaksanaan_rekomendasi', 'custom_pelaksanaan_rekomendasi')" required>
            <option value="">Pilih Pelaksanaan</option>
            <option value="Ya" <?php echo ($data['pelaksanaan_rekomendasi'] == 'Ya') ? 'selected' : ''; ?>>Ya</option>
            <option value="Tidak" <?php echo ($data['pelaksanaan_rekomendasi'] == 'Tidak') ? 'selected' : ''; ?>>Tidak</option>
            <option value="other" <?php if (!in_array($data['pelaksanaan_rekomendasi'], ['Ya', 'Tidak'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        </select>
        <input type="text" id="custom_pelaksanaan_rekomendasi" name="custom_pelaksanaan_rekomendasi" 
        value="<?php echo (!in_array($data['pelaksanaan_rekomendasi'], ['Ya', 'Tidak'])) ? $data['pelaksanaan_rekomendasi'] : ''; ?>" 
        style="display: <?php echo (!in_array($data['pelaksanaan_rekomendasi'], ['Ya', 'Tidak'])) ? 'block' : 'none'; ?>;">

        <label for="anggaran">ANGGARAN</label>
        <select name="anggaran" id="anggaran" onchange="toggleCustomInput('anggaran', 'custom_anggaran')" required>
            <option value="">Pilih Anggaran</option>
            <option value="Dipa" <?php echo ($data['anggaran'] == 'Dipa') ? 'selected' : ''; ?>>DIPA</option>
            <option value="Non Dipa" <?php echo ($data['anggaran'] == 'Non Dipa') ? 'selected' : ''; ?>>Non DIPA</option>
            <option value="other" <?php if (!in_array($data['anggaran'], ['Dipa', 'Non Dipa'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        <input type="text" id="custom_anggaran" name="custom_anggaran" 
        value="<?php echo (!in_array($data['anggaran'], ['Dipa', 'Non Dipa'])) ? $data['anggaran'] : ''; ?>" 
        style="display: <?php echo (!in_array($data['anggaran'], ['Dipa', 'Non Dipa'])) ? 'block' : 'none'; ?>;">

        <label for="gambar">Upload Gambar</label>
        <input type="file" name="gambar" id="gambar">
    
    <!-- Jika ada gambar yang sudah diupload, tampilkan -->
    <?php if (!empty($data['gambar'])): ?>
        <p>Gambar Saat Ini:</p>
        <img src="uploads/<?php echo $data['gambar']; ?>" alt="Gambar Tersangka" style="max-width: 200px; margin-bottom:20px;">
    <?php endif; ?>

        <div class="button-group">
            <button type="submit">Simpan</button>
            <a href="home.php" class="cancel-btn">Batal</a>
        </div>
    </form>
</div>
<script src="js/edit.js"></script>

</body>
</html>