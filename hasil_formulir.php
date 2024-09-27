<?php
// Tampilkan semua error (untuk debugging, sebaiknya dimatikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Menghubungkan ke config.php
require 'config.php';

// Set charset untuk koneksi
$conn->set_charset("utf8mb4");

// Set lokasi penyimpanan file
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true); // Buat folder jika belum ada
}

// Definisikan berkas yang ada di tabel (sesuaikan jumlah dengan loop)
$berkas_pengajuan = [
    1 => "Surat Permohonan Asesmen Terpadu dari Penyidik kepada Ketua Tim Asesmen Terpadu Tingkat Provinsi atau Tingkat Kabupaten/Kota",
    2 => "Fotocopy Kartu Identitas Tersangka (KTP atau Kartu Pelajar atau Kartu Mahasiswa dan Kartu Keluarga)",
    3 => "Laporan Informasi",
    4 => "Berita Acara Interograsi",
    5 => "Surat Perintah Penangkapan",
    6 => "Surat Keterangan Hasil Pemeriksaan Urine yang dikeluarkan oleh Fasilitas Kesehatan Milik Pemerintah (seperti Labkesda, Klinik Polres, IPWL BNN, IPWL BNNP, IPWL BNN Kabupaten/Kota, Puskesmas IPWL, RSUD, dll) dengan jangka waktu maksimal 3 x 24 jam setelah ditangkap, dengan hasil <strong>positif</strong>",
    7 => "Data dukung elektronik seperti screenshoot percakapan, pembelian barang, transfer (bila ada).",
];

// Ambil data formulir dari POST
$nama_tersangka = isset($_POST['nama_tersangka']) ? $conn->real_escape_string($_POST['nama_tersangka']) : '';
$pengaju = isset($_POST['pengaju']) ? $conn->real_escape_string($_POST['pengaju']) : '';
$jabatan = isset($_POST['jabatan']) ? $conn->real_escape_string($_POST['jabatan']) : '';
$instansi = isset($_POST['instansi']) ? $conn->real_escape_string($_POST['instansi']) : '';
$tanggal = isset($_POST['tanggal']) ? $conn->real_escape_string($_POST['tanggal']) : '';

// Simpan data formulir ke database (tabel 'formulir_tidak_ada')
$stmt_formulir_tidak_ada = $conn->prepare("INSERT INTO formulir_tidak_ada (nama_tersangka, pengaju, jabatan, instansi, tanggal) VALUES (?, ?, ?, ?, ?)");
$stmt_formulir_tidak_ada->bind_param("sssss", $nama_tersangka, $pengaju, $jabatan, $instansi, $tanggal);
$stmt_formulir_tidak_ada->execute();
$formulir_id = $stmt_formulir_tidak_ada->insert_id; // Gunakan variabel yang benar
$stmt_formulir_tidak_ada->close();

// Simpan setiap berkas yang diunggah
for ($i = 1; $i <= count($berkas_pengajuan); $i++) { // Sesuaikan loop dengan jumlah berkas
    // Cek apakah 'ada' atau 'tidak_ada' diisi
    $ada = isset($_POST['berkas_tidak_ada'][$i]['ada']) ? 1 : 0;
    $tidak_ada = isset($_POST['berkas_tidak_ada'][$i]['tidak_ada']) ? 1 : 0;

    // Handle file upload untuk berkas ini
    $file_name = '';
    $file_path = '';
    if (isset($_FILES["file_$i"]) && $_FILES["file_$i"]['error'] === UPLOAD_ERR_OK) {
        // Sanitasi nama berkas
        $file_name = basename($_FILES["file_$i"]['name']);
        // Tambahkan timestamp untuk mencegah konflik nama berkas
        $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file_name);
        $file_path = $upload_dir . $file_name;

        // Validasi jenis file (opsional tapi disarankan)
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf']; // Sesuaikan dengan kebutuhan Anda
        if (in_array($_FILES["file_$i"]['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES["file_$i"]['tmp_name'], $file_path)) {
                // File berhasil diupload
            } else {
                $file_path = ''; // Reset jika gagal upload
                // Optional: Tambahkan penanganan error
            }
        } else {
            $file_path = ''; // Reset jika jenis file tidak diizinkan
            // Optional: Tambahkan penanganan error
        }
    }

    // Insert data berkas ke database dengan formulir_id
    $stmt = $conn->prepare("INSERT INTO berkas_tidak_ada (formulir_id, nomor, deskripsi, ada, tidak_ada, file_name, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iississ", $formulir_id, $i, $berkas_pengajuan[$i], $ada, $tidak_ada, $file_name, $file_path);
    $stmt->execute();
    $stmt->close();
}

// Ambil semua data berkas dari database untuk formulir ini
$stmt_select = $conn->prepare("SELECT * FROM berkas_tidak_ada WHERE formulir_id = ? ORDER BY nomor ASC");
$stmt_select->bind_param("i", $formulir_id);
$stmt_select->execute();
$result_berkas = $stmt_select->get_result();

$berkas_tidak_ada = [];
if ($result_berkas->num_rows > 0) {
    while ($row = $result_berkas->fetch_assoc()) {
        $berkas_tidak_ada[$row['nomor']] = $row;  // Simpan berkas dengan key nomor
    }
}
$stmt_select->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Formulir</title>
    <link rel="stylesheet" href="css/prosesFormulir.css">
</head>
<body>

<div class="container">

    <h3>FORMULIR REGISTRASI KLIEN TAT PADA MASA PENANGKAPAN (APABILA DIDAPATKAN BARANG BUKTI)</h3>

    <table class="info-table">
    <tr>
            <td>Nama Tersangka</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($nama_tersangka); ?></td>
        </tr>
        <tr>
            <td>Yang Mengajukan Berkas</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($pengaju); ?></td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($jabatan); ?></td>
        </tr>
        <tr>
            <td>Asal Instansi</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($instansi); ?></td>
        </tr>
        <tr>
            <td>Tanggal Pengajuan</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($tanggal); ?></td>
        </tr>
    </table>

    <table class="form-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Daftar Berkas Pengajuan</th>
                <th>Ada</th>
                <th>Tidak Ada</th>
                <th>Upload Berkas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop setiap berkas pengajuan
            foreach ($berkas_pengajuan as $index => $judul_berkas) {
                // Pastikan indeks tidak melebihi jumlah berkas yang diunggah
                if ($index > count($berkas_pengajuan)) {
                    continue;
                }

                $ada = isset($berkas_tidak_ada[$index]['ada']) && $berkas_tidak_ada[$index]['ada'] ? '✔' : '';
                $tidak_ada = isset($berkas_tidak_ada[$index]['tidak_ada']) && $berkas_tidak_ada[$index]['tidak_ada'] ? '✔' : '';
                $file_name = isset($berkas_tidak_ada[$index]['file_name']) ? $berkas_tidak_ada[$index]['file_name'] : '';
                $file_path = isset($berkas_tidak_ada[$index]['file_path']) ? $berkas_tidak_ada[$index]['file_path'] : '';

                echo "<tr>";
                echo "<td>" . ($index) . ".</td>";
                echo "<td>" . $judul_berkas . "</td>";
                echo "<td>" . $ada . "</td>";
                echo "<td>" . $tidak_ada . "</td>";
                echo "<td>";
                if (!empty($file_path)) {
                    echo "<a href='" . htmlspecialchars($file_path) . "' target='_blank'>Buka Berkas</a>";
                } else {
                    echo "Tidak ada file";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

     <!-- Bagian Tanda Tangan -->
     <table class="ttd-table">
            <tr>
                <div class="signature-class">
                <td class="no-padding">Nama Pengaju</td>
                <td class="no-padding">Yang menerima</td>
            </tr>
            <tr>
                <td class="no-padding"></td>
                <td class="no-padding">Sekretaris TAT</td>
            </tr>
            </div>
            <tr>
                <td class="no-padding"></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td>.....................</td>
                <td>.....................</td>
            </tr>
    </table>

</div>

</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
