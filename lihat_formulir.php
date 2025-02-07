<?php
session_start();

// Periksa apakah pengguna sudah login, jika tidak maka redirect ke login.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Menghubungkan ke config.php
require 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$instansi_filter = isset($_GET['instansi']) ? trim($_GET['instansi']) : '';
$tanggal_filter = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : '';

$sql = "SELECT id, nama_tersangka, instansi, tanggal FROM formulir WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (nama_tersangka LIKE ? OR instansi LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = &$searchTerm;
    $params[] = &$searchTerm;
    $types .= "ss";
}

if (!empty($instansi_filter)) {
    $sql .= " AND instansi = ?";
    $params[] = &$instansi_filter;
    $types .= "s";
}

if (!empty($tanggal_filter)) {
    $sql .= " AND tanggal = ?";
    $params[] = &$tanggal_filter;
    $types .= "s";
}

$sql .= " ORDER BY id ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Formulir</title>
    <link rel="stylesheet" href="css/lihat.css"> 
</head>
<body>

<!-- Tombol Home berbentuk panah di pojok kiri atas -->

<div class="container">
    <h1>Daftar Formulir Ada Barang Bukti</h1>
    <a href="index.php" class="home-button">Home</a>
    
    <!-- Form Filter -->
    <div class="form-container">
    <form method="GET" action="" class="filter-form">
        <select name="instansi" class="filter">
            <option value="">Pilih Instansi</option>
            <?php
            $instansiQuery = "SELECT DISTINCT instansi FROM formulir ORDER BY instansi ASC";
            $instansiResult = $conn->query($instansiQuery);
            while ($row = $instansiResult->fetch_assoc()) {
                $selected = ($row['instansi'] == $instansi_filter) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($row['instansi']) . "' $selected>" . htmlspecialchars($row['instansi']) . "</option>";
            }
            ?>
        </select>
        
        <input type="date" name="tanggal" class="filter" value="<?php echo htmlspecialchars($tanggal_filter); ?>">
        
        <button type="submit" name="filter">Filter</button>
    </form>

    <!-- Form Pencarian -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" class="cari" placeholder="Cari Nama Tersangka" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" name="cari">Cari</button>
    </form>
</div>
    
    <!-- Tabel untuk menampilkan daftar formulir -->
    <table class="results-table">
        <thead>
            <tr>
                <th>NAMA TERSANGKA</th>
                <th>INSTANSI</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $nama_tersangka = !empty(trim($row['nama_tersangka'])) ? htmlspecialchars($row['nama_tersangka']) : "Data Tidak Tersedia";
                    echo "<tr>";
                    echo "<td style='width:40%;'>";
                    if ($nama_tersangka !== "Data Tidak Tersedia") {
                        echo "<a href='detail_form.php?formulir_id=" . htmlspecialchars($row['id']) . "'>$nama_tersangka</a>";
                    } else {
                        echo $nama_tersangka;
                    }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['instansi']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' class='no-data'>Tidak ada data yang sesuai.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
