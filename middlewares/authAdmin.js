// middlewares/authAdmin.js
module.exports = (req, res, next) => {
  if (!req.session.user) {
    return res.redirect("/login");
  }
  if (req.session.user.role === "2") {
    return res.status(403).send("Bạn không có quyền truy cập khu vực này");
  }
  next();
};
