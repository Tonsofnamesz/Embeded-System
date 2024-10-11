const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const mysql = require('mysql');

// Set up the app and server
const app = express();
const server = http.createServer(app);
const io = socketIO(server);

// MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'embedandpervasive'  // Change this to your database name
});

db.connect((err) => {
    if (err) throw err;
    console.log('MySQL Connected...');
});

// Handle connection to clients
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Function to emit updated data from the database to the client
    const sendUsageData = () => {
        db.query('SELECT * FROM toilets', (err, results) => {
            if (err) throw err;
            socket.emit('usageUpdate', results); // Emit the latest data
        });
    };

    // Send initial data when a client connects
    sendUsageData();

    // Listen for custom events if needed, like manually refreshing data
    socket.on('refreshData', () => {
        sendUsageData();
    });

    // Handle disconnect
    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

// Start the server on port 3000
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
