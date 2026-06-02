<?php
require_once '../config/koneksi.php';
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['table_name'] . "\n";
}
