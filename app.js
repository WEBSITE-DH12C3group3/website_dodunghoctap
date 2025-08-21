const express = require("express");
const path = require("path");
const session = require("express-session");

const app = express();
const PORT = process.env.PORT || 4000;

// Middleware setUser
const setUser = require("./middlewares/setUser");

// Import các router
const favoriteRoutes = require("./routes/favorites.routes");
const userRoutes = require("./routes/user.routes");
const cartRoutes = require("./routes/cart.routes");
const authRoutes = require("./routes/auth.routes");
const resetPasswordRoutes = require("./routes/resetpassword.routes");
const forgotPasswordRoutes = require("./routes/forgotpassword.routes");
const contactRoutes = require("./routes/contact.routes");

// Router admin và middleware phân quyền
const adminRouter = require("./routes/admin.routes");
const authAdmin = require("./middlewares/authAdmin");

// Controller view sản phẩm và trang chủ
const view_products = require("./controllers/viewproducts.controller");

// Cấu hình template engine và đường dẫn views
app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

// Static files
app.use(express.static(path.join(__dirname, "public")));
app.use("/uploads", express.static(path.join(__dirname, "public", "uploads")));

// Body parsers
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// Session
app.use(
  session({
    secret: "secret-key",
    resave: false,
    saveUninitialized: false, // Không tạo session rỗng
    cookie: {
      maxAge: 24 * 60 * 60 * 1000, // 1 ngày
      httpOnly: true,
      secure: false,
    },
  })
);

// Middleware gán user cho req và biến dùng chung trong view
app.use(setUser);
app.use((req, res, next) => {
  res.locals.user = req.session.user || null;
  res.locals.email = "";
  res.locals.error = "";
  next();
});

// Trang chủ
app.get("/", view_products.showHome);

// Trang cá nhân
app.get("/personal", (req, res) => {
  if (!req.session.user) {
    return res.redirect("/login");
  }
  const account = req.session.user;
  res.render("pages/personal", {
    title: "Thông tin tài khoản",
    account,
  });
});

// Router auth và user
app.use("/", authRoutes);
app.use("/", userRoutes);

// Đăng xuất
app.get("/logout", (req, res) => {
  req.session.destroy(() => {
    res.redirect("/login");
  });
});

// Yêu thích và giỏ hàng
app.use("/liked", favoriteRoutes);
app.use("/cart", cartRoutes);

// Router admin bảo vệ bằng middleware authAdmin
app.use("/admin", authAdmin, adminRouter);

// Các router còn lại
app.use("/resetpassword", resetPasswordRoutes);
app.use("/change", forgotPasswordRoutes);
app.use("/partials/contact", contactRoutes);

// Trang giới thiệu
app.get("/about", (req, res) => {
  res.render("partials/about", {
    user: req.session.user || null,
    title: "Giới thiệu",
  });
});

// Xử lý lỗi 404: Phải để ở cuối cùng
app.use((req, res) => {
  res.status(404).send("404 - Trang không tồn tại");
});

// Khởi động server
app.listen(PORT, () => {
  console.log(`Server đang chạy tại http://localhost:${PORT}`);
});
