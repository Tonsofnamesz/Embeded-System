<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $toilet_id = $_POST['toilet_id'];

    // Reset the usage count for the specified toilet
    $sql = "UPDATE toilets SET usage_count = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $toilet_id);

    if ($stmt->execute()) {
        echo "Counter reset successfully.";
    } else {
        echo "Error resetting counter: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>

