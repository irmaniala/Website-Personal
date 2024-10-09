<?php
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
$bulan_filter = [];
$tahun_filter_awal = '';
$tahun_filter_akhir = '';
$jenis_narkotika_filter = '';
$instansi_pengirim_filter = '';
$cari = '';
$data_available = false;

// Ambil filter dan pencarian dari session jika ada
if (isset($_SESSION['minggu_filter'])) {
    $minggu_filter = $_SESSION['minggu_filter'];
}
if (isset($_SESSION['bulan_filter'])) {
    $bulan_filter = $_SESSION['bulan_filter']; 
}
if (isset($_SESSION['tahun_awal_filter'])) {
    $tahun_awal_filter = $_SESSION['tahun_awal_filter'];
}
if (isset($_SESSION['tahun_akhir_filter'])) {
    $tahun_akhir_filter = $_SESSION['tahun_akhir_filter'];
}
if (isset($_SESSION['jenis_narkotika_filter'])) {
    $jenis_narkotika_filter = $_SESSION['jenis_narkotika_filter'];
}
if (isset($_SESSION['instansi_pengirim_filter'])) {
    $instansi_pengirim_filter = $_SESSION['instansi_pengirim_filter'];
}
if (isset($_SESSION['cari'])) {
    $cari = $_SESSION['cari'];
}

// Memeriksa jenis formulir yang disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah form filter disubmit
    if (isset($_POST['filter_submit'])) {
        // Ambil input filter dan simpan ke session
        $minggu_filter = isset($_POST['minggu']) ? $_POST['minggu'] : '';
        $tahun_filter_awal = isset($_POST['tahun_awal']) ? $_POST['tahun_awal'] : '';
        $tahun_filter_akhir = isset($_POST['tahun_akhir']) ? $_POST['tahun_akhir'] : '';
        
        // Proses bulan
        if (isset($_POST['bulan']) && is_array($_POST['bulan'])) {
            $bulan_filter = array_filter($_POST['bulan']);
            if (count($bulan_filter) == 1) {
                $bulan_filter = [$bulan_filter[0], $bulan_filter[0]];
            }
            $_SESSION['bulan_filter'] = $bulan_filter;
        } else {
            // Reset filter bulan jika tidak ada yang dipilih
            unset($_SESSION['bulan_filter']);
        }

        $_SESSION['minggu_filter'] = $minggu_filter;
        $_SESSION['tahun_filter_awal'] = $tahun_filter_awal;
        $_SESSION['tahun_filter_akhir'] = $tahun_filter_akhir;
        $_SESSION['jenis_narkotika_filter'] = isset($_POST['jenis_narkotika']) ? $_POST['jenis_narkotika'] : '';
        $_SESSION['instansi_pengirim_filter'] = isset($_POST['instansi_pengirim']) ? $_POST['instansi_pengirim'] : '';
        
        // Reset pencarian
        $cari = '';
        unset($_SESSION['cari']);
    }

    // Cek apakah form pencarian disubmit
    if (isset($_POST['search_submit'])) {
        $cari = isset($_POST['cari']) ? trim($_POST['cari']) : '';
        $_SESSION['cari'] = $cari;

        // Reset filter
        $minggu_filter = '';
        $bulan_filter = [];
        $tahun_awal_filter = '';
        $tahun_akhir_filter = '';
        unset($_SESSION['minggu_filter']);
        unset($_SESSION['bulan_filter']);
        unset($_SESSION['tahun_awal_filter']);
        unset($_SESSION['tahun_akhir_filter']);
    }

    // Tentukan apakah data tersedia berdasarkan filter atau pencarian
    $data_available = !empty($minggu_filter) || !empty($bulan_filter) || !empty($tahun_filter_awal) || !empty($tahun_filter_akhir) || !empty($jenis_narkotika_filter) || !empty($instansi_pengirim_filter) || !empty($cari);
}

// Query untuk mengambil daftar orang dengan filter bulan, tahun, minggu, dan pencarian jika data tersedia
if ($data_available) {
    $sql = "SELECT id, nomor_register, nama_tersangka, jenis_narkotika, instansi_pengirim, tanggal_pelaksanaan_asesmen_terpadu 
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

    $bulan_filter_awal = isset($_SESSION['bulan_filter'][0]) ? $_SESSION['bulan_filter'][0] : null;
    $bulan_filter_akhir = isset($_SESSION['bulan_filter'][1]) ? $_SESSION['bulan_filter'][1] : null;
    if ($bulan_filter_awal && $bulan_filter_akhir) {
        // Jika rentang bulan dipilih
        $sql .= " AND MONTH(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN ? AND ?";
        $params[] = intval($bulan_filter_awal);
        $params[] = intval($bulan_filter_akhir);
        $types .= 'ii';
    } elseif ($bulan_filter_awal) {
        // Jika hanya bulan awal dipilih, tampilkan hanya bulan tersebut
        $sql .= " AND MONTH(tanggal_pelaksanaan_asesmen_terpadu) = ?";
        $params[] = intval($bulan_filter_awal);
        $types .= 'i';
    }    

    if ($tahun_filter_awal && $tahun_filter_akhir) {
        // Jika rentang tahun dipilih
        $sql .= " AND YEAR(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN ? AND ?";
        $params[] = intval($tahun_filter_awal);
        $params[] = intval($tahun_filter_akhir);
        $types .= 'ii';
    } elseif ($tahun_filter_awal) {
        // Jika hanya tahun awal dipilih, tampilkan hanya tahun tersebut
        $sql .= " AND YEAR(tanggal_pelaksanaan_asesmen_terpadu) = ?";
        $params[] = intval($tahun_filter_awal);
        $types .= 'i';
    }          

    // Filter barang bukti
    if ($jenis_narkotika_filter) {
        $sql .= " AND jenis_narkotika = ?";
        $params[] = $jenis_narkotika_filter;
        $types .= 's';
    }

    // Filter instansi pengirim
    if ($instansi_pengirim_filter) {
        $sql .= " AND instansi_pengirim = ?";
        $params[] = $instansi_pengirim_filter;
        $types .= 's';
    }

    if ($cari) {
        // Jika pencarian berupa nomor register, gunakan kondisi '=' untuk lebih spesifik
        if (is_numeric($cari)) {
            $sql .= " AND nomor_register = ?";
            $params[] = $cari;
            $types .= 's';
        } else {
            $sql .= " AND LOWER(nama_tersangka) LIKE LOWER(?)";
            $params[] = "%" . $cari . "%";
            $types .= 's';
    }
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

    <!-- Tambahkan kode untuk menampilkan alert -->
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?php echo $_SESSION['message']; ?>");
        </script>
        <?php unset($_SESSION['message']); // Hapus pesan setelah ditampilkan ?>
    <?php endif; ?>

    <!-- Container untuk kedua formulir -->
    <div class="form-container">
        <button class="collapsible">Filter Data</button>
        <div class="filter-content">
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
                
                <?php
                $bulan_nama = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                ?>
                <select name="bulan[]" id="bulan_awal">
                    <option value="">Pilih Bulan Awal</option>
                    <?php
                    foreach ($bulan_nama as $b => $nama_bulan) {
                        $selected = (is_array($bulan_filter) && $bulan_filter[0] == $b) ? 'selected' : '';
                        echo "<option value=\"$b\" $selected>$nama_bulan</option>";
                    }
                    ?>
                </select>

                <select name="bulan[]" id="bulan_akhir">
                    <option value="">Pilih Bulan Akhir (Opsional)</option>
                    <?php
                    foreach ($bulan_nama as $b => $nama_bulan) {
                        $selected = (is_array($bulan_filter) && isset($bulan_filter[1]) && $bulan_filter[1] == $b) ? 'selected' : '';
                        echo "<option value=\"$b\" $selected>$nama_bulan</option>";
                    }
                    ?>
                </select>

                <select name="tahun_awal" id="tahun_awal">
                    <option value="">Pilih Tahun Awal</option>
                    <?php
                    $tahun_sekarang = 2025; // Mendapatkan tahun saat ini
                    $tahun_filter_awal = isset($_POST['tahun_awal']) ? $_POST['tahun_awal'] : '';
                    // Dropdown untuk tahun awal dari 2020 hingga tahun saat ini
                    for ($t = 2020; $t <= $tahun_sekarang; $t++) {
                        $selected = ($tahun_filter_awal == $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>

                <select name="tahun_akhir" id="tahun_akhir">
                    <option value="">Pilih Tahun Akhir (Opsional)</option>
                    <?php
                    // Dropdown untuk tahun akhir dari 2020 hingga tahun saat ini + 1
                    for ($t = 2020; $t <= $tahun_sekarang; $t++) {
                        $selected = (isset($tahun_filter_akhir) && $tahun_filter_akhir == $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>

                <select name="jenis_narkotika" id="jenis_barang_bukti">
                    <option value="">Pilih Jenis Barang Bukti</option>
                    <option value="Sabu" <?php echo isset($_POST['jenis_narkotika']) && $_POST['jenis_narkotika'] === 'Sabu' ? 'selected' : ''; ?>>Sabu</option>
                    <option value="Ganja" <?php echo isset($_POST['jenis_narkotika']) && $_POST['jenis_narkotika'] === 'Ganja' ? 'selected' : ''; ?>>Ganja</option>
                    <option value="Linting Ganja" <?php echo isset($_POST['jenis_narkotika']) && $_POST['jenis_narkotika'] === 'Linting Ganja' ? 'selected' : ''; ?>>Linting Ganja</option>
                    <option value="Heroin" <?php echo isset($_POST['jenis_narkotika']) && $_POST['jenis_narkotika'] === 'Heroin' ? 'selected' : ''; ?>>Heroin</option>
                    <option value="Tembakau Sintetis" <?php echo isset($_POST['jenis_narkotika']) && $_POST['jenis_narkotika'] === 'Tembakau Sintetis' ? 'selected' : ''; ?>>Tembakau Sintetis</option>
                </select>

                <select name="instansi_pengirim" id="instansi_pengirim">
                    <option value="">Pilih Instansi Pengirim</option>
                    <option value="Polres Cimahi" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polres Cimahi' ? 'selected' : ''; ?>>Polres Cimahi</option>
                    <option value="Polres Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polres Bandung' ? 'selected' : ''; ?>>Polres Bandung</option>
                    <option value="Polresta Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polresta Bandung' ? 'selected' : ''; ?>>Polresta Bandung</option>
                    <option value="Polrestabes Bandung" <?php echo isset($_POST['instansi_pengirim']) && $_POST['instansi_pengirim'] === 'Polrestabes Bandung' ? 'selected' : ''; ?>>Polrestabes Bandung</option>
                </select>

                <button type="submit">Tampilkan Data</button>
            </div>
        </form>
        </div>

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
    <input type="hidden" name="minggu" value="<?php echo $minggu_filter; ?>">
    <input type="hidden" name="bulan_awal" value="<?php echo $bulan_filter_awal; ?>">
    <input type="hidden" name="bulan_akhir" value="<?php echo $bulan_filter_akhir; ?>">
    <input type="hidden" name="tahun_awal" value="<?php echo $tahun_filter_awal; ?>">
    <input type="hidden" name="tahun_akhir" value="<?php echo $tahun_filter_akhir; ?>">
    <input type="hidden" name="jenis_narkotika" value="<?php echo $jenis_narkotika_filter; ?>">
    <input type="hidden" name="instansi_pengirim" value="<?php echo $instansi_pengirim_filter; ?>">
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
