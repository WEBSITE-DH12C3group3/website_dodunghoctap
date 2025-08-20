const getConnection = require("../config/db");

// Lấy tất cả sản phẩm
exports.getAllProducts = async () => {
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query(`
      SELECT p.product_id, p.product_name, p.image_url, p.description, p.price, 
             p.stock_quantity, p.created_at, c.category_name
      FROM products p
      LEFT JOIN categories c ON p.category_id = c.category_id
      ORDER BY p.product_id
    `);
    return rows;
  } finally {
    if (connection) await connection.end();
  }
};

// Hiển thị swiper
// exports.showProductsSwiper = async (req, res, next) => {
//   try {
//     const products = await exports.getAllProducts();
//     res.render("pages/view_products", { products });
//   } catch (error) {
//     next(error);
//   }
// };

// Trang home
exports.showHome = async (req, res, next) => {
  try {
    const connection = await getConnection();
    const [rows] = await connection.query("SELECT * FROM products");
    await connection.end();

    console.log("✅ Dữ liệu lấy từ DB:", rows);

    res.render("pages/home", { 
      products: rows,
      user: req.session.user || null,
      title: "Trang chủ"
    });
  } catch (error) {
    console.error("❌ Lỗi khi lấy sản phẩm:", error);
    res.render("pages/home", { 
      products: [],
      user: req.session.user || null,
      title: "Trang chủ"
    });
  }
}

// Render form thêm sản phẩm
exports.renderAddProduct = async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    const [categories] = await connection.query(`
      SELECT category_id, category_name
      FROM categories
      ORDER BY category_name
    `);
    res.render("admin_pages/products/product_add", {
      error: "",
      product: {
        product_name: "",
        image_url: "",
        description: "",
        price: "",
        stock_quantity: "",
        category_id: "",
      },
      categories,
    });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Lấy sản phẩm theo id — API
exports.getProductById = async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query(
      `
      SELECT p.*, c.category_name 
      FROM products p
      LEFT JOIN categories c ON p.category_id = c.category_id
      WHERE p.product_id = ?
    `,
      [req.params.id]
    );
    if (rows.length === 0) {
      return res.status(404).json({ message: "Sản phẩm không tồn tại" });
    }
    res.json(rows[0]);
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Thêm sản phẩm (upload ảnh)
exports.addProduct = async (req, res, next) => {
  let connection;
  try {
    const { product_name, description, price, stock_quantity, category_id } =
      req.body || {};
    const image_url = req.file ? req.file.filename : null;

    if (!product_name || !price) {
      let categories = [];
      try {
        connection = await getConnection();
        [categories] = await connection.query(`
          SELECT category_id, category_name
          FROM categories
          ORDER BY category_name
        `);
      } catch (_) {
      } finally {
        if (connection) {
          await connection.end();
          connection = null;
        }
      }
      return res.status(400).render("admin_pages/products/product_add", {
        error: "Vui lòng nhập tên sản phẩm và giá.",
        product: {
          product_name,
          description,
          price,
          stock_quantity,
          category_id,
          image_url,
        },
        categories,
      });
    }

    connection = await getConnection();
    await connection.query(
      `
      INSERT INTO products (product_name, image_url, description, price, stock_quantity, category_id, created_at)
      VALUES (?, ?, ?, ?, ?, ?, NOW())
      `,
      [
        product_name,
        image_url,
        description,
        price,
        stock_quantity,
        category_id || null,
      ]
    );

    res.redirect("/admin/products");
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Render form sửa sản phẩm
exports.renderEditProduct = async (req, res, next) => {
  let connection;
  try {
    const id = req.params.id;
    connection = await getConnection();

    const [[product]] = await connection.query(
      `
      SELECT p.product_id, p.product_name, p.image_url, p.description, p.price, p.stock_quantity, p.category_id
      FROM products p
      WHERE p.product_id = ?
      `,
      [id]
    );
    if (!product) {
      return res.status(404).render("admin_pages/products/product", {
        products: [],
        error: "Không tìm thấy sản phẩm.",
      });
    }

    const [categories] = await connection.query(`
      SELECT category_id, category_name
      FROM categories
      ORDER BY category_name
    `);

    res.render("admin_pages/products/product_edit", {
      product,
      categories,
      error: "",
    });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Cập nhật sản phẩm, có thể upload ảnh mới
exports.updateProduct = async (req, res, next) => {
  let connection;
  try {
    const id = req.params.id;
    const { product_name, description, price, stock_quantity, category_id } =
      req.body || {};
    const newImage = req.file ? req.file.filename : null;

    connection = await getConnection();

    const [[current]] = await connection.query(
      `SELECT image_url FROM products WHERE product_id = ?`,
      [id]
    );
    if (!current) {
      return res.status(404).render("admin_pages/products/product", {
        products: [],
        error: "Không tìm thấy sản phẩm để cập nhật.",
      });
    }

    const image_url = newImage || current.image_url;

    const [result] = await connection.query(
      `
      UPDATE products
      SET product_name = ?, image_url = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?
      WHERE product_id = ?
      `,
      [
        product_name,
        image_url,
        description,
        price,
        stock_quantity,
        category_id || null,
        id,
      ]
    );
    if (result.affectedRows === 0) {
      return res.status(400).render("admin_pages/products/product_edit", {
        product: {
          product_id: id,
          product_name,
          image_url,
          description,
          price,
          stock_quantity,
          category_id,
        },
        categories: [],
        error: "Cập nhật không thành công.",
      });
    }

    res.redirect("/admin/products");
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Render form xác nhận xóa sản phẩm
exports.renderDeleteProduct = async (req, res, next) => {
  let connection;
  try {
    const id = req.params.id;
    connection = await getConnection();

    const [[product]] = await connection.query(
      `SELECT product_id, product_name FROM products WHERE product_id = ?`,
      [id]
    );
    if (!product) {
      return res.status(404).redirect("/admin/products");
    }

    res.render("admin_pages/products/product_delete", { product });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Xóa sản phẩm
exports.deleteProduct = async (req, res, next) => {
  let connection;
  try {
    const id = req.params.id;
    connection = await getConnection();

    const [result] = await connection.query(
      `DELETE FROM products WHERE product_id = ?`,
      [id]
    );
    if (result.affectedRows === 0) {
      return res.status(404).send("Sản phẩm không tồn tại hoặc đã bị xóa.");
    }

    res.redirect("/admin/products");
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};
