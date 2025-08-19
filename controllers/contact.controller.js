// controllers/contact.controller.js

const getConnection = require("../config/db");

exports.renderContactPage = (req, res) => {
  const success = req.query.success || null;
  res.render("partials/contact", { 
    success, 
    user: req.session.user || null   // gửi thông tin user xuống form
  });
};

exports.submitContact = async (req, res) => {
  const { name, email, phone, message } = req.body;

  try {
    const connection = await getConnection();

    await connection.execute(
      `INSERT INTO contact_submissions (name, email, phone, message, submit_date) 
       VALUES (?, ?, ?, ?, NOW())`,
      [name, email, phone, message]
    );

    await connection.end();

    res.redirect("/partials/contact?success=1");
  } catch (err) {
    console.error("❌ Lỗi khi lưu liên hệ:", err);
    res.redirect("/partials/contact?success=0");
  }
};
