const getConnection = require("../config/db");

// Hàm hiện thị dashboard, nếu cần
exports.dashboard = async (req, res) => {
  let connection;
  try {
    connection = await getConnection();

    const [dayResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(order_date) = CURDATE()"
    );
    const totalDay = dayResult[0].total || 0;

    const [weekResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE WEEK(order_date) = WEEK(CURDATE())"
    );
    const totalWeek = weekResult.total || 0;

    const [monthResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE MONTH(order_date) = MONTH(CURDATE())"
    );
    const totalMonth = monthResult.total || 0;

    const [hourlyResult] = await connection.query(`
      SELECT HOUR(order_date) AS hour, SUM(total_amount) AS revenue
      FROM orders
      WHERE DATE(order_date) = CURDATE()
      GROUP BY HOUR(order_date)
      ORDER BY hour ASC
    `);

    let hours = Array.from({ length: 24 }, (_, i) => i);
    let revenues = Array(24).fill(0);
    hourlyResult.forEach(row => {
      revenues[row.hour] = row.revenue;
    });

    res.render("admin/statistical", {
      title: "Thống kê doanh thu",
      totalDay,
      totalWeek,
      totalMonth,
      hours,
      revenues
    });
  } catch (err) {
    console.error(err);
    res.status(500).send("Lỗi lấy dữ liệu thống kê");
  } finally {
    if (connection) await connection.end();
  }
};

// Hàm lấy dữ liệu thống kê trả về cho router /admin/static gọi
exports.getStatistics = async () => {
  let connection;
  try {
    connection = await getConnection();

    const [dayResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(order_date) = CURDATE()"
    );
    const totalDay = dayResult[0].total || 0;

    const [weekResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE WEEK(order_date) = WEEK(CURDATE())"
    );
    const totalWeek = weekResult.total || 0;

    const [monthResult] = await connection.query(
      "SELECT SUM(total_amount) AS total FROM orders WHERE MONTH(order_date) = MONTH(CURDATE())"
    );
    const totalMonth = monthResult.total || 0;

    const [hourlyResult] = await connection.query(`
      SELECT HOUR(order_date) AS hour, SUM(total_amount) AS revenue
      FROM orders
      WHERE DATE(order_date) = CURDATE()
      GROUP BY HOUR(order_date)
      ORDER BY hour ASC
    `);

    let hours = Array.from({ length: 24 }, (_, i) => i);
    let revenues = Array(24).fill(0);
    hourlyResult.forEach(row => {
      revenues[row.hour] = row.revenue;
    });

    return {
      totalDay,
      totalWeek,
      totalMonth,
      hours,
      revenues,
      title: "Thống kê doanh thu"
    };
  } finally {
    if (connection) await connection.end();
  }
};
