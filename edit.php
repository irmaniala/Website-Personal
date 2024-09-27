<?php
// Menghubungkan ke config.php
require 'config.php';

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah form dikirim
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        // Cek apakah file gambar di-upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $uploadOk = 1;

            // Cek apakah gambar atau bukan
            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File bukan gambar.";
                $uploadOk = 0;
            }

            // Cek ukuran file
            if ($_FILES["gambar"]["size"] > 500000) { // 500KB limit
                echo "Maaf, ukuran file terlalu besar.";
                $uploadOk = 0;
            }

            // Hanya izinkan format tertentu
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
                $uploadOk = 0;
            }

            // Jika semuanya oke, lakukan upload
            if ($uploadOk == 1) {
                // Hapus gambar lama jika ada
                if (!empty($dest_path)) {
                    unlink("uploads/$dest_path");
                }

                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // Update nama file gambar ke database
                    $gambar = basename($_FILES["gambar"]["name"]);
                } else {
                    echo "Maaf, terjadi kesalahan saat meng-upload file.";
                    $gambar = $dest_path; // Gunakan gambar lama jika upload gagal
                }
            }
        } else {
            // Jika tidak ada gambar yang di-upload, gunakan gambar lama
            $gambar = $dest_path;
        }

        // Query untuk mengupdate data berdasarkan ID
        $sql = "UPDATE data_tersangka SET nomor_register='$nomor_register', nama_tersangka='$nama_tersangka', instansi_pengirim='$instansi_pengirim', tanggal_penangkapan='$tanggal_penangkapan', tanggal_permohonan_tat='$tanggal_permohonan_tat', tanggal_pelaksanaan_asesmen_terpadu='$tanggal_pelaksanaan_asesmen_terpadu', tanggal_rekomendasi='$tanggal_rekomendasi', jenis_narkotika='$jenis_narkotika',
                berat_barang_bukti='$berat_barang_bukti', pasal_yang_disangkakan='$pasal_yang_disangkakan', hasil_rekomendasi_tat='$hasil_rekomendasi_tat', rekomendasi='$rekomendasi', jenis_rehab='$jenis_rehab', pelaksanaan_rekomendasi='$pelaksanaan_rekomendasi', anggaran='$anggaran', gambar='$gambar' WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            // Redirect kembali ke detail.php setelah data diupdate
            header("Location: detail.php?id=$id");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // Query untuk mengambil data orang berdasarkan ID
        $sql = "SELECT * FROM data_tersangka WHERE id='$id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Ambil data orang
            $row = $result->fetch_assoc();
            $nomor_register = $row['nomor_register'];
            $nama_tersangka = $row['nama_tersangka'];
            $instansi_pengirim = $row['instansi_pengirim'];
            $tanggal_penangkapan = $row['tanggal_penangkapan'];
            $tanggal_permohonan_tat = $row['tanggal_permohonan_tat'];
            $tanggal_pelaksanaan_asesmen_terpadu = $row['tanggal_pelaksanaan_asesmen_terpadu'];
            $tanggal_rekomendasi = $row['tanggal_rekomendasi'];
            $jenis_narkotika = $row['jenis_narkotika'];
            $berat_barang_bukti = $row['berat_barang_bukti'];
            $pasal_yang_disangkakan = $row['pasal_yang_disangkakan'];
            $hasil_rekomendasi_tat = $row['hasil_rekomendasi_tat'];
            $rekomendasi = $row['rekomendasi'];
            $jenis_rehab = $row['jenis_rehab'];
            $pelaksanaan_rekomendasi = $row['pelaksanaan_rekomendasi'];
            $anggaran = $row['anggaran'];
            $dest_path = $row['gambar'];
        } else {
            echo "Data tidak ditemukan.";
            exit();
        }
    }
} else {
    echo "ID tidak ditemukan.";
    exit();
}

$conn->close();
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
    <h1>Edit Data</h1>

    <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <label for="nomor_register">NOMOR REGISTER</label>
        <input type="number" name="nomor_register" id="nomor_register" value="<?php echo $nomor_register; ?>" required>

        <label for="nama_tersangka">NAMA TERSANGKA</label>
        <input type="text" name="nama_tersangka" id="nama_tersangka" value="<?php echo $nama_tersangka; ?>" required>

        <label for="instansi_pengirim">INSTANSI PENGIRIM</label>
        <input type="text" name="instansi_pengirim" id="instansi_pengirim" value="<?php echo $instansi_pengirim; ?>" required>

        <label for="tanggal_penangkapan">TANGGAL PENANGKAPAN</label>
        <input type="datetime-local" name="tanggal_penangkapan" id="tanggal_penangkapan" value="<?php echo $tanggal_penangkapan; ?>" required>

        <label for="tanggal_permohonan_tat">TANGGAL PERMOHONAN TAT</label>
        <input type="date" name="tanggal_permohonan_tat" id="tanggal_permohonan_tat" value="<?php echo $tanggal_permohonan_tat; ?>" required>

        <label for="tanggal_pelaksanaan_asesmen_terpadu">TANGGAL PELAKSANAAN ASESMENT TERPADU</label>
        <input type="date" name="tanggal_pelaksanaan_asesmen_terpadu" id="tanggal_pelaksanaan_asesmen_terpadu" value="<?php echo $tanggal_pelaksanaan_asesmen_terpadu; ?>" required>

        <label for="tanggal_rekomendasi">TANGGAL REKOMENDASI</label>
        <input type="date" name="tanggal_rekomendasi" id="tanggal_rekomendasi" value="<?php echo $tanggal_rekomendasi; ?>" required>

        <label for="jenis_narkotika">JENIS NARKOTIKA</label>
        <select name="jenis_narkotika" id="jenis_narkotika" onchange="toggleCustomInput('jenis_narkotika', 'custom_jenis_narkotika')" required>
            <option value="Ganja" <?php if ($jenis_narkotika === 'Ganja') echo 'selected'; ?>>Ganja</option>
            <option value="Sabu" <?php if ($jenis_narkotika === 'Sabu') echo 'selected'; ?>>Sabu</option>
            <option value="Heroin" <?php if ($jenis_narkotika === 'Heroin') echo 'selected'; ?>>Heroin</option>
            <option value="Linting Ganja" <?php if ($jenis_narkotika === 'Linting Ganja') echo 'selected'; ?>>Linting Ganja</option>
            <option value="Tembakau Sintetis" <?php if ($jenis_narkotika === 'Tembakau SIntetis') echo 'selected'; ?>>Tembakau Sintetis</option>
            <option value="other" <?php if (!in_array($jenis_narkotika, ['Ganja', 'Sabu', 'Heroin', 'Linting Ganja', 'Tembakau SIntetis'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        <input type="text" id="custom_jenis_narkotika" name="custom_jenis_narkotika" value="<?php echo $jenis_narkotika; ?>" style="display: <?php echo (!in_array($jenis_narkotika, ['ganja', 'sabu', 'ekstasi'])) ? 'block' : 'none'; ?>;">

        <label for="berat_barang_bukti">BERAT BARANG BUKTI (Gram)</label>
        <input type="text" name="berat_barang_bukti" id="berat_barang_bukti" value="<?php echo $berat_barang_bukti; ?>" required>

        <label for="pasal_yang_disangkakan">PASAL YANG DISANGKAKAN</label>
        <select name="pasal_yang_disangkakan" id="pasal_yang_disangkakan" onchange="toggleCustomInput('pasal_yang_disangkakan', 'custom_pasal')" required>
            <option value="Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika') echo 'selected'; ?>>Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika') echo 'selected'; ?>>Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika</option>
            <option value="Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika" <?php if ($pasal_yang_disangkakan === 'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika') echo 'selected'; ?>>Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika</option>
            <option value="other" <?php if (!in_array($pasal_yang_disangkakan, ['Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika', 'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika', 
            'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika', 
            'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika', 
            'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika', 'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika'])) echo 'selected'; ?>>Lainnya</option>
        </select>
        <input type="text" id="custom_pasal" name="custom_pasal" value="<?php echo $pasal_yang_disangkakan; ?>" style="display: <?php echo (!in_array($pasal_yang_disangkakan, ['Pasal 111 ayat (1) dan atau 127 ayat (1) Huruf (a) UU 35 Thn 2009 Tentang Narkotika', 
        'Pasal 112 Ayat (1) dan atau 127 Ayat (1) UU No. 35 Tahun 2009 Tentang Narkotika',
        'Pasal 112 Ayat (1) dan atau Pasal 111 ayat (1) dan atau Pasal 127 AYAT (1) HURUF (a) UU No. 35 Thn 2009 Tentang Narkotika',
        'Pasal 114 ayat (1) dan atau Pasal 112 Ayat (1) dan atau Pasal 127 Ayat (1) a Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika',
        'Pasal 114 ayat (1) Sub Pasal 112 ayat (1) Undang-Undang Nomor 35 Tahun 2009 tentang Narkotika',
        'Pasal 127 Ayat (1) huruf (a) UU NO. 35 Tahun 2009 Tentang Narkotika'])) ? 'block' : 'none'; ?>;">

        <label for="hasil_rekomendasi_tat">HASIL REKOMENDASI TAT</label>
        <input type="text" name="hasil_rekomendasi_tat" id="hasil_rekomendasi_tat" value="<?php echo $hasil_rekomendasi_tat; ?>" required>

        <label for="rekomendasi">REKOMENDASI</label>
        <select name="rekomendasi" id="rekomendasi" class="capitalize">
                <option value="Rehabilitasi" <?php if ($rekomendasi === 'Rehabilitasi') echo 'selected'; ?>>Rehabilitasi</option>
                <option value="Lanjut" <?php if ($rekomendasi === 'Lanjut') echo 'selected'; ?>>Proses Hukum Lanjut</option>
            </select>

        <div id="rehab-options" class="<?php echo $rekomendasi !== 'Rehabilitasi' ? 'hidden' : ''; ?>">
            <label for="jenis_rehab">JENIS REHAB</label>
            <select name="jenis_rehab" id="jenis_rehab" class="capitalize">
                <option value="Rawat Inap" <?php if ($jenis_rehab === 'Rawat Inap') echo 'selected'; ?>>Rawat Inap</option>
                <option value="Rawat Jalan" <?php if ($jenis_rehab === 'Rawat Jalan') echo 'selected'; ?>>Rawat Jalan</option>
                <option value="lainnya" <?php if ($jenis_rehab === 'lainnya') echo 'selected'; ?>>Lainnya</option>
            </select>
        </div>

        <label for="pelaksanaan_rekomendasi">PELAKSANAAN REKOMENDASI</label>
        <input type="text" name="pelaksanaan_rekomendasi" id="pelaksanaan_rekomendasi" value="<?php echo $pelaksanaan_rekomendasi; ?>" required>

        <label for="anggaran">ANGGARAN</label>
        <input type="text" name="anggaran" id="anggaran" value="<?php echo $anggaran; ?>" required>

        <label for="gambar">Upload Gambar</label>
    <input type="file" name="gambar" id="gambar">
    
    <!-- Jika ada gambar yang sudah diupload, tampilkan -->
    <?php if ($dest_path): ?>
        <p>Gambar Saat Ini:</p>
        <img src="uploads/<?php echo $dest_path; ?>" alt="Gambar Tersangka" style="max-width: 200px; margin-bottom:20px;">
    <?php endif; ?>

        <div class="button-group">
            <button type="submit">Simpan</button>
            <a href="index.php" class="cancel-btn">Batal</a>
        </div>
    </form>
</div>

<script>
    // Fungsi untuk menampilkan atau menyembunyikan input teks
    function toggleCustomInput(selectId, customInputId) {
        var selectElement = document.getElementById(selectId);
        var customInput = document.getElementById(customInputId);
        
        // Jika opsi 'Lainnya' dipilih, tampilkan input custom
        if (selectElement.value === 'other') {
            customInput.style.display = 'block';
        } else {
            customInput.style.display = 'none';
        }
    }

    // Fungsi untuk menampilkan atau menyembunyikan jenis rehab berdasarkan rekomendasi
    function toggleRehabOptions() {
        var rekomendasi = document.getElementById('rekomendasi').value;
        var rehabOptions = document.getElementById('rehab-options');
        
        if (rekomendasi === 'Rehabilitasi') {
            rehabOptions.style.display = 'block';
        } else {
            rehabOptions.style.display = 'none';
        }
    }
</script>


</body>
</html>
