const express = require("express");
const router = express.Router();
const categoryController = require("../controllers/category.controller");

// GET /admin/category - Hiển thị trang quản lý danh mục
router.get("/", categoryController.getCategories);

// POST /admin/category/add - Xử lý thêm mới danh mục
router.post("/add", categoryController.addCategory);

// POST /admin/category/edit - Xử lý cập nhật danh mục
router.post("/edit", categoryController.updateCategory);

// GET /admin/category/delete/:id - Xử lý xóa danh mục
// Sử dụng phương thức DELETE sẽ đúng chuẩn RESTful hơn, nhưng GET đơn giản hơn với thẻ <a>
router.get("/delete/:id", categoryController.deleteCategory);

module.exports = router;
