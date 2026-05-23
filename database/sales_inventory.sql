CREATE DATABASE IF NOT EXISTS sales_inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sales_inventory_db;

DROP TABLE IF EXISTS order_logs, payments, order_items, orders, products, categories, customers, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  contact_number VARCHAR(30),
  email VARCHAR(120),
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(120) NOT NULL UNIQUE,
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(60) NOT NULL UNIQUE,
  product_name VARCHAR(150) NOT NULL,
  category_id INT NOT NULL,
  stock_on_hand INT NOT NULL DEFAULT 0,
  low_stock_alert INT NOT NULL DEFAULT 5,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  cost_per_item DECIMAL(12,2) NOT NULL DEFAULT 0,
  product_photo VARCHAR(255),
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(40) NOT NULL UNIQUE,
  order_date DATE NOT NULL,
  customer_id INT NOT NULL,
  payment_type VARCHAR(40) DEFAULT 'Cash',
  discount DECIMAL(12,2) DEFAULT 0,
  shipping_fee DECIMAL(12,2) DEFAULT 0,
  subtotal DECIMAL(12,2) DEFAULT 0,
  total_amount DECIMAL(12,2) DEFAULT 0,
  total_cost DECIMAL(12,2) DEFAULT 0,
  status ENUM('Draft','Unpaid','Partial','Paid','Overdue','Cancelled') DEFAULT 'Draft',
  due_date DATE NULL,
  is_confirmed TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON UPDATE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  unit_cost DECIMAL(12,2) NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  payment_method VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE order_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  activity VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO users (username,email,password_hash) VALUES
('admin','admin@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'); -- password: password

INSERT INTO customers (name, contact_number, email, address) VALUES
('Juan Dela Cruz','09171234567','juan@example.com','Makati City'),
('Maria Santos','09181234567','maria@example.com','Taguig City');

INSERT INTO categories (category_name,status) VALUES
('Electronics','Active'),('Accessories','Active'),('Office Supplies','Active');

INSERT INTO products (sku,product_name,category_id,stock_on_hand,low_stock_alert,price,cost_per_item,status) VALUES
('SKU-001','Wireless Mouse',2,30,5,450,250,'Active'),
('SKU-002','Mechanical Keyboard',2,15,5,1850,1200,'Active'),
('SKU-003','USB-C Cable',2,4,10,180,80,'Active'),
('SKU-004','Notebook',3,100,20,65,35,'Active');
