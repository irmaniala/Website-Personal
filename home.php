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

// Menghubungkan ke config.php
require 'config.php';

// Inisialisasi variabel
$minggu_filter = '';
$bulan_filter = '';
$tahun_filter = '';
$cari = '';
$data_available = false;

// Ambil filter dan pencarian dari session jika ada
if (isset($_SESSION['minggu_filter'])) {
    $minggu_filter = $_SESSION['minggu_filter'];
}
if (isset($_SESSION['bulan_filter'])) {
    $bulan_filter = $_SESSION['bulan_filter'];
}
if (isset($_SESSION['tahun_filter'])) {
    $tahun_filter = $_SESSION['tahun_filter'];
}
if (isset($_SESSION['cari'])) {
    $cari = $_SESSION['cari'];
}

// Memeriksa jenis formulir yang disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah form filter disubmit
    if (isset($_POST['filter_submit'])) {
        // Ambil input filter
        $minggu_filter = isset($_POST['minggu']) ? $_POST['minggu'] : '';
        $bulan_filter = isset($_POST['bulan']) ? $_POST['bulan'] : '';
        $tahun_filter = isset($_POST['tahun']) ? $_POST['tahun'] : '';

        // Simpan filter di session
        $_SESSION['minggu_filter'] = $minggu_filter;
        $_SESSION['bulan_filter'] = $bulan_filter;
        $_SESSION['tahun_filter'] = $tahun_filter;

        // Reset pencarian
        $cari = '';
        unset($_SESSION['cari']);
    }

    // Cek apakah form pencarian disubmit
    if (isset($_POST['search_submit'])) {
        // Ambil input pencarian
        $cari = isset($_POST['cari']) ? trim($_POST['cari']) : '';

        // Simpan pencarian di session
        $_SESSION['cari'] = $cari;

        // Reset filter
        $minggu_filter = '';
        $bulan_filter = '';
        $tahun_filter = '';
        unset($_SESSION['minggu_filter']);
        unset($_SESSION['bulan_filter']);
        unset($_SESSION['tahun_filter']);
    }

    // Tentukan apakah data tersedia berdasarkan filter atau pencarian
    if ($minggu_filter || $bulan_filter || $tahun_filter ||  $cari) {
        $data_available = true;
    } else {
        $data_available = false;
    }
}

// Query untuk mengambil daftar orang dengan filter bulan, tahun, minggu, dan pencarian jika data tersedia
if ($data_available) {
    $sql = "SELECT id, nomor_register, nama_tersangka, instansi_pengirim, tanggal_pelaksanaan_asesmen_terpadu 
            FROM data_tersangka WHERE 1=1";

    $params = [];
    $types = '';

    // Menambahkan filter Minggu berdasarkan rentang tanggal
    if ($minggu_filter) {
        // Tambahkan kondisi untuk rentang minggu
        switch ($minggu_filter) {
            case 1:
                $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 1 AND 7";
                break;
            case 2:
                $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 8 AND 14";
                break;
            case 3:
                $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 15 AND 21";
                break;
            case 4:
                $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 22 AND 31";
                break;
        }
    }

    if ($bulan_filter) {
        $sql .= " AND MONTH(tanggal_pelaksanaan_asesmen_terpadu) = ?";
        $params[] = intval($bulan_filter);
        $types .= 'i';
    }

    if ($tahun_filter) {
        $sql .= " AND YEAR(tanggal_pelaksanaan_asesmen_terpadu) = ?";
        $params[] = intval($tahun_filter);
        $types .= 'i';
    }

    if ($cari) {
        $sql .= " AND (nomor_register LIKE ? OR nama_tersangka LIKE ?)";
        $cari_like = '%' . $cari . '%';
        $params[] = $cari_like;
        $params[] = $cari_like;
        $types .= 'ss';
    }

    $sql .= " ORDER BY tanggal_pelaksanaan_asesmen_terpadu ASC, nomor_register ASC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data_available = $result->num_rows > 0;

    $stmt->close();
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="stylesheet" href="css/home.css">

</head>
<body>

<!-- Tombol Home berbentuk panah di pojok kiri atas -->
<a href="index.php" class="home-button"></a>

<div class="container">
    <h1>Info Data Personal</h1>

    <!-- Container untuk kedua formulir -->
    <div class="form-container">
        <!-- Formulir Filter Bulan, Tahun, dan Minggu -->
        <form action="" method="POST" class="filter-form">
            <input type="hidden" name="filter_submit" value="1">
            <div class="form-inline">

                <select name="minggu" id="minggu">
                    <option value="">Pilih Minggu</option>
                    <option value="1" <?php echo $minggu_filter == 1 ? 'selected' : ''; ?>>Minggu 1</option>
                    <option value="2" <?php echo $minggu_filter == 2 ? 'selected' : ''; ?>>Minggu 2</option>
                    <option value="3" <?php echo $minggu_filter == 3 ? 'selected' : ''; ?>>Minggu 3</option>
                    <option value="4" <?php echo $minggu_filter == 4 ? 'selected' : ''; ?>>Minggu 4</option>
                </select>
                
                <select name="bulan" id="bulan">
                    <option value="">Pilih Bulan</option>
                    <?php
                    // Generate dropdown untuk bulan
                    $bulan = range(1, 12);
                    $bulan_nama = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    foreach ($bulan as $b) {
                        $selected = $bulan_filter == $b ? 'selected' : '';
                        echo "<option value=\"$b\" $selected>{$bulan_nama[$b]}</option>";
                    }
                    ?>
                </select>

                <select name="tahun" id="tahun">
                    <option value="">Pilih Tahun</option>
                    <?php
                    // Generate dropdown untuk tahun dari 2020 hingga tahun saat ini + 1
                    $tahun_sekarang = date('Y') + 1; // Menambahkan satu tahun ke depan
                    for ($t = 2020; $t <= $tahun_sekarang; $t++) {
                        $selected = $tahun_filter == $t ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>

                <button type="submit">Tampilkan Data</button>
            </div>
        </form>

        <!-- Formulir Pencarian -->
        <form action="" method="POST" class="search-form">
            <input type="hidden" name="search_submit" value="1">
            <div class="search-container">
                <input type="text" class="search-input" name="cari" id="cari" placeholder="Nomor Register atau Nama Tersangka" value="<?php echo isset($_POST['cari']) ? htmlspecialchars($_POST['cari']) : ''; ?>">
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
    <input type="hidden" name="minggu" value="<?php echo $minggu_filter; ?>">
    <input type="hidden" name="bulan" value="<?php echo $bulan_filter; ?>">
    <input type="hidden" name="tahun" value="<?php echo $tahun_filter; ?>">
    <input type="hidden" name="cari" value="<?php echo htmlspecialchars($cari); ?>">
    
    <button type="submit" name="export_pdf">Ekspor ke PDF</button>
</form>
</div>

</body>
</html>

<script src="js/home.js"></script>

<?php
$conn->close();
?>
