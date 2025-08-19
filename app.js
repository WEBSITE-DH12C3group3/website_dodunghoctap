const express = require('express');
const path = require('path');
const session = require('express-session');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware khác
const setUser = require('./middlewares/setUser');

// View engine & Views
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Static files
app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'public', 'uploads')));

// Body parsers
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// Session
app.use(
  session({
    secret: 'secret-key',
    resave: false,
    saveUninitialized: true,
  })
);

// Middleware setUser và locals
app.use(setUser);
app.use((req, res, next) => {
  res.locals.user = req.session.user || null;
  res.locals.email = '';
  res.locals.error = '';
  next();
});

// Import các router khác
const favoriteRoutes = require('./routes/favorites.routes');
const userRoutes = require('./routes/user.routes');
const cartRoutes = require('./routes/cart.routes');
const authRoutes = require('./routes/auth.routes');
const resetPasswordRoutes = require('./routes/resetpassword.routes');
const forgotPasswordRoutes = require('./routes/forgotpassword.routes');
const contactRoutes = require('./routes/contact.routes');

// Router quản trị sản phẩm (duy nhất)
const productAdminRouter = require('./routes/product.routes');

// Trang chủ
app.get('/', (req, res) => {
  res.render('pages/home', {
    user: req.session.user || null,
    title: 'Trang chủ',
  });
});

// Auth & User
app.use('/', authRoutes);
app.use('/', userRoutes);

// Đăng xuất
app.get('/logout', (req, res) => {
  req.session.destroy(() => res.redirect('/login'));
});

// Yêu thích & Giỏ hàng
app.use('/liked', favoriteRoutes);
app.use('/cart', cartRoutes);

// Mount router sản phẩm admin tại /admin/products
app.use('/admin/products', productAdminRouter);

// Redirect /admin → /admin/products (tuỳ chọn)
app.get('/admin', (req, res) => res.redirect('/admin/products'));

// Các router còn lại
app.use('/resetpassword', resetPasswordRoutes);
app.use('/change', forgotPasswordRoutes);
app.use('/partials/contact', contactRoutes);

// About page
app.get('/about', (req, res) => {
  res.render('partials/about', {
    user: req.session.user || null,
    title: 'Giới thiệu',
  });
});

// Lắng nghe
app.listen(PORT, () => {
  console.log(`Server chạy tại http://localhost:${PORT}`);
});
