<?php
require_once '../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Harap isi semua kolom.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Sahabat Mebel</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon">S</div>
      <div class="login-title">Panel Admin</div>
      <div class="login-sub">Sistem Manajemen Sahabat Mebel</div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group" style="margin-bottom:1.2rem">
        <label for="username">Nama Pengguna</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="form-group" style="margin-bottom:1.8rem">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.85rem">
        Masuk ke Dashboard →
      </button>
    </form>

    <p style="text-align:center;margin-top:1.5rem;font-size:0.85rem;color:var(--text-muted)">
      <a href="../index.php" style="color:var(--brown);text-decoration:none">← Kembali ke Toko</a>
    </p>
  </div>
</body>
</html>
