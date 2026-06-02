<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$current_page = basename($_SERVER['PHP_SELF']);

// Basic RBAC Check for page access
$allowed = false;
if ($role === 'admin') {
    $allowed = true; // Admin has full access
} elseif ($role === 'teller' && $current_page === 'transaksi.php') {
    $allowed = true;
} elseif ($role === 'nasabah' && $current_page === 'profil.php') {
    $allowed = true;
}

// Exception for actions (this could be improved by checking each action specifically)
if (strpos($_SERVER['PHP_SELF'], '/actions/') !== false) {
    if ($role === 'admin' || ($role === 'teller' && strpos($_SERVER['PHP_SELF'], 'transaksi') !== false)) {
        $allowed = true;
    }
}

// Redirect unauthorized access (unless it's an action, then maybe die)
if (!$allowed && strpos($_SERVER['PHP_SELF'], '/actions/') === false && $current_page != 'logout.php') {
    if ($role === 'teller') header("Location: transaksi.php");
    elseif ($role === 'nasabah') header("Location: profil.php?id=" . $user['id_nasabah']);
    else header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Perbankan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --bg-color: #F4F7FE; --sidebar-bg: #0B1437; --primary: #4318FF; --text-main: #2B3674; --text-muted: #A3AED0; --card-bg: #FFFFFF; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }

        .sidebar { width: 90px; background-color: var(--sidebar-bg); border-radius: 0 25px 25px 0; display: flex; flex-direction: column; align-items: center; padding: 30px 0; z-index: 10; position: relative;}
        .logo { color: white; font-size: 24px; font-weight: bold; margin-bottom: 50px; }
        .nav-item { width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; color: var(--text-muted); border-radius: 12px; margin-bottom: 15px; text-decoration: none; transition: all 0.3s; position: relative;}
        .nav-item.active { background-color: var(--primary); color: white; box-shadow: 0 4px 15px rgba(67, 24, 255, 0.4); }
        .nav-item:hover:not(.active) { background-color: rgba(255,255,255,0.1); color: white; }
        .nav-bottom { position: absolute; bottom: 30px; }
        
        .main-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .card { background: var(--card-bg); border-radius: 20px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        
        input, select { width: 100%; padding: 12px; margin: 8px 0 20px 0; border: 1px solid #E0E5F2; border-radius: 10px; outline: none; font-family: 'Poppins', sans-serif;}
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-block; cursor: pointer; border: none; font-family: 'Poppins', sans-serif;}
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-secondary { background-color: #E2E8F0; color: #475569; }
        
        /* Dashboard Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card-bg); border-radius: 20px; padding: 20px; display: flex; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .stat-icon { width: 56px; height: 56px; border-radius: 50%; background: #F4F7FE; color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 24px; margin-right: 20px; }
        .stat-info h4 { color: var(--text-muted); font-size: 14px; font-weight: 500; margin-bottom: 5px; }
        .stat-info h2 { color: var(--text-main); font-size: 24px; font-weight: 700; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 10px; color: var(--text-muted); border-bottom: 2px solid #F4F7FE; font-weight: 600; font-size: 14px;}
        td { padding: 15px 10px; border-bottom: 1px solid #F4F7FE; font-size: 14px; color: var(--text-main); }
        
        .badge { padding: 5px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #DCFCE7; color: #16A34A; }
        .badge-danger { background: #FEE2E2; color: #EF4444; }
        .badge-warning { background: #FEF9C3; color: #CA8A04; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"><i class="fa-solid fa-building-columns"></i></div>
        
        <?php if ($role === 'admin'): ?>
        <a href="index.php" class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>" title="Dashboard"><i class="fa-solid fa-chart-pie"></i></a>
        <a href="cabang.php" class="nav-item <?= $current_page == 'cabang.php' ? 'active' : '' ?>" title="Cabang"><i class="fa-solid fa-code-branch"></i></a>
        <?php endif; ?>
        
        <?php if ($role === 'admin' || $role === 'teller'): ?>
        <a href="transaksi.php" class="nav-item <?= $current_page == 'transaksi.php' ? 'active' : '' ?>" title="Transaksi"><i class="fa-solid fa-money-bill-transfer"></i></a>
        <?php endif; ?>
        
        <?php if ($role === 'nasabah'): ?>
        <a href="profil.php?id=<?= $user['id_nasabah'] ?>" class="nav-item <?= $current_page == 'profil.php' ? 'active' : '' ?>" title="Profil Saya"><i class="fa-solid fa-user"></i></a>
        <?php endif; ?>
        
        <div class="nav-bottom">
            <a href="actions/logout.php" class="nav-item" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
    <div class="main-content">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <div style="background: white; padding: 10px 20px; border-radius: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <?= htmlspecialchars($user['username']) ?> <span style="color: var(--text-muted); font-weight: 400; text-transform: capitalize;">(<?= $role ?>)</span>
            </div>
        </div>