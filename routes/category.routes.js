const express = require("express");
const router = express.Router();
const categoryController = require("../controllers/category.controller");

// Trang danh mục - danh sách
router.get("/", categoryController.getCategories);

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
