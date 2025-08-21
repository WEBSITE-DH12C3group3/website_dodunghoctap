const express = require("express");
const router = express.Router();
const getConnection = require("../config/db");

// Hiển thị danh sách nhóm người dùng
router.get("/", async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [roles] = await connection.query("SELECT * FROM roles");
    await connection.end();

    res.render("admin_pages/role/roles", {
      title: "Quản lý Nhóm Người Dùng",
      roles,
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi lấy danh sách nhóm người dùng:", err);
    res.status(500).send("Lỗi lấy danh sách nhóm người dùng");
  }
});

// Hiển thị form thêm nhóm người dùng kèm danh sách quyền
router.get("/add", async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [permissions] = await connection.query("SELECT * FROM permissions");
    await connection.end();

    res.render("admin_pages/role/role_add", {
      title: "Thêm Nhóm Người Dùng",
      permissions,
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi tải form thêm nhóm:", err);
    res.status(500).send("Lỗi tải form thêm nhóm");
  }
});

// Xử lý thêm nhóm và lưu phân quyền
router.post("/add", async (req, res) => {
  const { role_name, description, permissions } = req.body;
  let connection;
  try {
    connection = await getConnection();

    // Thêm nhóm mới vào bảng roles
    const [result] = await connection.execute(
      "INSERT INTO roles (role_name, description) VALUES (?, ?)",
      [role_name, description]
    );
    const roleId = result.insertId;

    // Lưu phân quyền cho nhóm nếu có chọn
    if (permissions) {
      const perms = Array.isArray(permissions) ? permissions : [permissions];
      const values = perms.map(p => [roleId, p]);
      await connection.query(
        "INSERT INTO role_permissions (role_id, permission_id) VALUES ?",
        [values]
      );
    }

    await connection.end();
    res.redirect("/admin/roles");
  } catch (err) {
    if (connection) {
      try {
        await connection.end();
      } catch (closeErr) {
        console.error("Lỗi khi đóng kết nối:", closeErr);
      }
    }
    console.error("Lỗi thêm nhóm:", err);
    res.status(500).send("Lỗi thêm nhóm người dùng");
  }
});
// Hiển thị form sửa nhóm người dùng cùng phân quyền
router.get("/edit/:id", async (req, res) => {
  const roleId = req.params.id;
  let connection;
  try {
    connection = await getConnection();

    const [roles] = await connection.query("SELECT * FROM roles WHERE role_id = ?", [roleId]);
    if (roles.length === 0) {
      await connection.end();
      return res.status(404).send("Nhóm không tồn tại");
    }
    const role = roles[0];

    const [permissions] = await connection.query("SELECT * FROM permissions");

    const [rolePerms] = await connection.query(
      "SELECT permission_id FROM role_permissions WHERE role_id = ?",
      [roleId]
    );
    await connection.end();

    const selectedPermissions = rolePerms.map(rp => rp.permission_id);

    res.render("admin_pages/role/role_edit", {
      title: "Sửa Nhóm Người Dùng",
      role,
      permissions,
      selectedPermissions,
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi tải form sửa nhóm:", err);
    res.status(500).send("Lỗi tải form sửa nhóm");
  }
});

// Xử lý cập nhật nhóm người dùng và phân quyền
router.post("/edit/:id", async (req, res) => {
  const roleId = req.params.id;
  const { role_name, description, permissions } = req.body;
  let connection;
  try {
    connection = await getConnection();

    await connection.execute(
      "UPDATE roles SET role_name = ?, description = ? WHERE role_id = ?",
      [role_name, description, roleId]
    );

    await connection.execute("DELETE FROM role_permissions WHERE role_id = ?", [roleId]);

    if (permissions) {
      const perms = Array.isArray(permissions) ? permissions : [permissions];
      const values = perms.map(p => [roleId, p]);
      await connection.query(
        "INSERT INTO role_permissions (role_id, permission_id) VALUES ?",
        [values]
      );
    }

    await connection.end();
    res.redirect("/admin/roles");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi cập nhật nhóm:", err);
    res.status(500).send("Lỗi cập nhật nhóm người dùng");
  }
});

module.exports = router;
