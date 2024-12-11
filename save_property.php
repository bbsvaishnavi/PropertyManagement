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

// Input Validation
$location = $_POST['location'] ?? '';
$floor_plan = $_POST['floor_plan'] ?? '';
$num_bedrooms = $_POST['num_bedrooms'] ?? 0;
$base_value = $_POST['base_value'] ?? 0.0;
$seller_id = $_SESSION['user_id'] ?? 0;

if (empty($location) || empty($floor_plan) || $num_bedrooms <= 0 || $base_value <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
    exit;
}

// Image Handling
$image_name = $_FILES['image']['name'] ?? '';
$image_tmp = $_FILES['image']['tmp_name'] ?? '';
$upload_dir = "images/uploads/";
$image_path = $upload_dir . basename($image_name);

if (move_uploaded_file($image_tmp, $image_path)) {
    // Store Property Data in DB
    $insert_property = "INSERT INTO property (location, floor_plan, num_bedrooms, base_value, image) 
                        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_property);
    $stmt->bind_param("ssids", $location, $floor_plan, $num_bedrooms, $base_value, $image_path);

    if ($stmt->execute()) {
        $property_id = $stmt->insert_id;

        // Map Property to User
        $map_user_property = "INSERT INTO user_property (user_id, property_id) VALUES (?, ?)";
        $map_stmt = $conn->prepare($map_user_property);
        $map_stmt->bind_param("ii", $seller_id, $property_id);
        $map_stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Property added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error saving property']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading image']);
}

$conn->close();
?>
