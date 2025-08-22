function setUserSession(req, user) {
  req.session.user = {
    user_id: user.user_id,
    full_name: user.full_name,
    email: user.email,
    role: user.role_id,
    phone: user.phone,
    address: user.address
  };
}

module.exports = { setUserSession };
