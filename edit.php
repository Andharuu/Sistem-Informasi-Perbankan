<?php
require_once 'config/koneksi.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM nasabah WHERE id_nasabah = :id");
$stmt->execute([':id' => $id]);
$nasabah = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px; color: #2B3674;">Edit Data Nasabah</h2>
    
    <form action="actions/update_data.php" method="POST">
        <input type="hidden" name="id_nasabah" value="<?= $nasabah['id_nasabah'] ?>">
        
        <label>ID Nasabah (Tidak bisa diubah):</label>
        <input type="number" value="<?= $nasabah['id_nasabah'] ?>" disabled>
        
        <label>Nama Nasabah:</label>
        <input type="text" name="nama_nasabah" value="<?= htmlspecialchars($nasabah['nama_nasabah']) ?>" required>
        
        <label>Alamat:</label>
        <input type="text" name="alamat_nasabah" value="<?= htmlspecialchars($nasabah['alamat_nasabah']) ?>" required>
        
        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Update Data</button>
            <a href="index.php" class="btn btn-secondary" style="text-align: center; flex: 1;">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>