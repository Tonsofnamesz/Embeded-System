<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['floor_id'])) {
        $floor_id = $_POST['floor_id'];

        // Start transaction
        $conn->begin_transaction();

        try {
            // Delete all toilets associated with the floor
            $stmt = $conn->prepare("DELETE FROM toilets WHERE floor_id = ?");
            $stmt->bind_param("i", $floor_id);
            $stmt->execute();
            $stmt->close();

            // Delete the floor
            $stmt = $conn->prepare("DELETE FROM floors WHERE id = ?");
            $stmt->bind_param("i", $floor_id);
            $stmt->execute();
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            echo "Floor and associated toilets deleted successfully.";
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Missing floor ID.";
    }
} else {
    echo "No POST data received.";
}

$conn->close();
?>