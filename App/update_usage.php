<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $toilet_id = $_POST['toilet_id'];

    // Update usage count
    $sql = "UPDATE toilets SET usage_count = usage_count + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $toilet_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
