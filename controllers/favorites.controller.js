const getConnection = require("../config/db");

// Hàm đồng bộ danh sách yêu thích từ session vào bảng favourite
const syncSessionFavoritesToDB = async (userId, sessionFavorites, conn) => {
  if (!sessionFavorites || sessionFavorites.length === 0) return;

  for (const productId of sessionFavorites) {
    // Kiểm tra xem sản phẩm đã có trong bảng favourite chưa
    const [existingItem] = await conn.query(
      "SELECT * FROM favourite WHERE user_id = ? AND product_id = ?",
      [userId, productId]
    );

    if (existingItem.length === 0) {
      // Thêm sản phẩm mới vào bảng favourite
      await conn.query(
        "INSERT INTO favourite (user_id, product_id) VALUES (?, ?)",
        [userId, productId]
      );
    }
  }
};

// Thêm sản phẩm vào danh sách yêu thích
exports.addFavorite = async (req, res) => {
  const { userId, productId } = req.body;

  if (!productId) {
    return res.status(400).json({ success: false, error: "Thiếu productId" });
  }

  try {
    const connection = await getConnection();
    const [product] = await connection.execute(
      "SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?",
      [productId]
    );

    if (product.length === 0) {
      await connection.end();
      return res.status(404).json({ success: false, error: "Sản phẩm không tồn tại" });
    }

    if (req.session.user && userId && userId == req.session.user.user_id) {
      // Khách đã đăng nhập: Thêm vào bảng favourite
      const [existingItem] = await connection.execute(
        "SELECT * FROM favourite WHERE user_id = ? AND product_id = ?",
        [userId, productId]
      );

      if (existingItem.length > 0) {
        await connection.end();
        return res.status(400).json({ success: false, error: "Sản phẩm đã có trong yêu thích" });
      }

      await connection.execute(
        "INSERT INTO favourite (user_id, product_id) VALUES (?, ?)",
        [userId, productId]
      );

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

      return res.status(201).json({ success: true, message: "Đã thêm sản phẩm vào yêu thích", favorites: results });
    } else {
      // Khách vãng lai: Thêm vào session
      if (!req.session.favorites) req.session.favorites = [];
      if (req.session.favorites.includes(productId)) {
        await connection.end();
        return res.status(400).json({ success: false, error: "Sản phẩm đã có trong yêu thích" });
      }

      req.session.favorites.push(productId);
      const [results] = await connection.execute(
        `
          SELECT product_id, product_name, price, image_url
          FROM products
          WHERE product_id IN (?)
        `,
        [req.session.favorites]
      );
      await connection.end();

      const favorites = results.map(item => ({
        ...item,
        added_date: new Date()
      }));

      return res.status(201).json({ success: true, message: "Đã thêm sản phẩm vào yêu thích", favorites });
    }
  } catch (err) {
    console.error("Lỗi thêm sản phẩm yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};

// Xóa sản phẩm khỏi danh sách yêu thích
exports.removeFavorite = async (req, res) => {
  const { userId } = req.body;
  const { productId } = req.params;

  if (!productId) {
    return res.status(400).json({ success: false, error: "Thiếu productId" });
  }

  try {
    if (req.session.user && userId && userId == req.session.user.user_id) {
      // Khách đã đăng nhập: Xóa từ bảng favourite
      const connection = await getConnection();
      const [result] = await connection.execute(
        "DELETE FROM favourite WHERE user_id = ? AND product_id = ?",
        [userId, productId]
      );

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

      if (result.affectedRows === 0) {
        return res.status(404).json({ success: false, message: "Không tìm thấy sản phẩm trong yêu thích" });
      }

      return res.status(200).json({ success: true, message: "Đã xóa sản phẩm khỏi yêu thích", favorites: results });
    } else {
      // Khách vãng lai: Xóa từ session
      if (!req.session.favorites || !req.session.favorites.includes(productId)) {
        return res.status(404).json({ success: false, message: "Không tìm thấy sản phẩm trong yêu thích" });
      }

      req.session.favorites = req.session.favorites.filter(id => id != productId);
      const connection = await getConnection();
      const [results] = await connection.execute(
        `
          SELECT product_id, product_name, price, image_url
          FROM products
          WHERE product_id IN (?)
        `,
        [req.session.favorites.length > 0 ? req.session.favorites : [0]]
      );
      await connection.end();

      const favorites = results.map(item => ({
        ...item,
        added_date: new Date()
      }));

      return res.status(200).json({ success: true, message: "Đã xóa sản phẩm khỏi yêu thích", favorites });
    }
  } catch (err) {
    console.error("Lỗi xóa sản phẩm yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};

// Lấy danh sách sản phẩm yêu thích của 1 user
exports.getFavorites = async (req, res) => {
  const { userId } = req.params;

  if (!userId) {
    return res.status(400).json({ success: false, error: "Thiếu userId" });
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

    return res.status(200).json({ success: true, favorites: results });
  } catch (err) {
    console.error("Lỗi lấy danh sách yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};

// Render trang yêu thích
exports.renderFavorites = async (req, res) => {
  const userId = req.user ? req.user.user_id : null;

  try {
    let favorites = [];
    if (userId) {
      // Lấy từ database cho user đã đăng nhập
      const [rows] = await req.db.execute(
        'SELECT product_id FROM favorites WHERE user_id = ?',
        [userId]
      );
      favorites = rows.map(row => Number(row.product_id)); // Convert sang number
    } else {
      // Lấy từ session cho khách vãng lai
      const sessionFavorites = req.session.favorites || '[]';
      favorites = JSON.parse(sessionFavorites).map(id => Number(id)); // Parse và convert sang number
    }

    // Nếu mảng rỗng, trả về danh sách sản phẩm rỗng mà không chạy query
    if (favorites.length === 0) {
      return res.render('favorites', { products: [], user: req.user });
    }

    // Tạo placeholder động
    const placeholders = favorites.map(() => '?').join(', ');
    const query = `
      SELECT product_id, product_name, price, image_url
      FROM products
      WHERE product_id IN (${placeholders})
    `;

    // Debug để kiểm tra (bạn có thể xóa sau khi test)
    console.log('Favorites:', favorites);
    console.log('Query:', query);
    console.log('Parameters:', favorites);

    // Thực thi query
    const [products] = await req.db.execute(query, favorites);

    // Render trang
    res.render('favorites', { products, user: req.user });
  } catch (error) {
    console.error('Lỗi render trang yêu thích:', error);
    res.status(500).send('Lỗi server');
  }
};

// Đồng bộ danh sách yêu thích khi đăng nhập
exports.syncFavoritesOnLogin = async (req, res) => {
  try {
    if (!req.session.user || !req.session.user.user_id) {
      return res.status(401).json({ success: false, error: "Chưa đăng nhập" });
    }

    const connection = await getConnection();
    await syncSessionFavoritesToDB(req.session.user.user_id, req.session.favorites, connection);
    req.session.favorites = []; // Xóa danh sách yêu thích trong session sau khi đồng bộ

    const [results] = await connection.execute(
      `
        SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
        FROM favourite f
        JOIN products p ON f.product_id = p.product_id
        WHERE f.user_id = ?
        ORDER BY f.added_date DESC
      `,
      [req.session.user.user_id]
    );
    await connection.end();

    return res.status(200).json({ success: true, favorites: results });
  } catch (err) {
    console.error("Lỗi đồng bộ danh sách yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};