<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "embedandpervasive";

$conn = new mysqli($servername, $username, $password, $dbname);

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
$output = '<div class="flex-container">';

while ($row = $result->fetch_assoc()) {
    // New floor container
    if ($current_floor != $row['floor_id']) {
        if ($current_floor != -1) {
            $output .= '</div><br>';
        }

        $output .= '<div class="floor-container">';
        $output .= '<h3 class="floor-title">Floor ' . $row['floor_number'] . '</h3>';
        // Delete Floor button
        $output .= '<button class="delete-floor-btn" onclick="deleteFloor(' . $row['floor_id'] . ')">Delete Floor</button>';
        $current_floor = $row['floor_id'];
        $output .= '<div class="toilet-row">';
    }

    $gender_image = 'assets/images/default_icon.png';

    if ($row['label'] == 'male') {
        $gender_image = 'assets/images/male_icon.png';
    } else if ($row['label'] == 'female') {
        $gender_image = 'assets/images/female_icon.png';
    }

    // CSS classes for the toilet box
    $output .= '<div class="toilet-box">';
    $output .= '<img class="gender-icon" src="' . $gender_image . '" alt="' . $row['label'] . ' Toilet">';
    $output .= '<span class="usage-count">' . $row['usage_count'] . '</span>';
    // Reset Counter button
    $output .= '<button class="reset-counter-btn" onclick="resetCounter(' . $row['toilet_id'] . ')">Reset</button>';
    $output .= '</div>';
}

$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

echo $output;

$stmt->close();
$conn->close();
?>