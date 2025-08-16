const express = require("express");
const router = express.Router();
const cartController = require("../controllers/cart.controller");

router.get("/", cartController.getCart);
router.post("/add", cartController.addToCart);
router.get("/plus/:id", cartController.plusItem);
router.get("/minus/:id", cartController.minusItem);
router.get("/remove/:id", cartController.removeItem);
router.get("/clear", cartController.clearCart);

module.exports = router;
