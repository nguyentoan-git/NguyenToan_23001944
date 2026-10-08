CREATE DATABASE IF NOT EXISTS shopping_cart ;
USE shopping_cart;

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
);

INSERT INTO products (name, price, quantity) VALUES
('Điện thoại iPhone 15', 24990000.00, 10),
('Laptop Asus Zenbook', 21500000.00, 5),
('Tai nghe Sony WH-1000XM5', 6500000.00, 15),
('Bàn phím cơ Logitech G Pro', 2990000.00, 8),
('Nồi chiên không dầu', 6500000.00, 2),
('RAM máy tính', 2000000.00,2),
('Chuột không dây Razer DeathAdder', 1250000.00, 20);

