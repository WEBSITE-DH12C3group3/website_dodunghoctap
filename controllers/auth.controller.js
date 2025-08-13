// Giả lập database
const users = [
    { email: 'admin@gmail.com', password: '123456', name: 'Admin' },
    { email: 'user@gmail.com', password: '654321', name: 'User' }
];

// Hiển thị form login
exports.showLoginForm = (req, res) => {
    res.render('pages/login');
};

exports.login = (req, res) => {
    const { email, password } = req.body;

    if (email !== 'admin@gmail.com' || password !== '123456') {
        return res.render('pages/login', { error: 'Sai email hoặc mật khẩu!' });
    }

    req.session.user = { email };
    res.redirect('/');
};
// Hiển thị form đăng ký
exports.showRegisterForm = (req, res) => {  
    res.render('pages/registers');
}

