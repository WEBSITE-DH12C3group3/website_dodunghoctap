import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1',
  forceTLS: true,            // dùng https/wss (khớp .env của bạn)
  // Nếu cần auth thủ công:
  // authorizer: (channel) => ({
  //   authorize: (socketId, callback) => {
  //     window.axios.post('/broadcasting/auth', { socket_id: socketId, channel_name: channel.name })
  //       .then(res => callback(null, res.data))
  //       .catch(err => callback(err));
  //   }
  // }),
})
