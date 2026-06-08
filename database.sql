-- Sahabat Mebel Database Schema
CREATE DATABASE IF NOT EXISTS furnihome CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE furnihome;

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    customer_address TEXT NOT NULL,
    customer_whatsapp VARCHAR(20) NOT NULL,
    notes TEXT,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site settings table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default admin (password: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Default categories
INSERT INTO categories (name, slug) VALUES
('Sofas', 'sofas'),
('Tables', 'tables'),
('Chairs', 'chairs'),
('Storage', 'storage');

-- Default products
INSERT INTO products (category_id, name, description, price, image, stock, is_featured) VALUES
(1, 'Sofa Minimalis Velvet', 'Sofa mewah berbahan velvet premium dengan kaki kayu solid. Desain minimalis elegan cocok untuk ruang tamu modern. Tersedia dalam berbagai pilihan warna menarik.', 8500000, 'sofa-minimalis.jpg', 15, 1),
(2, 'Meja Makan Kayu Jati', 'Meja makan solid kayu jati dengan finishing natural. Kapasitas 6 orang, dibuat oleh pengrajin terampil dengan detail ukiran halus dan tahan lama.', 6200000, 'meja-makan-kayu.jpg', 8, 1),
(3, 'Kursi Santai Premium', 'Kursi santai ergonomis berbahan kain premium dengan rangka kayu solid. Desain Skandinavia yang elegan, nyaman untuk bersantai di rumah.', 3800000, 'kursi-santai.jpg', 20, 1),
(4, 'Lemari 2 Pintu Modern', 'Lemari pakaian 2 pintu dengan cermin besar dan rak penyimpanan internal yang luas. Finishing walnut elegan, cocok untuk kamar tidur minimalis maupun klasik.', 4500000, 'lemari-2pintu.jpg', 12, 1),
(1, 'Sofa L-Shape Corner', 'Sofa sudut L-shape luas dengan chaise lounge. Busa densitas tinggi dengan kain premium yang tahan lama dan mudah dibersihkan, cocok untuk keluarga.', 12500000, 'sofa-minimalis.jpg', 5, 0),
(2, 'Meja Kopi Minimalis', 'Meja kopi kayu minimalis dengan permukaan halus dan kaki besi hitam kokoh. Desain sederhana namun elegan untuk ruang tamu Anda.', 2800000, 'meja-makan-kayu.jpg', 10, 0),
(3, 'Kursi Makan Skandinavia', 'Kursi makan desain Skandinavia dengan dudukan empuk dan rangka kayu alami. Ringan, kuat, dan cocok dipadukan dengan berbagai meja makan.', 1500000, 'kursi-santai.jpg', 18, 0),
(4, 'Lemari Rak Terbuka', 'Lemari rak terbuka serbaguna dengan 5 susun penyimpanan luas. Ideal untuk buku, pajangan, maupun perlengkapan dapur. Mudah dirakit.', 2100000, 'lemari-2pintu.jpg', 25, 0);

-- Default testimonials
INSERT INTO testimonials (customer_name, content, rating, is_active) VALUES
('Sarah Miller', 'The quality is outstanding! My velvet sofa arrived perfectly and looks even better in person. The delivery team was professional and set everything up for me.', 5, 1),
('James Wilson', 'Fast shipping and excellent customer service. The dining table is exactly what I wanted. The oak finish is beautiful and matches perfectly with my dining room.', 5, 1),
('Emily Davis', 'Beautiful craftsmanship. The attention to detail in each piece is remarkable. I bought the leather accent chair and I am absolutely in love with it!', 5, 1);

-- Default site settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('hero_title', 'Elevate Your Living Space'),
('hero_subtitle', 'Temukan furnitur premium buatan tangan yang menghadirkan kehangatan, gaya, dan keanggunan di setiap sudut rumah Anda.'),
('hero_banner', 'hero-banner.jpg'),
('site_description', 'Sahabat Mebel — Furnitur premium untuk hunian modern. Dibuat dengan penuh perhatian, dikirim ke depan pintu Anda.'),
('whatsapp_number', '6281234567890'),
('address', 'Jl. Furniture No. 1, Jakarta, Indonesia');
