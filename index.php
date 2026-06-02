<?php
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Fetch Analytics
// 1. Total Keseluruhan Uang Bank (SUM saldo)
$stmtSum = $pdo->query("SELECT SUM(saldo) as total_saldo FROM rekening");
$total_uang = $stmtSum->fetch(PDO::FETCH_ASSOC)['total_saldo'] ?? 0;

// 2. Jumlah Rekening Aktif
$stmtCount = $pdo->query("SELECT COUNT(no_rekening) as total_rekening FROM rekening");
$total_rekening = $stmtCount->fetch(PDO::FETCH_ASSOC)['total_rekening'] ?? 0;

// 3. Top Depositor (Nasabah dengan saldo tertinggi)
$stmtTop = $pdo->query("
    SELECT n.nama_nasabah, SUM(r.saldo) as total_saldo 
    FROM nasabah n 
    JOIN nasabah_has_rekening nhr ON n.id_nasabah = nhr.id_nasabahfk
    JOIN rekening r ON nhr.no_rekeningfk = r.no_rekening
    GROUP BY n.id_nasabah, n.nama_nasabah
    ORDER BY total_saldo DESC LIMIT 1
");
$top_depositor = $stmtTop->fetch(PDO::FETCH_ASSOC);

// Fetch Nasabah List
$stmt = $pdo->query("SELECT * FROM nasabah ORDER BY id_nasabah ASC");
$data_nasabah = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_nasabah = count($data_nasabah);
?>

<div>
    <h2 style="margin-bottom: 20px;">Analytics Dashboard</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <h4>Total Uang Bank</h4>
                <h2>Rp <?= number_format($total_uang, 0, ',', '.') ?></h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: #10B981; background: #D1FAE5;"><i class="fa-solid fa-credit-card"></i></div>
            <div class="stat-info">
                <h4>Rekening Aktif</h4>
                <h2><?= $total_rekening ?> <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">Akun</span></h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: #F59E0B; background: #FEF3C7;"><i class="fa-solid fa-crown"></i></div>
            <div class="stat-info">
                <h4>Top Depositor</h4>
                <?php if ($top_depositor): ?>
                    <h2 style="font-size: 18px;"><?= htmlspecialchars($top_depositor['nama_nasabah']) ?></h2>
                    <h4 style="color: #F59E0B; margin-top: 5px;">Rp <?= number_format($top_depositor['total_saldo'], 0, ',', '.') ?></h4>
                <?php else: ?>
                    <h2>-</h2>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 40px;">
    <h2>Daftar Nasabah Aktif <span style="font-size:14px; color:var(--text-muted);">(<?= $total_nasabah ?> Nasabah)</span></h2>
    <a href="tambah.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nasabah Baru</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NAMA NASABAH</th>
                <th>ALAMAT</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data_nasabah as $n): ?>
            <tr>
                <td>#<?= $n['id_nasabah'] ?></td>
                <td style="color: var(--primary); font-weight: 600;">
                    <a href="profil.php?id=<?= $n['id_nasabah'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($n['nama_nasabah']) ?></a>
                </td>
                <td style="color: var(--text-muted);"><?= htmlspecialchars($n['alamat_nasabah']) ?></td>
                <td>
                    <a href="profil.php?id=<?= $n['id_nasabah'] ?>" class="btn btn-secondary" style="padding: 6px 12px; background: #E0E7FF; color: var(--primary);" title="Lihat Profil"><i class="fa-solid fa-eye"></i></a>
                    <a href="edit.php?id=<?= $n['id_nasabah'] ?>" class="btn btn-secondary" style="padding: 6px 12px;"><i class="fa-solid fa-pen"></i></a>
                    <a href="actions/hapus_data.php?id=<?= $n['id_nasabah'] ?>" class="btn" style="padding: 6px 12px; background: #FEE2E2; color: #EF4444;" onclick="return confirm('Hapus nasabah ini?')"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>