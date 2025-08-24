const getConnection = require("../config/db");

/**
 * Danh sách phiếu nhập
 */
exports.list = async (req, res) => {
  try {
    const conn = await getConnection();
    const [rows] = await conn.query(
      `SELECT po.*, s.supplier_name
       FROM purchase_orders po
       JOIN suppliers s ON s.supplier_id = po.supplier_id
       ORDER BY po.created_at DESC`
    );
    await conn.end();

    res.render("admin_pages/purchases/index", {
      orders: rows,
      activePage: "purchases",
      user: req.session.user || null,
    });
  } catch (err) {
    console.error("Lỗi khi lấy danh sách phiếu nhập:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Form tạo phiếu nhập
 */
exports.newForm = async (req, res) => {
  try {
    const conn = await getConnection();

    const [suppliers] = await conn.query(
      `SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name`
    );
    const [products] = await conn.query(
      `SELECT product_id, product_name, stock_quantity 
       FROM products 
       ORDER BY product_name`
    );
    await conn.end();

    res.render("admin_pages/purchases/new", {
      suppliers,
      products,
      activePage: "purchases",
      user: req.session.user || null,
    });
  } catch (err) {
    console.error("Lỗi khi hiển thị form tạo phiếu:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Tạo phiếu nhập mới (draft)
 */
// Tạo phiếu nhập mới (draft)
exports.createDraft = async (req, res) => {
  try {
    const { supplier_id, order_date /*, note */ } = req.body; // note bỏ nếu bảng không có cột này
    const conn = await getConnection();

    // total_amount khởi tạo = 0, tự cộng sau khi thêm item
    const [result] = await conn.query(
      `INSERT INTO purchase_orders 
         (supplier_id, order_date, total_amount, created_at, updated_at)
       VALUES (?, ?, 0, NOW(), NOW())`,
      [supplier_id, order_date]
    );

    await conn.end();

    // Redirect về đúng route /admin (KHÔNG phải /admin_pages)
    res.redirect(`/admin/purchases/${result.insertId}`);
  } catch (err) {
    console.error("Lỗi khi tạo phiếu nhập:", err);
    res.status(500).send(`Lỗi server: ${err.sqlMessage || err.message}`);
  }
};


/**
 * Chi tiết phiếu nhập
 */
exports.detail = async (req, res) => {
  try {
    const id = req.params.id;
    const conn = await getConnection();

    const [[po]] = await conn.query(
      `SELECT po.*, s.supplier_name
       FROM purchase_orders po
       JOIN suppliers s ON s.supplier_id = po.supplier_id
       WHERE po.purchase_order_id = ?`,
      [id]
    );

    if (!po) {
      await conn.end();
      return res.status(404).send("Phiếu nhập không tồn tại");
    }

    const [items] = await conn.query(
      `SELECT i.*, p.product_name 
       FROM purchase_order_items i
       JOIN products p ON p.product_id = i.product_id
       WHERE i.purchase_order_id = ?`,
      [id]
    );

    const [products] = await conn.query(
      `SELECT product_id, product_name, stock_quantity 
       FROM products 
       ORDER BY product_name`
    );

    await conn.end();

    res.render("admin_pages/purchases/detail", {
      po,
      items,
      products,
      activePage: "purchases",
      user: req.session.user || null,
    });
  } catch (err) {
    console.error("Lỗi khi lấy chi tiết phiếu:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Thêm sản phẩm vào phiếu nhập
 */
exports.addItem = async (req, res) => {
  try {
    const { id } = req.params;
    const { product_id, quantity, price } = req.body;
    const conn = await getConnection();

    const [[po]] = await conn.query(
      `SELECT status FROM purchase_orders WHERE purchase_order_id = ?`,
      [id]
    );

    if (!po || ["received", "cancelled"].includes(po.status)) {
      await conn.end();
      return res.status(400).send("Không thể thêm mục");
    }

    await conn.query(
      `INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, price)
       VALUES (?, ?, ?, ?)`,
      [id, product_id, quantity, price]
    );

    // Tính tổng tiền
    const [[{ total }]] = await conn.query(
      `SELECT COALESCE(SUM(quantity * price), 0) AS total
       FROM purchase_order_items
       WHERE purchase_order_id = ?`,
      [id]
    );

    await conn.query(
      `UPDATE purchase_orders 
       SET total_amount = ?, updated_at = NOW() 
       WHERE purchase_order_id = ?`,
      [total, id]
    );

    await conn.end();
    res.redirect(`/admin/purchases/${id}`);
  } catch (err) {
    console.error("Lỗi khi thêm sản phẩm:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Submit phiếu nhập (draft -> submitted)
 */
exports.submit = async (req, res) => {
  try {
    const id = req.params.id;
    const conn = await getConnection();

    const [[po]] = await conn.query(
      `SELECT status FROM purchase_orders WHERE purchase_order_id = ?`,
      [id]
    );

    if (!po || po.status !== "draft") {
      await conn.end();
      return res.status(400).send("Trạng thái không hợp lệ");
    }

    await conn.query(
      `UPDATE purchase_orders 
       SET status = 'submitted', updated_at = NOW() 
       WHERE purchase_order_id = ?`,
      [id]
    );

    await conn.end();
    res.redirect(`/admin/purchases/${id}`);
  } catch (err) {
    console.error("Lỗi khi submit phiếu:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Nhập kho (submitted -> received)
 */
exports.receive = async (req, res) => {
  const id = req.params.id;
  const conn = await getConnection();
  try {
    await conn.beginTransaction();

    const [[po]] = await conn.query(
      `SELECT status FROM purchase_orders 
       WHERE purchase_order_id = ? FOR UPDATE`,
      [id]
    );

    if (!po || !["draft", "submitted"].includes(po.status)) {
      await conn.rollback();
      await conn.end();
      return res.status(400).send("Trạng thái không hợp lệ");
    }

    const [items] = await conn.query(
      `SELECT product_id, quantity 
       FROM purchase_order_items 
       WHERE purchase_order_id = ?`,
      [id]
    );

    if (items.length === 0) {
      await conn.rollback();
      await conn.end();
      return res.status(400).send("Phiếu chưa có sản phẩm");
    }

    // Cập nhật tồn kho
    for (const item of items) {
      await conn.query(
        `UPDATE products 
         SET stock_quantity = stock_quantity + ? 
         WHERE product_id = ?`,
        [item.quantity, item.product_id]
      );
    }

    await conn.query(
      `UPDATE purchase_orders 
       SET status = 'received', updated_at = NOW() 
       WHERE purchase_order_id = ?`,
      [id]
    );

    await conn.commit();
    await conn.end();
    res.redirect(`/admin/purchases/${id}`);
  } catch (err) {
    await conn.rollback();
    await conn.end();
    console.error("Lỗi khi nhập kho:", err);
    res.status(500).send("Lỗi server");
  }
};

/**
 * Hủy phiếu nhập
 */
exports.cancel = async (req, res) => {
  try {
    const id = req.params.id;
    const conn = await getConnection();

    const [[po]] = await conn.query(
      `SELECT status FROM purchase_orders WHERE purchase_order_id = ?`,
      [id]
    );

    if (!po || po.status === "received") {
      await conn.end();
      return res.status(400).send("Không thể hủy phiếu đã nhập kho");
    }

    await conn.query(
      `UPDATE purchase_orders 
       SET status = 'cancelled', updated_at = NOW() 
       WHERE purchase_order_id = ?`,
      [id]
    );

    await conn.end();
    res.redirect("/admin/purchases");
  } catch (err) {
    console.error("Lỗi khi hủy phiếu:", err);
    res.status(500).send("Lỗi server");
  }
};
