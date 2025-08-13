// db.js
const mysql = require("mysql2");

const connection = mysql.createConnection({
  host: "localhost",      // Server MySQL
  user: "root",           // User MySQL (mặc định là root)
  password: "",           // Mật khẩu (nếu có)
  database: "sellschoolsupplies" // Tên database của bạn
});

// Kết nối
connection.connect((err) => {
  if (err) {
    console.error("Lỗi kết nối MySQL:", err);
    return;
  }
  console.log("✅ Đã kết nối MySQL");
});

module.exports = connection;
