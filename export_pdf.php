<?php

// Sertakan library TCPDF
require_once('libraries/tcpdf/tcpdf.php');
require 'config.php';

ob_start();

// Ambil data filter dan pencarian dari POST
$minggu_filter = isset($_POST['minggu']) ? $_POST['minggu'] : '';
$bulan_awal = isset($_POST['bulan_awal']) ? $_POST['bulan_awal'] : '';
$bulan_akhir = isset($_POST['bulan_akhir']) ? $_POST['bulan_akhir'] : '';
$tahun_awal = isset($_POST['tahun_awal']) ? $_POST['tahun_awal'] : ''; 
$tahun_akhir = isset($_POST['tahun_akhir']) ? $_POST['tahun_akhir'] : ''; 
$jenis_narkotika_filter = isset($_POST['jenis_narkotika']) ? $_POST['jenis_narkotika'] : '';
$instansi_pengirim_filter = isset($_POST['instansi_pengirim']) ? $_POST['instansi_pengirim'] : '';
$cari = isset($_POST['cari']) ? trim($_POST['cari']) : '';

// Inisialisasi TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Aplikasi Anda');
$pdf->SetTitle('Data Tersangka');
$pdf->SetSubject('Daftar Data Tersangka');

// Tambahkan halaman baru
$pdf->AddPage('L');

// Set judul di header PDF
$pdf->SetFont('times', 'B', 14);
$pdf->Cell(0, 15, 'Daftar Data Tersangka', 0, 1, 'C');

// Set font untuk konten
$pdf->SetFont('times', '', 12);

// Query untuk mengambil data berdasarkan filter dan pencarian
$sql = "SELECT id, nomor_register, nama_tersangka, instansi_pengirim,
        tanggal_penangkapan, tanggal_pelaksanaan_asesmen_terpadu,
        jenis_narkotika, berat_barang_bukti, hasil_rekomendasi_tat, rekomendasi, 
        jenis_rehab, pelaksanaan_rekomendasi, anggaran 
        FROM data_tersangka WHERE 1=1";
$params = [];
$types = '';

if ($minggu_filter) {
    switch ($minggu_filter) {
        case 1: $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 1 AND 7"; break;
        case 2: $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 8 AND 14"; break;
        case 3: $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 15 AND 21"; break;
        case 4: $sql .= " AND DAY(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN 22 AND 31"; break;
    }
}

if ($bulan_awal && $bulan_akhir) {
    $sql .= " AND MONTH(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN ? AND ?";
    $params[] = intval($bulan_awal);
    $params[] = intval($bulan_akhir);
    $types .= 'ii';
} elseif ($bulan_awal) {
    $sql .= " AND MONTH(tanggal_pelaksanaan_asesmen_terpadu) = ?";
    $params[] = intval($bulan_awal);
    $types .= 'i';
}

if ($tahun_awal && $tahun_akhir) {
    $sql .= " AND YEAR(tanggal_pelaksanaan_asesmen_terpadu) BETWEEN ? AND ?";
    $params[] = intval($tahun_awal);
    $params[] = intval($tahun_akhir);
    $types .= 'ii';
} elseif ($tahun_awal) {
    $sql .= " AND YEAR(tanggal_pelaksanaan_asesmen_terpadu) = ?";
    $params[] = intval($tahun_awal);
    $types .= 'i';
}

if ($jenis_narkotika_filter) {
    $sql .= " AND jenis_narkotika = ?";
    $params[] = $jenis_narkotika_filter;
    $types .= 's';
}

if ($instansi_pengirim_filter) {
    $sql .= " AND instansi_pengirim = ?";
    $params[] = $instansi_pengirim_filter;
    $types .= 's';
}

if ($cari) {
    if (is_numeric($cari)) {
        $sql .= " AND nomor_register = ?";
        $params[] = $cari;
        $types .= 's';
    } else {
        $sql .= " AND nama_tersangka LIKE ?";
        $cari_like = '%' . $cari . '%';
        $params[] = $cari_like;
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

if ($result->num_rows > 0) {
    // Kode untuk menggenerate PDF dengan data yang diambil
    // Misalnya menggunakan FPDF atau library lainnya
} else {
    echo "Tidak ada data yang tersedia untuk diekspor.";
}

// Buat tabel untuk data
$tbl = '
<style>
    .table {
        border-collapse: collapse;
        width: 100%;
        font-size: 11px;
    }
    .table th, .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .table tr:hover {
        background-color: #e2e2e2;
    }
</style>
<table class="table">
    <thead>
        <tr>
            <th>Nomor Register</th>
            <th>Nama Tersangka</th>
            <th>Instansi Pengirim</th>
            <th>Tanggal Penangkapan</th>
            <th>Tanggal Pelaksanaan Asesmen Terpadu</th>
            <th>Jenis Narkotika</th>
            <th>Berat Barang Bukti (Gram)</th>
            <th>Hasil Rekomendasi TAT</th>
            <th>Rekomendasi</th>
            <th>Jenis Rehab</th>
            <th>Pelaksanaan Rekomendasi</th>
            <th>Anggaran</th>
        </tr>
    </thead>
    <tbody>';

// Isi tabel dengan data dari hasil query
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tbl .= '<tr>
                    <td>' . htmlspecialchars($row['nomor_register']) . '</td>
                    <td>' . htmlspecialchars($row['nama_tersangka']) . '</td>
                    <td>' . htmlspecialchars($row['instansi_pengirim']) . '</td>
                    <td>' . htmlspecialchars($row['tanggal_penangkapan']) . '</td>
                    <td>' . htmlspecialchars($row['tanggal_pelaksanaan_asesmen_terpadu']) . '</td>
                    <td>' . htmlspecialchars($row['jenis_narkotika']) . '</td>
                    <td>' . htmlspecialchars($row['berat_barang_bukti']) . '</td>
                    <td>' . htmlspecialchars($row['hasil_rekomendasi_tat']) . '</td>
                    <td>' . htmlspecialchars($row['rekomendasi']) . '</td>
                    <td>' . htmlspecialchars($row['jenis_rehab']) . '</td>
                    <td>' . htmlspecialchars($row['pelaksanaan_rekomendasi']) . '</td>
                    <td>' . htmlspecialchars($row['anggaran']) . '</td>
                </tr>';
    }
} else {
    $tbl .= '<tr><td colspan="12">Tidak ada data yang ditemukan.</td></tr>';
}

$tbl .= '</tbody></table>';

// Tampilkan tabel di PDF
$pdf->writeHTML($tbl, true, false, false, false, '');

// Output file PDF (akan otomatis diunduh)
$pdf->Output('data_tersangka.pdf', 'I');

$stmt->close();
$conn->close();
?>
