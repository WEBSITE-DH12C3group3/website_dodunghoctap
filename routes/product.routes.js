const express = require("express");
const ejs = require("ejs");
const router = express.Router();
const path = require("path");
const multer = require("multer");
const productController = require("../controllers/product.controller");

// Cấu hình multer lưu file ảnh lên public/uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) =>
    cb(null, path.join(__dirname, "..", "public", "uploads")),
  filename: (req, file, cb) => {
    const unique = Date.now() + "-" + Math.round(Math.random() * 1e9);
    const ext = path.extname(file.originalname || "");
    cb(null, unique + ext);
  },
});
const upload = multer({ storage });

// Danh sách sản phẩm
// Danh sách sản phẩm
router.get("/", async (req, res, next) => {
  try {
    // Lấy dữ liệu từ controller (ví dụ findAll hoặc getAll)
    const products = await productController.getAllProducts();

    const body = await ejs.renderFile(
      path.join(__dirname, "../views/admin_pages/products/product.ejs"),
      { activePage: "products", products } // 👈 truyền thêm products
    );

    res.render("admin_pages/layout", {
      title: "Quản lý sản phẩm",
      activePage: "products",
      body: body,
    });
  } catch (error) {
    next(error);
  }
});

// Form thêm sản phẩm (dùng dấu gạch dưới như bạn muốn)
router.get("/product_add", productController.renderAddProduct);

// Xử lý thêm sản phẩm có upload ảnh
router.post("/add", upload.single("image"), productController.addProduct);

// Form sửa sản phẩm
router.get("/edit/:id", productController.renderEditProduct);

// Xử lý sửa sản phẩm, có thể upload ảnh mới
router.post(
  "/edit/:id",
  upload.single("image"),
  productController.updateProduct
);

// Form xác nhận xóa sản phẩm (GET)
router.get("/delete/:id", productController.renderDeleteProduct);

// Xử lý xóa sản phẩm (POST)
router.post("/delete/:id", productController.deleteProduct);

module.exports = router;
