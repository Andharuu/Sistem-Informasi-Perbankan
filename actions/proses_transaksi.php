<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_transaksi'])) {
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'teller' && $_SESSION['user']['role'] !== 'admin')) {
        $_SESSION['error'] = 'Hanya Teller dan Admin yang dapat memproses transaksi.';
        header("Location: ../transaksi.php");
        exit;
    }

    $no_rekening = $_POST['no_rekening'] ?? '';
    $jenis_transaksi = $_POST['jenis_transaksi'] ?? '';
    $jumlah = $_POST['jumlah'] ?? 0;
    $tanggal = date('Y-m-d');
    
    try {
        $pdo->beginTransaction();
        
        // Check if rekening exists and get current saldo
        $stmtRek = $pdo->prepare("SELECT saldo, (SELECT id_nasabahfk FROM nasabah_has_rekening WHERE no_rekeningfk = r.no_rekening LIMIT 1) as id_nasabahfk FROM rekening r WHERE no_rekening = ?");
        $stmtRek->execute([$no_rekening]);
        $rek = $stmtRek->fetch(PDO::FETCH_ASSOC);
        
        if (!$rek) {
            throw new Exception("Nomor rekening tidak ditemukan.");
        }
        
        $id_nasabah = $rek['id_nasabahfk'];
        
        if ($jenis_transaksi == 'tarik') {
            if ($rek['saldo'] < $jumlah) {
                throw new Exception("Saldo tidak mencukupi untuk penarikan.");
            }
            $new_saldo = $rek['saldo'] - $jumlah;
        } elseif ($jenis_transaksi == 'setor') {
            $new_saldo = $rek['saldo'] + $jumlah;
        } else {
            throw new Exception("Jenis transaksi tidak valid.");
        }
        
        // Update saldo
        $stmtUpd = $pdo->prepare("UPDATE rekening SET saldo = ? WHERE no_rekening = ?");
        $stmtUpd->execute([$new_saldo, $no_rekening]);
        
        // Generate simple transaction ID
        $stmtMax = $pdo->query("SELECT MAX(no_transaksi) FROM transaksi");
        $maxId = $stmtMax->fetchColumn();
        $nextId = $maxId ? $maxId + 1 : 1;
        
        // Insert transaction record
        $stmtIns = $pdo->prepare("INSERT INTO transaksi (no_transaksi, id_nasabahfk, no_rekeningfk, jenis_transaksi, tanggal, jumlah) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtIns->execute([$nextId, $id_nasabah, $no_rekening, $jenis_transaksi, $tanggal, $jumlah]);
        
        $pdo->commit();
        $_SESSION['success'] = "Transaksi $jenis_transaksi sebesar Rp " . number_format($jumlah, 0, ',', '.') . " berhasil diproses.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: ../transaksi.php");
exit;
