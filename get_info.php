<?php
// Menghubungkan ke config.php
require 'config.php';

// Mengambil ID dari parameter GET
$id = intval($_GET['id']);

// Query untuk mengambil detail orang berdasarkan ID
$sql = "SELECT * FROM data_tersangka WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Menampilkan informasi orang
    $row = $result->fetch_assoc();
    echo "<strong>NOMOR REGISTER:</strong> " . $row['nomor_register'] . "<br>";
    echo "<strong>NAMA TERSANGKA:</strong> " . $row['nama_tersangka'] . "<br>";
    echo "<strong>INSTANSI PENGIRIM:</strong> " . $row['instansi_pengirim'] . "<br>";
    echo "<strong>TANGGAL PENANGKAPAN:</strong> " . $row['tanggal_penangkapan'] . "<br>";
    echo "<strong>TANGGAL PERMOHONAN TAT:</strong> " . $row['tanggal_permohonan_tat'] . "<br>";
    echo "<strong>TANGGAL PELAKSANAAN ASESMEN TERPADU:</strong> " . $row['tanggal_pelaksanaan_asesmen_terpadu'] . "<br>";
    echo "<strong>TANGGAL REKOMENDASI:</strong> " . $row['tanggal_rekomendasi'] . "<br>";
    echo "<strong>JENIS NARKOTIKA:</strong> " . $row['jenis_narkotika'] . "<br>";
    echo "<strong>BERAT BARANG BUKTI:</strong> " . $row['berat_barang_bukti'] . "<br>";
    echo "<strong>PASAL YANG DISANGKAKAN:</strong> " . $row['pasal_yang_disangkakan'] . "<br>";
    echo "<strong>HASIL REKOMENDASI TAT:</strong> " . $row['hasil_rekomendasi'] . "<br>";
    echo "<strong>REKOMENDASI:</strong> " . $row['rekomendasi'] . "<br>";
    echo "<strong>JENIS REHAB:</strong> " . $row['jenis_rehab'] . "<br>";
    echo "<strong>PELAKSANAAN REKOMENDASI:</strong> " . $row['pelaksanaan_rekomendasi'] . "<br>";
    echo "<strong>ANGGARAN:</strong> " . $row['anggaran'] . "<br>";

    // Menampilkan gambar
    if (!empty($row['gambar'])) {
        echo "<strong>GAMBAR:</strong><br>";
        echo "<img src='" . $row['gambar'] . "' alt='Gambar Tersangka' style='max-width: 300px; height: auto;'><br>";
    } else {
        echo "<strong>GAMBAR:</strong> Tidak ada gambar yang diunggah.<br>";
    }
} else {
    echo "Data tidak ditemukan.";
}

$conn->close();
?>
