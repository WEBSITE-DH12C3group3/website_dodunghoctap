const db = require("../config/db");

// Hiển thị giỏ hàng
exports.getCart = (req, res) => {
  const cart = req.session.cart || [];
  let total = 0;

  cart.forEach(item => {
    item.thanhtien = item.price_sale * item.quantity;
    total += item.thanhtien;
  });

  res.render("pages/cart", {
    title: "Giỏ hàng",
    cart,
    total,
    user: req.session.user || null
  });
};

// Thêm sản phẩm vào giỏ
exports.addToCart = (req, res) => {
  const { id, name, image, price_sale } = req.body;

  if (!req.session.cart) req.session.cart = [];

  let cart = req.session.cart;
  let existing = cart.find(item => item.id == id);

  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({
      id,
      name,
      image,
      price_sale: parseInt(price_sale),
      quantity: 1
    });
  }

  req.session.cart = cart;
  res.redirect("/cart");
};

// Cộng sản phẩm
exports.plusItem = (req, res) => {
  const { id } = req.params;
  let cart = req.session.cart || [];
  let item = cart.find(i => i.id == id);
  if (item) item.quantity += 1;
  req.session.cart = cart;
  res.redirect("/cart");
};

// Trừ sản phẩm
exports.minusItem = (req, res) => {
  const { id } = req.params;
  let cart = req.session.cart || [];
  let item = cart.find(i => i.id == id);
  if (item && item.quantity > 1) item.quantity -= 1;
  req.session.cart = cart;
  res.redirect("/cart");
};

// Xóa 1 sản phẩm
exports.removeItem = (req, res) => {
  const { id } = req.params;
  let cart = req.session.cart || [];
  req.session.cart = cart.filter(i => i.id != id);
  res.redirect("/cart");
};

// Xóa tất cả
exports.clearCart = (req, res) => {
  req.session.cart = [];
  res.redirect("/cart");
};
