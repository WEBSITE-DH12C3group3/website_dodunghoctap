module.exports = (req, res, next) => {
  res.locals.user = req.session.user || null;
  res.locals.cart = req.session.cart || [];
  res.locals.favoriteIds = (Array.isArray(req.session.favorites) ? req.session.favorites.map(f => f && f.product_id).filter(id => id != null) : []) || [];
  next();
};

