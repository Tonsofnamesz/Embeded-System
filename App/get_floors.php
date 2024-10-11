<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "embedandpervasive";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$building_id = $_GET['building_id'];

// Fetch floors and associated toilets
$sql = "SELECT f.id AS floor_id, f.floor_number, t.id AS toilet_id, t.gender_id, g.label, t.usage_count
        FROM floors f
        LEFT JOIN toilets t ON f.id = t.floor_id
        LEFT JOIN gender g ON t.gender_id = g.id
        WHERE f.building_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

$current_floor = -1;
$output = '<div class="floor-list">'; // Wrap everything in a container for vertical stacking

while ($row = $result->fetch_assoc()) {
    // New floor container
    if ($current_floor != $row['floor_id']) {
        if ($current_floor != -1) {
            $output .= '</div>'; // Close previous floor container
        }

        $output .= '<div class="floor-container">'; // Start new floor container
        $output .= '<h3 class="floor-title">Floor ' . $row['floor_number'] . '</h3>'; // Floor heading
        $output .= '<button class="delete-floor-btn" onclick="deleteFloor(' . $row['floor_id'] . ')">Delete Floor</button>'; // Delete button
        $current_floor = $row['floor_id'];
        $output .= '<div class="toilet-row">'; // Start a new row for toilets
    }


    // Initialize gender_image with a default value
    $gender_image = 'assets/images/default_icon.png'; // Fallback image in case of errors

    // Display male/female toilet usage with custom image
    if ($row['label'] == 'male') {
        $gender_image = 'assets/images/male_icon.png';
    } else if ($row['label'] == 'female') {
        $gender_image = 'assets/images/female_icon.png';
    }

    // Add CSS classes for the toilet box (3-column layout)
    $output .= '<div class="floor-container">'; // Start new floor container
        $output .= '<h3 class="floor-title">Floor ' . $row['floor_number'] . '</h3>'; // Floor heading
        $output .= '<button class="delete-floor-btn" onclick="deleteFloor(' . $row['floor_id'] . ')">Delete Floor</button>'; // Delete button
        $current_floor = $row['floor_id'];
        $output .= '<div class="toilet-row">'; // Start a new row for toilets
    }

$output .= '</div>'; // Close last toilet row
$output .= '</div>'; // Close last floor container
$output .= '</div>'; // Close the main container for all floors

echo $output;

$stmt->close();
$conn->close();
?>

