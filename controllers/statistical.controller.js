const db = require("../config/db");

exports.dashboard = async (req, res) => {
  try {
    const [dayResult] = await db.query(
      "SELECT SUM(total) AS total FROM orders WHERE DATE(order_date) = CURDATE()"
    );
    const totalDay = dayResult[0].total || 0;

    const [weekResult] = await db.query(
      "SELECT SUM(total) AS total FROM orders WHERE WEEK(order_date) = WEEK(CURDATE())"
    );
    const totalWeek = weekResult[0].total || 0;

    const [monthResult] = await db.query(
      "SELECT SUM(total) AS total FROM orders WHERE MONTH(order_date) = MONTH(CURDATE())"
    );
    const totalMonth = monthResult[0].total || 0;

    // dữ liệu cho biểu đồ (ví dụ theo giờ)
    const [hourlyResult] = await db.query(`
      SELECT HOUR(order_date) AS hour, SUM(total) AS revenue
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
  }
};
