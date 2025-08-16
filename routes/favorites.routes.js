const express = require("express");
const router = express.Router();
const favoritesController = require("../controllers/favorites.controller");

router.get("/", (req, res) => {
  res.render("pages/favorites", { user: req.session.user || null, title: "Trang yêu thích" });
});
// POST /favorites/add
router.post("/add", favoritesController.addFavorite);

// DELETE /favorites/remove/:productId
router.delete("/remove/:productId", favoritesController.removeFavorite);

// GET /favorites/:userId
router.get("/:userId", favoritesController.getFavorites);

module.exports = router;
