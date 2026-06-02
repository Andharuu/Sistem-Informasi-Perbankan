<?php require_once 'includes/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px; color: #2B3674;">Tambah Nasabah Baru</h2>
    
    <form action="actions/simpan_data.php" method="POST">
        <label>ID Nasabah:</label>
        <input type="number" name="id_nasabah" required>
        
        <label>Nama Nasabah:</label>
        <input type="text" name="nama_nasabah" required>
        
        <label>Alamat:</label>
        <input type="text" name="alamat_nasabah" required>
        
        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan</button>
            <a href="index.php" class="btn btn-secondary" style="text-align: center; flex: 1;">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>