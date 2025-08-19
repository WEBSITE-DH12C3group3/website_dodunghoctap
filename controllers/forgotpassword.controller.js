const nodemailer = require("nodemailer");
const getConnection = require("../config/db");

let resetCodes = {}; // Lưu tạm trong RAM (nên dùng DB/Redis)

exports.renderForgotPassword = (req, res) => {
  res.render("pages/forgotpassword", { error: "", email: "" });
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


exports.verifyResetCode = async (req, res) => {
  const { email, code } = req.body;
  const resetData = resetCodes[email];

  console.log(`Mã nhập cho ${email}: ${code}, Mã lưu: ${resetData ? resetData.code : "null"}`);

  if (resetData && resetData.code === code && Date.now() < resetData.expiryTime) {
    delete resetCodes[email];
  return res.redirect(`/resetpassword?email=${encodeURIComponent(email)}`);
  } else {
    return res.render("pages/forgotpassword", { error: "Mã xác nhận không hợp lệ hoặc đã hết hạn", email });
  }
};
