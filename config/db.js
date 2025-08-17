const mysql = require("mysql2/promise");

async function getConnection() {
  const connection = await mysql.createConnection({
    host: "localhost",      // Server MySQL
    user: "root",           // User MySQL
    password: "",           // Mật khẩu (nếu có)
    database: "sellschoolsupplies" // Tên database
  });

  console.log("✅ Đã kết nối MySQL");
  return connection;
}

module.exports = getConnection;