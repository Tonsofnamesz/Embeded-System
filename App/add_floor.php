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
        $floor_id = $stmt->insert_id;

        // Variables for gender IDs
        $male_gender_id = 1;
        $female_gender_id = 2;

        // Insert male and female toilets for the new floor
        $sql_toilets = "INSERT INTO toilets (floor_id, gender_id, usage_count) VALUES (?, ?, 0), (?, ?, 0)";
        $stmt_toilets = $conn->prepare($sql_toilets);
        $stmt_toilets->bind_param("iiii", $floor_id, $male_gender_id, $floor_id, $female_gender_id);

        if ($stmt_toilets->execute()) {
            // Get building name
            $buildings = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
            $building_name = $buildings[$building_id - 1];

            echo "New floor added: $building_name, Floor $floor_number. Male and Female toilets initialized.";
        } else {
            echo "Error adding toilets: " . $stmt_toilets->error;
        }

        $stmt_toilets->close();
    } else {
        echo "Error adding floor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>


