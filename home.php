<?php
session_start();

require 'config.php';
include 'functions.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$cari = '';
$tanggal_mulai = '';
$tanggal_akhir = '';
$jenis_narkotika = '';
$instansi_pengirim = '';
$data_available = false;

if (isset($_SESSION['cari'])) {
    $cari = $_SESSION['cari'];
}
if (isset($_SESSION['tanggal_mulai'])) {
    $tanggal_mulai = $_SESSION['tanggal_mulai'];
}
if (isset($_SESSION['tanggal_akhir'])) {
    $tanggal_akhir = $_SESSION['tanggal_akhir'];
}
if (isset($_SESSION['jenis_narkotika'])) {
    $jenis_narkotika = $_SESSION['jenis_narkotika'];
}
if (isset($_SESSION['instansi_pengirim'])) {
    $instansi_pengirim = $_SESSION['instansi_pengirim'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cari = isset($_POST['cari']) ? trim($_POST['cari']) : '';
    $tanggal_mulai = isset($_POST['tanggal_mulai']) ? trim($_POST['tanggal_mulai']) : '';
    $tanggal_akhir = isset($_POST['tanggal_akhir']) ? trim($_POST['tanggal_akhir']) : '';
    $jenis_narkotika = isset($_POST['jenis_narkotika']) ? trim($_POST['jenis_narkotika']) : '';
    $instansi_pengirim = isset($_POST['instansi_pengirim']) ? trim($_POST['instansi_pengirim']) : '';

    $_SESSION['cari'] = $cari;
    $_SESSION['tanggal_mulai'] = $tanggal_mulai;
    $_SESSION['tanggal_akhir'] = $tanggal_akhir;
    $_SESSION['jenis_narkotika'] = $jenis_narkotika;
    $_SESSION['instansi_pengirim'] = $instansi_pengirim;
}

    $data_available = !empty($cari) || (!empty($tanggal_mulai) && !empty($tanggal_akhir)) || !empty($jenis_narkotika) || !empty($instansi_pengirim);


    if ($data_available) {
        $sql = "SELECT id, nomor_register, nama_tersangka, jenis_narkotika, instansi_pengirim, tanggal_pelaksanaan_asesmen_terpadu 
                FROM data_tersangka WHERE 1=1";
    
        $params = [];
        $types = '';
    
        // Pencarian berdasarkan 'cari'
        if ($cari) {
            if (is_numeric($cari)) {
                $sql .= " AND nomor_register = ?";
                $params[] = $cari;
                $types .= 's';
            } else {
                $sql .= " AND LOWER(nama_tersangka) LIKE LOWER(?)";
                $params[] = $cari . "%";
                $types .= 's';
            }
        }
    
        // Filter berdasarkan tanggal mulai dan tanggal akhir
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $sql .= " AND tanggal_pelaksanaan_asesmen_terpadu BETWEEN ? AND ?";
            $params[] = $tanggal_mulai;
            $params[] = $tanggal_akhir;
            $types .= 'ss';
        }
        
        if (!empty($jenis_narkotika)) {
            $sql .= " AND jenis_narkotika = ?";
            $params[] = $jenis_narkotika;
            $types .= 's';
        }
    
        // Filter berdasarkan instansi pengirim
        if (!empty($instansi_pengirim)) {
            $sql .= " AND LOWER(instansi_pengirim) LIKE LOWER(?)";
            $params[] = "%" . $instansi_pengirim . "%";
            $types .= 's';
        }
    
        // Urutkan hasil pencarian berdasarkan tanggal dan nomor register
        $sql .= " ORDER BY tanggal_pelaksanaan_asesmen_terpadu ASC, nomor_register ASC";
    
        // Persiapkan statement SQL
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
    
        // Bind parameter jika ada
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        // Eksekusi query
        $stmt->execute();
        $result = $stmt->get_result();
        $data_available = $result->num_rows > 0;
    
        $stmt->close();
    }
        
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/home.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<input type="checkbox" id="check">
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="tambah.php">Riwayat Rehab</a></li>
        <li><a href="home.php">Rekap Rehab</a></li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn">Formulir TAT</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="formulir.php">Formulir ada barang bukti</a>
                <a href="formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn">Riwayat Formulir</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="lihat_formulir.php">Formulir ada barang bukti</a>
                <a href="lihat_formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<header>
    <div class="container">
        <h1><a href="">Berantas</a></h1>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="tambah.php">Riwayat Rehab</a></li>
            <li><a href="home.php" class="<?php echo isActive('home.php'); ?>">Rekap Rehab</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">Formulir TAT</a>
                <div class="dropdown-content" id="dropdownMenu">
                    <a href="formulir.php">Formulir ada barang bukti</a>
                    <a href="formulir2.php">Formulir tidak ada barang bukti</a>
                </div>
            </li>
            <li class="dropdown">
            <a href="#" class="dropdown-btn">Riwayat Formulir</a>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="lihat_formulir.php">Formulir ada barang bukti</a>
                <a href="lihat_formulir2.php">Formulir tidak ada barang bukti</a>
            </div>
        </li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        
        <!-- menu mobile -->
        <label for="check" class="mobile-menu"><i class="fas fa-bars fa-2x"></i></label>
    </div>
</header>

<div class="container">
    <h1>Info Data Tersangka Rehabilitasi</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?php echo $_SESSION['message']; ?>");
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="form-container">

        <!-- Formulir Filter Tanggal -->
        <form action="" method="POST" class="filter-form">

            <input type="hidden" name="filter_submit" value="1">
            <div class="filter-container">
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" name="tanggal_mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>" class="tanggal">
                <label for="tanggal_akhir">Tanggal Akhir:</label>
                <input type="date" name="tanggal_akhir" value="<?php echo htmlspecialchars($tanggal_akhir); ?>" class="tanggal">

            <select name="jenis_narkotika" class="filter">
                <option value="">-- Pilih Jenis Narkotika --</option>
                <option value="Ganja" <?php echo ($jenis_narkotika == "Ganja") ? 'selected' : ''; ?>>Ganja</option>
                <option value="Sabu" <?php echo ($jenis_narkotika == "Sabu") ? 'selected' : ''; ?>>Sabu</option>
                <option value="Heroin" <?php echo ($jenis_narkotika == "Heroin") ? 'selected' : ''; ?>>Heroin</option>
                <option value="Linting Ganja" <?php echo ($jenis_narkotika == "Linting Ganja") ? 'selected' : ''; ?>>Linting Ganja</option>
                <option value="Tembakau Sintetis" <?php echo ($jenis_narkotika == "Tembakau Sintetis") ? 'selected' : ''; ?>>Tembakau Sintetis</option>
                <option value="Tidak Ada" <?php echo ($jenis_narkotika == "Tidak Ada") ? 'selected' : ''; ?>>Tidak Ada</option>
            </select>

            <select name="instansi_pengirim" id="instansi_pengirim" class="filter">
                <option value="">-- Pilih Instansi Pengirim --</option>
                <option value="Polres Cimahi" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polres Cimahi' ? 'selected' : ''; ?>>Polres Cimahi</option>
                <option value="Polres Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polres Bandung' ? 'selected' : ''; ?>>Polres Bandung</option>
                <option value="Polresta Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polresta Bandung' ? 'selected' : ''; ?>>Polresta Bandung</option>
                <option value="Polrestabes Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polrestabes Bandung' ? 'selected' : ''; ?>>Polrestabes Bandung</option>
            </select>
            
            <button type="submit">Filter</button>
            </div>

        </form>

        <form action="" method="POST" class="search-form">
            <input type="hidden" name="search_submit" value="1">
            <div class="search-container">
                <input type="text" class="search-input" name="cari" id="cari" placeholder="Nomor Register atau Nama Tersangka" value="<?php echo htmlspecialchars($cari); ?>">
                <button type="submit">Cari</button>
            </div>
        </form>
    </div>

    <?php if ($data_available) { ?>
        <table class="results-table">
            <thead>
                <tr>
                    <th>NOMOR REGISTER</th>
                    <th>NAMA TERSANGKA</th>
                    <th>JENIS NARKOTIKA</th>
                    <th>INSTANSI PENGIRIM</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nomor_register']) . "</td>";
                    echo "<td><a href='detail.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nama_tersangka']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['jenis_narkotika']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['instansi_pengirim']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='no-data'>Tidak ada data untuk filter atau pencarian ini.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    
    <?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $data_available === false) { ?>
        <div class="no-data">Tidak ada data yang ditemukan untuk filter yang dipilih.</div>
    <?php } else { ?>
        <div class="no-data">Silakan gunakan filter atau pencarian untuk menampilkan data.</div>
    <?php } ?>

    <form action="export_pdf.php" method="POST" target="_blank" class="form-inline">
    <!-- Hidden inputs untuk mengirim filter dan pencarian -->
    <input type="hidden" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>">
    <input type="hidden" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
    <input type="hidden" name="jenis_narkotika" value="<?php echo $jenis_narkotika; ?>">
    <input type="hidden" name="instansi_pengirim" value="<?php echo $instansi_pengirim; ?>">
    <input type="hidden" name="cari" value="<?php echo htmlspecialchars($cari); ?>">
    
    <button type="submit" name="export_pdf">Ekspor ke PDF</button>
</form>
</div>

<script src="js/jquery-3.7.1.js"></script>
<script src="js/home.js"></script>
</body>
</html>


<?php
$conn->close();
?>
