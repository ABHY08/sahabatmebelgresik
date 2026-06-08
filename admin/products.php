<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$msg = '';
$msgType = 'success';

// ── Handle POST actions ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Helper: handle image upload
    function handleImageUpload($file, $oldImage = '') {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return $oldImage;
        $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
        if (!in_array($file['type'], $allowed)) return $oldImage;
        if ($file['size'] > 5 * 1024 * 1024) return $oldImage; // 5MB max

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName  = 'prod_' . uniqid() . '.' . strtolower($ext);
        $dest     = UPLOAD_DIR . $newName;

        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            // Delete old image if exists
            if ($oldImage && file_exists(UPLOAD_DIR . $oldImage)) {
                @unlink(UPLOAD_DIR . $oldImage);
            }
            return $newName;
        }
        return $oldImage;
    }

    if ($action === 'add') {
        $name       = trim($_POST['name'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $price      = floatval($_POST['price'] ?? 0);
        $stock      = intval($_POST['stock'] ?? 0);
        $cat        = intval($_POST['category_id'] ?? 0);
        $featured   = isset($_POST['is_featured']) ? 1 : 0;
        $image      = handleImageUpload($_FILES['image'] ?? null);

        if ($name && $price > 0 && $cat > 0) {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, image, stock, is_featured) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$cat, $name, $desc, $price, $image, $stock, $featured]);
            $msg = 'Produk berhasil ditambahkan!';
        } else {
            $msg = 'Lengkapi semua kolom wajib.'; $msgType = 'error';
        }
    }

    if ($action === 'edit') {
        $id       = intval($_POST['id']);
        $name     = trim($_POST['name'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $price    = floatval($_POST['price'] ?? 0);
        $stock    = intval($_POST['stock'] ?? 0);
        $cat      = intval($_POST['category_id'] ?? 0);
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $oldImg   = $_POST['old_image'] ?? '';
        $image    = handleImageUpload($_FILES['image'] ?? null, $oldImg) ?: $oldImg;

        $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, image=?, stock=?, is_featured=? WHERE id=?");
        $stmt->execute([$cat, $name, $desc, $price, $image, $stock, $featured, $id]);
        $msg = 'Produk berhasil diperbarui!';
    }

    if ($action === 'delete') {
        $id  = intval($_POST['id']);
        $row = $pdo->prepare("SELECT image FROM products WHERE id=?"); $row->execute([$id]); $row = $row->fetch();
        if ($row && $row['image'] && file_exists(UPLOAD_DIR . $row['image'])) @unlink(UPLOAD_DIR . $row['image']);
        $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
        $msg = 'Produk berhasil dihapus.';
    }
}

// ── Fetch data ──────────────────────────────────────────
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$products   = $pdo->query("
  SELECT p.*, c.name AS cat_name
  FROM products p
  JOIN categories c ON p.category_id = c.id
  ORDER BY p.is_featured DESC, p.created_at DESC
")->fetchAll();

// Edit mode
$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([intval($_GET['edit'])]);
    $editProduct = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kelola Produk — Sahabat Mebel Admin</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <style>
    /* ── Product Management Extras ── */
    .prod-table-img {
      width: 56px; height: 44px; border-radius: 8px;
      object-fit: cover; background: var(--beige);
      border: 1px solid var(--beige-dark);
    }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-grid .span-2 { grid-column: 1 / -1; }

    /* Image upload preview */
    .img-upload-wrap {
      border: 2px dashed var(--beige-dark);
      border-radius: 12px; padding: 1.5rem;
      text-align: center; cursor: pointer;
      transition: all 0.3s; position: relative;
      background: var(--cream);
    }
    .img-upload-wrap:hover { border-color: var(--brown); background: var(--beige); }
    .img-upload-wrap input[type="file"] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .img-upload-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .img-upload-label { font-size: 0.85rem; color: var(--text-muted); }
    .img-preview {
      width: 100%; max-height: 180px; object-fit: cover;
      border-radius: 8px; margin-top: 0.75rem; display: none;
    }
    .img-preview.show { display: block; }
    .current-img-wrap { margin-bottom: 0.75rem; }
    .current-img { width: 100px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--beige-dark); }

    /* Inline alert */
    .alert {
      padding: 0.9rem 1.2rem; border-radius: 10px; margin-bottom: 1.2rem;
      font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;
    }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Toggle badge */
    .badge-featured { background: #fff3cd; color: #856404; padding: 2px 10px; border-radius: 50px; font-size: 0.72rem; font-weight: 700; }
    .badge-normal   { background: var(--beige); color: var(--text-muted); padding: 2px 10px; border-radius: 50px; font-size: 0.72rem; }

    /* Responsive table */
    .table-wrap { overflow-x: auto; }
    .form-section {
      background: var(--white); border-radius: 16px;
      padding: 1.5rem 2rem; box-shadow: var(--shadow-sm);
      border: 1px solid var(--beige-dark);
      margin-bottom: 2rem;
    }
    .form-section h2 { font-size: 1.1rem; color: var(--brown-dark); margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; }
  </style>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>">
      <?= $msgType === 'success' ? '✓' : '✕' ?> <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- ── FORM ADD / EDIT ── -->
    <div class="form-section">
      <h2><?= $editProduct ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?></h2>
      <form method="POST" enctype="multipart/form-data" id="productForm">
        <input type="hidden" name="action" value="<?= $editProduct ? 'edit' : 'add' ?>">
        <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>">
        <?php endif; ?>

        <div class="form-grid">
          <!-- Product Name -->
          <div class="form-group">
            <label class="form-label">Nama Produk *</label>
            <input type="text" name="name" class="form-control"
              value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>"
              placeholder="Contoh: Sofa Minimalis Velvet" required>
          </div>

          <!-- Category -->
          <div class="form-group">
            <label class="form-label">Kategori *</label>
            <select name="category_id" class="form-control" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                <?= ($editProduct && $editProduct['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Price -->
          <div class="form-group">
            <label class="form-label">Harga (Rp) *</label>
            <input type="number" name="price" class="form-control"
              value="<?= $editProduct['price'] ?? '' ?>"
              placeholder="Contoh: 3500000" min="0" required>
          </div>

          <!-- Stock -->
          <div class="form-group">
            <label class="form-label">Stok</label>
            <input type="number" name="stock" class="form-control"
              value="<?= $editProduct['stock'] ?? 0 ?>"
              placeholder="0" min="0">
          </div>

          <!-- Description -->
          <div class="form-group span-2">
            <label class="form-label">Deskripsi Produk</label>
            <textarea name="description" class="form-control" rows="3"
              placeholder="Deskripsi singkat dan menarik tentang produk ini..."><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
          </div>

          <!-- Image Upload -->
          <div class="form-group span-2">
            <label class="form-label">Gambar Produk <?= $editProduct ? '(kosongkan jika tidak ingin mengganti)' : '' ?></label>

            <?php if ($editProduct && !empty($editProduct['image'])): ?>
            <div class="current-img-wrap">
              <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:6px">Gambar saat ini:</p>
              <img class="current-img" id="currentImg"
                src="../assets/images/products/<?= htmlspecialchars($editProduct['image']) ?>"
                alt="<?= htmlspecialchars($editProduct['name']) ?>"
                onerror="this.src='../assets/images/placeholder.jpg'">
            </div>
            <?php endif; ?>

            <div class="img-upload-wrap" id="uploadWrap">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp"
                onchange="previewImage(this)">
              <div class="img-upload-icon">🖼️</div>
              <div class="img-upload-label">Klik atau drag & drop gambar (JPG, PNG, WebP — maks 5MB)</div>
              <img class="img-preview" id="imgPreview" src="" alt="Preview">
            </div>
          </div>

          <!-- Featured toggle -->
          <div class="form-group">
            <label class="form-label" style="display:flex;align-items:center;gap:10px;cursor:pointer">
              <input type="checkbox" name="is_featured" id="featuredCheck" style="width:18px;height:18px;accent-color:var(--brown-dark)"
                <?= ($editProduct && $editProduct['is_featured']) ? 'checked' : '' ?>>
              Tampilkan sebagai Produk Unggulan
            </label>
          </div>
        </div>

        <div style="display:flex;gap:1rem;margin-top:0.5rem">
          <button type="submit" class="btn btn-primary">
            <?= $editProduct ? '💾 Simpan Perubahan' : '➕ Tambah Produk' ?>
          </button>
          <?php if ($editProduct): ?>
          <a href="products.php" class="btn btn-outline">✕ Batal Edit</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- ── PRODUCT TABLE ── -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">🪑 Semua Produk (<?= count($products) ?>)</span>
        <input type="text" id="searchInput" class="form-control" style="width:220px;padding:0.45rem 0.9rem"
          placeholder="🔍 Cari produk..." oninput="filterTable(this.value)">
      </div>
      <div class="card-body table-wrap">
        <table id="prodTable">
          <thead>
            <tr>
              <th style="width:60px">Gambar</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Stok</th>
              <th>Status</th>
              <th style="width:120px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
            <tr class="prod-row">
              <td>
                <img class="prod-table-img"
                  src="../assets/images/products/<?= htmlspecialchars($p['image'] ?? '') ?>"
                  alt="<?= htmlspecialchars($p['name']) ?>"
                  onerror="this.src='../assets/images/placeholder.jpg'">
              </td>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong><br>
                <span style="font-size:0.78rem;color:var(--text-muted)"><?= mb_strimwidth(htmlspecialchars($p['description'] ?? ''), 0, 60, '…') ?></span>
              </td>
              <td><?= htmlspecialchars($p['cat_name']) ?></td>
              <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
              <td><?= $p['stock'] ?></td>
              <td>
                <?php if ($p['is_featured']): ?>
                  <span class="badge-featured">⭐ Unggulan</span>
                <?php else: ?>
                  <span class="badge-normal">Biasa</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="products.php?edit=<?= $p['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
                <form method="POST" style="display:inline"
                  onsubmit="return confirm('Hapus produk ini?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#b91c1c;border-color:#fca5a5">🗑️</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">
              Belum ada produk. Tambahkan produk pertama Anda! 🪑
            </td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script>
// Image preview
function previewImage(input) {
  const preview = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.add('show');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Live table search
function filterTable(query) {
  const rows = document.querySelectorAll('.prod-row');
  const q = query.toLowerCase();
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

// Drag-and-drop feedback
const wrap = document.getElementById('uploadWrap');
if (wrap) {
  wrap.addEventListener('dragover', e => { e.preventDefault(); wrap.style.borderColor = 'var(--brown)'; });
  wrap.addEventListener('dragleave', () => { wrap.style.borderColor = ''; });
  wrap.addEventListener('drop', e => {
    e.preventDefault(); wrap.style.borderColor = '';
    const dt = e.dataTransfer;
    if (dt.files.length) {
      document.getElementById('imageInput').files = dt.files;
      previewImage(document.getElementById('imageInput'));
    }
  });
}

// Auto-scroll to form if editing
<?php if ($editProduct): ?>
document.getElementById('productForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
<?php endif; ?>
</script>
</body>
</html>
