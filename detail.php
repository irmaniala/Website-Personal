<?php
// Menghubungkan ke config.php
require 'config.php';

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    echo "<script>alert('Data berhasil diubah!');</script>";
    echo "<script>window.location.href = 'detail.php?id=" . htmlspecialchars($_GET['id']) . "';</script>";
    exit();
}

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

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
        $gambar = $row['gambar']; // Ambil path gambar
    } else {
        echo "Data tidak ditemukan.";
        exit();
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
    <title>Detail</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/detail.css">
    
</head>
<body>

<div class="container">
    <h1>Data Personal Tersangka</h1>
        <!-- Tabel untuk menampilkan detail -->
        <table class="details-table">
            <tr class="no-border">
            <td colspan="2" class="photo">
            <?php (!empty($gambar) && file_exists($gambar)) ?>
                <img src="uploads/<?php echo $gambar; ?>" alt="Foto Tersangka">
                </td>
            </tr>
            <tr>
                <th>NOMOR REGISTER</th>
                <td><?php echo $nomor_register; ?></td>
            </tr>
            <tr>
                <th>NAMA TERSANGKA</th>
                <td><?php echo $nama_tersangka; ?></td>
            </tr>
            <tr>
                <th>INSTANSI PENGIRIM</th>
                <td><?php echo $instansi_pengirim; ?></td>
            </tr>
            <tr>
                <th>TANGGAL PENANGKAPAN</th>
                <td><?php echo $tanggal_penangkapan; ?></td>
            </tr>
            <tr>
                <th>TANGGAL PERMOHONAN TAT</th>
                <td><?php echo $tanggal_permohonan_tat; ?></td>
            </tr>
            <tr>
                <th>TANGGAL PELAKSANAAN ASESMEN TERPADU</th>
                <td><?php echo $tanggal_pelaksanaan_asesmen_terpadu; ?></td>
            </tr>
            <tr>
                <th>TANGGAL REKOMENDASI</th>
                <td><?php echo $tanggal_rekomendasi; ?></td>
            </tr>
            <tr>
                <th>JENIS NARKOTIKA</th>
                <td><?php echo $jenis_narkotika; ?></td>
            </tr>
            <tr>
                <th>BERAT BARANG BUKTI (Gram)</th>
                <td><?php echo $berat_barang_bukti; ?></td>
            </tr>
            <tr>
                <th>PASAL YANG DISANGKAKAN</th>
                <td><?php echo $pasal_yang_disangkakan; ?></td>
            </tr>
            <tr>
                <th>HASIL REKOMENDASI TAT</th>
                <td><?php echo $hasil_rekomendasi_tat; ?></td>
            </tr>
            <tr>
                <th>REKOMENDASI</th>
                <td><?php echo $rekomendasi; ?></td>
            </tr>
            <tr>
                <th>JENIS REHAB</th>
                <td><?php echo $jenis_rehab; ?></td>
            </tr>
            <tr>
                <th>PELAKSANAAN REKOMENDASI</th>
                <td><?php echo $pelaksanaan_rekomendasi; ?></td>
            </tr>
            <tr>
                <th>ANGGARAN</th>
                <td><?php echo $anggaran; ?></td>
            </tr>
        </table>
        <div class="button-group">
        <a href="edit.php?id=<?php echo $id; ?>" class="btn">Edit</a>
        <a href="delete.php?id=<?php echo $id; ?>" class="btn delete-btn">Hapus</a>
        <a href="index.php" class="btn back-btn">Selesai</a>
    </div>
    </div>
</div>

</body>
</html>