<?php
// Redirect ke login.php
header("Location: login.php");
exit;
?>

<?php
// Menghubungkan ke config.php
require 'config.php';

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data orang berdasarkan ID
    $sql = "DELETE FROM orang WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect kembali ke halaman daftar (index.php) setelah penghapusan
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "ID tidak ditemukan.";
}

$conn->close();
?>
