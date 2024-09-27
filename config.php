<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Detail koneksi database
$servername = "localhost"; // Biasanya "localhost"
$db_username = "root"; // Ganti dengan username MySQL Anda
$db_password = ""; // Ganti dengan password MySQL Anda
$dbname = "data_laporan_db";

// Membuat koneksi
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengatur charset untuk koneksi
$conn->set_charset("utf8mb4");
?>