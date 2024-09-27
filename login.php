<?php
// Tampilkan semua error (untuk debugging, sebaiknya dimatikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
session_start();
require_once 'config.php';

// Jika pengguna sudah login, redirect ke halaman utama
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil input dan sanitasi
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Menggunakan prepared statements untuk mencegah SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Memeriksa apakah pengguna ada
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username_db, $hashed_password);
        $stmt->fetch();

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Password benar, set sesi
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username_db;
            header("Location: index.php");
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Username atau password salah.';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Data</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <div class="back-link">
            <a href="register.php">Belum punya akun? Register di sini.</a>
        </div>
    </div>

</body>
</html>
