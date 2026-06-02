<?php
require_once '../config/koneksi.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL,
            id_nasabah INTEGER NULL
        );
    ");

    $stmt = $pdo->query("SELECT count(*) FROM users");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (username, password, role, id_nasabah) VALUES
            ('admin', '$password', 'admin', NULL),
            ('teller', '$password', 'teller', NULL),
            ('nasabah1', '$password', 'nasabah', 1)
        ");
        echo "Users created.\n";
    } else {
        echo "Users already exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
