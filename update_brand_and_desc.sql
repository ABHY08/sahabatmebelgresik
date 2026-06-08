-- ============================================================
-- Sahabat Mebel — Update Nama, Deskripsi & Gambar Produk
-- Sesuaikan nama & deskripsi produk dengan foto yang ada
-- Jalankan di phpMyAdmin atau MySQL CLI
-- ============================================================
USE furnihome;

-- ── Produk ID 1: sofa-minimalis.jpg → Sofabed Proceella ─────
UPDATE products SET
  name        = 'Sofabed Proceella',
  description = 'Sofabed multifungsi tipe Chiffon dengan bahan kain premium anti-kusut. Bisa berfungsi sebagai sofa maupun tempat tidur, rangka kayu solid, ukuran P:182 L:58 T:48 cm. Cocok untuk ruang tamu atau kamar tidur modern.',
  image       = 'sofa-minimalis.jpg',
  price       = 3200000,
  is_featured = 1
WHERE id = 1;

-- ── Produk ID 2: meja-makan-kayu.jpg → Set Meja Makan Kaca ──
UPDATE products SET
  name        = 'Set Meja Makan Kaca Marmer',
  description = 'Set meja makan modern dengan meja top kaca motif marmer hitam dan kaki besi kokoh. Sudah termasuk 4 kursi bahan oscar berkualitas. Ukuran meja 120x70 cm, anti rayap dan anti jamur.',
  image       = 'meja-makan-kayu.jpg',
  price       = 4800000,
  is_featured = 1
WHERE id = 2;

-- ── Produk ID 3: kursi-santai.jpg → Kursi Direktur ──────────
UPDATE products SET
  name        = 'Kursi Direktur Premium',
  description = 'Kursi direktur ergonomis tipe D011 berbahan kulit sintetis hitam premium. Rangka besi kuat dengan roda putar dan sistem tilt, sandaran tinggi untuk kenyamanan kerja seharian. Anti rayap dan anti jamur.',
  image       = 'kursi-santai.jpg',
  price       = 2500000,
  is_featured = 1
WHERE id = 3;

-- ── Produk ID 4: lemari-2pintu.jpg → Lemari Pakaian 2 Pintu ─
UPDATE products SET
  name        = 'Lemari Pakaian 2 Pintu Vannessa',
  description = 'Lemari pakaian 2 pintu tipe Vannessa LP 217 dengan bahan papan partikel berkualitas, finishing tekstur kayu elegan. Dilengkapi gantungan pakaian dan rak internal yang luas. Anti rayap, anti jamur, gratis ongkir & rakit.',
  image       = 'lemari-2pintu.jpg',
  price       = 3900000,
  is_featured = 1
WHERE id = 4;

-- ── Produk ID 5: Pakai meja-kantor.jpg → Meja Meeting ───────
UPDATE products SET
  name        = 'Meja Meeting Minimalis DCT 1890',
  description = 'Meja meeting / meja kantor tipe DCT 1890 dengan bahan partikel board premium. Desain minimalis modern cocok untuk ruang rapat atau ruang kerja. Ukuran P:180 L:90 T:75 cm. Rangka kokoh, gratis ongkir.',
  image       = 'meja-kantor.jpg',
  price       = 2900000,
  is_featured = 0
WHERE id = 5;

-- ── Produk ID 6: Pakai meja-rias.jpg → Meja Rias LED ────────
UPDATE products SET
  name        = 'Meja Rias LED Touchscreen',
  description = 'Meja rias premium tipe 20 dengan cermin LED touchscreen 3 mode warna. Dilengkapi 2 laci penyimpanan luas dan gratis kursi stool. Bahan MDF finishing putih glossy elegan. Sempurna untuk kamar tidur Anda.',
  image       = 'meja-rias.jpg',
  price       = 1850000,
  is_featured = 0
WHERE id = 6;

-- ── Produk ID 7: Pakai rak-sepatu.jpg → Rak Sepatu ──────────
UPDATE products SET
  name        = 'Rak Sepatu Serbaguna RS 001',
  description = 'Rak sepatu serbaguna tipe RS 001 dengan pintu kaca transparan 2 panel dan 2 laci atas. Kapasitas besar, bahan MDF anti rayap & anti jamur. Ukuran P:120 L:34 T:80 cm. Tersedia gratis ongkir & rakit area SDA-SBY-GSK.',
  image       = 'rak-sepatu.jpg',
  price       = 1350000,
  is_featured = 0
WHERE id = 7;

-- ── Produk ID 8: Pakai springbed.jpg → Springbed ─────────────
UPDATE products SET
  name        = 'Springbed Pocket Spring Serena',
  description = 'Kasur springbed premium tipe Serena Premier Pocket Spring dengan teknologi per individual yang meminimalkan transfer gerak. Permukaan quilted lembut, headboard berlapis linen elegan, cocok untuk kamar tidur modern minimalis.',
  image       = 'springbed.jpg',
  price       = 7500000,
  is_featured = 0
WHERE id = 8;

-- ── Update setting situs: pastikan branding Sahabat Mebel ────
UPDATE site_settings
  SET setting_value = 'Sahabat Mebel — Furnitur premium untuk hunian modern. Dibuat dengan penuh perhatian, dikirim ke depan pintu Anda.'
  WHERE setting_key = 'site_description';

UPDATE site_settings
  SET setting_value = 'Temukan furnitur premium buatan tangan yang menghadirkan kehangatan, gaya, dan keanggunan di setiap sudut rumah Anda.'
  WHERE setting_key = 'hero_subtitle';

-- Selesai. Jalankan SELECT untuk verifikasi:
-- SELECT id, name, image FROM products ORDER BY id;
