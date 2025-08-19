// routes/resetpassword.js
const express = require("express");
const router = express.Router();
const resetPasswordController = require("../controllers/resetpassword.controller");
// Hiển thị form
router.get("/", resetPasswordController.getResetPassword);

// Xử lý submit
router.post("/", resetPasswordController.postResetPassword);

module.exports = router;
