<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Stats
$totalProducts    = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders      = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCategories  = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalRevenue     = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?? 0;

// Recent orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
// Recent products
$recentProducts = $pdo->query("SELECT p.*, c.name AS cat FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dasbor — Admin Sahabat Mebel</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon brown">📦</div>
        <div class="stat-info"><div class="label">Total Produk</div><div class="value"><?= $totalProducts ?></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon gold">🛒</div>
        <div class="stat-info"><div class="label">Total Pesanan</div><div class="value"><?= $totalOrders ?></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-info"><div class="label">Total Pendapatan</div><div class="value">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">🏷️</div>
        <div class="stat-info"><div class="label">Kategori</div><div class="value"><?= $totalCategories ?></div></div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
      <!-- RECENT ORDERS -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Pesanan Terbaru</span>
          <a href="orders.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body">
          <table>
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach($recentOrders as $o): ?>
              <tr>
                <td><strong><?= htmlspecialchars($o['order_code']) ?></strong></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td>Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></td>
                <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($recentOrders)): ?><tr><td colspan="4" style="text-align:center;color:var(--text-muted)">Belum ada pesanan</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RECENT PRODUCTS -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Produk Terbaru</span>
          <a href="products.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body">
          <table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th></tr></thead>
            <tbody>
              <?php foreach($recentProducts as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['cat']) ?></td>
                <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>
