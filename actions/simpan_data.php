<?php
require_once '../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_nasabah'];
    $nama = $_POST['nama_nasabah'];
    $alamat = $_POST['alamat_nasabah'];

    $sql = "INSERT INTO nasabah (id_nasabah, nama_nasabah, alamat_nasabah) VALUES (:id, :nama, :alamat)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id, ':nama' => $nama, ':alamat' => $alamat]);

    header("Location: ../index.php");
    exit();
}
?>