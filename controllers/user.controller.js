const db = require("../config/db");

// Hiển thị trang thông tin tài khoản
exports.getProfile = (req, res) => {
  const user = req.session.user;
  if (!user) return res.redirect("/login");

  const sql = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
  db.query(sql, [user.user_id], (err, result) => {
    if (err) return res.status(500).send("Lỗi truy vấn: " + err.message);

    if (result.length == 0) {
      return res.send("Không tìm thấy thông tin người dùng.");
    }

    res.render("pages/personal", {
      title: "Thông tin tài khoản",
      user: req.session.user,
      account: result[0],
    });
  });
};

// Cập nhật thông tin tài khoản
exports.updateProfile = (req, res) => {
  const user = req.session.user;
  if (!user) return res.redirect("/login");

  const { full_name, email, phone, address } = req.body;
  const sql =
    "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";

  db.query(sql, [full_name, email, phone, address, user.user_id], (err) => {
    if (err) return res.status(500).send("Lỗi cập nhật: " + err.message);

    res.redirect("/personal?status=success");
  });
};
