const express = require("express");
const router = express.Router();
const favoritesController = require("../controllers/favorites.controller");

// Trang yêu thích
router.get("/", favoritesController.renderFavorites);

// API thêm/xóa yêu thích
router.post("/add", favoritesController.addFavorite);
router.post("/remove/:productId", favoritesController.removeFavorite);
router.get("/:userId", favoritesController.getFavorites);
router.post("/sync", favoritesController.syncFavoritesOnLogin);

module.exports = router;