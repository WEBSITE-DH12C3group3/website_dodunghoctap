const express = require("express");
const router = express.Router();
const ejs = require("ejs");
const path = require("path");

// Route chính Dashboard
router.get("/", async (req, res, next) => {
  try {
    const body = "<h2>Chào mừng đến trang quản trị</h2>";
    res.render("admin_pages/layout", {
      title: "Dashboard",
      activePage: "dashboard",
      body,
      user: req.session.user,
    });
  } catch (error) {
    next(error);
  }
});

// Routes sản phẩm và danh mục
router.use("/products", require("./product.routes"));
router.use("/category", require("./category.routes"));

// Route thống kê
router.get("/static", async (req, res, next) => {
  try {
    const statsController = require("../controllers/statistical.controller");
    const data = await statsController.getStatistics();

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/statistical/statistical.ejs"),
      { ...data, activePage: "static" }
    );

    res.render("admin_pages/layout", {
      title: "Thống kê doanh thu",
      activePage: "static",
      body,
      user: req.session.user,
    });
  } catch (error) {
    next(error);
  }
});

// Routes quản lý Người dùng
const userRoutes = require("./user.routes");
router.use("/users", userRoutes);

// Routes quản lý Nhóm Người dùng
const roleRoutes = require("./role.routes");
router.use("/roles", roleRoutes);

module.exports = router;
