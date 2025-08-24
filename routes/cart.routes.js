const express = require("express");
const router = express.Router();
const cartController = require("../controllers/cart.controller");

router.get("/", cartController.getCart);
router.post("/add/:id", cartController.addToCart);
router.post("/plus/:id", cartController.plusItem);
router.post("/minus/:id", cartController.minusItem);
router.post("/remove/:id", cartController.removeItem);
router.post("/clear", cartController.clearCart);

module.exports = router;