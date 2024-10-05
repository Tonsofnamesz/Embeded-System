<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $floor_id = $_POST['floor_id'];

    // Delete the floor (and cascade delete the associated toilets)
    $sql = "DELETE FROM floors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $floor_id);

    if ($stmt->execute()) {
        echo "Floor successfully removed.";
    } else {
        echo "Error removing floor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
