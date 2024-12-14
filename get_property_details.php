<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['property_id'];

if (!$property_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid property ID']);
    exit;
}

$servername = "localhost"; 
$username = "root";  
$password = "123456";      
$dbname = "bbharatula1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$sql = "SELECT p.*, 
        GROUP_CONCAT(a.name SEPARATOR ', ') AS amenities,
        ROUND(p.base_value * 1.07, 2) AS value_after_tax,
        (SELECT rating FROM property_ratings WHERE property_id = p.id AND user_id = ?) AS user_rating
        FROM property p 
        LEFT JOIN amenity a ON p.id = a.property_id
        WHERE p.id = ?
        GROUP BY p.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $property = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'property' => $property]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Property not found']);
}

$conn->close();
?>
