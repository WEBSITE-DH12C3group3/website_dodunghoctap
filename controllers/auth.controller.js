const bcrypt = require("bcrypt");
const nodemailer = require("nodemailer");
const getConnection = require("../config/db");

let resetCodes = {}; // Lưu tạm trong bộ nhớ (nên lưu vào DB)

exports.renderForgotPassword = async (req, res) => {
  res.render("pages/forgotpassword", { error: "" });
};

// Hiển thị form login
exports.login = async (req, res) => {
  const { email, password } = req.body;

  try {
    const connection = await getConnection();
    const [results] = await connection.execute(
      "SELECT * FROM users WHERE email = ? AND password = ?",
      [email, password]
    );
    await connection.end();

    if (results.length > 0) {
      const user = results[0];
      req.session.user = {
        user_id: user.user_id,
        full_name: user.full_name,
        email: user.email,
        role: user.role,
      };

      console.log("Đăng nhập thành công:", user.full_name, "Với ID:", user.user_id);
      res.redirect("/");
    } else {
      res.send("Sai tài khoản hoặc mật khẩu!");
    }
  } catch (err) {
    console.error("Lỗi đăng nhập:", err);
    res.status(500).send("Lỗi server");
  }
};

// Hiển thị form đăng ký
exports.register = async (req, res) => {
  const { fullname, email, password, confirmPassword, phone, address } = req.body;

  // 1. Kiểm tra mật khẩu nhập lại
  if (password !== confirmPassword) {
    return res.send("Mật khẩu không khớp!");
  }

  try {
    const connection = await getConnection();
    // 2. Kiểm tra email tồn tại chưa
    const [emailResult] = await connection.execute(
      "SELECT * FROM users WHERE email = ?",
      [email]
    );
    if (emailResult.length > 0) {
      await connection.end();
      return res.send("Email đã được sử dụng!");
    }

    // 3. Mã hóa mật khẩu
    //const hashedPassword = await bcrypt.hash(password, 10);

    // 4. Lưu vào DB
    const [result] = await connection.execute(
      "INSERT INTO users (full_name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'customer')",
      [fullname, email, phone, address, hashedPassword]
    );
    await connection.end();

    req.session.user = { full_name: fullname, email };
    res.redirect("/login");
  } catch (err) {
    console.error("Lỗi đăng ký:", err);
    res.status(500).send("Lỗi server");
  }
};

// Gửi mã xác nhận

exports.sendResetCode = async (req, res) => {
  const { email } = req.body;

  try {
    const connection = await getConnection();
    const [rows] = await connection.execute(
      "SELECT * FROM users WHERE email = ?",
      [email]
    );
    await connection.end();

    if (rows.length === 0) {
      return res.render("pages/forgotpassword", { error: "Email chưa được đăng ký.", email });
    }

    const code = Math.floor(100000 + Math.random() * 900000).toString();
    resetCodes[email] = { code, expiryTime: Date.now() + 10 * 60 * 1000 };
    console.log(`Mã xác nhận cho ${email}: ${code}, Hết hạn: ${new Date(resetCodes[email].expiryTime)}`);

    const transporter = nodemailer.createTransport({
      service: "gmail",
      auth: {
        user: "mynameistrang19012004@gmail.com",
        pass: "evzy ewzu ufci gpli",
      },
    });

    await transporter.sendMail({
      from: "mynameistrang19012004@gmail.com",
      to: email,
      subject: "Mã xác nhận đặt lại mật khẩu",
      text: `Mã xác nhận của bạn là: ${code}. Mã có hiệu lực trong 10 phút.`,
    });

    res.render("pages/forgotpassword", { error: "Mã xác nhận đã được gửi", email });
  } catch (err) {
    console.error("Lỗi gửi mã xác nhận:", err);
    res.render("pages/forgotpassword", { error: "Lỗi server", email });
  }
};

exports.verifyResetCode = async (req, res) => {
  const { email, code } = req.body;
  const resetData = resetCodes[email];
  console.log(`Mã nhập cho ${email}: ${code}, Mã lưu: ${resetData ? resetData.code : 'null'}`);

  if (resetData && resetData.code === code && Date.now() < resetData.expiryTime) {
    delete resetCodes[email];
    return res.redirect('/reset-password'); // Chuyển đến trang đổi mật khẩu
  } else {
    return res.render("pages/forgotpassword", { error: "Mã xác nhận không hợp lệ hoặc đã hết hạn", email });
  }
};