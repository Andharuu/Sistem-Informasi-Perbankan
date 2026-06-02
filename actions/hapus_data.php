<?php
require_once '../config/koneksi.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM nasabah WHERE id_nasabah = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../index.php");
exit();
?>