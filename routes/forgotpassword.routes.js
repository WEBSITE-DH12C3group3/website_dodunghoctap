const express = require("express");
const router = express.Router();
const forgotPasswordController = require("../controllers/forgotpassword.controller");

// 👉 Quên mật khẩu
router.get("/forgotpassword", forgotPasswordController.renderForgotPassword);
router.post("/sendResetCode", forgotPasswordController.sendResetCode);
router.post("/verify-code", forgotPasswordController.verifyResetCode);

module.exports = router;
