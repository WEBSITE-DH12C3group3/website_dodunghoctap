// db.js
const mysql = require("mysql2");

const connection = mysql.createConnection({
  host: "localhost",      // Server MySQL
  user: "root",           // User MySQL
  password: "",           // Mật khẩu (nếu có)
  database: "sellschoolsupplies" // Tên database
});

// Dùng promise wrapper
const db = connection.promise();

db.connect()
  .then(() => console.log("✅ Đã kết nối MySQL"))
  .catch(err => console.error("❌ Lỗi kết nối MySQL:", err));

module.exports = db;
