<?php
$pageTitles = [
  'dashboard.php'    => 'Dasbor',
  'products.php'     => 'Kelola Produk',
  'add_product.php'  => 'Tambah Produk',
  'edit_product.php' => 'Edit Produk',
  'categories.php'   => 'Kategori',
  'orders.php'       => 'Pesanan',
  'testimonials.php' => 'Ulasan',
  'settings.php'     => 'Pengaturan Situs',
];
$pageTitle = $pageTitles[basename($_SERVER['PHP_SELF'])] ?? 'Admin';
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<header class="topbar">
  <div style="display:flex;align-items:center;gap:1rem">
    <button onclick="document.getElementById('sidebar').classList.toggle('open')"
            style="display:none;background:none;border:none;cursor:pointer;font-size:1.4rem" id="menuToggle">☰</button>
    <h1 class="topbar-title"><?= $pageTitle ?></h1>
  </div>
  <div class="topbar-user">
    <div class="user-avatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
    <span style="font-weight:600;font-size:0.9rem;color:var(--brown-dark)"><?= htmlspecialchars($adminName) ?></span>
  </div>
</header>
<style>
@media(max-width:768px){#menuToggle{display:block!important}}
</style>
