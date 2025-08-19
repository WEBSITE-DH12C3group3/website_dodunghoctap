// controllers/resetPasswordController.js
const getConnection = require("../config/db"); // import hàm kết nối DB

// Hiển thị form reset password
exports.renderResetPassword = (req, res) => {
  res.render("pages/resetpassword", {
    error: req.session.error || null,
    email: req.query.email || "",  
    user_logged_in: !!req.session.user,
    user_id: req.session.user ? req.session.user.user_id : null
  });
  req.session.error = null;
};

// Xử lý reset password
exports.postResetPassword = async (req, res) => {
  const { email, new_password, confirm_password, old_password } = req.body;
  const loggedInUser = req.session.user; // user đang đăng nhập (nếu có)

  // Trường hợp người dùng đã đăng nhập
  let targetEmail = email;
  if (loggedInUser && loggedInUser.email) {
    targetEmail = loggedInUser.email;
  }

  if (!targetEmail || !new_password) {
    req.session.error = "Thiếu thông tin email hoặc mật khẩu!";
    return res.redirect(`/resetpassword?email=${encodeURIComponent(targetEmail || "")}`);
  }

  if (new_password !== confirm_password) {
    req.session.error = "Mật khẩu không khớp. Vui lòng thử lại!";
    return res.redirect(`/resetpassword?email=${encodeURIComponent(targetEmail)}`);
  }

  try {
    const connection = await getConnection();

    // Lấy user theo email
    const [rows] = await connection.execute(
      "SELECT user_id, password FROM users WHERE email = ?",
      [targetEmail]
    );

    if (rows.length === 0) {
      req.session.error = "Không tìm thấy tài khoản với email này!";
      await connection.end();
      return res.redirect("/resetpassword");
    }

    const user = rows[0];

    // Nếu đã đăng nhập thì kiểm tra mật khẩu cũ
    if (loggedInUser && old_password) {
      if (user.password !== old_password) {
        req.session.error = "Mật khẩu cũ không chính xác!";
        await connection.end();
        return res.redirect("/resetpassword");
      }
    }

    // Cập nhật mật khẩu
    await connection.execute(
      "UPDATE users SET password = ? WHERE user_id = ?",
      [new_password, user.user_id]
    );

    await connection.end();

    if (loggedInUser) {
      // Nếu đổi mật khẩu khi đã đăng nhập → quay về profile
      res.redirect("/personal?reset=success");
    } else {
      // Nếu đặt lại mật khẩu khi quên → quay về login
      res.redirect("/login?reset=success");
    }
  } catch (err) {
    console.error("Lỗi đặt lại mật khẩu:", err);
    req.session.error = "Có lỗi xảy ra khi cập nhật mật khẩu!";
    res.redirect(`/resetpassword?email=${encodeURIComponent(targetEmail)}`);
  }
};

