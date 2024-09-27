<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nomor_register = $_POST['nomor_register'];
    $nama_tersangka = $_POST['nama_tersangka'];
    $instansi_pengirim = $_POST['instansi_pengirim'];
    $tanggal_penangkapan = $_POST['tanggal_penangkapan'];
    $tanggal_permohonan_tat = $_POST['tanggal_permohonan_tat'];
    $tanggal_pelaksanaan_asesmen_terpadu = $_POST['tanggal_pelaksanaan_asesmen_terpadu'];
    $tanggal_rekomendasi = $_POST['tanggal_rekomendasi'];
    $jenis_narkotika = $_POST['jenis_narkotika'];
    if ($jenis_narkotika === 'other') {
        $jenis_narkotika = $_POST['custom_jenis_narkotika'];
    }
    $berat_barang_bukti = $_POST['berat_barang_bukti'];
    $pasal_yang_disangkakan = $_POST['pasal_yang_disangkakan'];
    if ($pasal_yang_disangkakan === 'other') {
        $pasal_yang_disangkakan = $_POST['custom_pasal'];
    }
    $hasil_rekomendasi_tat = $_POST['hasil_rekomendasi_tat'];
    $rekomendasi = $_POST['rekomendasi'];
    $jenis_rehab = '';
    if ($rekomendasi === 'Rehabilitasi') {
        $jenis_rehab = $_POST['jenis_rehab'];
    }
    $pelaksanaan_rekomendasi = $_POST['pelaksanaan_rekomendasi'];
    $anggaran = $_POST['anggaran'];

    // Menangani upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];
        $fileType = $_FILES['gambar']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Definisikan ekstensi yang diizinkan
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Tentukan lokasi upload
            $uploadFileDir = './uploads/';
            // Pastikan direktori upload ada
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file yang diupload
            if(move_uploaded_file($fileTmpPath, $dest_path)) {

                // Siapkan query dengan prepared statements
                $sql = "INSERT INTO data_tersangka 
                        (nomor_register, nama_tersangka, instansi_pengirim, tanggal_penangkapan, tanggal_permohonan_tat, 
                         tanggal_pelaksanaan_asesmen_terpadu, tanggal_rekomendasi, jenis_narkotika, berat_barang_bukti, 
                         pasal_yang_disangkakan, hasil_rekomendasi_tat, rekomendasi, jenis_rehab, pelaksanaan_rekomendasi, 
                         anggaran, gambar)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    die("Prepare failed: " . htmlspecialchars($conn->error));
                }

                // Bind parameter
                $stmt->bind_param(
                    "isssssssdsssssss",
                    $nomor_register,
                    $nama_tersangka,
                    $instansi_pengirim,
                    $tanggal_penangkapan,
                    $tanggal_permohonan_tat,
                    $tanggal_pelaksanaan_asesmen_terpadu,
                    $tanggal_rekomendasi,
                    $jenis_narkotika,
                    $berat_barang_bukti,
                    $pasal_yang_disangkakan,
                    $hasil_rekomendasi_tat,
                    $rekomendasi,
                    $jenis_rehab,
                    $pelaksanaan_rekomendasi,
                    $anggaran,
                    $dest_path
                );
                

                if ($stmt->execute()) {
                    // Redirect ke halaman tambah.php setelah data ditambahkan
                    header("Location: tambah.php");
                    exit();
                } else {
                    echo "Error: " . htmlspecialchars($stmt->error);
                }

                $stmt->close();
            } else {
                echo "Error: Gagal mengunggah gambar.";
            }
        } else {
            echo "Error: Ekstensi file tidak diizinkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    } else {
        echo "Error: Tidak ada gambar yang diunggah atau terjadi kesalahan saat mengunggah.";
    }
} 

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data</title>
    <link rel="stylesheet" href="css/tambah.css">
    
</head>
<body>

<div class="container">
    <h1>Tambah Data</h1>

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

        <label for="gambar">UPLOAD GAMBAR</label>
        <input type="file" name="gambar" id="gambar" accept=".jpg, .jpeg, .png, .pdf" required>

        <!-- Container untuk tombol Simpan dan Batal -->
        <div class="button-group">
            <button type="submit">Simpan Data</button>
            <a href="home.php" class="cancel-btn">Batal</a>
        </div>
    </form>
</div>

<script>
    function toggleRehabOptions() {
        const rekomendasi = document.getElementById('rekomendasi').value;
        const rehabOptions = document.getElementById('rehab-options');
        if (rekomendasi === 'Rehabilitasi') {
            rehabOptions.classList.remove('hidden');
        } else {
            rehabOptions.classList.add('hidden');
            document.getElementById('jenis_rehab').value = '';
        }
    }

    document.getElementById('jenis_narkotika').addEventListener('change', function() {
    const selectElement = this;
    const customJenisNarkotikaInput = document.getElementById('custom_jenis_narkotika');
    const customJenisNarkotikaLabel = document.getElementById('custom_jenis_narkotika_label');
    
    if (selectElement.value === 'other') {
        customJenisNarkotikaInput.classList.remove('hidden');
        customJenisNarkotikaLabel.classList.remove('hidden');
    } else {
        customJenisNarkotikaInput.classList.add('hidden');
        customJenisNarkotikaLabel.classList.add('hidden');
        customJenisNarkotikaInput.value = ''; // Clear the input field when another option is selected
    }
});


    document.getElementById('pasal_yang_disangkakan').addEventListener('change', function() {
        const selectElement = this;
        const customPasalInput = document.getElementById('custom_pasal');
        const customPasalLabel = document.getElementById('custom_pasal_label');
        
        if (selectElement.value === 'other') {
            customPasalInput.style.display = 'block';
            customPasalLabel.style.display = 'block';
        } else {
            customPasalInput.style.display = 'none';
            customPasalLabel.style.display = 'none';
            customPasalInput.value = ''; // Clear the input field when an option is selected
        }
    });
</script>

</body>
</html>
