const express = require("express");
const router = express.Router();
const favoritesController = require("../controllers/favorites.controller");

// Trang yêu thích
router.get("/", favoritesController.getOrSyncFavorites);
router.post("/", favoritesController.getOrSyncFavorites); // Hỗ trợ cả POST nếu cần

// API thêm/xóa/lấy yêu thích
router.post("/add", favoritesController.addFavorite);
router.post("/remove/:productId", favoritesController.removeFavorite);
router.get("/:userId", favoritesController.getFavorites);

module.exports = router;