const express = require('express');
const { createServer } = require('node:http');
const { Server } = require('socket.io');

const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*", // Adjust this to your specific frontend URL in production for security
    methods: ["GET", "POST"]
  }
});

app.use(express.json());

app.get('/', (req, res) => {
  res.send('<h1>Socket.IO Server is running</h1>');
});

// Endpoint that the CodeIgniter Backend can hit when a queue job finishes
app.post('/emit-notification', (req, res) => {
    const data = req.body;

    // Validasi token sederhana (bisa diperketat di production)
    const token = req.headers['authorization'];
    const expectedToken = process.env.INTERNAL_SOCKET_TOKEN || 'Bearer my-secret-internal-token';
    if (!token || token !== expectedToken) {
        return res.status(401).json({ error: 'Unauthorized' });
    }

    if (!data || !data.user_id) {
        return res.status(400).json({ error: 'user_id is required in the payload' });
    }

    // Emit spesifik ke room milik user_id
    const roomName = `user_${data.user_id}`;
    io.to(roomName).emit('notification', data);

    res.json({ success: true, message: `Notification emitted to ${roomName}` });
});

io.on('connection', (socket) => {
  console.log('a user connected', socket.id);

  // Client bergabung ke room berdasarkan user ID mereka setelah terhubung
  socket.on('join_room', (userId) => {
      const roomName = `user_${userId}`;
      socket.join(roomName);
      console.log(`Socket ${socket.id} joined room ${roomName}`);
  });

  socket.on('disconnect', () => {
    console.log('user disconnected', socket.id);
  });
});

server.listen(3000, () => {
  console.log('Socket.IO server running at http://localhost:3000');
});