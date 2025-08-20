const express = require("express");
const router = express.Router();
const authController = require("../controllers/auth.controller");
const productController = require("../controllers/product.controller");

// 👉 Trang đăng nhập
router.get("/login", (req, res) => {
  res.render("pages/login", { title: "Đăng nhập" });
});
router.post("/login", authController.login);

// 👉 Trang đăng ký
router.get("/registers", (req, res) => {
  res.render("pages/registers", { title: "Đăng ký" });
});
router.post("/registers", authController.register);

// router.get("/", productController.showHome);

// ⚡ LƯU Ý: đã bỏ forgotpassword sang forgotpassword.routes.js

module.exports = router;
