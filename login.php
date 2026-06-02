<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Perbankan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-color: #F4F7FE; --primary: #4318FF; --text-main: #2B3674; --card-bg: #FFFFFF; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: var(--card-bg); border-radius: 20px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .login-card h2 { text-align: center; margin-bottom: 30px; color: var(--text-main); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid #E0E5F2; border-radius: 12px; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: var(--primary); }
        .btn-primary { width: 100%; padding: 12px; background-color: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 16px; }
        .btn-primary:hover { opacity: 0.9; }
        .error { color: #EF4444; font-size: 14px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login Perbankan</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" action="actions/proses_login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
    </div>
</body>
</html>
