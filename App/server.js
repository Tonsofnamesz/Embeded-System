// Import necessary libraries
const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const mysql = require('mysql');
const bodyParser = require('body-parser');

// Set up the app and server
const app = express();
const server = http.createServer(app);
const io = socketIO(server);

// Use bodyParser middleware to handle POST data
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'embedandpervasive'  // Replace with your database name
});

db.connect((err) => {
    if (err) throw err;
    console.log('MySQL Connected...');
});

// Function to send updated usage count to all connected clients
const sendUsageData = () => {
    db.query('SELECT * FROM toilets', (err, results) => {
        if (err) throw err;
        io.emit('usageUpdate', results); // Emit updated data to all connected clients
    });
};

// When a client connects
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Send initial data when a client connects
    sendUsageData();

    // Handle disconnect
    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

// Endpoint to trigger real-time updates from PHP
app.post('/trigger-update', (req, res) => {
    console.log("Real-time update triggered by PHP.");
    sendUsageData();  // Fetch the latest data from the database and emit to clients
    res.send('Real-time update triggered.');
});

// Start the server on port 3000
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
