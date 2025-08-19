const express = require("express");
const router = express.Router();
const statsController = require("../controllers/statistical.controller");
const productRoutes = require("./product.routes");
const categoryRoutes = require("./category.routes");
const ejs = require("ejs");
const path = require("path");

// Dashboard tổng quan
router.get("/", (req, res) => {
  res.render("admin_pages/layout", {
    title: "Dashboard",
    activePage: "dashboard",
    body: "<h2>Chào mừng đến trang quản trị</h2>",
  });
});

// Router con cho sản phẩm và danh mục
router.use("/products", productRoutes);
router.use("/category", categoryRoutes);

// Route thống kê (static)
router.get("/static", async (req, res, next) => {
  try {
    const data = await statsController.getStatistics();

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/statistical/statistical.ejs"),
      { ...data, activePage: "static" } // Truyền thêm activePage để view có biến này
    );

    res.render("admin_pages/layout", {
      title: "Thống kê doanh thu",
      activePage: "static",
      body: body,
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;
