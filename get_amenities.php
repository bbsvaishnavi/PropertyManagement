<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$property_id = $_GET['property_id'];
$servername = "localhost";
$username = "skaushik5";
$password = "skaushik5";
$dbname = "skaushik5";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$sql = "SELECT name FROM amenity WHERE property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $amenities = [];
    while ($row = $result->fetch_assoc()) {
        $amenities[] = ['name' => $row['name']];
    }
    echo json_encode(['status' => 'success', 'amenities' => $amenities]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No amenities found']);
}

$conn->close();
?>
