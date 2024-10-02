<?php
include 'db.php'; // Reusing the db connection

// Fetch buildings (if you want to make buildings dynamic in the future)
$buildings = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toilet Usage Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Topbar with building buttons -->
    <div class="topbar">
        <?php foreach ($buildings as $index => $building): ?>
            <button class="building-btn" data-building="<?= $index + 1 ?>"><?= $building ?></button>
        <?php endforeach; ?>
    </div>

    <!-- Main content section to display floors and toilets -->
    <div id="content">
        <!-- Content dynamically loaded here -->
    </div>

    <script>
        // Function to fetch and display floors and toilets
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

        // Load default building (Gedung A) on page load
        window.onload = function() {
            loadBuilding(1); // Default to building 1 (Gedung A)
        };

        // Attach event listeners to buttons
        document.querySelectorAll('.building-btn').forEach(button => {
            button.addEventListener('click', function() {
                const buildingId = this.getAttribute('data-building');
                loadBuilding(buildingId);
            });
        });

        // Function to reset toilet usage
        function resetUsage(toiletId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'reset_usage.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    loadBuilding(1); // Reload the content after reset
                }
            };
            xhr.send(`toilet_id=${toiletId}`);
        }
    </script>

</body>
</html>
