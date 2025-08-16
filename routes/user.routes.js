const express = require("express");
const router = express.Router();
const userController = require("../controllers/user.controller");

// Trang profile
router.get("/personal", userController.getProfile);

// Cập nhật profile
router.post("/personal/update", userController.updateProfile);

module.exports = router;
