const db = require("../config/db");

// Thêm sản phẩm vào danh sách yêu thích
exports.addFavorite = (req, res) => {
    const { userId, productId } = req.body;

    if (!userId || !productId) {
        return res.status(400).json({ error: "Thiếu userId hoặc productId" });
    }

    const sql = "INSERT INTO favourite (user_id, product_id) VALUES (?, ?)";
    db.query(sql, [userId, productId], (err, result) => {
        if (err) {
            // Kiểm tra lỗi trùng (nếu user đã thích sp đó rồi)
            if (err.code === "ER_DUP_ENTRY") {
                return res.status(400).json({ error: "Sản phẩm đã có trong yêu thích" });
            }
            return res.status(500).json({ error: err.message });
        }
        res.status(201).json({ message: "Đã thêm sản phẩm vào yêu thích" });
    });
};

// Xóa sản phẩm khỏi danh sách yêu thích
exports.removeFavorite = (req, res) => {
    const { userId } = req.body;
    const { productId } = req.params;

    if (!userId || !productId) {
        return res.status(400).json({ error: "Thiếu userId hoặc productId" });
    }

    const sql = "DELETE FROM favourite WHERE user_id = ? AND product_id = ?";
    db.query(sql, [userId, productId], (err, result) => {
        if (err) {
            return res.status(500).json({ error: err.message });
        }
        if (result.affectedRows === 0) {
            return res.status(404).json({ message: "Không tìm thấy sản phẩm trong yêu thích" });
        }
        res.status(200).json({ message: "Đã xóa sản phẩm khỏi yêu thích" });
    });
};

// Lấy danh sách sản phẩm yêu thích của 1 user
exports.getFavorites = (req, res) => {
    const { userId } = req.params;

    if (!userId) {
        return res.status(400).json({ error: "Thiếu userId" });
    }

    const sql = `
        SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
        FROM favourite f
        JOIN products p ON f.product_id = p.product_id
        WHERE f.user_id = ?
        ORDER BY f.added_date DESC
    `;
    db.query(sql, [userId], (err, results) => {
        if (err) {
            return res.status(500).json({ error: err.message });
        }
        res.status(200).json(results);
    });
};
// Render trang yêu thích
exports.renderFavorites = (req, res) => {
    const user = req.session.user; // user sau khi login lưu trong session

    if (!user) {
        return res.redirect("/login"); // chưa đăng nhập thì quay lại login
    }

    const sql = `
        SELECT p.product_id, p.product_name, p.price, p.image_url, f.added_date
        FROM favourite f
        JOIN products p ON f.product_id = p.product_id
        WHERE f.user_id = ?
        ORDER BY f.added_date DESC
    `;

    db.query(sql, [user.user_id], (err, results) => {
        if (err) {
            return res.status(500).send("Lỗi khi lấy sản phẩm yêu thích: " + err.message);
        }

        res.render("pages/liked", {
            title: "Trang yêu thích",
            user: user,
            favorites: results
        });
    });
};
