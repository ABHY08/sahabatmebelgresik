<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">S</div>
    <span>Sahabat<span>Mebel</span></span>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Utama</div>
    <div class="nav-item">
      <a href="dashboard.php" class="<?= $currentPage==='dashboard.php'?'active':'' ?>">
        <span class="nav-icon">📊</span> Dasbor
      </a>
    </div>

    <div class="nav-section">Katalog</div>
    <div class="nav-item">
      <a href="products.php" class="<?= in_array($currentPage,['products.php','add_product.php','edit_product.php'])?'active':'' ?>">
        <span class="nav-icon">🪑</span> Produk
      </a>
    </div>
    <div class="nav-item">
      <a href="categories.php" class="<?= $currentPage==='categories.php'?'active':'' ?>">
        <span class="nav-icon">🏷️</span> Kategori
      </a>
    </div>

    <div class="nav-section">Penjualan</div>
    <div class="nav-item">
      <a href="orders.php" class="<?= $currentPage==='orders.php'?'active':'' ?>">
        <span class="nav-icon">🛒</span> Pesanan
      </a>
    </div>

    <div class="nav-section">Konten</div>
    <div class="nav-item">
      <a href="testimonials.php" class="<?= $currentPage==='testimonials.php'?'active':'' ?>">
        <span class="nav-icon">⭐</span> Ulasan
      </a>
    </div>
    <div class="nav-item">
      <a href="settings.php" class="<?= $currentPage==='settings.php'?'active':'' ?>">
        <span class="nav-icon">⚙️</span> Pengaturan
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <a href="../index.php" target="_blank">🌐 Lihat Toko</a>
    <a href="logout.php" style="margin-top:0.5rem;color:rgba(255,100,100,0.8)">🚪 Keluar</a>
  </div>
</aside>
