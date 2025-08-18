// controllers/resetPasswordController.js

// Hiển thị form reset password
exports.getResetPassword = (req, res) => {
  res.render("resetpassword", {
    error: req.session.error || null,
    user_logged_in: req.session.user_logged_in || false
  });
  req.session.error = null; // clear error sau khi render
};

// Xử lý reset password
exports.postResetPassword = (req, res) => {
  const { old_password, new_password, confirm_password } = req.body;

  if (req.session.user_logged_in) {
    const userOldPass = req.session.user_password; // giả sử mật khẩu cũ lưu trong session (sau này thay bằng DB)
    if (old_password !== userOldPass) {
      req.session.error = "Mật khẩu cũ không đúng!";
      return res.redirect("pages/resetpassword");
    }
  }

  if (new_password !== confirm_password) {
    req.session.error = "Mật khẩu không khớp. Vui lòng thử lại!";
    return res.redirect("pages/resetpassword");
  }

  // TODO: Update mật khẩu trong DB
  console.log("Cập nhật mật khẩu mới:", new_password);
  return res.render("pages/resetpassword");
  res.send("✅ Đặt lại mật khẩu thành công!");
};
