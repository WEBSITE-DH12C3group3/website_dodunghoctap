const express = require("express");
const ejs = require("ejs");
const path = require("path");
const router = express.Router();
const categoryController = require("../controllers/category.controller");

// Trang danh sách danh mục
// Trang danh sách danh mục
router.get("/", async (req, res, next) => {
  try {
    const categories = await categoryController.getCategories(); // ✅ lấy dữ liệu từ DB

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/category/category.ejs"),
      {
        activePage: "category",
        categories, // ✅ truyền vào view
        status: req.query.status,
        title: req.query.title,
        message: req.query.message,
      }
    );

    res.render("admin_pages/layout", {
      title: "Quản lý danh mục",
      activePage: "category",
      body: body,
    });
  } catch (error) {
    next(error);
  }
});

// Form thêm danh mục
router.get("/add", categoryController.renderAddCategory);

// Xử lý thêm mới danh mục
router.post("/add", categoryController.addCategory);

// Form sửa danh mục
router.get("/edit/:id", categoryController.renderEditCategory);

// Xử lý cập nhật danh mục
router.post("/edit", categoryController.updateCategory);

// Xóa danh mục
router.get("/delete/:id", categoryController.deleteCategory);

module.exports = router;
