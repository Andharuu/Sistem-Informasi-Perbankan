<?php
require_once 'config/koneksi.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Fetch all transactions
$stmtTrans = $pdo->query("
    SELECT t.*, n.nama_nasabah 
    FROM transaksi t
    LEFT JOIN nasabah n ON t.id_nasabahfk = n.id_nasabah
    ORDER BY t.tanggal DESC, t.no_transaksi DESC
    LIMIT 100
");
$transaksi_list = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);

?>

<h2 style="margin-bottom: 20px;">Mutasi Rekening & Transaksi</h2>

<?php if ($error): ?>
    <div style="background: #FEE2E2; color: #EF4444; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="background: #DCFCE7; color: #16A34A; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    
    <?php if ($_SESSION['user']['role'] === 'teller' || $_SESSION['user']['role'] === 'admin'): ?>
    <!-- Transaction Form -->
    <div class="card" style="height: fit-content;">
        <h3 style="margin-bottom: 20px; color: var(--text-main);"><i class="fa-solid fa-cash-register" style="color: var(--primary); margin-right: 8px;"></i> Input Transaksi Baru</h3>
        <form method="POST" action="actions/proses_transaksi.php">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-muted);">Nomor Rekening</label>
                <input type="number" name="no_rekening" required placeholder="Masukkan nomor rekening">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-muted);">Jenis Transaksi</label>
                <select name="jenis_transaksi" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="setor">Setor Tunai</option>
                    <option value="tarik">Tarik Tunai</option>
                </select>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-muted);">Nominal (Rp)</label>
                <input type="number" name="jumlah" required placeholder="Contoh: 500000" min="10000">
            </div>
            
            <button type="submit" name="submit_transaksi" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> Proses Transaksi</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Transaction History -->
    <div class="card" style="<?= ($_SESSION['user']['role'] === 'nasabah') ? 'grid-column: 1 / span 2;' : '' ?>">
        <h3 style="margin-bottom: 20px; color: var(--text-main);"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Riwayat Transaksi Terbaru</h3>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>NO. TRANS</th>
                        <th>TANGGAL</th>
                        <th>NASABAH</th>
                        <th>REKENING</th>
                        <th>JENIS</th>
                        <th>NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transaksi_list) > 0): ?>
                        <?php foreach($transaksi_list as $t): ?>
                        <tr>
                            <td>#<?= $t['no_transaksi'] ?></td>
                            <td style="color: var(--text-muted);"><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
                            <td style="font-weight: 500;"><?= htmlspecialchars($t['nama_nasabah'] ?? 'Unknown') ?></td>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($t['no_rekeningfk']) ?></td>
                            <td>
                                <?php if (strtolower($t['jenis_transaksi']) == 'setor'): ?>
                                    <span class="badge badge-success">Setor</span>
                                <?php elseif (strtolower($t['jenis_transaksi']) == 'tarik'): ?>
                                    <span class="badge badge-danger">Tarik</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($t['jenis_transaksi']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: <?= strtolower($t['jenis_transaksi']) == 'setor' ? '#16A34A' : '#EF4444' ?>;">
                                <?= strtolower($t['jenis_transaksi']) == 'setor' ? '+' : '-' ?> Rp <?= number_format($t['jumlah'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">Belum ada riwayat transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
