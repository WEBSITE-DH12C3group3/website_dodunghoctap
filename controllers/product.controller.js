// Ở đầu file
const getConnection = require("../config/db");

// Helpers mới:
async function getAllAttributes(connection) {
  const [attrs] = await connection.query(`
    SELECT id, name, description
    FROM attributes
    ORDER BY name
  `);
  return attrs;
}

async function getProductAttributes(connection, productId) {
  const [rows] = await connection.query(
    `
    SELECT pa.attribute_id, pa.value, a.name
    FROM product_attributes pa
    JOIN attributes a ON a.id = pa.attribute_id
    WHERE pa.product_id = ?
    ORDER BY a.name
  `,
    [productId]
  );
  return rows;
}
// Đảm bảo có attributes với danh sách tên cho trước. Trả về map { name: id }
async function ensureAttributesByName(connection, items) {
  // items: [{ name, description }]
  const cleaned = (items || [])
    .map(({ name, description }) => ({
      name: (name || "").trim(),
      description: (description || "").trim(),
    }))
    .filter((x) => x.name.length > 0);

  if (!cleaned.length) return {};

  const names = [...new Set(cleaned.map((x) => x.name))];

  // Lấy những cái đã có
  const [exist] = await connection.query(
    `SELECT id, name FROM attributes WHERE name IN (${names
      .map(() => "?")
      .join(",")})`,
    names
  );
  const nameToId = {};
  exist.forEach((r) => (nameToId[r.name] = r.id));

  // Còn lại thì chèn mới
  const toInsert = cleaned.filter((x) => !nameToId[x.name]);
  if (toInsert.length) {
    const vals = toInsert.map((x) => [x.name, x.description || null]);
    await connection.query(
      `INSERT INTO attributes (name, description) VALUES ${vals
        .map(() => "(?, ?)")
        .join(",")}`,
      vals.flat()
    );

    // Reselect để có id
    const [inserted] = await connection.query(
      `SELECT id, name FROM attributes WHERE name IN (${toInsert
        .map(() => "?")
        .join(",")})`,
      toInsert.map((x) => x.name)
    );
    inserted.forEach((r) => (nameToId[r.name] = r.id));
  }

  return nameToId;
}

// Lấy tất cả sản phẩm
exports.getAllProducts = async () => {
  let connection;
  try {
    connection = await getConnection();
    // Thay nội dung truy vấn trong getAllProducts:
    const [rows] = await connection.query(`
  SELECT 
    p.product_id, p.product_name, p.image_url, p.description, p.price, 
    p.stock_quantity, p.created_at, c.category_name,
    GROUP_CONCAT(CONCAT(a.name, ': ', pa.value) ORDER BY a.name SEPARATOR ', ') AS attributes_text
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.category_id
  LEFT JOIN product_attributes pa ON pa.product_id = p.product_id
  LEFT JOIN attributes a ON a.id = pa.attribute_id
  GROUP BY p.product_id
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
      title: "Trang chủ",
    });
  } catch (error) {
    console.error("❌ Lỗi khi lấy sản phẩm:", error);
    res.render("pages/home", {
      products: [],
      user: req.session.user || null,
      title: "Trang chủ",
    });
  }
};

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
    const attributes = await getAllAttributes(connection);
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
      attributes,
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

// addProduct: bọc trong transaction, insert sản phẩm xong thì insert product_attributes
exports.addProduct = async (req, res, next) => {
  let connection;
  try {
    const { product_name, description, price, stock_quantity, category_id } =
      req.body || {};
    const image_url = req.file ? req.file.filename : null;

    if (!product_name || !price) {
      connection = await getConnection();
      const [categories] = await connection.query(`
        SELECT category_id, category_name FROM categories ORDER BY category_name
      `);
      const attributes = await getAllAttributes(connection);
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
        attributes,
      });
    }

    connection = await getConnection();
    await connection.beginTransaction();

    const [result] = await connection.query(
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

    const productId = result.insertId;

    // attributes gửi từ form dạng attributes[<id>] = <value>
    const attrsObj = req.body.attributes || {};
    let entries = Object.entries(attrsObj)
      .map(([attribute_id, value]) => [
        productId,
        Number(attribute_id),
        (value || "").trim(),
      ])
      .filter(([, , value]) => value.length > 0);
    // ======= XỬ LÝ THUỘC TÍNH MỚI NGAY TẠI FORM =======
    const newAttrNames = [].concat(req.body.new_attr_names || []);
    const newAttrValues = [].concat(req.body.new_attr_values || []);
    const newAttrDescs = [].concat(req.body.new_attr_descs || []);

    // Tạo list name/desc để đảm bảo tồn tại trong bảng attributes
    const items = newAttrNames.map((name, i) => ({
      name,
      description: newAttrDescs[i] || "",
    }));

    const nameToId = await ensureAttributesByName(connection, items);

    // Gán giá trị vào product_attributes với id vừa đảm bảo/khớp
    newAttrNames.forEach((name, i) => {
      const value = (newAttrValues[i] || "").trim();
      const aid = nameToId[(name || "").trim()];
      if (aid && value.length > 0) {
        entries.push([productId, Number(aid), value]);
      }
    });

    // DEDUPE: nếu lỡ trùng attribute_id (vd: gõ tên mới trùng tên có sẵn), ưu tiên dòng sau cùng
    const dedup = new Map();
    entries.forEach((row) => dedup.set(row[1], row)); // key = attribute_id
    entries = Array.from(dedup.values());

    if (entries.length) {
      const placeholders = entries.map(() => "(?,?,?)").join(",");
      await connection.query(
        `INSERT INTO product_attributes (product_id, attribute_id, value) VALUES ${placeholders}`,
        entries.flat()
      );
    }

    await connection.commit();
    res.redirect("/admin/products");
  } catch (error) {
    if (connection) await connection.rollback();
    next(error);
  } finally {
    if (connection) await connection.end();
  }
};

// Render form sửa sản phẩm
// renderEditProduct: lấy sản phẩm, categories, tất cả attributes và thuộc tính của sản phẩm
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

    const attributes = await getAllAttributes(connection);
    const productAttrs = await getProductAttributes(connection, id);
    const attrMap = {};
    productAttrs.forEach((p) => (attrMap[p.attribute_id] = p.value));

    res.render("admin_pages/products/product_edit", {
      product,
      categories,
      error: "",
      attributes,
      productAttrs,
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
    const id = Number(req.params.id); // <-- dùng id này thay cho productId
    const { product_name, description, price, stock_quantity, category_id } =
      req.body || {};
    const newImage = req.file ? req.file.filename : null;

    connection = await getConnection();
    await connection.beginTransaction();

    const [[current]] = await connection.query(
      `SELECT image_url FROM products WHERE product_id = ?`,
      [id]
    );
    if (!current) {
      await connection.rollback();
      return res
        .status(404)
        .render("admin_pages/products/product", {
          products: [],
          error: "Không tìm thấy sản phẩm để cập nhật.",
        });
    }

    const image_url = newImage || current.image_url;

    await connection.query(
      `UPDATE products
       SET product_name = ?, image_url = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?
       WHERE product_id = ?`,
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

    // reset hết thuộc tính cũ
    await connection.query(
      `DELETE FROM product_attributes WHERE product_id = ?`,
      [id]
    );

    // Helper ép về mảng (trường hợp form chỉ có 1 dòng)
    const toArray = (v) => (Array.isArray(v) ? v : v != null ? [v] : []);

    // ---- thuộc tính đã có (attributes[attribute_id] = value)
    const attrsObj = req.body.attributes || {};
    let entries = Object.entries(attrsObj)
      .map(([attribute_id, value]) => [
        id,
        Number(attribute_id),
        String(value || "").trim(),
      ])
      .filter(([, , value]) => value.length > 0);

    // ---- thuộc tính mới (new_attr_names/new_attr_values/new_attr_descs)
    const newNames = toArray(req.body.new_attr_names);
    const newValues = toArray(req.body.new_attr_values);
    const newDescs = toArray(req.body.new_attr_descs);

    if (newNames.length) {
      const items = newNames
        .map((name, i) => ({
          name: String(name || "").trim(),
          description: String(newDescs[i] || "").trim(),
        }))
        .filter((x) => x.name.length > 0);

      // đảm bảo có attributes theo tên (tạo nếu chưa có)
      const nameToId = await ensureAttributesByName(connection, items);

      newNames.forEach((name, i) => {
        const aid = nameToId[String(name || "").trim()];
        const value = String(newValues[i] || "").trim();
        if (aid && value) {
          entries.push([id, Number(aid), value]); // <-- dùng id, KHÔNG dùng productId
        }
      });
    }

    // loại trùng attribute_id (giữ giá trị cuối cùng)
    const dedup = new Map();
    for (const row of entries) dedup.set(row[1], row);
    entries = [...dedup.values()];

    if (entries.length) {
      const placeholders = entries.map(() => "(?,?,?)").join(",");
      await connection.query(
        `INSERT INTO product_attributes (product_id, attribute_id, value) VALUES ${placeholders}`,
        entries.flat()
      );
    }

    await connection.commit();
    res.redirect("/admin/products");
  } catch (error) {
    if (connection) await connection.rollback();
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
