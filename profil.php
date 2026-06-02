<?php
require_once 'config/koneksi.php';
require_once 'includes/header.php';

$id_nasabah = $_GET['id'] ?? null;

if (!$id_nasabah) {
    echo "<div class='card'><h2>Nasabah tidak ditemukan.</h2></div>";
    require_once 'includes/footer.php';
    exit;
}

// Security Check for Nasabah role
if ($_SESSION['user']['role'] === 'nasabah' && $_SESSION['user']['id_nasabah'] != $id_nasabah) {
    echo "<div class='card'><h2>Akses Ditolak. Anda hanya dapat melihat profil sendiri.</h2></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch Nasabah Data
$stmt = $pdo->prepare("SELECT * FROM nasabah WHERE id_nasabah = ?");
$stmt->execute([$id_nasabah]);
$nasabah = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nasabah) {
    echo "<div class='card'><h2>Data Nasabah tidak valid.</h2></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch Rekening with JOIN
$stmtRekening = $pdo->prepare("
    SELECT r.no_rekening, r.saldo, c.nama_cabang 
    FROM rekening r
    JOIN nasabah_has_rekening nhr ON r.no_rekening = nhr.no_rekeningfk
    LEFT JOIN cabang_bank c ON r.kode_cabangfk = c.kode_cabang
    WHERE nhr.id_nasabahfk = ?
");
$stmtRekening->execute([$id_nasabah]);
$rekening_list = $stmtRekening->fetchAll(PDO::FETCH_ASSOC);

$total_saldo = 0;
foreach($rekening_list as $r) {
    $total_saldo += $r['saldo'];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Profil Komprehensif Nasabah</h2>
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Profile Card -->
    <div class="card" style="text-align: center;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px auto;">
            <?= strtoupper(substr($nasabah['nama_nasabah'], 0, 1)) ?>
        </div>
        <h3 style="color: var(--text-main); margin-bottom: 5px;"><?= htmlspecialchars($nasabah['nama_nasabah']) ?></h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">ID Nasabah: #<?= $nasabah['id_nasabah'] ?></p>
        
        <div style="text-align: left; margin-top: 20px; padding-top: 20px; border-top: 1px solid #F4F7FE;">
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 5px;">Alamat Domisili:</p>
            <p style="color: var(--text-main); font-weight: 500;"><i class="fa-solid fa-map-location-dot" style="color: var(--primary); margin-right: 8px;"></i> <?= htmlspecialchars($nasabah['alamat_nasabah']) ?></p>
        </div>
        
        <div style="text-align: left; margin-top: 20px; padding-top: 20px; border-top: 1px solid #F4F7FE;">
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 5px;">Total Aset Tersimpan:</p>
            <h2 style="color: var(--primary);">Rp <?= number_format($total_saldo, 0, ',', '.') ?></h2>
        </div>
    </div>

    <!-- Accounts Card -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Daftar Rekening <span style="font-size: 14px; color: var(--text-muted); font-weight: 400;">(<?= count($rekening_list) ?> Rekening)</span></h3>
        </div>
        
        <?php if (count($rekening_list) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>NO. REKENING</th>
                        <th>CABANG PEMBUKA</th>
                        <th>SALDO TERSEDIA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rekening_list as $r): ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--text-main);"><i class="fa-solid fa-wallet" style="color: #10B981; margin-right: 8px;"></i> <?= htmlspecialchars($r['no_rekening']) ?></td>
                        <td style="color: var(--text-muted);"><?= htmlspecialchars($r['nama_cabang'] ?? 'Pusat') ?></td>
                        <td style="font-weight: 600; color: var(--primary);">Rp <?= number_format($r['saldo'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Nasabah belum memiliki rekening aktif.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
