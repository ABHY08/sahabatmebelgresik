-- ============================================================
-- Sahabat Mebel — Tambah Produk Baru
-- ============================================================
USE furnihome;

-- Kategori sudah ada (kasur=5, meja-kerja=6, rak-khusus=7, lemari=4, meja=2)

INSERT INTO products (category_id, name, slug, description, price, image, is_featured) VALUES

-- Meja Rias → kategori lemari (id=4)
(4,
 'Meja Rias Cermin Minimalis',
 'meja-rias-cermin-minimalis',
 'Meja rias modern dengan cermin besar dan laci penyimpanan di kedua sisi. Rangka kayu solid finishing walnut elegan, cocok untuk kamar tidur minimalis maupun mewah.',
 3200000,
 'meja-rias.jpg',
 1),

-- Meja Kantor → kategori meja-kerja (id=6)
(6,
 'Meja Kantor Skandinavia',
 'meja-kantor-skandinavia',
 'Meja kerja desain Skandinavia dengan permukaan kayu terang dan kaki besi hitam kokoh. Dilengkapi manajemen kabel rapi, ideal untuk home office maupun ruang kerja profesional.',
 2750000,
 'meja-kantor.jpg',
 1),

-- Springbed → kategori kasur (id=5)
(5,
 'Springbed Premium Walnut',
 'springbed-premium-walnut',
 'Set kasur springbed premium dengan busa pegas berkualitas tinggi, lapisan kain quilted lembut, dan rangka tempat tidur kayu walnut dengan headboard elegan. Tidur lebih nyaman setiap malam.',
 8900000,
 'springbed.jpg',
 1),

-- Kasur Spons → kategori kasur (id=5)
(5,
 'Kasur Busa High Density',
 'kasur-busa-high-density',
 'Kasur busa high density dengan lapisan kain abu-abu premium yang breathable. Nyaman untuk tidur harian, ringan dan mudah dipindahkan. Tersedia ukuran single, double, dan queen.',
 1350000,
 'kasur-spons.jpg',
 0),

-- Rak Sepatu → kategori rak-khusus (id=7)
(7,
 'Rak Sepatu 5 Susun Modern',
 'rak-sepatu-5-susun-modern',
 'Rak sepatu 5 tingkat dengan desain minimalis modern, kombinasi kayu natural dan panel putih bersih. Kapasitas hingga 20 pasang sepatu, kokoh dan mudah dirakit.',
 850000,
 'rak-sepatu.jpg',
 0);
