const express = require("express");
const router = express.Router();
const favoritesController = require("../controllers/favorites.controller");

// Trang yêu thích
router.get("/", favoritesController.renderFavorites);

// API thêm/xóa JSON giữ nguyên
router.post("/add", favoritesController.addFavorite);
router.delete("/remove/:productId", favoritesController.removeFavorite);
router.get("/:userId", favoritesController.getFavorites);

module.exports = router;
