const getConnection = require("../config/db");

// Hàm đồng bộ danh sách yêu thích từ session vào bảng favourite
const syncSessionFavoritesToDB = async (userId, sessionFavorites, conn) => {
  if (!sessionFavorites || !Array.isArray(sessionFavorites) || sessionFavorites.length === 0) {
    console.log('Không có sản phẩm yêu thích trong session để đồng bộ');
    return;
  }

  const favorites = sessionFavorites.map(id => Number(id)).filter(id => !isNaN(id));
  if (favorites.length === 0) {
    console.log('Danh sách yêu thích trong session không hợp lệ');
    return;
  }

  try {
    for (const productId of favorites) {
      // Kiểm tra xem sản phẩm có tồn tại trong bảng products không
      const [product] = await conn.execute(
        'SELECT product_id FROM products WHERE product_id = ?',
        [productId]
      );
      if (product.length === 0) {
        console.log(`Sản phẩm ${productId} không tồn tại, bỏ qua`);
        continue;
      }

      // Kiểm tra xem sản phẩm đã có trong bảng favourite chưa
      const [existingItem] = await conn.execute(
        'SELECT * FROM favourite WHERE user_id = ? AND product_id = ?',
        [userId, productId]
      );

      if (existingItem.length === 0) {
        // Thêm sản phẩm mới vào bảng favourite
        await conn.execute(
          'INSERT INTO favourite (user_id, product_id) VALUES (?, ?)',
          [userId, productId]
        );
        console.log(`Đã thêm sản phẩm ${productId} vào favourite cho user ${userId}`);
      } else {
        console.log(`Sản phẩm ${productId} đã có trong favourite của user ${userId}, bỏ qua`);
      }
    }
  } catch (err) {
    console.error('Lỗi đồng bộ sản phẩm yêu thích:', err);
    throw err;
  }
};

// Thêm sản phẩm vào danh sách yêu thích
exports.addFavorite = async (req, res) => {
  const { userId, productId } = req.body;
  const productIdNum = Number(productId);

  if (!productId || isNaN(productIdNum)) {
    return res.status(400).json({ success: false, error: "Thiếu hoặc productId không hợp lệ" });
  }

  try {
    const connection = await getConnection();
    const [product] = await connection.execute(
      "SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?",
      [productIdNum]
    );

    if (product.length === 0) {
      await connection.end();
      return res.status(404).json({ success: false, error: "Sản phẩm không tồn tại" });
    }

    if (req.session.user && userId && userId == req.session.user.user_id) {
      const [existingItem] = await connection.execute(
        "SELECT * FROM favourite WHERE user_id = ? AND product_id = ?",
        [userId, productIdNum]
      );

      if (existingItem.length > 0) {
        await connection.end();
        return res.status(400).json({ success: false, error: "Sản phẩm đã có trong yêu thích" });
      }

      await connection.execute(
        "INSERT INTO favourite (user_id, product_id) VALUES (?, ?)",
        [userId, productIdNum]
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
      let favorites = req.session.favorites ? JSON.parse(req.session.favorites).map(id => Number(id)) : [];
      if (favorites.includes(productIdNum)) {
        await connection.end();
        return res.status(400).json({ success: false, error: "Sản phẩm đã có trong yêu thích" });
      }

      favorites.push(productIdNum);
      req.session.favorites = JSON.stringify(favorites);

      let results = [];
      if (favorites.length > 0) {
        const placeholders = favorites.map(() => '?').join(', ');
        [results] = await connection.execute(
          `
            SELECT product_id, product_name, price, image_url
            FROM products
            WHERE product_id IN (${placeholders})
          `,
          favorites
        );
      }
      await connection.end();

      const favoritesWithDate = results.map(item => ({
        ...item,
        added_date: new Date()
      }));

      return res.status(201).json({ success: true, message: "Đã thêm sản phẩm vào yêu thích", favorites: favoritesWithDate });
    }
  } catch (err) {
    console.error("Lỗi thêm sản phẩm yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};

// Xóa sản phẩm khỏi danh sách yêu thích
exports.removeFavorite = async (req, res) => {
  const { userId } = req.body;
  const productIdNum = Number(req.params.productId);

  if (!productIdNum || isNaN(productIdNum)) {
    return res.status(400).json({ success: false, error: "Thiếu hoặc productId không hợp lệ" });
  }

  try {
    if (req.session.user && userId && userId == req.session.user.user_id) {
      const connection = await getConnection();
      const [result] = await connection.execute(
        "DELETE FROM favourite WHERE user_id = ? AND product_id = ?",
        [userId, productIdNum]
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
      let favorites = req.session.favorites ? JSON.parse(req.session.favorites).map(id => Number(id)) : [];
      if (!favorites.includes(productIdNum)) {
        return res.status(404).json({ success: false, message: "Không tìm thấy sản phẩm trong yêu thích" });
      }

      favorites = favorites.filter(id => id !== productIdNum);
      req.session.favorites = JSON.stringify(favorites);

      const connection = await getConnection();
      let results = [];
      if (favorites.length > 0) {
        const placeholders = favorites.map(() => '?').join(', ');
        [results] = await connection.execute(
          `
            SELECT product_id, product_name, price, image_url
            FROM products
            WHERE product_id IN (${placeholders})
          `,
          favorites
        );
      }
      await connection.end();

      const favoritesWithDate = results.map(item => ({
        ...item,
        added_date: new Date()
      }));

      return res.status(200).json({ success: true, message: "Đã xóa sản phẩm khỏi yêu thích", favorites: favoritesWithDate });
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
  const userId = req.session.user ? req.session.user.user_id : null;

  try {
    let favorites = [];
    let products = [];
    const connection = await getConnection();

    if (userId) {
      // Lấy từ database cho user đã đăng nhập
      const [rows] = await connection.execute(
        `
          SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
          FROM favourite f
          JOIN products p ON f.product_id = p.product_id
          WHERE f.user_id = ?
          ORDER BY f.added_date DESC
        `,
        [userId]
      );
      products = rows.map(row => ({
        product_id: Number(row.product_id),
        product_name: row.product_name,
        price: row.price,
        image_url: row.image_url,
        added_date: row.added_date
      }));
    } else {
      // Lấy từ session cho khách vãng lai
      favorites = req.session.favorites ? JSON.parse(req.session.favorites || '[]').map(id => Number(id)) : [];
      if (favorites.length > 0) {
        const placeholders = favorites.map(() => '?').join(', ');
        const query = `
          SELECT product_id, product_name, price, image_url
          FROM products
          WHERE product_id IN (${placeholders})
        `;
        const [rows] = await connection.execute(query, favorites);
        products = rows.map(row => ({
          product_id: Number(row.product_id),
          product_name: row.product_name,
          price: row.price,
          image_url: row.image_url,
          added_date: null // Không có added_date cho khách vãng lai
        }));
      }
    }

    await connection.end();

    // Debug
    console.log('Session user:', req.session.user);
    console.log('Favorites:', favorites);
    console.log('Products:', products);

    // Render trang
    res.render('pages/liked', { products, user: req.session.user });
  } catch (error) {
    console.error('Lỗi render trang yêu thích:', error);
    res.status(500).send('Lỗi server');
  }
};

// Đồng bộ danh sách yêu thích khi đăng nhập
exports.syncFavoritesOnLogin = async (req, res) => {
  try {
    if (!req.session.user || !req.session.user.user_id) {
      return res.status(401).json({ success: false, error: 'Chưa đăng nhập' });
    }

    const userId = req.session.user.user_id;
    const sessionFavorites = req.session.favorites ? JSON.parse(req.session.favorites || '[]').map(id => Number(id)) : [];

    const connection = await getConnection();
    await connection.beginTransaction(); // Bắt đầu transaction

    try {
      // Đồng bộ session favorites vào bảng favourite
      await syncSessionFavoritesToDB(userId, sessionFavorites, connection);
      req.session.favorites = JSON.stringify([]); // Xóa session favorites sau khi đồng bộ

      // Lấy danh sách yêu thích từ database
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

      const favorites = results.map(row => ({
        product_id: Number(row.product_id),
        product_name: row.product_name,
        price: row.price,
        image_url: row.image_url,
        added_date: row.added_date
      }));

      await connection.commit();
      await connection.end();

      return res.status(200).json({ success: true, message: 'Đã đồng bộ danh sách yêu thích', favorites });
    } catch (err) {
      await connection.rollback();
      throw err;
    }
  } catch (err) {
    console.error('Lỗi đồng bộ danh sách yêu thích:', err);
    return res.status(500).json({ success: false, error: err.message });
  }
};