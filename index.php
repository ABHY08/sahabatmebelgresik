<?php require_once 'config.php'; ?>
<?php
// Fetch data
$heroTitle    = getSetting($pdo, 'hero_title', 'Wujudkan Hunian');
$heroSubtitle = getSetting($pdo, 'hero_subtitle', 'Furnitur custom berkualitas tinggi, dibuat khusus sesuai selera Anda. Konsultasi gratis via WhatsApp!');

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$products = $pdo->query("
  SELECT p.*, c.name AS category_name, c.slug AS category_slug
  FROM products p
  JOIN categories c ON p.category_id = c.id
  WHERE p.is_featured = 1 AND p.is_active = 1
  ORDER BY p.created_at DESC
  LIMIT 8
")->fetchAll();

$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY id DESC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sahabat Mebel â€” Furnitur custom premium untuk hunian modern. Konsultasi gratis, pengerjaan profesional, garansi 2 tahun. Hubungi kami via WhatsApp sekarang!">
  <title>Sahabat Mebel â€” Furnitur Custom Premium untuk Hunian Impian Anda</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-logo">
    <div class="logo-icon">S</div>
    <span class="logo-text">Sahabat<span>Mebel</span></span>
  </a>

  <ul class="nav-menu" id="navMenu">
    <li><a href="#home" class="active">Beranda</a></li>
    <li><a href="products.php">Produk</a></li>

    <li><a href="#testimonials">Ulasan</a></li>
    <li><a href="#tentang">Tentang</a></li>
    <li>
      <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20konsultasi%20furnitur"
         target="_blank" class="nav-wa-btn">
        ðŸ’¬ Konsultasi WA
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="cart-btn" id="cartToggle" aria-label="Buka keranjang">
      ðŸ›’
      <span class="cart-count" id="cartCount">0</span>
    </button>
    <div class="hamburger" id="hamburger" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
  <div class="hero-content">
    <div class="hero-text fade-in">
      <div class="hero-eyebrow">
        <span class="hero-eyebrow-dot"></span>
        Furnitur Premium Custom Buatan Tangan
      </div>
      <h1 class="hero-title">
        <?= htmlspecialchars($heroTitle) ?><br>
        <span>Impian Anda</span>
      </h1>
      <p class="hero-desc"><?= htmlspecialchars($heroSubtitle) ?></p>
      <div class="hero-btns">
        <a href="products.php" class="btn btn-primary">Lihat Katalog â†’</a>
        <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20konsultasi%20furnitur%20custom"
           target="_blank" class="btn btn-wa">ðŸ’¬ Konsultasi Gratis</a>
      </div>
      <div class="hero-stats">
        <div class="stat">
          <div class="stat-num" data-count="500">500+</div>
          <div class="stat-label">Produk</div>
        </div>
        <div class="stat">
          <div class="stat-num">10K+</div>
          <div class="stat-label">Pelanggan</div>
        </div>
        <div class="stat">
          <div class="stat-num">4.9â˜…</div>
          <div class="stat-label">Rating</div>
        </div>
        <div class="stat">
          <div class="stat-num">2 Thn</div>
          <div class="stat-label">Garansi</div>
        </div>
      </div>
    </div>

    <div class="hero-image-area fade-in fade-in-delay-2">
      <div class="hero-main-img">
        <img src="assets/images/hero-banner.jpg" alt="Koleksi furnitur premium Sahabat Mebel"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#EDE5D8,#C9B89A)'">
      </div>
      <div class="hero-floating-card">
        <div class="floating-icon">ðŸª‘</div>
        <div class="floating-text">
          <div class="label">Terlaris Bulan Ini</div>
          <div class="value">Sofa Velvet Premium</div>
        </div>
      </div>
      <div class="hero-floating-card-2">
        <div class="floating-icon">â­</div>
        <div class="floating-text">
          <div class="label">Rating Pelanggan</div>
          <div class="value">4.9 / 5.0 â˜…</div>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- PRODUCTS SECTION -->
<section class="section" id="products">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Koleksi Kami</span>
      <h2 class="section-title">Dipilih Khusus untuk Rumah Anda</h2>
      <p class="section-desc">Setiap produk dibuat dari bahan premium dengan perhatian luar biasa terhadap detail dan kenyamanan.</p>
    </div>

    <div class="filter-bar">
      <button class="filter-btn active" data-filter="all">Semua</button>
      <?php foreach($categories as $cat): ?>
      <button class="filter-btn" data-filter="<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="products-grid" id="productsGrid">
      <?php foreach($products as $p): ?>
      <div class="product-card" data-category="<?= $p['category_slug'] ?>" data-id="<?= $p['id'] ?>">
        <div class="product-img loading">
          <img
            src="assets/images/products/<?= htmlspecialchars($p['image']) ?>"
            alt="<?= htmlspecialchars($p['name']) ?>"
            onerror="this.onerror=null;this.src='assets/images/placeholder.jpg'"
            loading="lazy"
            onload="this.closest('.product-img').classList.remove('loading')"
          >
          <span class="product-badge"><?= htmlspecialchars($p['category_name']) ?></span>
          <div class="product-rating"><span class="star">â˜…</span> 4.9</div>
          <div class="product-img-actions">
            <button class="img-action-btn" title="Lihat Detail" onclick="event.stopPropagation()">ðŸ”</button>
          </div>
        </div>
        <div class="product-body">
          <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
          <p class="product-desc"><?= htmlspecialchars($p['description']) ?></p>
          <div class="product-specs-mini">
            <?php if(!empty($p['material'])): ?>
            <span class="spec-tag">ðŸªµ <?= htmlspecialchars($p['material']) ?></span>
            <?php endif; ?>
            <?php if(!empty($p['dimensions'])): ?>
            <span class="spec-tag">ðŸ“ <?= htmlspecialchars($p['dimensions']) ?></span>
            <?php endif; ?>
            <?php if(empty($p['material']) && empty($p['dimensions'])): ?>
            <span class="spec-tag"><svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> Custom tersedia</span>
            <?php endif; ?>
          </div>
          <div class="product-footer">
            <span class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
            <button class="add-cart-btn"
              data-id="<?= $p['id'] ?>"
              data-name="<?= htmlspecialchars($p['name']) ?>"
              data-price="<?= $p['price'] ?>"
              data-img="assets/images/products/<?= htmlspecialchars($p['image']) ?>"
              data-material="<?= htmlspecialchars($p['material'] ?? '') ?>"
              data-dimensions="<?= htmlspecialchars($p['dimensions'] ?? '') ?>">
              ðŸ›’ Tambah
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:3rem">
      <a href="products.php" class="btn btn-outline" style="padding:0.9rem 2.8rem;font-size:1rem">
        Lihat Semua Produk â†’
      </a>
    </div>
  </div>
</section>


<!-- TESTIMONIALS SECTION -->
<section class="section section-alt" id="testimonials">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Ulasan Pelanggan</span>
      <h2 class="section-title">Apa Kata Pelanggan Kami</h2>
      <p class="section-desc">Ribuan pelanggan puas mempercayakan furnitur impian mereka kepada Sahabat Mebel.</p>
    </div>

    <div class="testimonials-carousel-wrap">
      <div class="testimonials-carousel" id="testimonialsCarousel">
        <?php foreach($testimonials as $t): ?>
        <div class="testimonial-card">
          <?php if(!empty($t['project_image'])): ?>
          <div class="testimonial-project-img">
            <img src="assets/images/<?= htmlspecialchars($t['project_image']) ?>"
                 alt="Hasil proyek <?= htmlspecialchars($t['customer_name']) ?>"
                 onerror="this.closest('.testimonial-project-img').style.display='none'" loading="lazy">
          </div>
          <?php endif; ?>
          <div class="stars"><?= str_repeat('â˜…', (int)$t['rating']) ?><?= str_repeat('â˜†', 5 - (int)$t['rating']) ?></div>
          <p class="testimonial-text">"<?= htmlspecialchars($t['content']) ?>"</p>
          <div class="testimonial-author">
            <div class="author-avatar"><?= strtoupper(substr($t['customer_name'], 0, 1)) ?></div>
            <div>
              <div class="author-name"><?= htmlspecialchars($t['customer_name']) ?></div>
              <?php if(!empty($t['location'])): ?>
              <div class="author-location">ðŸ“ <?= htmlspecialchars($t['location']) ?></div>
              <?php endif; ?>
              <div class="author-verified">âœ“ Verified Buyer</div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($testimonials)): ?>
        <div class="testimonial-card">
          <div class="stars">â˜…â˜…â˜…â˜…â˜…</div>
          <p class="testimonial-text">"Sangat puas dengan hasil furnitur custom dari Sahabat Mebel. Kualitas kayu jatinya luar biasa, detail finishingnya rapi, dan pengerjaan tepat waktu. Ruang tamu kami jadi terasa jauh lebih elegan!"</p>
          <div class="testimonial-author">
            <div class="author-avatar">B</div>
            <div>
              <div class="author-name">Budi Santoso</div>
              <div class="author-location">ðŸ“ Jakarta Selatan</div>
              <div class="author-verified">âœ“ Verified Buyer</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="stars">â˜…â˜…â˜…â˜…â˜…</div>
          <p class="testimonial-text">"Paket interior lengkap benar-benar worth it! Dari konsultasi desain, pemilihan bahan, sampai pemasangan semuanya profesional. Tim Sahabat Mebel sangat responsif dan komunikatif via WhatsApp."</p>
          <div class="testimonial-author">
            <div class="author-avatar">S</div>
            <div>
              <div class="author-name">Sari Dewi</div>
              <div class="author-location">ðŸ“ Bekasi</div>
              <div class="author-verified">âœ“ Verified Buyer</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="stars">â˜…â˜…â˜…â˜…â˜…</div>
          <p class="testimonial-text">"Custom lemari built-in sesuai ukuran kamar yang pas banget. Harga transparan, tidak ada biaya tersembunyi. Sahabat Mebel akan selalu jadi pilihan pertama kalau butuh furnitur baru."</p>
          <div class="testimonial-author">
            <div class="author-avatar">R</div>
            <div>
              <div class="author-name">Rizky Pratama</div>
              <div class="author-location">ðŸ“ Tangerang Selatan</div>
              <div class="author-verified">âœ“ Verified Buyer</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="stars">â˜…â˜…â˜…â˜…â˜…</div>
          <p class="testimonial-text">"Orderan sofa velvet custom warna sage green persis seperti referensi yang saya kirim. Respon cepat, pengerjaan rapih, pengiriman aman. Recommended banget buat yang mau furnitur custom!"</p>
          <div class="testimonial-author">
            <div class="author-avatar">A</div>
            <div>
              <div class="author-name">Anita Wijaya</div>
              <div class="author-location">ðŸ“ Surabaya</div>
              <div class="author-verified">âœ“ Verified Buyer</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Carousel Controls -->
    <div class="carousel-controls">
      <button class="carousel-btn" id="carouselPrev" aria-label="Sebelumnya">â†</button>
      <div class="carousel-dots" id="carouselDots"></div>
      <button class="carousel-btn" id="carouselNext" aria-label="Berikutnya">â†’</button>
    </div>

    <div style="text-align:center;margin-top:2.5rem">
      <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20berbagi%20ulasan%20produk%20saya"
         target="_blank" class="btn btn-outline">
        â­ Bagikan Ulasan Anda
      </a>
    </div>
  </div>
</section>

<!-- ABOUT / TENTANG KAMI -->
<section class="section" id="tentang">
  <div class="container">
    <div class="about-grid">
      <div class="about-img-wrap">
        <div class="about-img-main">
          <img src="assets/images/hero-banner.jpg" alt="Tim Sahabat Mebel â€” pengrajin furnitur profesional"
               onerror="this.parentElement.style.background='linear-gradient(135deg,#EDE5D8,#C9B89A)'">
        </div>
        <div class="about-img-badge">
          <div class="num">10+</div>
          <div class="lbl">Tahun<br>Pengalaman</div>
        </div>
      </div>

      <div class="about-text">
        <span class="section-tag">Tentang Kami</span>
        <h2 class="about-title">Pengrajin Furnitur Terpercaya sejak 2014</h2>
        <p class="about-desc">
          Sahabat Mebel lahir dari passion terhadap keindahan kayu dan kecintaan pada hunian yang nyaman. Selama lebih dari 10 tahun, kami telah membantu ribuan keluarga Indonesia mewujudkan furnitur impian mereka â€” dari yang sederhana hingga yang paling detail.
        </p>
        <p class="about-desc">
          Setiap produk kami kerjakan dengan tangan oleh pengrajin terampil menggunakan bahan pilihan terbaik. Kami percaya furnitur bukan sekadar barang, tapi investasi untuk kenyamanan keluarga Anda.
        </p>

        <div class="about-milestones">
          <div class="milestone">
            <div class="milestone-num">10.000+</div>
            <div class="milestone-label">Pelanggan Puas</div>
          </div>
          <div class="milestone">
            <div class="milestone-num">500+</div>
            <div class="milestone-label">Produk Tersedia</div>
          </div>
          <div class="milestone">
            <div class="milestone-num">50+</div>
            <div class="milestone-label">Pengrajin Terampil</div>
          </div>
          <div class="milestone">
            <div class="milestone-num">4.9/5</div>
            <div class="milestone-label">Rating Kepuasan</div>
          </div>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap">
          <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20mengenal%20lebih%20jauh"
             target="_blank" class="btn btn-wa"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Hubungi Kami</a>
          <a href="products.php" class="btn btn-outline">Lihat Produk</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-logo">Sahabat<span>Mebel</span></div>
      <p class="footer-desc">Furnitur premium custom untuk hunian modern. Kualitas yang terlihat, kenyamanan yang terasa â€” dibuat dengan hati oleh pengrajin terbaik Indonesia.</p>
      <div class="footer-social">
        <a href="#" class="social-link" title="Instagram">ðŸ“·</a>
        <a href="#" class="social-link" title="Facebook">ðŸ‘¥</a>
        <a href="#" class="social-link" title="TikTok">ðŸŽµ</a>
        <a href="https://wa.me/6281234567890" target="_blank" class="social-link" title="WhatsApp">ðŸ’¬</a>
      </div>
    </div>
    <div>
      <div class="footer-heading">Navigasi</div>
      <ul class="footer-links">
        <li><a href="#home">Beranda</a></li>
        <li><a href="products.php">Produk</a></li>

        <li><a href="#testimonials">Ulasan</a></li>
        <li><a href="#tentang">Tentang Kami</a></li>
        <li><a href="admin/login.php">Admin</a></li>
      </ul>
    </div>
    <div>
      <div class="footer-heading">Kategori Produk</div>
      <ul class="footer-links">
        <?php foreach($categories as $cat): ?>
        <li><a href="products.php?cat=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <li><a href="products.php">Lihat Semua â†’</a></li>
      </ul>
    </div>
    <div>
      <div class="footer-heading">Kontak Kami</div>
      <div class="footer-contact-item">
        <span class="icon">ðŸ“</span>
        <span>Jl. Furniture No. 123, Jakarta Selatan, DKI Jakarta 12345</span>
      </div>
      <div class="footer-contact-item">
        <span class="icon">ðŸ“ž</span>
        <a href="tel:+6281234567890">+62 812-3456-7890</a>
      </div>
      <div class="footer-contact-item">
        <span class="icon">âœ‰ï¸</span>
        <a href="mailto:halo@sahabatmebel.com">halo@sahabatmebel.com</a>
      </div>
      <div class="footer-contact-item">
        <span class="icon">ðŸ•</span>
        <span>Seninâ€“Sabtu: 08.00 â€“ 20.00 WIB</span>
      </div>
      <a href="https://wa.me/6281234567890?text=Halo%20Sahabat%20Mebel%2C%20saya%20ingin%20konsultasi"
         target="_blank" class="footer-wa-btn">
        ðŸ’¬ Chat WhatsApp Sekarang
      </a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>Â© <?= date('Y') ?> Sahabat Mebel. Semua hak dilindungi.</span>
    <span>Dibuat dengan â¤ï¸ untuk hunian terbaik Indonesia</span>
  </div>
</footer>

<!-- CART SIDEBAR -->
<div class="cart-overlay" id="cartOverlay"></div>
<aside class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <h2 class="cart-title">ðŸ›’ Keranjang Belanja</h2>
    <button class="cart-close" id="cartClose">âœ•</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <div class="cart-empty-icon">ðŸª‘</div>
      <p>Keranjang masih kosong.<br>Yuk mulai belanja!</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-total">
      <span class="cart-total-label">Total</span>
      <span class="cart-total-value" id="cartTotal">Rp 0</span>
    </div>
    <button class="btn btn-gold checkout-btn" id="checkoutBtn">Checkout â†’</button>
  </div>
</aside>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Selesaikan Pesanan</h2>
      <button class="cart-close" id="modalClose">âœ•</button>
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
          <label class="form-label" for="custNotes">Catatan / Permintaan Custom (opsional)</label>
          <textarea class="form-control" id="custNotes" placeholder="Contoh: warna cat, ukuran khusus, bahan pilihan..."></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer" id="modalFooter">
      <button class="btn btn-primary w-full" id="placeOrderBtn">Buat Pesanan ðŸ›’</button>
    </div>
  </div>
</div>

<!-- PRODUCT DETAIL MODAL -->
<div class="modal-overlay" id="productModal">
  <div class="modal" style="max-width:740px">
    <div class="modal-header">
      <h2 class="modal-title">Detail Produk</h2>
      <button class="cart-close" id="productModalClose">âœ•</button>
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
              <li>Bersihkan permukaan secara rutin dengan kain lembut dan kering</li>
              <li>Hindari paparan sinar matahari langsung terlalu lama</li>
              <li>Gunakan alas meja untuk melindungi dari goresan</li>
              <li>Oleskan minyak kayu setiap 6 bulan untuk mempertahankan kilap</li>
              <li>Segera bersihkan tumpahan cairan untuk mencegah noda permanen</li>
            </ul>
          </div>
          <div style="margin-top:1rem;padding:0.85rem 1rem;background:var(--cream-dark);border-radius:var(--radius-xs);font-size:0.83rem;color:var(--text-muted);border:1px solid var(--beige)">
            ðŸŽ¨ <strong style="color:var(--brown-dark)">Ingin custom ukuran atau warna?</strong><br>
            Chat kami di WhatsApp untuk permintaan khusus!
          </div>
          <div class="detail-qty" style="margin-top:1rem">
            <span style="font-weight:600;color:var(--text-mid)">Jumlah:</span>
            <div class="qty-control">
              <button class="qty-btn" id="detailQtyMinus">âˆ’</button>
              <span id="detailQtyVal">1</span>
              <button class="qty-btn" id="detailQtyPlus">+</button>
            </div>
          </div>
          <div style="display:flex;gap:0.75rem;flex-direction:column">
            <button class="btn btn-primary w-full" id="detailAddCart">ðŸ›’ Tambah ke Keranjang</button>
            <a href="#" id="detailWaBtn" target="_blank" class="btn btn-wa w-full" style="justify-content:center">ðŸ’¬ Tanya via WhatsApp</a>
          </div>
        </div>
      </div>
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

<!-- SOCIAL PROOF POPUP (NEW) -->
<div class="social-proof-popup" id="socialProofPopup">
  <div class="sp-dot"></div>
  <div class="sp-avatar" id="spAvatar">B</div>
  <div class="sp-content">
    <div class="sp-name" id="spName">Budi S.</div>
    <div class="sp-msg" id="spMsg">baru saja konsultasi furnitur custom ðŸª‘</div>
    <div class="sp-time" id="spTime">2 menit yang lalu</div>
  </div>
  <button class="sp-close" id="spClose">âœ•</button>
</div>

<!-- SCROLL TO TOP (NEW) -->
<button class="scroll-top-btn" id="scrollTopBtn" aria-label="Kembali ke atas">â†‘</button>

<script src="assets/js/app.js"></script>
</body>
</html>