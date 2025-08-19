const getConnection = require("../config/db");

// Hiển thị danh mục
const getCategories = async (req, res) => {
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query(
      "SELECT * FROM categories ORDER BY category_id, category_name"
    );
    // res.render("admin_pages/category/category", {
    //   categories: rows,
    //   status: req.query.status,
    //   title: req.query.title,
    //   message: req.query.message,
    // });
    return rows;
  } catch (error) {
    console.error(error);
    res.render("admin_pages/category/category", {
      categories: [],
      status: "error",
      title: "Lỗi!",
      message: "Không thể tải dữ liệu danh mục.",
    });
  } finally {
    if (connection) await connection.end();
  }
};

// Render form thêm danh mục
const renderAddCategory = (req, res) => {
  res.render("admin_pages/category/category_add", {
    error: "",
    category: { category_id: "", category_name: "" },
  });
};

// Thêm danh mục mới
const addCategory = async (req, res) => {
  const { ID, Name } = req.body;
  let connection;

  // Validation
  if (!ID) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Mã danh mục không được để trống!")
    );
  }
  if (/\s/.test(ID)) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Mã danh mục không được chứa khoảng trắng!")
    );
  }
  if (!/^[A-Za-z0-9]+$/.test(ID)) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Mã danh mục chỉ được chứa chữ cái và số!")
    );
  }
  if (!/[A-Za-z]/.test(ID) || !/[0-9]/.test(ID)) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent(
          "Mã danh mục phải bao gồm ít nhất một chữ cái và một số!"
        )
    );
  }
  if (!Name) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục không được để trống!")
    );
  }
  if (Name.length > 29) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục quá dài, tối đa 29 ký tự!")
    );
  }

  try {
    connection = await getConnection();

    // Kiểm tra mã danh mục đã tồn tại
    const [idExists] = await connection.query(
      "SELECT category_id FROM categories WHERE category_id = ?",
      [ID]
    );
    if (idExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Mã danh mục đã tồn tại!")
      );
    }

    // Kiểm tra tên danh mục đã tồn tại
    const [nameExists] = await connection.query(
      "SELECT category_id FROM categories WHERE category_name = ?",
      [Name]
    );
    if (nameExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Tên danh mục đã tồn tại!")
      );
    }

    // Thêm vào database
    await connection.query(
      "INSERT INTO categories (category_id, category_name) VALUES (?, ?)",
      [ID, Name]
    );
    res.redirect(
      "/admin/category?status=success&title=Thành công!&message=" +
        encodeURIComponent("Thêm danh mục thành công!")
    );
  } catch (error) {
    console.error(error);
    res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Lỗi khi thêm danh mục.")
    );
  } finally {
    if (connection) await connection.end();
  }
};

// Render form sửa danh mục
const renderEditCategory = async (req, res) => {
  const id = req.params.id;
  let connection;
  try {
    connection = await getConnection();

    const [rows] = await connection.query(
      "SELECT * FROM categories WHERE category_id = ?",
      [id]
    );
    if (rows.length === 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Danh mục không tồn tại!")
      );
    }
    res.render("admin_pages/category/category_edit", {
      error: "",
      category: rows[0],
    });
  } catch (error) {
    console.error(error);
    res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Lỗi khi tải dữ liệu danh mục.")
    );
  } finally {
    if (connection) await connection.end();
  }
};

// Cập nhật danh mục
const updateCategory = async (req, res) => {
  const { cateid, catename } = req.body;
  let connection;

  // Validation
  if (!catename) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục không được để trống!")
    );
  }
  if (catename.length > 29) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục quá dài, tối đa 29 ký tự!")
    );
  }
  const forbiddenCharsPattern = /[#\$%\^&\*\(\)=\+\[\]\{\};:'"<>,\?\\/\\|]/;
  if (forbiddenCharsPattern.test(catename)) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục không được chứa ký tự đặc biệt!")
    );
  }

  try {
    connection = await getConnection();

    // Kiểm tra tên danh mục đã tồn tại (ngoại trừ chính nó)
    const [nameExists] = await connection.query(
      "SELECT category_id FROM categories WHERE category_name = ? AND category_id != ?",
      [catename, cateid]
    );
    if (nameExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Tên danh mục đã tồn tại!")
      );
    }

    // Cập nhật database
    await connection.query(
      "UPDATE categories SET category_name = ? WHERE category_id = ?",
      [catename, cateid]
    );
    res.redirect(
      "/admin/category?status=success&title=Thành công!&message=" +
        encodeURIComponent("Cập nhật danh mục thành công!")
    );
  } catch (error) {
    console.error(error);
    res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Lỗi khi cập nhật danh mục.")
    );
  } finally {
    if (connection) await connection.end();
  }
};

// Xóa danh mục
const deleteCategory = async (req, res) => {
  const { id } = req.params;
  let connection;

  try {
    connection = await getConnection();
    await connection.beginTransaction();

    // Xóa các sản phẩm thuộc danh mục này
    await connection.query("DELETE FROM products WHERE category_id = ?", [id]);

    // Xóa danh mục
    await connection.query("DELETE FROM categories WHERE category_id = ?", [
      id,
    ]);

    await connection.commit();

    res.redirect(
      "/admin/category?status=success&title=Thành công!&message=" +
        encodeURIComponent("Xóa danh mục và các sản phẩm liên quan thành công!")
    );
  } catch (error) {
    if (connection) await connection.rollback();
    console.error(error);
    res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Lỗi khi xóa danh mục.")
    );
  } finally {
    if (connection) await connection.end();
  }
};

module.exports = {
  getCategories,
  renderAddCategory,
  addCategory,
  renderEditCategory,
  updateCategory,
  deleteCategory,
};
