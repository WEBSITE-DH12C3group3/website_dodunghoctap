// middlewares/setUser.js

module.exports = (req, res, next) => {
  // Gán user từ session vào biến cục bộ (locals) của response
  res.locals.user = req.session.user || null;
  next();
};
