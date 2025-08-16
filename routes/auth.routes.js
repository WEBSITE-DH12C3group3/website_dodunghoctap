const express = require("express");
const router = express.Router();
const app = express();
const path = require("path");
const session = require("express-session");
const authController = require("../controllers/auth.controller");

//  xử lý đăng nhập
router.get("/login", (req, res) => {
  res.render("pages/login", { title: "Đăng nhập" });
});
router.post("/login", authController.login);

// GET hiển thị form đăng ký
router.get("/registers", (req, res) => {
  res.render("pages/registers", { title: "Đăng ký" });
});
router.post("/registers", authController.register);

// Xử lý quên mật khẩu

router.get("/forgotpassword", authController.renderForgotPassword);
router.post("/forgotpassword", authController.sendResetCode);
router.post("/verify-code", authController.verifyResetCode);

module.exports = router;  
