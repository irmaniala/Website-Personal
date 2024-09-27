<?php
// Tampilkan semua error (untuk debugging, sebaiknya dimatikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai sesi
session_start();
require_once 'config.php';

// Jika pengguna sudah login, redirect ke halaman utama
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil input dan sanitasi
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (!validatePassword($password)) {
        $error = 'Password harus mengandung minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan simbol.';
    } else {
        // Memeriksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Menyimpan pengguna baru
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Anda bisa <a href="login.php">login</a> sekarang.';
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }

        $stmt->close();
    }

    $conn->close();
}

// Fungsi untuk validasi password
function validatePassword($password) {
    // Regex: minimal 8 karakter, setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu simbol
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Data</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">

</head>
<body>

    <div class="register-container">
        <h2>Registrasi</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php" onsubmit="return validatePassword()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required oninput="checkPasswordStrength()">
            <small id="passwordHelp" class="password-requirements">
                Password harus mengandung minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan simbol.
            </small>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <input type="submit" value="Register">
        </form>
        <div class="back-link">
            <a href="login.php">Sudah punya akun? Login di sini.</a>
        </div>
    </div>

<script src="js/register.js"></script>

</body>
</html>
