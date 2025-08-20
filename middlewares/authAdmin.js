module.exports = (req, res, next) => {
  if (!req.session.user) {
    // Nếu chưa đăng nhập → chuyển về login
    return res.redirect("/login");
  }

  // Nếu cần phân quyền (chỉ admin mới vào được)
  if (req.session.user.role !== "admin") {
    return res.status(403).send("Bạn không có quyền truy cập khu vực này");
  }

  next();
};
