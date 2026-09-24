CREATE DATABASE IF NOT EXISTS hotel_db;

USE hotel_db;

-- =========================================
-- MENU ITEMS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    available TINYINT(1) NOT NULL DEFAULT 1
);

-- =========================================
-- ORDERS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    table_number VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('NEW', 'ACCEPTED', 'COMPLETED') NOT NULL DEFAULT 'NEW',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- ORDER ITEMS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    FOREIGN KEY (menu_item_id)
        REFERENCES menu_items(id)
        ON DELETE RESTRICT
);

-- =========================================
-- INSERT MENU ITEMS
-- =========================================

INSERT INTO menu_items (name, price, image, available)
VALUES
('Chicken Biryani', 180.00, 'biryani.jpg', 1),
('Veg Biryani', 120.00, 'veg-biryani.jpg', 1),
('Manchurian', 100.00, 'manchurian.jpg', 1),
('Noodles', 90.00, 'noodles.jpg', 1),
('Roti', 30.00, 'roti.jpg', 1);