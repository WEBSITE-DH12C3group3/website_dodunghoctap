const getConnection = require("../config/db");

// Danh sách
exports.getAllSuppliers = async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [suppliers] = await connection.query("SELECT * FROM suppliers");
    await connection.end();

    res.render("admin_pages/supplier/supplier_list", {
      title: "Quản lý Nhà cung cấp",
      activePage: "suppliers",
      suppliers,
    });
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi lấy danh sách nhà cung cấp:", err);
    res.status(500).send("Lỗi lấy danh sách nhà cung cấp");
  }
};

// Form thêm
exports.renderAddSupplier = (req, res) => {
  res.render("admin_pages/supplier/supplier_add", {
    title: "Thêm Nhà cung cấp",
    activePage: "suppliers",
  });
};

// Xử lý thêm
exports.addSupplier = async (req, res) => {
  const { supplier_name, contact_info } = req.body;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute(
      "INSERT INTO suppliers (supplier_name, contact_info) VALUES (?, ?)",
      [supplier_name, contact_info]
    );
    await connection.end();
    res.redirect("/admin/suppliers");
  } catch (err) {
    if (connection) await connection.end();
    console.error("Lỗi thêm nhà cung cấp:", err);
    res.status(500).send("Lỗi thêm nhà cung cấp");
  }
};

// Form sửa
exports.renderEditSupplier = async (req, res) => {
  const id = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query("SELECT * FROM suppliers WHERE supplier_id = ?", [id]);
    await connection.end();

    if (rows.length === 0) return res.status(404).send("Không tìm thấy nhà cung cấp");

    res.render("admin_pages/supplier/supplier_edit", {
      title: "Sửa Nhà cung cấp",
      activePage: "suppliers",
      supplier: rows[0],
    });
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi tải form sửa nhà cung cấp");
  }
};

// Xử lý update
exports.updateSupplier = async (req, res) => {
  const id = req.params.id;
  const { supplier_name, contact_info } = req.body;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute(
      "UPDATE suppliers SET supplier_name = ?, contact_info = ? WHERE supplier_id = ?",
      [supplier_name, contact_info, id]
    );
    await connection.end();
    res.redirect("/admin/suppliers");
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi cập nhật nhà cung cấp");
  }
};

// Xử lý xóa
exports.deleteSupplier = async (req, res) => {
  const id = req.params.id;
  let connection;
  try {
    connection = await getConnection();
    await connection.execute("DELETE FROM suppliers WHERE supplier_id = ?", [id]);
    await connection.end();
    res.redirect("/admin/suppliers");
  } catch (err) {
    if (connection) await connection.end();
    res.status(500).send("Lỗi xóa nhà cung cấp");
  }
};
