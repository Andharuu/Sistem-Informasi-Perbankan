<?php
require_once '../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_nasabah'];
    $nama = $_POST['nama_nasabah'];
    $alamat = $_POST['alamat_nasabah'];

    $sql = "UPDATE nasabah SET nama_nasabah = :nama, alamat_nasabah = :alamat WHERE id_nasabah = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nama' => $nama, ':alamat' => $alamat, ':id' => $id]);

    header("Location: ../index.php");
    exit();
}
?>