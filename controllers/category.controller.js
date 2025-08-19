// Giả sử bạn có một file kết nối database, ví dụ: /config/database.js
const db = require("../config/db"); // Import kết nối database

// Hiển thị trang quản lý danh mục
const getCategories = async (req, res) => {
  try {
    const [rows] = await db.query(
      "SELECT * FROM categories ORDER BY id, category_name"
    );
    // Render trang EJS và truyền dữ liệu danh mục vào
    res.render("admin_pages/category/category", {
      categories: rows,
      // Giả sử bạn có thông báo từ các hành động khác (thêm, sửa, xóa)
      status: req.query.status,
      title: req.query.title,
      message: req.query.message,
    });
  } catch (error) {
    console.error(error);
    res.render("admin_pages/category/category", {
      categories: [],
      status: "error",
      title: "Lỗi!",
      message: "Không thể tải dữ liệu danh mục.",
    });
  }
};

// Thêm danh mục mới
const addCategory = async (req, res) => {
  const { ID, Name } = req.body;

  // --- VALIDATION ---
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
    // Kiểm tra mã danh mục đã tồn tại
    const [idExists] = await db.query(
      "SELECT id FROM categories WHERE id = ?",
      [ID]
    );
    if (idExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Mã danh mục đã tồn tại!")
      );
    }

    // Kiểm tra tên danh mục đã tồn tại
    const [nameExists] = await db.query(
      "SELECT id FROM categories WHERE category_name = ?",
      [Name]
    );
    if (nameExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Tên danh mục đã tồn tại!")
      );
    }

    // Thêm vào database
    await db.query("INSERT INTO categories (id, category_name) VALUES (?, ?)", [
      ID,
      Name,
    ]);
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
  }
};

// Cập nhật danh mục
const updateCategory = async (req, res) => {
  const { cateid, catename } = req.body;

  // --- VALIDATION ---
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
  const forbiddenCharsPattern = /[#\$%\^&\*\(\)=\+\[\]\{\};:\'\"<>,\?\/\\\\|]/;
  if (forbiddenCharsPattern.test(catename)) {
    return res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Tên danh mục không được chứa ký tự đặc biệt!")
    );
  }

  try {
    // Kiểm tra tên danh mục đã tồn tại (ngoại trừ chính nó)
    const [nameExists] = await db.query(
      "SELECT id FROM categories WHERE category_name = ? AND id != ?",
      [catename, cateid]
    );
    if (nameExists.length > 0) {
      return res.redirect(
        "/admin/category?status=error&title=Lỗi!&message=" +
          encodeURIComponent("Tên danh mục đã tồn tại!")
      );
    }

    // Cập nhật database
    await db.query("UPDATE categories SET category_name = ? WHERE id = ?", [
      catename,
      cateid,
    ]);
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
  }
};

// Xóa danh mục
const deleteCategory = async (req, res) => {
  const { id } = req.params; // Lấy id từ URL

  try {
    // Bắt đầu transaction (nếu DB engine của bạn hỗ trợ)
    await db.beginTransaction();

    // Xóa các sản phẩm liên quan
    await db.query("DELETE FROM products WHERE category_id = ?", [id]);

    // Xóa danh mục
    await db.query("DELETE FROM categories WHERE id = ?", [id]);

    // Commit transaction
    await db.commit();

    res.redirect(
      "/admin/category?status=success&title=Thành công!&message=" +
        encodeURIComponent("Xóa danh mục và các sản phẩm liên quan thành công!")
    );
  } catch (error) {
    // Rollback nếu có lỗi
    await db.rollback();
    console.error(error);
    res.redirect(
      "/admin/category?status=error&title=Lỗi!&message=" +
        encodeURIComponent("Lỗi khi xóa danh mục.")
    );
  }
};

module.exports = {
  getCategories,
  addCategory,
  updateCategory,
  deleteCategory,
};
