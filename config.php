<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "pendaftaran";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Gagal koneksi ke database: " . mysqli_connect_error()]));
}

?>
