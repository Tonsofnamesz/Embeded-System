<?php
include 'db.php';

$building_id = $_GET['building_id'];
$floor_id = $_GET['floor_id'];

$sql = "SELECT t.id, f.floor_number, g.label, t.usage_count
        FROM toilets t
        JOIN floors f ON t.floor_id = f.id
        JOIN gender g ON t.gender_id = g.id
        WHERE f.building_id = ? AND f.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $building_id, $floor_id);
$stmt->execute();
$result = $stmt->get_result();

$toilets = [];
while ($row = $result->fetch_assoc()) {
    $toilets[] = $row;
}

echo json_encode($toilets);

$stmt->close();
$conn->close();
?>
