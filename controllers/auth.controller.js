// database
const db = require("../config/db");
const bcrypt = require("bcrypt");
const nodemailer = require("nodemailer");

let resetCodes = {}; // lưu tạm trong bộ nhớ (nên lưu DB)

exports.renderForgotPassword = (req, res) => {
  res.render("pages/forgotpassword", { error: "" });
};
// Hiển thị form login
exports.login = (req, res) => {
    const { email, password } = req.body;

    const sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    db.query(sql, [email, password], (err, results) => {
    if (err) throw err;

    if (results.length > 0) {
        const user = results[0];
        req.session.user = {
        user_id: user.user_id,
        full_name: user.full_name,
        email: user.email,
        role: user.role
        };

        console.log("Đăng nhập thành công:", user.full_name, "Với ID:", user.user_id);
        res.redirect("/");
    } else {
        res.send("Sai tài khoản hoặc mật khẩu!");
    }
    });
};
// Hiển thị form đăng ký
exports.register = (req, res) => {
    const { fullname, email, password, confirmPassword, phone, address } = req.body;

    // 1. Kiểm tra mật khẩu nhập lại
    if (password !== confirmPassword) {
        return res.send("Mật khẩu không khớp!");
    }

    // 2. Kiểm tra email tồn tại chưa
    const checkEmailSql = "SELECT * FROM users WHERE email = ?";
    db.query(checkEmailSql, [email], (err, result) => {
        if (err) throw err;
        if (result.length > 0) {
            return res.send("Email đã được sử dụng!");
        }

        // 3. Mã hóa mật khẩu
        // const hashedPassword = bcrypt.hashSync(password, 10);

        // 4. Lưu vào DB
        const insertSql = `
            INSERT INTO users (full_name, email, phone, address, password, role)
            VALUES (?, ?, ?, ?, ?, 'customer')
        `;
        db.query(insertSql, [fullname, email, phone, address, password], (err2, result2) => {
            if (err2) throw err2;

            req.session.user = { fullname, email };
            res.redirect("/");
        });
    });
};

// Gửi mã xác nhận
exports.sendResetCode = async (req, res) => {
  const { email } = req.body;

  try {
    const [rows] = await db.query("SELECT * FROM users WHERE email = ?", [email]);

    if (rows.length === 0) {
      return res.render("forgotpassword", { error: "Email chưa được đăng ký." });
    }

    // Tạo mã xác nhận ngẫu nhiên
    const code = Math.floor(100000 + Math.random() * 900000).toString();
    resetCodes[email] = code;

    // Gửi mail
    const transporter = nodemailer.createTransport({
      service: "gmail",
      auth: {
        user: "mynameistrang19012004@gmail.com",
        pass: "evzy ewzu ufci gpli", // nhớ dùng app password
      },
    });

    await transporter.sendMail({
      from: "mynameistrang19012004@gmail.com",
      to: email,
      subject: "Mã xác nhận đặt lại mật khẩu",
      text: `Mã xác nhận của bạn là: ${code}`,
    });

    res.render("pages/forgotpassword", { error: "Mã xác nhận đã được gửi" });
  } catch (err) {
    console.error(err);
    res.render("pages/forgotpassword", { error: "Lỗi server" });
  }
};

// Kiểm tra mã xác nhận
exports.verifyResetCode = (req, res) => {
  const { email, code } = req.body;

  if (resetCodes[email] && resetCodes[email] === code) {
    delete resetCodes[email];
    return res.send("✅ Xác nhận thành công! Bạn có thể đổi mật khẩu.");
  } else {
    return res.render("pages/forgotpassword", { error: "Mã xác nhận không hợp lệ" });
  }
};


 