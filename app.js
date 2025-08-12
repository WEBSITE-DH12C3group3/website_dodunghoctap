const express = require('express');
const path = require('path');

const app = express();

// Cấu hình EJS
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Chỉ định thư mục public cho file tĩnh (CSS, JS, ảnh)
app.use(express.static(path.join(__dirname, 'public')));

// Route trang chủ
app.get('/', (req, res) => {
  res.render('pages/registers', { title: 'Cửa hàng đồ dùng học tập' });
});

// Chạy server
app.listen(3000, () => {
  console.log('Server chạy tại http://localhost:3000');
});
