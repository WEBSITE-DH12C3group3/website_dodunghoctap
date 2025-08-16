// database
const db = require("../config/db");
const bcrypt = require("bcrypt");

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

 