<?php
session_start();

// Periksa apakah pengguna sudah login, jika tidak maka redirect ke login.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Menghubungkan ke config.php
require 'config.php';

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data formulir berdasarkan ID
    $sql = "SELECT * FROM formulir WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Ambil data formulir
        $row = $result->fetch_assoc();
        $nama_tersangka = $row['nama_tersangka'];
        $pengaju = $row['pengaju'];
        $jabatan = $row['jabatan'];
        $instansi = $row['instansi'];
        $tanggal = $row['tanggal'];
    } else {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID tidak ditemukan.";
    exit();
}

// Ambil data berkas terkait
$sql_berkas = "SELECT * FROM berkas WHERE formulir_id = ?";
$stmt_berkas = $conn->prepare($sql_berkas);
$stmt_berkas->bind_param("i", $formulir_id);
$stmt_berkas->execute();
$result_berkas = $stmt_berkas->get_result();
$berkas_pengajuan = $result_berkas->fetch_all(MYSQLI_ASSOC);


$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Formulir Ada Barang Bukti</title>
    <link rel="stylesheet" href="css/formulir.css">
</head>
<body>

<div class="container">
    <h2>FORMULIR REGISTRASI KLIEN TAT PADA MASA PENANGKAPAN (APABILA DIDAPATKAN BARANG BUKTI)</h2>

    <a href="lihat_formulir.php">form</a>

    <form id="formTersangka" action="updateForm.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
        <table class="info-table">
            <tr>
                <td>Nama Tersangka</td>
                <td>:</td>
                <td>
                    <input type="text" name="nama_tersangka" id="namaTersangka" value="<?php echo htmlspecialchars($nama_tersangka); ?>" required>
                </td>
                <td colspan="3">
                    <div id="warning"></div>
                </td>
            </tr>
            <tr>
                <td>Yang Mengajukan Berkas</td>
                <td>:</td>
                <td><input type="text" name="pengaju" value="<?php echo htmlspecialchars($pengaju); ?>" required></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><input type="text" name="jabatan" value="<?php echo htmlspecialchars($jabatan); ?>" required></td>
            </tr>
            <tr>
                <td>Asal Instansi</td>
                <td>:</td>
                <td><input type="text" name="instansi" value="<?php echo htmlspecialchars($instansi); ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>:</td>
                <td><input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" required></td>
            </tr>
        </table>
        <table>
    <thead>
        <tr>
            <th>No</th>
            <th>Daftar Berkas Pengajuan Pada Masa Penangkapan</th>
            <th>Ada</th>
            <th>Tidak Ada</th>
            <th>Upload Berkas</th>
        </tr>
    </thead>
    <tbody>
        <?php
       // Daftar berkas yang akan ditampilkan
$daftar_berkas = [
    1 => "Surat Permohonan Asesmen Terpadu dari Penyidik kepada Ketua Tim Asesmen Terpadu Tingkat Nasional atau Tingkat Provinsi atau Tingkat Kabupaten/Kota",
    2 => "Fotokopi Kartu Identitas Tersangka (KTP atau Kartu Pelajar atau Kartu Mahasiswa dan Kartu Keluarga)",
    3 => "Laporan Polisi (LP) atau Laporan Kasus Narkotika (LKN)",
    4 => "Berita Acara Pemeriksaan Tersangka",
    5 => "Surat Perintah Penangkapan",
    6 => "Surat Perintah Penyitaan Barang Bukti",
    7 => "Berita Acara Penyitaan Barang Bukti",
    8 => "Hasil Pemeriksaan Laboratorium Sementara",
    9 => "Surat Keterangan Hasil Pemeriksaan Urine yang dikeluarkan oleh Fasilitas Kesehatan Milik Pemerintah dengan jangka waktu maksimal 3 x 24 jam setelah diterbitkan Surat Perintah Penangkapan dengan Kriteria :
        <ol type='a'>
            <li>Hasil Pemeriksaan Urin Positif atau Negatif apabila Berat Barang Bukti Kurang dari SEMA;</li>
            <li>Hasil Pemeriksaan Urin Positif Apabila Berat Barang Bukti Lebih dari SEMA.</li>
        </ol>",
    10 => "Data dukung elektronik seperti <i>screenshoot</i> percakapan, pembelian barang, transfer (bila ada)",
];

        // Loop setiap berkas pengajuan
        foreach ($daftar_berkas as $index => $judul_berkas) {
            echo "<tr>";
            echo "<td>" . $index . ".</td>";
            echo "<td>" . $judul_berkas . "</td>";

            $berkas_found = false;
            foreach ($berkas_pengajuan as $berkas) {
                if ($berkas['berkas_id'] == $index) {
                    $berkas_found = true;
                    $ada_checked = $berkas['ada'] ? 'checked' : '';
                    $tidak_ada_checked = $berkas['tidak_ada'] ? 'checked' : '';
                    $file_name = htmlspecialchars($berkas['file_name']); // Menampilkan nama file
                    break; // Keluar dari loop jika ditemukan
                }
            }

            if (!$berkas_found) {
                $ada_checked = '';
                $tidak_ada_checked = '';
                $file_name = '';
            }

            echo "<td><input type=\"checkbox\" name=\"berkas_tidak_ada[$index][ada]\" value=\"1\" $ada_checked></td>";
            echo "<td><input type=\"checkbox\" name=\"berkas_tidak_ada[$index][tidak_ada]\" value=\"1\" $tidak_ada_checked></td>";
            echo "<td>
                <input type=\"file\" name=\"file_$index\" accept=\"image/jpeg, image/jpg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document\">
                <input type=\"hidden\" name=\"old_file_$index\" value=\"$file_name\">";
            if ($file_name) {
                echo "<br><small>File saat ini: $file_name</small>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

        <button type="submit" class="submit-button">Simpan Perubahan</button>
    </form>
</div>

<script src="js/form.js"></script>

</body>
</html>