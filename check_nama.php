<?php
session_start();

// Menghubungkan ke config.php
require 'config.php';

// Ambil data yang dikirim melalui metode POST
if (isset($_POST['nama_tersangka'])) {
    $nama_tersangka = $_POST['nama_tersangka'];

    // Debugging: Lihat apa yang diterima
    error_log("Nama tersangka yang diterima: " . $nama_tersangka); 

    // Query untuk mengecek apakah nama tersangka ada
    $sql = "SELECT * FROM data_tersangka WHERE nama_tersangka = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nama_tersangka);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Ambil data tersangka
        $data_tersangka = $result->fetch_assoc();
        // Kirim respons dengan nama yang sudah ada
        echo json_encode([
            'exists' => true,
            'id' => $data_tersangka['id'],
            'gambar' => 'uploads/' . $data_tersangka['gambar'] // Ambil path gambar
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Nama tersangka tidak diterima.']);
}


$conn->close();
?>
