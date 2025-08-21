const getConnection = require("../config/db");


exports.showHome = async (req, res) => {
  let connection;
  try {
    connection = await getConnection();

    // Lấy tất cả category
    const [categories] = await connection.query(
      "SELECT * FROM categories ORDER BY category_id"
    );

    let catProducts = {};

    for (let cat of categories) {
      const [products] = await connection.query(
        "SELECT * FROM products WHERE category_id = ?",
        [cat.category_id]
      );
      catProducts[cat.category_id] = {
        category_name: cat.category_name,
        products,
      };
    }

    res.render("pages/home", { catProducts });
  } catch (error) {
    console.error(error);
    res.render("pages/home", { catProducts: {} });
  } finally {
    if (connection) await connection.end();
  }
};
