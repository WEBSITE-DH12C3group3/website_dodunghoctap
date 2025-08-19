// routes/contact.routes.js
const express = require("express");
const router = express.Router();
const contactController = require("../controllers/contact.controller");

router.get("/", contactController.renderContactPage);
router.post("/", contactController.submitContact);

module.exports = router;
