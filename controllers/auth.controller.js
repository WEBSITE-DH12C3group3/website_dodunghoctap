const getConnection = require("../config/db");
const { setUserSession } = require("../utils/sessionHelper");

let resetCodes = {}; // Lưu tạm trong bộ nhớ (nên lưu vào DB)

exports.renderForgotPassword = async (req, res) => {
  res.render("pages/forgotpassword", { error: "" });
};

// Hiển thị form login và xử lý đăng nhập
exports.login = async (req, res) => {
  const { email, password } = req.body;

  let connection;
  try {
    connection = await getConnection();

    // Lấy user theo email
    const [results] = await connection.execute(
      "SELECT * FROM users WHERE email = ?",
      [email]
    );

    if (results.length === 0) {
      return res.send("Sai tài khoản hoặc mật khẩu!");
    }

    const user = results[0];

    // So sánh mật khẩu thẳng (plaintext)
    if (password !== user.password) {
      return res.send("Sai tài khoản hoặc mật khẩu!");
    }

    // Gán user vào session
    setUserSession(req, user);

    console.log(`Đăng nhập thành công: ${user.full_name} (ID: ${user.user_id})`);

    res.redirect("/");
  } catch (err) {
    console.error("Lỗi đăng nhập:", err);
    res.status(500).send("Lỗi server");
  } finally {
    if (connection) await connection.end();
  }
};

// Hiển thị form đăng ký và xử lý đăng ký
exports.register = async (req, res) => {
  const { fullname, email, password, confirmPassword, phone, address } = req.body;

  if (password !== confirmPassword) {
    return res.send("Mật khẩu không khớp!");
  }

  let connection;
  try {
    connection = await getConnection();

    // Kiểm tra email đã tồn tại chưa
    const [emailResult] = await connection.execute(
      "SELECT * FROM users WHERE email = ?",
      [email]
    );
    if (emailResult.length > 0) {
      return res.send("Email đã được sử dụng!");
    }

    // Lưu user mới với mật khẩu thẳng (plaintext)
    const [result] = await connection.execute(
      "INSERT INTO users (full_name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'customer')",
      [fullname, email, phone, address, password]
    );

    const newUser = {
      user_id: result.insertId,
      full_name: fullname,
      email,
      phone,
      address,
      role: "customer"
    };

    // Gán user mới vào session
    setUserSession(req, newUser);

    res.redirect("/");
  } catch (err) {
    console.error("Lỗi đăng ký:", err);
    res.status(500).send("Lỗi server");
  } finally {
    if (connection) await connection.end();
  }
};
