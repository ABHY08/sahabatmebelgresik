<?php
require_once 'config.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$catFilter = isset($_GET['cat']) ? $_GET['cat'] : 'all';
$search    = isset($_GET['q'])   ? trim($_GET['q']) : '';

// Build query
$sql    = "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($catFilter !== 'all') {
    $sql .= " AND c.slug = ?";
    $params[] = $catFilter;
}
if ($search !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Katalog lengkap produk furnitur premium Sahabat Mebel — sofa, meja, kursi, lemari berkualitas tinggi.">
  <title>Katalog Produk — Sahabat Mebel</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* ── Products Page Extras ── */
    .page-hero {
      background: linear-gradient(160deg, var(--cream) 0%, var(--beige) 40%, var(--cream-dark) 100%);
      padding: 5.5rem 2.5rem 3.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .page-hero::before {
      content: '';
      position: absolute; top: -50%; left: -10%; width: 80%; height: 200%;
      background: radial-gradient(ellipse 60% 40% at 30% 50%, rgba(201,148,58,0.10) 0%, transparent 70%);
      pointer-events: none;
    }
    .page-hero::after {
      content: '';
      position: absolute; top: -30%; right: -10%; width: 60%; height: 160%;
      background: radial-gradient(ellipse 50% 40% at 70% 40%, rgba(184,134,90,0.08) 0%, transparent 70%);
      pointer-events: none;
    }
    .page-hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,0.80); backdrop-filter: blur(12px);
      border: 1px solid rgba(201,184,154,0.40);
      padding: 0.38rem 1.1rem; border-radius: 50px; font-size: 0.78rem;
      color: var(--brown); font-weight: 600; margin-bottom: 1.2rem;
      box-shadow: var(--shadow-xs);
    }
    .page-hero-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2rem, 4vw, 3.2rem);
      color: var(--brown-dark); margin-bottom: 0.8rem; letter-spacing: -0.3px;
    }
    .page-hero-desc { color: var(--text-muted); font-size: 1rem; max-width: 520px; margin: 0 auto; line-height: 1.8; }

    /* Search bar */
    .search-wrap {
      max-width: 520px; margin: 2rem auto 0;
      display: flex; gap: 0;
      background: rgba(255,255,255,0.90); backdrop-filter: blur(12px);
      border: 1.5px solid rgba(201,184,154,0.40); border-radius: 50px;
      overflow: hidden; transition: border-color 0.3s, box-shadow 0.3s;
      box-shadow: var(--shadow-sm);
    }
    .search-wrap:focus-within { border-color: var(--brown-light); box-shadow: 0 0 0 3px rgba(184,134,90,0.12); }
    .search-input {
      flex: 1; border: none; outline: none; padding: 0.85rem 1.5rem;
      font-size: 0.95rem; background: transparent; color: var(--text-dark);
      font-family: 'DM Sans', 'Inter', sans-serif;
    }
    .search-btn {
      padding: 0.85rem 1.5rem;
      background: linear-gradient(135deg, var(--brown) 0%, var(--brown-dark) 100%);
      color: #fff; border: none; cursor: pointer; font-size: 1rem;
      transition: opacity 0.3s;
    }
    .search-btn:hover { opacity: 0.88; }

    /* Results count */
    .results-info {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 1.8rem; flex-wrap: wrap; gap: 0.75rem;
    }
    .results-count { font-size: 0.88rem; color: var(--text-muted); }
    .results-count strong { color: var(--brown-dark); }

    /* Sort select */
    .sort-select {
      padding: 0.55rem 1.1rem; border: 1.5px solid var(--beige-mid);
      border-radius: var(--radius-xs); background: var(--white); color: var(--text-muted);
      font-size: 0.85rem; cursor: pointer; outline: none;
      font-family: 'DM Sans', 'Inter', sans-serif; transition: border-color 0.3s;
    }
    .sort-select:focus { border-color: var(--brown-light); }

    /* Empty state */
    .empty-state {
      text-align: center; padding: 5rem 1rem;
      color: var(--text-muted);
    }
    .empty-state-icon { font-size: 4.5rem; margin-bottom: 1rem; }
    .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--brown-dark); margin-bottom: 0.6rem; }

    /* Breadcrumb */
    .breadcrumb {
      max-width: 1240px; margin: 0 auto 2rem;
      font-size: 0.83rem; color: var(--text-muted);
      display: flex; align-items: center; gap: 6px;
    }
    .breadcrumb a { color: var(--brown); }
    .breadcrumb a:hover { color: var(--brown-dark); }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-logo">
    <div class="logo-icon">S</div>
    <span class="logo-text">Sahabat<span>Mebel</span></span>
  </a>
  <ul class="nav-menu" id="navMenu">
    <li><a href="index.php">Beranda</a></li>
    <li><a href="products.php" class="active">Produk</a></li>
    <li><a href="index.php#room-ideas">Inspirasi</a></li>
    <li><a href="index.php#layanan">Layanan Custom</a></li>
    <li><a href="index.php#testimonials">Ulasan</a></li>
    <li><a href="index.php#tentang">Tentang Kami</a></li>
    <li>
      <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20konsultasi%20furnitur"
         target="_blank" class="nav-wa-btn">💬 Konsultasi WA</a>
    </li>
  </ul>
  <div class="nav-actions">
    <button class="cart-btn" id="cartToggle" aria-label="Buka keranjang">
      🛒 <span class="cart-count" id="cartCount">0</span>
    </button>
    <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
  </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
  <span class="page-hero-badge">✦ Koleksi Premium</span>
  <h1 class="page-hero-title">Katalog Produk Sahabat Mebel</h1>
  <p class="page-hero-desc">Temukan furnitur impian Anda dari koleksi kami yang dipilih dengan cermat — berkualitas tinggi, elegan, dan tahan lama.</p>

  <form action="products.php" method="GET" class="search-wrap">
    <?php if ($catFilter !== 'all'): ?>
    <input type="hidden" name="cat" value="<?= htmlspecialchars($catFilter) ?>">
    <?php endif; ?>
    <input type="text" name="q" class="search-input"
      placeholder="Cari sofa, meja, kursi..."
      value="<?= htmlspecialchars($search) ?>" autocomplete="off">
    <button type="submit" class="search-btn">🔍</button>
  </form>
</div>

<!-- CATALOG SECTION -->
<section class="section" style="padding-top:2.5rem">
  <div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="index.php">Home</a> › <span>Produk</span>
      <?php if ($catFilter !== 'all'): ?>
      › <span><?= htmlspecialchars(ucfirst($catFilter)) ?></span>
      <?php endif; ?>
      <?php if ($search): ?>
      › <span>Hasil: "<?= htmlspecialchars($search) ?>"</span>
      <?php endif; ?>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar" style="justify-content:flex-start;margin-bottom:1.5rem">
      <a href="products.php" class="filter-btn <?= $catFilter === 'all' ? 'active' : '' ?>">Semua</a>
      <?php foreach ($categories as $cat): ?>
      <a href="products.php?cat=<?= $cat['slug'] ?><?= $search ? '&q='.urlencode($search) : '' ?>"
        class="filter-btn <?= $catFilter === $cat['slug'] ? 'active' : '' ?>">
        <?= htmlspecialchars($cat['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Results Info + Sort -->
    <div class="results-info">
      <p class="results-count">
        Menampilkan <strong><?= count($products) ?></strong> produk
        <?= $search ? ' untuk "<strong>'.htmlspecialchars($search).'</strong>"' : '' ?>
        <?= $catFilter !== 'all' ? ' dalam kategori <strong>'.htmlspecialchars(ucfirst($catFilter)).'</strong>' : '' ?>
      </p>
      <select class="sort-select" id="sortSelect" onchange="sortProducts(this.value)">
        <option value="default">Urutkan: Unggulan</option>
        <option value="price-asc">Harga: Termurah</option>
        <option value="price-desc">Harga: Termahal</option>
        <option value="name-asc">Nama: A–Z</option>
      </select>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">🔍</div>
      <h3>Produk tidak ditemukan</h3>
      <p>Coba kata kunci yang berbeda atau lihat semua produk kami.</p>
      <a href="products.php" class="btn btn-primary" style="margin-top:1.5rem">Lihat Semua Produk</a>
    </div>
    <?php else: ?>
    <div class="products-grid" id="productsGrid">
      <?php foreach ($products as $p): ?>
      <div class="product-card"
        data-category="<?= $p['cat_slug'] ?>"
        data-id="<?= $p['id'] ?>"
        data-price="<?= $p['price'] ?>"
        data-name="<?= htmlspecialchars($p['name']) ?>">

        <div class="product-img loading">
          <img
            src="assets/images/products/<?= htmlspecialchars($p['image'] ?? '') ?>"
            alt="<?= htmlspecialchars($p['name']) ?>"
            onerror="this.onerror=null;this.src='assets/images/placeholder.jpg'"
            loading="lazy"
            onload="this.closest('.product-img').classList.remove('loading')">

          <?php if ($p['is_featured']): ?>
          <span class="product-badge" style="background:linear-gradient(135deg,var(--gold),var(--brown-xs));color:#fff">⭐ Unggulan</span>
          <?php else: ?>
          <span class="product-badge"><?= htmlspecialchars($p['cat_name']) ?></span>
          <?php endif; ?>
          <div class="product-rating"><span class="star">★</span> 4.9</div>
          <div class="product-img-actions">
            <button class="img-action-btn" title="Lihat Detail" onclick="event.stopPropagation()">🔍</button>
          </div>
        </div>

        <div class="product-body">
          <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
          <p class="product-desc"><?= htmlspecialchars($p['description'] ?? '') ?></p>
          <div class="product-specs-mini">
            <?php if(!empty($p['material'])): ?>
            <span class="spec-tag">🪵 <?= htmlspecialchars($p['material']) ?></span>
            <?php endif; ?>
            <?php if(!empty($p['dimensions'])): ?>
            <span class="spec-tag">📐 <?= htmlspecialchars($p['dimensions']) ?></span>
            <?php endif; ?>
          </div>
          <div class="product-footer">
            <span class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
            <button class="add-cart-btn"
              data-id="<?= $p['id'] ?>"
              data-name="<?= htmlspecialchars($p['name']) ?>"
              data-price="<?= $p['price'] ?>"
              data-img="assets/images/products/<?= htmlspecialchars($p['image'] ?? '') ?>"
              data-material="<?= htmlspecialchars($p['material'] ?? '') ?>"
              data-dimensions="<?= htmlspecialchars($p['dimensions'] ?? '') ?>">
              🛒 Tambah
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-logo">Sahabat<span>Mebel</span></div>
      <p class="footer-desc">Furnitur premium custom untuk hunian modern. Kualitas yang terlihat, kenyamanan yang terasa — dibuat dengan hati oleh pengrajin terbaik Indonesia.</p>
      <div class="footer-social">
        <a href="#" class="social-link" title="Instagram">📷</a>
        <a href="#" class="social-link" title="Facebook">👥</a>
        <a href="#" class="social-link" title="TikTok">🎵</a>
        <a href="https://wa.me/6281234567890" target="_blank" class="social-link" title="WhatsApp">💬</a>
      </div>
    </div>
    <div>
      <div class="footer-heading">Navigasi</div>
      <ul class="footer-links">
        <li><a href="index.php">Beranda</a></li>
        <li><a href="products.php">Produk</a></li>
        <li><a href="index.php#room-ideas">Inspirasi Ruangan</a></li>
        <li><a href="index.php#layanan">Layanan Custom</a></li>
        <li><a href="index.php#testimonials">Ulasan</a></li>
        <li><a href="index.php#tentang">Tentang Kami</a></li>
        <li><a href="admin/login.php">Admin</a></li>
      </ul>
    </div>
    <div>
      <div class="footer-heading">Kategori Produk</div>
      <ul class="footer-links">
        <?php foreach($categories as $cat): ?>
        <li><a href="products.php?cat=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <li><a href="products.php">Lihat Semua →</a></li>
      </ul>
    </div>
    <div>
      <div class="footer-heading">Kontak Kami</div>
      <div class="footer-contact-item">
        <span class="icon">📍</span>
        <span>Jl. Furniture No. 123, Jakarta Selatan, DKI Jakarta 12345</span>
      </div>
      <div class="footer-contact-item">
        <span class="icon">📞</span>
        <a href="tel:+6281234567890">+62 812-3456-7890</a>
      </div>
      <div class="footer-contact-item">
        <span class="icon">✉️</span>
        <a href="mailto:halo@sahabatmebel.com">halo@sahabatmebel.com</a>
      </div>
      <div class="footer-contact-item">
        <span class="icon">🕐</span>
        <span>Senin–Sabtu: 08.00 – 17.00 WIB</span>
      </div>
      <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20konsultasi"
         target="_blank" class="footer-wa-btn">💬 Chat WhatsApp Sekarang</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> Sahabat Mebel. Semua hak dilindungi.</span>
    <span>Dibuat dengan ❤️ untuk hunian terbaik Indonesia</span>
  </div>
</footer>

<!-- CART SIDEBAR -->
<div class="cart-overlay" id="cartOverlay"></div>
<aside class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <h2 class="cart-title">🛒 Keranjang</h2>
    <button class="cart-close" id="cartClose">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <div class="cart-empty-icon">🪑</div>
      <p>Keranjang masih kosong.<br>Mulai belanja!</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-total">
      <span class="cart-total-label">Total</span>
      <span class="cart-total-value" id="cartTotal">Rp 0</span>
    </div>
    <a href="index.php" class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:0.75rem">← Lanjut Belanja</a>
    <button class="btn btn-gold checkout-btn" id="checkoutBtn">Checkout →</button>
  </div>
</aside>

<!-- PRODUCT DETAIL MODAL -->
<div class="modal-overlay" id="productModal">
  <div class="modal" style="max-width:720px">
    <div class="modal-header">
      <h2 class="modal-title">Detail Produk</h2>
      <button class="cart-close" id="productModalClose">✕</button>
    </div>
    <div class="modal-body" id="productModalBody">
      <div class="product-detail">
        <div class="product-detail-img"><img id="detailImg" src="" alt=""></div>
        <div>
          <div class="detail-category" id="detailCategory"></div>
          <h2 class="detail-name" id="detailName"></h2>
          <div class="detail-price" id="detailPrice"></div>
          <div class="detail-tabs">
            <button class="detail-tab-btn active" data-tab="desc">Deskripsi</button>
            <button class="detail-tab-btn" data-tab="spec">Spesifikasi</button>
            <button class="detail-tab-btn" data-tab="care">Perawatan</button>
          </div>
          <div class="detail-tab-content active" id="tabDesc">
            <p class="detail-desc" id="detailDesc"></p>
          </div>
          <div class="detail-tab-content" id="tabSpec">
            <table class="spec-table" id="detailSpecTable"><tbody></tbody></table>
          </div>
          <div class="detail-tab-content" id="tabCare">
            <ul class="care-list">
              <li>Bersihkan dengan kain lembut dan kering secara rutin</li>
              <li>Hindari paparan sinar matahari langsung berlebihan</li>
              <li>Gunakan alas untuk melindungi dari goresan</li>
              <li>Oleskan minyak kayu setiap 6 bulan</li>
              <li>Bersihkan tumpahan segera untuk mencegah noda</li>
            </ul>
          </div>
          <div style="margin:0.8rem 0;padding:0.8rem 1rem;background:var(--beige);border-radius:10px;font-size:0.84rem;color:var(--text-muted)">
            🎨 <strong style="color:var(--brown-dark)">Ingin custom ukuran atau warna?</strong><br>
            Chat kami di WhatsApp untuk permintaan khusus!
          </div>
          <div class="detail-qty">
            <span style="font-weight:600;color:var(--text-mid)">Jumlah:</span>
            <div class="qty-control">
              <button class="qty-btn" id="detailQtyMinus">−</button>
              <span id="detailQtyVal">1</span>
              <button class="qty-btn" id="detailQtyPlus">+</button>
            </div>
          </div>
          <div style="display:flex;gap:0.75rem;flex-direction:column">
            <button class="btn btn-primary w-full" id="detailAddCart">🛒 Tambah ke Keranjang</button>
            <a href="#" id="detailWaBtn" target="_blank" class="btn btn-wa w-full" style="justify-content:center">💬 Tanya via WhatsApp</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Selesaikan Pesanan</h2>
      <button class="cart-close" id="modalClose">✕</button>
    </div>
    <div class="modal-body" id="modalBody">
      <div class="order-summary" id="orderSummaryBox"></div>
      <form id="checkoutForm">
        <div class="form-group">
          <label class="form-label" for="custName">Nama Lengkap *</label>
          <input type="text" class="form-control" id="custName" required placeholder="Nama lengkap Anda">
        </div>
        <div class="form-group">
          <label class="form-label" for="custAddress">Alamat Pengiriman *</label>
          <textarea class="form-control" id="custAddress" required placeholder="Jalan, Kota, Provinsi, Kode Pos"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="custWa">Nomor WhatsApp *</label>
          <input type="tel" class="form-control" id="custWa" required placeholder="Contoh: 08123456789">
        </div>
        <div class="form-group">
          <label class="form-label" for="custNotes">Catatan Pesanan (opsional)</label>
          <textarea class="form-control" id="custNotes" placeholder="Permintaan khusus, preferensi warna..."></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary w-full" id="placeOrderBtn">Buat Pesanan 🛒</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast-container" id="toastContainer"></div>

<!-- WHATSAPP FLOATING BUTTON -->
<div class="wa-float" id="waFloat">
  <div class="wa-float-label">Konsultasi Gratis via WA!</div>
  <button class="wa-float-btn" id="waFloatBtn" aria-label="Chat WhatsApp">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
    </svg>
  </button>
</div>

<script src="assets/js/app.js"></script>
<script>
// ── Sort functionality ──────────────────────────────────
function sortProducts(mode) {
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  const cards = [...grid.querySelectorAll('.product-card')];

  cards.sort((a, b) => {
    if (mode === 'price-asc')  return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
    if (mode === 'price-desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
    if (mode === 'name-asc')   return a.dataset.name.localeCompare(b.dataset.name, 'id');
    return 0; // default (featured order from server)
  });

  cards.forEach(c => grid.appendChild(c));
}
</script>
</body>
</html>
