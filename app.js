const express = require("express");
const path = require("path");
const app = express();
const PORT = 3000;
const session = require("express-session");
const favoriteRoutes = require("./routes/favorites.routes");
const userRoutes = require("./routes/user.routes");
const cartRoutes = require("./routes/cart.routes");
const db = require("./config/db");
const productRoutes = require('./routes/product.routes');
// Import middleware
const setUser = require("./middlewares/setUser");

app.set("view engine", "ejs");

app.set("views", path.join(__dirname, "views"));

app.use(express.static(path.join(__dirname, "public")));

app.use(express.urlencoded({ extended: true }));

app.use(
  session({
    secret: "secret-key",
    resave: false,
    saveUninitialized: true,
  })
);
app.use(setUser);
//kiểm tra người dùng đã đăng nhập hay chưa
app.use((req, res, next) => {
  res.locals.user = req.session.user || null; 
  next();
});

app.use((req, res, next) => {
  res.locals.email = "";
  res.locals.error = "";
  next();
});

// Trang chủ
app.get("/", (req, res) => {
  res.render("pages/home", { 
    user: req.session.user || null,
    title: "Trang chủ"
  });
});

// Trang đăng nhập
app.use("/", require("./routes/auth.routes"));
// Trang thông tin tài khoản
app.use("/", userRoutes);

// Đăng xuất
app.get("/logout", (req, res) => {
  req.session.destroy();
  res.redirect("/login");
});
// Trang sản phẩm yêu thích

app.use("/liked", favoriteRoutes);
// Trang giỏ hàng
app.use("/cart", cartRoutes);

// Admin routes
app.use("/admin", require("./routes/product.routes"));

// Trang đặt lại mật khẩu
const resetPasswordRoutes = require("./routes/resetpassword.routes");
app.use("/resetpassword", resetPasswordRoutes);

app.listen(PORT, () => {
  console.log(`Server chạy tại http://localhost:${PORT}`);
});
