const getConnection = require("../config/db");

// Đồng bộ giỏ hàng từ session vào bảng cart khi người dùng đăng nhập
const syncSessionCartToDB = async (userId, sessionCart, conn) => {
  if (!sessionCart || sessionCart.length === 0) return;

  for (const item of sessionCart) {
    // Kiểm tra xem sản phẩm đã có trong bảng cart chưa
    const [existingItem] = await conn.query(
      "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
      [userId, item.product_id]
    );

    if (existingItem.length > 0) {
      // Cập nhật số lượng nếu sản phẩm đã có
      await conn.query(
        "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?",
        [item.quantity, userId, item.product_id]
      );
    } else {
      // Thêm sản phẩm mới vào bảng cart
      await conn.query(
        "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)",
        [userId, item.product_id, item.quantity]
      );
    }
  }
};

// Hiển thị giỏ hàng
exports.getCart = async (req, res) => {
  let cart = [];
  let total = 0;

  try {
    if (req.session.user) {
      // Khách đã đăng nhập: Lấy giỏ hàng từ bảng cart
      const conn = await getConnection();
      const [cartItems] = await conn.query(
        `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
         FROM cart c 
         JOIN products p ON c.product_id = p.product_id 
         WHERE c.user_id = ?`,
        [req.session.user.user_id]
      );
      await conn.end();

      cart = cartItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        image_url: item.image_url,
        price: item.price,
        quantity: item.quantity,
        thanhtien: item.price * item.quantity
      }));

      total = cart.reduce((sum, item) => sum + item.thanhtien, 0);
    } else {
      // Khách vãng lai: Lấy giỏ hàng từ session
      cart = req.session.cart || [];
      cart.forEach(item => {
        item.thanhtien = (item.price || 0) * item.quantity;
        total += item.thanhtien;
      });
    }

    res.render("pages/cart", {
      title: "Giỏ hàng",
      cart,
      total,
      user: req.session.user || null
    });
  } catch (err) {
    console.error("Lỗi getCart:", err);
    res.status(500).render("pages/cart", {
      title: "Giỏ hàng",
      cart: [],
      total: 0,
      user: req.session.user || null,
      error: "Lỗi khi tải giỏ hàng"
    });
  }
};

exports.addToCart = async (req, res) => {
  const productId = req.params.id;
  try {
    const conn = await getConnection();
    const [rows] = await conn.query(
      "SELECT product_id, product_name, image_url, price FROM products WHERE product_id = ?",
      [productId]
    );

    if (rows.length === 0) {
      await conn.end();
      return res.status(404).json({ success: false, message: "Không tìm thấy sản phẩm" });
    }

    const product = rows[0];
    let favorites = [];

    if (req.session.user) {
      // Khách đã đăng nhập: Thêm vào bảng cart
      const [existingItem] = await conn.query(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
        [req.session.user.user_id, product.product_id]
      );

      if (existingItem.length > 0) {
        await conn.query(
          "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?",
          [req.session.user.user_id, product.product_id]
        );
      } else {
        await conn.query(
          "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)",
          [req.session.user.user_id, product.product_id, 1]
        );
      }

      // Lấy giỏ hàng mới từ DB
      const [cartItems] = await conn.query(
        `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
         FROM cart c 
         JOIN products p ON c.product_id = p.product_id 
         WHERE c.user_id = ?`,
        [req.session.user.user_id]
      );

      // Lấy danh sách yêu thích từ bảng favourite
      const [favoriteItems] = await conn.query(
        `SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
         FROM favourite f
         JOIN products p ON f.product_id = p.product_id
         WHERE f.user_id = ?
         ORDER BY f.added_date DESC`,
        [req.session.user.user_id]
      );

      await conn.end();

      const cart = cartItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        image_url: item.image_url,
        price: item.price,
        quantity: item.quantity,
        thanhtien: item.price * item.quantity
      }));

      favorites = favoriteItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        price: item.price,
        image_url: item.image_url,
        added_date: item.added_date
      }));

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.thanhtien, 0);

      return res.json({ success: true, cart, cartCount, total, favorites });
    } else {
      // Khách vãng lai: Thêm vào session
      if (!req.session.cart) req.session.cart = [];
      let cart = req.session.cart;

      const existing = cart.find(p => p.product_id === product.product_id);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push({ ...product, quantity: 1 });
      }

      // Lấy danh sách yêu thích từ session
      if (!req.session.favorites) req.session.favorites = [];
      const sessionFavorites = req.session.favorites;

      // Lấy thông tin sản phẩm trong danh sách yêu thích
      const [favoriteProducts] = await conn.query(
        `SELECT product_id, product_name, price, image_url
         FROM products
         WHERE product_id IN (?)`,
        [sessionFavorites.length > 0 ? sessionFavorites : [0]]
      );

      await conn.end();

      favorites = favoriteProducts.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        price: item.price,
        image_url: item.image_url,
        added_date: new Date()
      }));
      

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

      req.session.cart = cart;
      return res.json({ success: true, cart, cartCount, total, favorites });
    }
  } catch (err) {
    console.error("Lỗi addToCart:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};

// Cộng sản phẩm
exports.plusItem = async (req, res) => {
  const { id } = req.params;
  try {
    if (req.session.user) {
      // Khách đã đăng nhập: Cập nhật bảng cart
      const conn = await getConnection();
      const [existingItem] = await conn.query(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
        [req.session.user.user_id, id]
      );

      if (existingItem.length === 0) {
        await conn.end();
        return res.status(404).json({ success: false, message: "Sản phẩm không có trong giỏ hàng" });
      }

      await conn.query(
        "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?",
        [req.session.user.user_id, id]
      );

      const [cartItems] = await conn.query(
        `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
         FROM cart c 
         JOIN products p ON c.product_id = p.product_id 
         WHERE c.user_id = ?`,
        [req.session.user.user_id]
      );
      await conn.end();

      const cart = cartItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        image_url: item.image_url,
        price: item.price,
        quantity: item.quantity,
        thanhtien: item.price * item.quantity
      }));

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.thanhtien, 0);

      return res.json({ success: true, cart, cartCount, total });
    } else {
      // Khách vãng lai: Cập nhật session
      let cart = req.session.cart || [];
      let item = cart.find(i => i.product_id == id);
      if (item) {
        item.quantity += 1;
      } else {
        return res.status(404).json({ success: false, message: "Sản phẩm không có trong giỏ hàng" });
      }

      req.session.cart = cart;
      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

      return res.json({ success: true, cart, cartCount, total });
    }
  } catch (err) {
    console.error("Lỗi plusItem:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};

// Trừ sản phẩm
exports.minusItem = async (req, res) => {
  const { id } = req.params;
  try {
    if (req.session.user) {
      // Khách đã đăng nhập: Cập nhật bảng cart
      const conn = await getConnection();
      const [existingItem] = await conn.query(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
        [req.session.user.user_id, id]
      );

      if (existingItem.length === 0) {
        await conn.end();
        return res.status(404).json({ success: false, message: "Sản phẩm không có trong giỏ hàng" });
      }

      if (existingItem[0].quantity > 1) {
        await conn.query(
          "UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?",
          [req.session.user.user_id, id]
        );
      } else {
        await conn.query(
          "DELETE FROM cart WHERE user_id = ? AND product_id = ?",
          [req.session.user.user_id, id]
        );
      }

      const [cartItems] = await conn.query(
        `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
         FROM cart c 
         JOIN products p ON c.product_id = p.product_id 
         WHERE c.user_id = ?`,
        [req.session.user.user_id]
      );
      await conn.end();

      const cart = cartItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        image_url: item.image_url,
        price: item.price,
        quantity: item.quantity,
        thanhtien: item.price * item.quantity
      }));

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.thanhtien, 0);

      return res.json({ success: true, cart, cartCount, total });
    } else {
      // Khách vãng lai: Cập nhật session
      let cart = req.session.cart || [];
      let item = cart.find(i => i.product_id == id);
      if (item && item.quantity > 1) {
        item.quantity -= 1;
      } else if (item) {
        cart = cart.filter(i => i.product_id != id);
      } else {
        return res.status(404).json({ success: false, message: "Sản phẩm không có trong giỏ hàng" });
      }

      req.session.cart = cart;
      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

      return res.json({ success: true, cart, cartCount, total });
    }
  } catch (err) {
    console.error("Lỗi minusItem:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};

// Xóa 1 sản phẩm
exports.removeItem = async (req, res) => {
  const { id } = req.params;
  try {
    if (req.session.user) {
      // Khách đã đăng nhập: Xóa từ bảng cart
      const conn = await getConnection();
      await conn.query(
        "DELETE FROM cart WHERE user_id = ? AND product_id = ?",
        [req.session.user.user_id, id]
      );

      const [cartItems] = await conn.query(
        `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
         FROM cart c 
         JOIN products p ON c.product_id = p.product_id 
         WHERE c.user_id = ?`,
        [req.session.user.user_id]
      );
      await conn.end();

      const cart = cartItems.map(item => ({
        product_id: item.product_id,
        product_name: item.product_name,
        image_url: item.image_url,
        price: item.price,
        quantity: item.quantity,
        thanhtien: item.price * item.quantity
      }));

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.thanhtien, 0);

      return res.json({ success: true, cart, cartCount, total });
    } else {
      // Khách vãng lai: Xóa từ session
      let cart = req.session.cart || [];
      cart = cart.filter(i => i.product_id != id);
      req.session.cart = cart;

      const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

      return res.json({ success: true, cart, cartCount, total });
    }
  } catch (err) {
    console.error("Lỗi removeItem:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};

// Xóa tất cả
exports.clearCart = async (req, res) => {
  try {
    if (req.session.user) {
      // Khách đã đăng nhập: Xóa từ bảng cart
      const conn = await getConnection();
      await conn.query(
        "DELETE FROM cart WHERE user_id = ?",
        [req.session.user.user_id]
      );
      await conn.end();
    }
    // Xóa session cart cho cả khách vãng lai và đã đăng nhập
    req.session.cart = [];
    const cartCount = 0;
    const total = 0;

    return res.json({ success: true, cart: [], cartCount, total });
  } catch (err) {
    console.error("Lỗi clearCart:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};

// Đồng bộ giỏ hàng khi đăng nhập
exports.syncCartOnLogin = async (req, res) => {
  try {
    if (!req.session.user || !req.session.user.user_id) {
      return res.status(401).json({ success: false, message: "Chưa đăng nhập" });
    }

    const conn = await getConnection();
    await syncSessionCartToDB(req.session.user.user_id, req.session.cart, conn);
    await conn.end();

    // Xóa giỏ hàng session sau khi đồng bộ
    req.session.cart = [];

    // Lấy giỏ hàng mới từ DB
    const [cartItems] = await conn.query(
      `SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_url 
       FROM cart c 
       JOIN products p ON c.product_id = p.product_id 
       WHERE c.user_id = ?`,
      [req.session.user.user_id]
    );

    const cart = cartItems.map(item => ({
      product_id: item.product_id,
      product_name: item.product_name,
      image_url: item.image_url,
      price: item.price,
      quantity: item.quantity,
      thanhtien: item.price * item.quantity
    }));

    const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    const total = cart.reduce((sum, item) => sum + item.thanhtien, 0);

    return res.json({ success: true, cart, cartCount, total });
  } catch (err) {
    console.error("Lỗi syncCartOnLogin:", err);
    return res.status(500).json({ success: false, message: "Lỗi server", error: err.message });
  }
};