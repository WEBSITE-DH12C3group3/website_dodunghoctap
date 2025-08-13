const express = require("express");
const router = express.Router();
const app = express();
const path = require("path");
const session = require("express-session");
const authController = require("../controllers/auth.controller");

// GET hiển thị form đăng nhập
app.get("/", (req, res) => {
  res.render("pages/login");
});
// POST xử lý đăng nhập
router.post("/login", authController.login);

// GET hiển thị form đăng ký
router.get("/registers", (req, res) => {
  res.render("pages/registers");
});
module.exports = router;  
