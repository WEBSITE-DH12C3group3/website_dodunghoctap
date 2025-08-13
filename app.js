const express = require("express");
const path = require("path");
const session = require("express-session");
const db = require("./config/db");

const app = express();
const PORT = 3000;

app.use(express.urlencoded({ extended: true }));
app.use(
  session({
    secret: "secret-key",
    resave: false,
    saveUninitialized: true,
  })
);

app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));
app.use(express.static(path.join(__dirname, "public")));

// Trang chủ
app.get("/", (req, res) => {
  res.render("pages/home", { user: req.session.user || null, title: "Trang chủ" });
});


// Trang đăng ký
// app.get("/registers", (req, res) => {
//   res.render("pages/registers", { title: "Đăng ký" });
// });
// app.post("/registers", (req, res) => {
//   const { username, password } = req.body;
//   req.session.user = { username };
//   res.redirect("/");
// });
app.use("/", require("./routes/auth.routes"));

// Trang đăng nhập
app.use("/", require("./routes/auth.routes"));


// Đăng xuất
app.get("/logout", (req, res) => {
  req.session.destroy();
  res.redirect("/login");
});

app.listen(PORT, () => {
  console.log(`Server chạy tại http://localhost:${PORT}`);
});
