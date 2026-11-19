CREATE DATABASE IF NOT EXISTS shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_db;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL CHECK (price > 0),
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    image VARCHAR(255) NULL
);

INSERT INTO products (name, price, stock, image) VALUES
('Bút bi Thiên Long TL-027', 5000, 50, NULL),
('Vở ô ly 96 trang Hồng Hà', 12000, 35, NULL),
('Thước kẻ nhựa dẻo 20cm', 8000, 40, NULL),
('Bút chì gỗ 2B Staedtler', 7000, 60, NULL),
('Tẩy chì gôm trắng Pentel', 10000, 25, NULL),
('Compa học sinh kim loại', 25000, 15, NULL),
('Hộp bút vải canvas đa năng', 45000, 12, NULL),
('Bút dạ quang Pastel 6 màu', 38000, 20, NULL),
('Giấy ghi chú Note 3x3 inch', 15000, 30, NULL),
('Bìa trình ký đơn khổ A4', 22000, 18, NULL),
('Kéo văn phòng lưỡi thép không gỉ', 28000, 10, NULL),
('Băng keo trong cuộn lớn', 14000, 45, NULL);
