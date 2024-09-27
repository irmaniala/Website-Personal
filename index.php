<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Periksa apakah pengguna sudah login, jika tidak maka redirect ke login.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">

</head>
<body>

    <!-- Bagian Header -->
    <header>
    <div class="navbar">
        <div class="navbar-logo">
            <!-- Tambahkan link Log Out di sini -->
            <img src="logo2.png" alt="Logo">
            <div class="navbar-text">Info Data Personal</div> <!-- Tambahkan teks di samping logo -->
        </div>

        <div class="buttons">
            <a href="tambah.php">Tambah Data</a>
            <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Buat Formulir</a>

            <!-- Dropdown yang muncul saat tombol Buat Formulir diklik -->
            <div class="dropdown" id="dropdownMenu">
                <a href="formulir.php">Formulir ada barang bukti</a>
                <a href="formulir2.php">Formulir tidak ada barang bukti</a>
            </div>

            <div class="buttons">
                <a href="logout.php" style="margin-right: 15px; text-decoration: none; color: #033969; font-weight: bold;">Log Out</a>
            </div>
        </div>

    </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2>Selamat Datang di Sistem Data</h2>
            <p>Kelola dan bagikan informasi dengan mudah dan cepat.</p>
            <a href="home.php">Mulai</a>
        </div>
    </section>

    <!-- Bagian Footer -->
    <footer>
        <p>&copy; 2024 Sistem Data. All Rights Reserved.</p>
    </footer>

<script src="js/index.js"></script>

</body>
</html>
