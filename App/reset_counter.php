<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if toilet_id is received
    if (isset($_POST['toilet_id'])) {
        $toilet_id = intval($_POST['toilet_id']);

        // Prepare the SQL statement to reset the counter
        $stmt = $conn->prepare("UPDATE toilets SET usage_count = 0 WHERE id = ?");
        $stmt->bind_param("i", $toilet_id);

        if ($stmt->execute()) {
            echo "Counter reset successfully.";
        } else {
            echo "Error resetting counter: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Toilet ID not received.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
