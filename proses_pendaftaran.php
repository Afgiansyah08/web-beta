<?php
header('Content-Type: application/json');
if (!headers_sent()) {
    header('Content-Type: application/json');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

include 'config.php';

// Validasi metode request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid."]);
    exit;
}

// Ambil data dari form
$nama   = mysqli_real_escape_string($conn, $_POST['nama']);
$email  = mysqli_real_escape_string($conn, $_POST['email']);
$no_hp  = mysqli_real_escape_string($conn, $_POST['no_hp']);
$kelas  = mysqli_real_escape_string($conn, $_POST['kelas']);
$harga  = mysqli_real_escape_string($conn, $_POST['harga_value']);
$bank   = mysqli_real_escape_string($conn, $_POST['bank']);
$status = 'pending';

// Log data untuk debugging
error_log("POST Data: " . json_encode($_POST));
error_log("FILES Data: " . json_encode($_FILES));

// Validasi file bukti transfer
if (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Bukti transfer wajib diunggah."]);
    exit;
}

// Simpan bukti transfer ke folder "uploads/"
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$file_name = time() . "_" . basename($_FILES["bukti_transfer"]["name"]);
$target_file = $target_dir . $file_name;

if (!move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $target_file)) {
    echo json_encode(["status" => "error", "message" => "Gagal mengunggah bukti transfer."]);
    exit;
}

// Simpan ke database
$query = "INSERT INTO mendaftar (nama, email, no_hp, kelas, harga, bank, status, bukti_transfer) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssssss", $nama, $email, $no_hp, $kelas, $harga, $bank, $status, $file_name);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Pendaftaran Anda berhasil! Saat ini, pendaftaran akan dibuka setelah bulan puasa. Kami akan menghubungi Anda segera setelah pendaftaran resmi dibuka. Terima kasih atas kesabaran dan dukungan Anda!"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
