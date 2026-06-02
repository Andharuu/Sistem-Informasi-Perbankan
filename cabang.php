<?php
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Only Admin should access this based on header.php, but let's double check
if ($_SESSION['user']['role'] !== 'admin') {
    echo "<div class='card'><h2>Akses Ditolak.</h2></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch Cabang Data
$stmt = $pdo->query("SELECT * FROM cabang_bank ORDER BY kode_cabang ASC");
$cabang_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2>Daftar Kantor Cabang <span style="font-size:14px; color:var(--text-muted);">(<?= count($cabang_list) ?> Lokasi)</span></h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php if (count($cabang_list) > 0): ?>
        <?php foreach($cabang_list as $c): ?>
        <div class="card" style="transition: transform 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: #E0E7FF; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 15px;">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 style="color: var(--text-main); font-size: 16px;"><?= htmlspecialchars($c['nama_cabang']) ?></h3>
                    <p style="color: var(--text-muted); font-size: 13px;">Kode: <?= htmlspecialchars($c['kode_cabang']) ?></p>
                </div>
            </div>
            
            <div style="border-top: 1px solid #F4F7FE; padding-top: 15px;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 5px;"><i class="fa-solid fa-location-dot" style="margin-right: 5px;"></i> Alamat Lokasi:</p>
                <p style="color: var(--text-main); font-size: 14px; line-height: 1.5;"><?= htmlspecialchars($c['alamat_cabang']) ?></p>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="#" class="btn btn-secondary" style="width: 100%; text-align: center; background: #F4F7FE; color: var(--primary); font-size: 13px;"><i class="fa-solid fa-map" style="margin-right: 5px;"></i> Lihat Peta</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
            <i class="fa-solid fa-building-circle-exclamation" style="font-size: 50px; color: #E0E5F2; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-muted);">Belum ada data kantor cabang.</h3>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
