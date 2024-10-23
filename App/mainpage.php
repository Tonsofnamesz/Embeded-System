<?php
session_start();
include 'db.php';

// Check if user is logged in and get their role
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
$role = $_SESSION['role'];
$username = $_SESSION['username']; 

// Fetch buildings (for the Add Floor functionality)
$buildings = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toilet Usage Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="background.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> <!-- Link to Google Fonts -->
    <script src="/socket.io/socket.io.js"></script>
</head>
<body>
    <!-- sigma -->
    <!-- Topbar with building buttons -->
    <header class="mb-5 tabs">
        <?php foreach ($buildings as $index => $building): ?>
            <input type="radio" id="radio-<?= $index + 1 ?>" name="building" <?= $index === 0 ? 'checked' : '' ?>>
            <label for="radio-<?= $index + 1 ?>" class="tab building-btn" data-building="<?= $index + 1 ?>"><?= $building ?></label>
            <?php endforeach; ?>
            <div class="glider"></div>
            <!-- Circle with + symbol to add another building -->
            <div id="add-building" class="circleAdd" onclick="showAddBuildingForm()" <?= ($role === 'admin') ? '' : 'style="display:none;"' ?>>+</div>
            <div class="srot mb-5">| S.R.O.T : ALPHA BUILD</div>
        </header>
        
    <!-- Topbar with user info, role, and logout button -->
        <header class="topbar">
            <div class="user-info">
                <span>Logged in as: <strong><?= $username ?></strong> (<em><?= ucfirst($role) ?></em>)</span>
            </div>
            <div class="logout-btn">
                <form action="index.php" method="post">
                    <button type="submit" class="button">Logout</button>
                </form>
            </div>
        </header>

    <!-- Form to add a new building (Initially hidden) -->
    <div id="add-building-form" class="hidden">
        <h11> Coming soon!</h1>
    </div>

    <script>
        // Show the add building form when the circle is clicked (admin only)
        function showAddBuildingForm() {
            document.getElementById('add-building-form').style.display = 'block';
        }
    </script>

    <div id="content" class="container mb-4">
        <!-- Your dynamically loaded content will appear here -->
    </div>

    <!-- Admin only: Add Floor button -->
    <?php if ($role === 'admin'): ?>
    <div id="add-floor-section" class="form-section">
        <button id="add-floor-btn" class="button">
            <span style="font-size: 200px;">+</span><br>
            <span style="font-size: 25px;">Add Floor</span>
        </button>

        <!-- Form to add a new floor (Initially hidden) -->
        <div id="add-floor-form" class="mt-4 hidden">
            <h3 class="floor-title">Add a New Floor</h3>
            <form id="floor-form">
                <label for="building_id">Building:</label>
                <select name="building_id" id="building_id" required>
                    <?php foreach ($buildings as $index => $building): ?>
                        <option value="<?= $index + 1 ?>"><?= $building ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="floor_number">Floor Number:</label>
                <input type="number" name="floor_number" id="floor_number" required>

                <button type="submit">Submit</button>
            </form>
        </div>
        <div id="new-floor-info" class="mt-2 text-green-600 font-bold"></div>
    </div>
    <?php endif; ?>

    <script>
        // Load default building (Gedung A) on page load
        window.onload = function() {
            loadBuilding(1); // Default to building 1 (Gedung A)
        };

        function loadBuilding(buildingId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_floors.php?building_id=${buildingId}`, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('content').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        // Attach event listeners to building buttons
        document.querySelectorAll('.building-btn').forEach(button => {
            button.addEventListener('click', function() {
                const buildingId = this.getAttribute('data-building');
                loadBuilding(buildingId);
            });
        });

        // Admin only: Show the add floor form when the button is clicked
        <?php if ($role === 'admin'): ?>
        document.getElementById('add-floor-btn').addEventListener('click', function() {
            document.getElementById('add-floor-form').style.display = 'block';
        });

        // Admin only: Handle form submission for adding a floor
        document.getElementById('floor-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const buildingId = document.getElementById('building_id').value;
            const floorNumber = document.getElementById('floor_number').value;
            const xhr = new XMLHttpRequest();

            xhr.open('POST', 'add_floor.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('new-floor-info').innerHTML = this.responseText;
                    document.getElementById('add-floor-form').style.display = 'none'; // Hide form after submission
                    loadBuilding(buildingId); // Reload the floors for the selected building
                }
            };
            xhr.send(`building_id=${buildingId}&floor_number=${floorNumber}`);
        });

        // Admin only: Function to delete a floor
        function deleteFloor(floorId) {
            if (confirm("Are you sure you want to delete this floor?")) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'remove_floor.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        alert(this.responseText);
                        // Reload the floors for the currently active building
                        const activeBuilding = document.querySelector('.building-btn.active').getAttribute('data-building');
                        loadBuilding(activeBuilding);
                    }
                };
                xhr.send(`floor_id=${floorId}`);
            }
        }

        // Admin only: Function to reset the usage counter
        function resetCounter(toilet_id) {
            if (confirm('Are you sure you want to reset the usage count for this toilet?')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "reset_counter.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        location.reload(); // Reload to reflect changes
                    }
                };
                xhr.send("toilet_id=" + toilet_id);
            }
        }
        <?php endif; ?>
    </script>

    <script src="/socket.io/socket.io.js"></script>
    <script>
        // Connect to the Socket.IO server
        const socket = io('http://localhost:3000'); // Adjust the URL to your Node.js server
        // Listen for real-time updates from the server
        socket.on('usageUpdate', function(data) {
            console.log('Real-time data received:', data); // Log data for debugging
            data.forEach(function(toilet) {
                // Update the usage count for the specific toilet in the DOM
                const usageElement = document.querySelector(`.toilet-box [data-toilet-id="${toilet.id}"] .usage-count`);
                if (usageElement) {
                    usageElement.textContent = toilet.usage_count; // Update the usage count dynamically
                }
            });
        });
    </script>
</body>
</html>

