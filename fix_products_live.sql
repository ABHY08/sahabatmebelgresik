-- ============================================================
-- Sahabat Mebel — Fix Live Database
-- Jalankan di phpMyAdmin untuk update data yang sudah ada
-- ============================================================
USE furnihome;

-- ── Update gambar produk sesuai file yang tersedia ──────────
UPDATE products SET image = 'sofa-minimalis.jpg'   WHERE id = 1;
UPDATE products SET image = 'meja-makan-kayu.jpg'  WHERE id = 2;
UPDATE products SET image = 'kursi-santai.jpg'     WHERE id = 3;
UPDATE products SET image = 'lemari-2pintu.jpg'    WHERE id = 4;
UPDATE products SET image = 'sofa-minimalis.jpg'   WHERE id = 5;
UPDATE products SET image = 'meja-makan-kayu.jpg'  WHERE id = 6;
UPDATE products SET image = 'kursi-santai.jpg'     WHERE id = 7;
UPDATE products SET image = 'lemari-2pintu.jpg'    WHERE id = 8;

-- ── Update nama & deskripsi produk (sesuai nama & foto) ─────
UPDATE products SET
  name        = 'Sofa Minimalis Velvet',
  description = 'Sofa mewah berbahan velvet premium dengan kaki kayu solid. Desain minimalis elegan cocok untuk ruang tamu modern. Tersedia dalam berbagai pilihan warna menarik.',
  price       = 8500000,
  is_featured = 1
WHERE id = 1;

UPDATE products SET
  name        = 'Meja Makan Kayu Jati',
  description = 'Meja makan solid kayu jati dengan finishing natural. Kapasitas 6 orang, dibuat oleh pengrajin terampil dengan detail ukiran halus dan tahan lama.',
  price       = 6200000,
  is_featured = 1
WHERE id = 2;

UPDATE products SET
  name        = 'Kursi Santai Premium',
  description = 'Kursi santai ergonomis berbahan kain premium dengan rangka kayu solid. Desain Skandinavia yang elegan, nyaman untuk bersantai di rumah.',
  price       = 3800000,
  is_featured = 1
WHERE id = 3;

UPDATE products SET
  name        = 'Lemari 2 Pintu Modern',
  description = 'Lemari pakaian 2 pintu dengan cermin besar dan rak penyimpanan internal yang luas. Finishing walnut elegan, cocok untuk kamar tidur minimalis maupun klasik.',
  price       = 4500000,
  is_featured = 1
WHERE id = 4;

UPDATE products SET
  name        = 'Sofa L-Shape Corner',
  description = 'Sofa sudut L-shape luas dengan chaise lounge. Busa densitas tinggi dengan kain premium yang tahan lama dan mudah dibersihkan, cocok untuk keluarga.',
  price       = 12500000,
  is_featured = 0
WHERE id = 5;

UPDATE products SET
  name        = 'Meja Kopi Minimalis',
  description = 'Meja kopi kayu minimalis dengan permukaan halus dan kaki besi hitam kokoh. Desain sederhana namun elegan untuk ruang tamu Anda.',
  price       = 2800000,
  is_featured = 0
WHERE id = 6;

UPDATE products SET
  name        = 'Kursi Makan Skandinavia',
  description = 'Kursi makan desain Skandinavia dengan dudukan empuk dan rangka kayu alami. Ringan, kuat, dan cocok dipadukan dengan berbagai meja makan.',
  price       = 1500000,
  is_featured = 0
WHERE id = 7;

UPDATE products SET
  name        = 'Lemari Rak Terbuka',
  description = 'Lemari rak terbuka serbaguna dengan 5 susun penyimpanan luas. Ideal untuk buku, pajangan, maupun perlengkapan dapur. Mudah dirakit.',
  price       = 2100000,
  is_featured = 0
WHERE id = 8;

-- ── Update setting situs: ganti FurniHome → Sahabat Mebel ───
UPDATE site_settings
  SET setting_value = 'Sahabat Mebel — Furnitur premium untuk hunian modern. Dibuat dengan penuh perhatian, dikirim ke depan pintu Anda.'
  WHERE setting_key = 'site_description';

UPDATE site_settings
  SET setting_value = 'Temukan furnitur premium buatan tangan yang menghadirkan kehangatan, gaya, dan keanggunan di setiap sudut rumah Anda.'
  WHERE setting_key = 'hero_subtitle';
