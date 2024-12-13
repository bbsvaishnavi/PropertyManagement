<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$servername = "localhost"; 
$username = "root";  
$password = "123456";      
$dbname = "bbharatula1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Decode the JSON payload from the request
$input = json_decode(file_get_contents('php://input'), true);
$property_id = $input['property_id'] ?? null;
error_log("Received Property ID: " . json_encode($property_id)); // Debugging
error_log("Received Input: " . json_encode($input));
// Validate the input
if (!$property_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid property ID.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the property is already in the wishlist
$sqlCheck = "SELECT id FROM user_property WHERE user_id = ? AND property_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $user_id, $property_id);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Property is already in your wishlist.']);
    exit;
}

// Add the property to the wishlist
$sqlInsert = "INSERT INTO user_property (user_id, property_id) VALUES (?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("ii", $user_id, $property_id);

if ($stmtInsert->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Property added to your wishlist.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add property to wishlist.']);
}

$stmtCheck->close();
$stmtInsert->close();
$conn->close();
?>
