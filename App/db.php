<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "embedandpervasive";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug: Check if POST data is received
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     // Check if all necessary parameters are present
//     if (isset($_POST['building'], $_POST['floor'], $_POST['gender'], $_POST['counter'])) {
//         $building = $_POST['building'];
//         $floor = $_POST['floor'];
//         $gender = $_POST['gender'];
//         $counter = intval($_POST['counter']); // Ensure counter is treated as an integer

//         // Find the gender ID from the database
//         $stmt = $conn->prepare("SELECT id FROM gender WHERE label = ?");
//         $stmt->bind_param("s", $gender);
//         $stmt->execute();
//         $result = $stmt->get_result();
        
//         if ($result->num_rows > 0) {
//             $row = $result->fetch_assoc();
//             $gender_id = $row['id'];

//             // Update the usage_count in the toilets table for the corresponding building, floor, and gender
//             $stmt = $conn->prepare("
//                 UPDATE toilets 
//                 INNER JOIN floors ON toilets.floor_id = floors.id 
//                 INNER JOIN building ON floors.building_id = building.id 
//                 SET toilets.usage_count = ? 
//                 WHERE building.name = ? AND floors.floor_number = ? AND toilets.gender_id = ?
//             ");
//             $stmt->bind_param("isii", $counter, $building, $floor, $gender_id);

//             if (!$stmt->execute()) {
//                 echo "Error executing query: " . $stmt->error;
//             }

//             if ($stmt->affected_rows > 0) {
//                 echo "Counter updated successfully.";
//             } else {
//                 echo "Error: No matching entry found to update.";
//             }
//         } else {
//             echo "Error: No matching gender found.";
//         }
//         $stmt->close();
//     } else {
//         echo "Error: Missing POST parameters.";
//     }
// } else {
//     echo "No POST data received.";
// }

// $conn->close();
?>
