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
  console.log('Request received for removeFavorite:', req.body, req.params);
  const { userId } = req.body;
  const productIdNum = Number(req.params.productId);

  if (!productIdNum || isNaN(productIdNum)) {
    console.log('Invalid productId:', req.params.productId);
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

// Lấy danh sách sản phẩm yêu thích của 1 user (cho route /:userId)
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

    const favorites = results.map(item => ({
      product_id: Number(item.product_id),
      product_name: item.product_name,
      price: Number(item.price),
      image_url: item.image_url,
      added_date: item.added_date,
      thanhtien: Number(item.price)
    }));

    const total = favorites.reduce((sum, item) => sum + item.thanhtien, 0);

    return res.status(200).json({ success: true, favorites, total });
  } catch (err) {
    console.error("Lỗi lấy danh sách yêu thích:", err);
    return res.status(500).json({ success: false, error: err.message });
  }
};

// Hàm lấy hoặc đồng bộ danh sách yêu thích (cho route /)
exports.getOrSyncFavorites = async (req, res) => {
  const userId = req.session.user ? req.session.user.user_id : null;
  let favorites = [];
  let total = 0;

  try {
    const connection = await getConnection();

    if (userId) {
      // Người dùng đã đăng nhập: Đồng bộ session vào database và lấy từ bảng favourite
      const sessionFavorites = req.session.favorites ? JSON.parse(req.session.favorites || '[]').map(id => Number(id)) : [];

      await connection.beginTransaction();
      try {
        // Đồng bộ session favorites vào bảng favourite
        await syncSessionFavoritesToDB(userId, sessionFavorites, connection);
        req.session.favorites = JSON.stringify([]); // Xóa session favorites sau khi đồng bộ

        // Lấy danh sách yêu thích từ database
        const [favoriteItems] = await connection.execute(
          `
            SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
            FROM favourite f
            JOIN products p ON f.product_id = p.product_id
            WHERE f.user_id = ?
            ORDER BY f.added_date DESC
          `,
          [userId]
        );

        favorites = favoriteItems.map(item => ({
          product_id: Number(item.product_id),
          product_name: item.product_name,
          image_url: item.image_url,
          price: Number(item.price),
          added_date: item.added_date,
          thanhtien: Number(item.price) // Tính giá mỗi sản phẩm (tương tự cart)
        }));

        total = favorites.reduce((sum, item) => sum + item.thanhtien, 0);

        await connection.commit();
      } catch (err) {
        await connection.rollback();
        throw err;
      }
    } else {
      // Khách vãng lai: Lấy từ session
      const sessionFavorites = req.session.favorites ? JSON.parse(req.session.favorites || '[]').map(id => Number(id)) : [];
      if (sessionFavorites.length > 0) {
        const placeholders = sessionFavorites.map(() => '?').join(', ');
        const [favoriteItems] = await connection.execute(
          `
            SELECT product_id, product_name, price, image_url
            FROM products
            WHERE product_id IN (${placeholders})
          `,
          sessionFavorites
        );

        favorites = favoriteItems.map(item => ({
          product_id: Number(item.product_id),
          product_name: item.product_name,
          image_url: item.image_url,
          price: Number(item.price),
          added_date: null, // Không có added_date cho khách vãng lai
          thanhtien: Number(item.price) // Tính giá mỗi sản phẩm
        }));

        total = favorites.reduce((sum, item) => sum + item.thanhtien, 0);
      }
    }

    await connection.end();

    // Debug
    console.log('Session user:', req.session.user);
    console.log('Session favorites:', req.session.favorites);
    console.log('Favorites:', favorites);
    console.log('Total:', total);

    res.render('pages/liked', {
      title: 'Danh sách yêu thích',
      favorites,
      total,
      user: req.session.user || null
    });
  } catch (err) {
    console.error('Lỗi lấy hoặc đồng bộ danh sách yêu thích:', err);
    res.status(500).render('pages/liked', {
      title: 'Danh sách yêu thích',
      favorites: [],
      total: 0,
      user: req.session.user || null,
      error: 'Lỗi khi tải danh sách yêu thích'
    });
  }
};