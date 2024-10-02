<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $building_id = $_POST['building_id'];
    $floor_number = $_POST['floor_number'];

    // Insert the new floor
    $sql = "INSERT INTO floors (building_id, floor_number) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $building_id, $floor_number);

    if ($stmt->execute()) {
        // Get building name
        $buildings = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
        $building_name = $buildings[$building_id - 1];
        echo "New floor added: $building_name, Floor $floor_number";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>

