const getConnection = require("../config/db");

exports.getAllProducts = async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query(`
      SELECT p.product_id, p.product_name, p.image_url, p.description, p.price, p.stock_quantity, p.created_at, c.category_name
      FROM products p
      LEFT JOIN categories c ON p.category_id = c.category_id
      ORDER BY p.product_id
    `);
    res.render("admin_pages/product", { products: rows });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

exports.getProductById = async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    const [rows] = await connection.query(
      `
      SELECT p.*, c.category_name FROM products p
      LEFT JOIN categories c ON p.category_id = c.id
      WHERE p.id = ?
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

exports.createProduct = async (req, res, next) => {
  let connection;
  try {
    const {
      product_name,
      image,
      description,
      price,
      sale,
      stock,
      sold,
      remark,
      category_id,
    } = req.body;
    connection = await getConnection();
    await connection.query(
      `
      INSERT INTO products (product_name, image, description, price, sale, stock, sold, remark, category_id)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    `,
      [
        product_name,
        image,
        description,
        price,
        sale,
        stock,
        sold || 0,
        remark,
        category_id,
      ]
    );
    res.status(201).json({ message: "Thêm sản phẩm thành công" });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

exports.updateProduct = async (req, res, next) => {
  let connection;
  try {
    const {
      product_name,
      image,
      description,
      price,
      sale,
      stock,
      sold,
      remark,
      category_id,
    } = req.body;
    const id = req.params.id;
    connection = await getConnection();
    await connection.query(
      `
      UPDATE products SET product_name=?, image=?, description=?, price=?, sale=?, stock=?, sold=?, remark=?, category_id=?
      WHERE id=?
    `,
      [
        product_name,
        image,
        description,
        price,
        sale,
        stock,
        sold,
        remark,
        category_id,
        id,
      ]
    );
    res.json({ message: "Cập nhật sản phẩm thành công" });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

exports.deleteProduct = async (req, res, next) => {
  let connection;
  try {
    connection = await getConnection();
    await connection.query(`DELETE FROM products WHERE id = ?`, [
      req.params.id,
    ]);
    res.json({ message: "Xóa sản phẩm thành công" });
  } catch (error) {
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};
