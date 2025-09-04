const express = require("express");
const ejs = require("ejs");
const path = require("path");
const router = express.Router();
const getConnection = require("../config/db");

// Danh sách nhà cung cấp
router.get("/", async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    const [suppliers] = await connection.query("SELECT * FROM suppliers");
    await connection.end();

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/supplier/supplier_list.ejs"),
      { suppliers }
    );

    res.render("admin_pages/layout", {
      title: "Quản lý Nhà cung cấp",
      activePage: "supplier",
      body: body,
    });
  } catch (err) {
    if (connection) await connection.end();
    next(err);
  }
});

// Form thêm
router.get("/add", async (req, res, next) => {
  try {
    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/supplier/supplier_add.ejs")
    );

    res.render("admin_pages/layout", {
      title: "Thêm Nhà cung cấp",
      activePage: "supplier",
      body: body,
    });
  } catch (err) {
    next(err);
  }
});

// Xử lý thêm
// Xử lý thêm
router.post("/add", async (req, res) => {
  const { supplier_name, contact_info } = req.body;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute(
      "INSERT INTO suppliers (supplier_name, contact_info) VALUES (?, ?)",
      [supplier_name, contact_info]
    );
    await connection.end();
    res.redirect("/admin/supplier");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi chi tiết thêm NCC:", err);
    res.status(500).send("Lỗi thêm nhà cung cấp");
  }
});


// Form sửa
router.get("/edit/:id", async (req, res, next) => {
  const id = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query("SELECT * FROM suppliers WHERE supplier_id = ?", [id]);
    await connection.end();

    if (rows.length === 0) return res.status(404).send("Không tìm thấy NCC");

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/supplier/supplier_edit.ejs"),
      { supplier: rows[0] }
    );

    res.render("admin_pages/layout", {
      title: "Sửa Nhà cung cấp",
      activePage: "supplier",
      body: body,
    });
  } catch (err) {
    if (connection) await connection.end();
    next(err);
  }
});

// Cập nhật
router.post("/edit/:id", async (req, res) => {
  const id = req.params.id;
  const { supplier_name, contact_info } = req.body;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute(
      "UPDATE suppliers SET supplier_name=?, contact_info=? WHERE supplier_id=?",
      [supplier_name, contact_info, id]
    );
    await connection.end();
    res.redirect("/admin/supplier");
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi cập nhật nhà cung cấp");
  }
});

// Xóa
router.post("/delete/:id", async (req, res) => {
  const id = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute("DELETE FROM suppliers WHERE supplier_id=?", [id]);
    await connection.end();
    res.redirect("/admin/supplier");
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi xóa nhà cung cấp");
  }
});

module.exports = router;
