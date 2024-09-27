<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Daftar Berkas</title>
    <link rel="stylesheet" href="css/formulir.css">
</head>
<body>

<div class="container">
    <h2>FORMULIR REGISTRASI KLIEN TAT PADA MASA PENANGKAPAN (APABILA DIDAPATKAN BARANG BUKTI)</h2>

    <form id="formTersangka" action="hasil_formulir.php" method="POST" enctype="multipart/form-data">
        <table class="info-table">
        <tr>
            <td>Nama Tersangka</td>
            <td>:</td>
            <td>
            <input type="text" name="nama_tersangka" id="namaTersangka" placeholder="Nama Tersangka" onkeyup="checkNamaTersangka()" required>
            </td>
            <td colspan="3">
            <div id="warning" style="color: red; margin-left:10px;"></div>
            </td>
            <td colspan="3" rowspan="3">
                <!-- Div untuk menampilkan gambar tersangka -->
                <div id="gambarTersangka"></div>
            </td>
        </tr>
            
            <tr>
                <td>Yang Mengajukan Berkas</td>
                <td>:</td>
                <td><input type="text" name="pengaju" placeholder="Nama yang mengajukan berkas" required></td>
            </tr>
            <tr>
            <td>Jabatan</td>
                <td>:</td>
                <td><input type="text" name="jabatan" placeholder="Jabatan" required></td>
            </tr>
            <tr>
                <td>Asal Instansi</td>
                <td>:</td>
                <td><input type="text" name="instansi" placeholder="Asal Instansi" required></td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>:</td>
                <td><input type="date" name="tanggal" required></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Daftar Berkas Pengajuan Pada Masa Penangkapan</th>
                    <th>Ada</th>
                    <th>Tidak Ada</th>
                    <th>Upload Berkas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Definisikan berkas yang ada di tabel (sesuaikan dengan PHP)
                $berkas_pengajuan = [
                    1 => "Surat Permohonan Asesmen Terpadu dari Penyidik kepada Ketua Tim Asesmen Terpadu Tingkat Provinsi atau Tingkat Kabupaten/Kota",
                    2 => "Fotocopy Kartu Identitas Tersangka (KTP atau Kartu Pelajar atau Kartu Mahasiswa dan Kartu Keluarga)",
                    3 => "Laporan Informasi",
                    4 => "Berita Acara Interograsi",
                    5 => "Surat Perintah Penangkapan",
                    6 => "Surat Keterangan Hasil Pemeriksaan Urine yang dikeluarkan oleh Fasilitas Kesehatan Milik Pemerintah (seperti Labkesda, Klinik Polres, IPWL BNN, IPWL BNNP, IPWL BNN Kabupaten/Kota, Puskesmas IPWL, RSUD, dll) dengan jangka waktu maksimal 3 x 24 jam setelah ditangkap, dengan hasil <strong>positif</strong>.",
                    7 => "Data dukung elektronik seperti screenshoot percakapan, pembelian barang, transfer (bila ada).",
                ];

                // Loop setiap berkas pengajuan
                foreach ($berkas_pengajuan as $index => $judul_berkas) {
                    echo "<tr>";
                    echo "<td>" . ($index) . ".</td>";
                    echo "<td>" . $judul_berkas . "</td>";
                    echo "<td><input type=\"checkbox\" name=\"berkas_tidak_ada[$index][ada]\" value=\"1\" onchange=\"toggleCheckbox(this)\"></td>";
                    echo "<td><input type=\"checkbox\" name=\"berkas_tidak_ada[$index][tidak_ada]\" value=\"1\" onchange=\"toggleCheckbox(this)\"></td>";
                    echo "<td><input type=\"file\" name=\"file_$index\" accept=\"image/jpeg, image/jpg, image/png, application/pdf\ application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document\"></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <button type="submit" class="submit-button">Kirim Formulir</button>
    </form>
</div>

<script src="js/form.js"></script>

</body>
</html>
