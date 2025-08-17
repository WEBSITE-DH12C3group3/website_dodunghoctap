const getConnection = require("../config/db");

// Thêm sản phẩm vào danh sách yêu thích
exports.addFavorite = async (req, res) => {
  const { userId, productId } = req.body;

  if (!userId || !productId) {
    return res.status(400).json({ error: "Thiếu userId hoặc productId" });
  }

  try {
    const connection = await getConnection();
    await connection.execute(
      "INSERT INTO favourite (user_id, product_id) VALUES (?, ?)",
      [userId, productId]
    );
    await connection.end();

    res.status(201).json({ message: "Đã thêm sản phẩm vào yêu thích" });
  } catch (err) {
    console.error("Lỗi thêm sản phẩm yêu thích:", err);
    if (err.code === "ER_DUP_ENTRY") {
      return res.status(400).json({ error: "Sản phẩm đã có trong yêu thích" });
    }
    return res.status(500).json({ error: err.message });
  }
};

// Xóa sản phẩm khỏi danh sách yêu thích
exports.removeFavorite = async (req, res) => {
  const { userId } = req.body;
  const { productId } = req.params;

  if (!userId || !productId) {
    return res.status(400).json({ error: "Thiếu userId hoặc productId" });
  }

  try {
    const connection = await getConnection();
    const [result] = await connection.execute(
      "DELETE FROM favourite WHERE user_id = ? AND product_id = ?",
      [userId, productId]
    );
    await connection.end();

    if (result.affectedRows === 0) {
      return res.status(404).json({ message: "Không tìm thấy sản phẩm trong yêu thích" });
    }
    res.status(200).json({ message: "Đã xóa sản phẩm khỏi yêu thích" });
  } catch (err) {
    console.error("Lỗi xóa sản phẩm yêu thích:", err);
    return res.status(500).json({ error: err.message });
  }
};

// Lấy danh sách sản phẩm yêu thích của 1 user
exports.getFavorites = async (req, res) => {
  const { userId } = req.params;

  if (!userId) {
    return res.status(400).json({ error: "Thiếu userId" });
  }

  try {
    const connection = await getConnection();
    const [results] = await connection.execute(
      `
        SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
        FROM favourite f
        JOIN products p ON f.product_id = p.product_id
        WHERE f.user_id = ?
        ORDER BY f.added_date DESC
      `,
      [userId]
    );
    await connection.end();

    res.status(200).json(results);
  } catch (err) {
    console.error("Lỗi lấy danh sách yêu thích:", err);
    return res.status(500).json({ error: err.message });
  }
};

// Render trang yêu thích
exports.renderFavorites = async (req, res) => {
  const user = req.session.user; // user sau khi login lưu trong session

  if (!user) {
    return res.redirect("/login"); // chưa đăng nhập thì quay lại login
  }

  try {
    const connection = await getConnection();
    const [results] = await connection.execute(
      `
        SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
        FROM favourite f
        JOIN products p ON f.product_id = p.product_id
        WHERE f.user_id = ?
        ORDER BY f.added_date DESC
      `,
      [user.user_id]
    );
    await connection.end();

    res.render("pages/liked", {
      title: "Trang yêu thích",
      user: user,
      favorites: results,
    });
  } catch (err) {
    console.error("Lỗi render trang yêu thích:", err);
    res.status(500).send("Lỗi khi lấy sản phẩm yêu thích: " + err.message);
  }
};