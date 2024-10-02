<?php
include 'db.php';

$building_id = $_GET['building_id'];

$sql = "SELECT f.id as floor_id, f.floor_number, t.gender_id, g.label, t.usage_count
        FROM floors f
        LEFT JOIN toilets t ON f.id = t.floor_id
        LEFT JOIN gender g ON t.gender_id = g.id
        WHERE f.building_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

$current_floor = -1;
$output = '';

while ($row = $result->fetch_assoc()) {
    // New floor container
    if ($current_floor != $row['floor_id']) {
        if ($current_floor != -1) {
            $output .= '</div>'; // Close previous floor container
        }
        $output .= '<div class="floor-container">';
        $output .= '<h3>Floor ' . $row['floor_number'] . '</h3>';
        $current_floor = $row['floor_id'];
    }

    // Male/Female icons and counters in the same container
    $gender_icon = $row['label'] == 'Male' ? '♂️' : '♀️';
    $output .= '<div class="toilet-box">';
    $output .= '<span class="icon">' . $gender_icon . '</span>';
    $output .= '<span class="usage-count">' . $row['usage_count'] . '</span>';
    $output .= '<button class="reset-btn" onclick="resetUsage(' . $row['gender_id'] . ')">Reset</button>';
    $output .= '</div>';
}
$output .= '</div>'; // Close last floor container

echo $output;

$stmt->close();
$conn->close();
?>

