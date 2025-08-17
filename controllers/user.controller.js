const getConnection = require("../config/db");

// Hiển thị trang thông tin tài khoản
exports.getProfile = async (req, res) => {
  const user = req.session.user;
  if (!user) return res.redirect("/login");

  try {
    const connection = await getConnection();
    const [result] = await connection.execute(
      "SELECT full_name, email, phone, address FROM users WHERE user_id = ?",
      [user.user_id]
    );
    await connection.end();

    if (result.length === 0) {
      return res.send("Không tìm thấy thông tin người dùng.");
    }

    res.render("pages/personal", {
      title: "Thông tin tài khoản",
      user: req.session.user,
      account: result[0],
    });
  } catch (err) {
    console.error("Lỗi truy vấn:", err);
    res.status(500).send("Lỗi truy vấn: " + err.message);
  }
};

// Cập nhật thông tin tài khoản
exports.updateProfile = async (req, res) => {
  const user = req.session.user;
  if (!user) return res.redirect("/login");

  const { full_name, email, phone, address } = req.body;
  try {
    const connection = await getConnection();
    await connection.execute(
      "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?",
      [full_name, email, phone, address, user.user_id]
    );
    await connection.end();

    res.redirect("/personal?status=success");
  } catch (err) {
    console.error("Lỗi cập nhật:", err);
    res.status(500).send("Lỗi cập nhật: " + err.message);
  }
};