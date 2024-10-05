<?php
include 'db.php';

// Fetch buildings (for the Add Floor functionality)
$buildings = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toilet Usage Tracker</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the new styles.css -->
</head>
<body>

    <!-- Topbar with building buttons -->
    <div class="mb-4">
        <?php foreach ($buildings as $index => $building): ?>
            <button class="button building-btn" data-building="<?= $index + 1 ?>"><?= $building ?></button>
        <?php endforeach; ?>
    </div>

    <!-- Main content section to display floors and toilets -->
    <div id="content" class="container mb-4">
        <!-- Content dynamically loaded here -->
    </div>

    <!-- Add Floor button -->
    <div id="add-floor-section" class="form-section">
        <button id="add-floor-btn" class="button">Add Floor</button>

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

        <!-- Display the newly added floor -->
        <div id="new-floor-info" class="mt-2 text-green-600 font-bold"></div>
    </div>

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

        // Show the add floor form when the button is clicked
        document.getElementById('add-floor-btn').addEventListener('click', function() {
            document.getElementById('add-floor-form').style.display = 'block';
        });

        // Handle form submission for adding a floor
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

        // Function to delete a floor
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
    </script>

</body>
</html>



