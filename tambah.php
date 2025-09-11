<?php
session_start();

require 'config.php';
include 'functions.php';
require_once 'classes/log.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nomor_register' => $_POST['nomor_register'],
        'nama_tersangka' => $_POST['nama_tersangka'],
        'instansi_pengirim' => $_POST['instansi_pengirim'],
        'tanggal_penangkapan' => $_POST['tanggal_penangkapan'],
        'tanggal_permohonan_tat' => $_POST['tanggal_permohonan_tat'],
        'tanggal_pelaksanaan_asesmen_terpadu' => $_POST['tanggal_pelaksanaan_asesmen_terpadu'],
        'tanggal_rekomendasi' => $_POST['tanggal_rekomendasi'],
        'jenis_narkotika' => $_POST['jenis_narkotika'] === 'other' ? $_POST['custom_jenis_narkotika'] : $_POST['jenis_narkotika'],
        'berat_barang_bukti' => $_POST['berat_barang_bukti'],
        'pasal_yang_disangkakan' => $_POST['pasal_yang_disangkakan'] === 'other' ? $_POST['custom_pasal'] : $_POST['pasal_yang_disangkakan'],
        'hasil_rekomendasi_tat' => $_POST['hasil_rekomendasi_tat'],
        'rekomendasi' => $_POST['rekomendasi'],
        'jenis_rehab' => $_POST['rekomendasi'] === 'Rehabilitasi' ? $_POST['jenis_rehab'] : '',
        'pelaksanaan_rekomendasi' => $_POST['pelaksanaan_rekomendasi'],
        'anggaran' => $_POST['anggaran']
    ];

    // upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['gambar']);
        if (isset($uploadResult['error'])) {
            echo "<script>alert('{$uploadResult['error']}'); window.history.back();</script>";
            exit();
        }
        $gambar = $uploadResult['filename'];

        $insertResult = insertDataTersangka($conn, $data, $gambar);

if (isset($insertResult['success'])) {
    echo "<script>alert('Data berhasil ditambahkan!'); 
    window.location.href='home.php';</script>";
    exit();
} else {
    echo "<script>alert('{$insertResult['error']}'); 
    window.history.back();</script>";
    exit;
}
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data</title>
    <link rel="stylesheet" href="css/tambah.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<input type="checkbox" id="check">
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="log_aktivitas.php">Log</a></li>
        <li><a href="index.php">Home</a></li>
        <li><a href="tambah.php" class="<?php echo isActive('tambah.php'); ?>">Riwayat Rehab</a></li>
        <li><a href="home.php">Rekap Rehab</a></li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn">Formulir TAT</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="formulir.php">Formulir ada barang bukti</a>
                <a href="formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn">Riwayat Formulir</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="lihat_formulir.php">Formulir ada barang bukti</a>
                <a href="lihat_formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<header>
    <div class="container">
        <h1><a href="">Berantas</a></h1>
        <ul>
        <li><a href="log_aktivitas.php">Log</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="tambah.php" class="<?php echo isActive('tambah.php'); ?>">Riwayat Rehab</a></li>
            <li><a href="home.php">Rekap Rehab</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">Formulir TAT</a>
                <div class="dropdown-content" id="dropdownMenu">
                    <a href="formulir.php">Formulir ada barang bukti</a>
                    <a href="formulir2.php">Formulir tidak ada barang bukti</a>
                </div>
            </li>
            <li class="dropdown">
            <a href="#" class="dropdown-btn">Riwayat Formulir</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="lihat_formulir.php">Formulir ada barang bukti</a>
                <a href="lihat_formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        
        <!-- menu mobile -->
        <label for="check" class="mobile-menu"><i class="fas fa-bars fa-2x"></i></label>
    </div>
</header>

<div class="container">
    <h1>Riwayat Rehabilitasi</h1>

    <form method="POST" action="tambah.php" enctype="multipart/form-data">
        <label for="nomor_register">NOMOR REGISTER</label>
        <input type="number" name="nomor_register" id="nomor_register" required>

        <label for="nama_tersangka">NAMA TERSANGKA</label>
        <input type="text" name="nama_tersangka" id="nama_tersangka" required>

        <label for="instansi_pengirim">INSTANSI PENGIRIM</label>
        <input type="text" name="instansi_pengirim" id="instansi_pengirim" required>

        <label for="tanggal_penangkapan">TANGGAL PENANGKAPAN</label>
        <input type="datetime-local" name="tanggal_penangkapan" id="tanggal_penangkapan" required>

        <label for="tanggal_permohonan_tat">TANGGAL PERMOHONAN</label>
        <input type="date" name="tanggal_permohonan_tat" id="tanggal_permohonan_tat" required>

        <label for="tanggal_pelaksanaan_asesmen_terpadu">TANGGAL PELAKSANAAN ASESMEN TERPADU</label>
        <input type="date" name="tanggal_pelaksanaan_asesmen_terpadu" id="tanggal_pelaksanaan_asesmen_terpadu" required>

        <label for="tanggal_rekomendasi">TANGGAL REKOMENDASI</label>
        <input type="date" name="tanggal_rekomendasi" id="tanggal_rekomendasi" required>

        <label for="jenis_narkotika">JENIS NARKOTIKA</label>
        <select id="jenis_narkotika" name="jenis_narkotika">
            <option value="Ganja">Ganja</option>
            <option value="Sabu">Sabu</option>
            <option value="Heroin">Heroin</option>
            <option value="Linting Ganja">Linting Ganja</option>
            <option value="Tembakau Sintetis">Tembakau Sintetis</option>
            <!-- Tambahkan opsi lainnya sesuai kebutuhan -->
            <option value="other">Lainnya</option>
        </select>

        <label for="custom_jenis_narkotika" id="custom_jenis_narkotika_label" class="hidden">Masukkan Jenis Narkotika Baru</label>
        <input type="text" id="custom_jenis_narkotika" name="custom_jenis_narkotika" placeholder="Masukkan jenis narkotika" class="hidden">

        <label for="berat_barang_bukti">BERAT BARANG BUKTI (Gram)</label>
        <input type="text" name="berat_barang_bukti" id="berat_barang_bukti" required>

        <label for="pasal_yang_disangkakan">PASAL YANG DISANGKAKAN</label>
        <select name="pasal_yang_disangkakan" id="pasal_yang_disangkakan" required>
            <option value="Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika">Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika">Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika">Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika">Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika">Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika">Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="other">Lainnya</option>
        </select>

        <label for="custom_pasal" id="custom_pasal_label" class="hidden">Masukkan Pasal Baru</label>
        <input type="text" id="custom_pasal" name="custom_pasal" placeholder="Masukkan pasal baru" class="hidden">

        <label for="hasil_rekomendasi_tat">HASIL REKOMENDASI TAT</label>
        <input type="text" name="hasil_rekomendasi_tat" id="hasil_rekomendasi_tat" required>

        <label for="rekomendasi">SURAT REKOMENDASI</label>
        <select name="rekomendasi" id="rekomendasi" onchange="toggleRehabOptions()">
            <option value="">Pilih Surat Rekomendasi</option>
            <option value="Rehabilitasi">Rehabilitasi</option>
            <option value="Lanjut">Proses Hukum Lanjut</option>
        </select>

        <div id="rehab-options" class="hidden">
            <label for="jenis_rehab">JENIS REHAB</label>
            <select name="jenis_rehab" id="jenis_rehab">
                <option value="">Pilih Jenis Rehab</option>
                <option value="Rawat Jalan">Rawat Jalan</option>
                <option value="Rawat Inap">Rawat Inap</option>
            </select>
        </div>

        <label for="pelaksanaan_rekomendasi">PELAKSANAAN REKOMENDASI APAKAH DILAKSANAKAN</label>
        <select name="pelaksanaan_rekomendasi" id="pelaksanaan_rekomendasi">
            <option value="">Pilih Pelaksanaan</option>
            <option value="ya">Ya</option>
            <option value="tidak">Tidak</option>
        </select>

        <label for="anggaran">ANGGARAN</label>
        <select name="anggaran" id="anggaran" required>
            <option value="dipa">DIPA</option>
            <option value="non_dipa">Non DIPA</option>
        </select>

        <label for="gambar">Upload Gambar</label>
        <input type="file" name="gambar" id="gambar" required>

        <!-- Container untuk tombol Simpan dan Batal -->
        <div class="button-group">
            <button type="submit">Simpan Data</button>
            <a href="index.php" class="cancel-btn">Batal</a>
        </div>
    </form>
</div>

<script src="js/tambah.js">
</script>

</body>
</html>