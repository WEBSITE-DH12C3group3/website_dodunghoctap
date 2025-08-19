const express = require("express");
const router = express.Router();
const statsController = require("../controllers/statistical.controller");
const { route } = require("./favorites.routes");

// Khi vào /admin → mặc định hiển thị dashboard thống kê
router.get("/", statsController.dashboard);

// Các module khác (quản lý danh mục, sản phẩm, đơn hàng…)
// router.use("/categories", require("./category.routes"));
// router.use("/products", require("./product.routes"));
// router.use("/orders", require("./order.routes"));
// router.use("/customers", require("./customer.routes"));

module.exports = router;
