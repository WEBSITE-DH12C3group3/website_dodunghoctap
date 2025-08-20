-- Tạo database sellschoolsuoolies nếu chưa có và chọn sử dụng
CREATE DATABASE IF NOT EXISTS sellschoolsupplies CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sellschoolsupplies;

-- **Bảng giữ nguyên từ database bán hoa (giả sử bạn đã có các bảng này):**

-- 1. users (giữ nguyên cấu trúc)
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. categories (giữ nguyên)
CREATE TABLE IF NOT EXISTS categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL,
  description TEXT
);

-- 3. products (giữ nguyên cấu trúc, bạn thay đổi dữ liệu nội dung phù hợp đồ dùng học tập)
CREATE TABLE IF NOT EXISTS products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(255) NOT NULL,
  category_id INT,
  brand_id INT, -- nếu chưa có trường này trong db bán hoa, bạn cần thêm hoặc tạo bảng brands mới và update trường này cho phù hợp
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock_quantity INT DEFAULT 0,
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
  -- , FOREIGN KEY (brand_id) REFERENCES brands(brand_id) -- thêm nếu bạn bổ sung bảng brands
);

-- Bảng attributes: Lưu danh sách các loại thuộc tính (ví dụ: Màu sắc, Kích thước, Chất liệu)
CREATE TABLE attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng product_attributes: Lưu giá trị thuộc tính cụ thể cho từng sản phẩm
CREATE TABLE product_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
);

-- 4. comments
CREATE TABLE IF NOT EXISTS comments (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  user_id INT,
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(product_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 5. favourite
CREATE TABLE IF NOT EXISTS favourite (
  favourite_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 6. suppliers (đã có trong db bán hoa hoặc tạo mới nếu chưa)
CREATE TABLE IF NOT EXISTS suppliers (
  supplier_id INT AUTO_INCREMENT PRIMARY KEY,
  supplier_name VARCHAR(255) NOT NULL,
  contact_info TEXT
);

-- 7. orders
CREATE TABLE IF NOT EXISTS orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(50) DEFAULT 'pending',
  total_amount DECIMAL(15,2),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 8. order_items (hoặc orders_item tùy tên bạn dùng)
CREATE TABLE IF NOT EXISTS order_items (
  order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(order_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 9. delivery
CREATE TABLE IF NOT EXISTS delivery (
  delivery_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  receiver_name VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  email VARCHAR(255),
  address TEXT,
  note TEXT,
  delivery_status VARCHAR(50) DEFAULT 'pending',
  expected_delivery_date DATE,
  FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- 10. contact_submissions (hoặc contacsubmit theo tên bạn dùng)
CREATE TABLE IF NOT EXISTS contact_submissions (
  submission_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  phone VARCHAR(20),
  message TEXT,
  submit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. momo (giữ lại cấu trúc nếu có trong database bán hoa)
CREATE TABLE IF NOT EXISTS momo (
  momo_payment_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  transaction_id VARCHAR(255),
  amount DECIMAL(15,2),
  payment_status VARCHAR(50),
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- 12. vnpay (giữ nguyên)
CREATE TABLE IF NOT EXISTS vnpay (
  vnpay_payment_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  transaction_id VARCHAR(255),
  amount DECIMAL(15,2),
  payment_status VARCHAR(50),
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- **Bảng bổ sung mới chỉ tạo nếu chưa có trong db bán hoa:**

-- 13. brands (Thương hiệu sản phẩm)
CREATE TABLE IF NOT EXISTS brands (
  brand_id INT AUTO_INCREMENT PRIMARY KEY,
  brand_name VARCHAR(100) NOT NULL,
  description TEXT
);

-- Nếu bảng `brand_id` không có trong bảng products ở db bán hoa, bạn cần ALTER thêm trường này, ví dụ:
-- ALTER TABLE products ADD COLUMN brand_id INT NULL AFTER category_id;
-- ALTER TABLE products ADD FOREIGN KEY (brand_id) REFERENCES brands(brand_id);

-- 14. purchase_orders (phiếu nhập hàng)
CREATE TABLE IF NOT EXISTS purchase_orders (
  purchase_order_id INT AUTO_INCREMENT PRIMARY KEY,
  supplier_id INT,
  order_date DATE,
  total_amount DECIMAL(15,2),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

-- 15. purchase_order_items (chi tiết phiếu nhập)
CREATE TABLE IF NOT EXISTS purchase_order_items (
  purchase_order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  purchase_order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(purchase_order_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 16. coupons (mã giảm giá - tùy chọn)
CREATE TABLE IF NOT EXISTS coupons (
  coupon_id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL,
  description TEXT,
  discount_amount DECIMAL(10,2),
  discount_percent TINYINT,
  valid_from DATE,
  valid_to DATE,
  usage_limit INT,
  used_count INT DEFAULT 0
);
