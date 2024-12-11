<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$servername = "localhost"; 
$username = "skaushik5";  
$password = "skaushik5";      
$dbname = "skaushik5";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$property_id = $_POST['property_id'];
$amenity_name = trim($_POST['amenity_name']);

if (empty($property_id) || empty($amenity_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
    exit;
}

// Insert Amenity into DB
$insert_amenity = "INSERT INTO amenity (name, property_id) VALUES (?, ?)";
$stmt = $conn->prepare($insert_amenity);
$stmt->bind_param("si", $amenity_name, $property_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Amenity added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error adding amenity']);
}

$conn->close();
?>
