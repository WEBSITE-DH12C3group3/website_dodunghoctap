const express = require("express");
const router = express.Router();
const getConnection = require("../config/db");
const bcrypt = require("bcrypt");

// Hiển thị danh sách người dùng
router.get("/", async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [users] = await connection.query("SELECT * FROM users");
    await connection.end();

    res.render("admin_pages/user/users", {
      users,
      title: "Quản lý Người dùng",
      activePage: "users",
    });
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi lấy danh sách người dùng");
  }
});

// Hiển thị form thêm người dùng kèm danh sách nhóm người dùng (roles)
router.get("/add", async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [roles] = await connection.query("SELECT * FROM roles");
    await connection.end();

    res.render("admin_pages/user/user_add", {
      title: "Thêm Người dùng",
      activePage: "users",
      roles, // truyền danh sách nhóm người dùng sang view
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi tải form thêm người dùng:", err);
    res.status(500).send("Lỗi tải form thêm người dùng");
  }
});

// Xử lý thêm người dùng
router.post("/add", async (req, res) => {
  const { full_name, email, phone, address, password, role } = req.body;
  let connection;
  try {
    connection = await getConnection();
    const hashedPassword = await bcrypt.hash(password, 10);
    await connection.execute(
      "INSERT INTO users (full_name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?)",
      [full_name, email, phone, address, hashedPassword, role]
    );
    await connection.end();
    res.redirect("/admin/users");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi chi tiết:", err);
    res.status(500).send("Lỗi thêm người dùng");
  }
});

// Hiển thị form sửa người dùng kèm danh sách nhóm người dùng (roles)
router.get("/edit/:id", async (req, res) => {
  const userId = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    const [result] = await connection.query("SELECT * FROM users WHERE user_id = ?", [userId]);
    const [roles] = await connection.query("SELECT * FROM roles");
    await connection.end();

    if (result.length === 0) {
      return res.status(404).send("Người dùng không tồn tại");
    }
    res.render("admin_pages/user/user_edit", {
      user: result[0],
      title: "Sửa Người dùng",
      activePage: "users",
      roles, // truyền danh sách nhóm người dùng sang view
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi tải form sửa người dùng:", err);
    res.status(500).send("Lỗi lấy người dùng");
  }
});

// Xử lý cập nhật người dùng
router.post("/edit/:id", async (req, res) => {
  const userId = req.params.id;
  const { full_name, email, phone, address, role, password } = req.body;
  let connection;
  try {
    connection = await getConnection();
    if (password && password.trim() !== "") {
      const hashedPassword = await bcrypt.hash(password, 10);
      await connection.execute(
        "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, role = ?, password = ? WHERE user_id = ?",
        [full_name, email, phone, address, role, hashedPassword, userId]
      );
    } else {
      await connection.execute(
        "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, role = ? WHERE user_id = ?",
        [full_name, email, phone, address, role, userId]
      );
    }
    await connection.end();
    res.redirect("/admin/users");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi cập nhật người dùng:", err);
    res.status(500).send("Lỗi cập nhật người dùng");
  }
});

// Xóa người dùng
router.post("/delete/:id", async (req, res) => {
  const userId = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute("DELETE FROM users WHERE user_id = ?", [userId]);
    await connection.end();
    res.redirect("/admin/users");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi xóa người dùng:", err);
    res.status(500).send("Lỗi xóa người dùng");
  }
});

module.exports = router;
