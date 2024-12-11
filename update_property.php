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
$location = $_POST['location'];
$floor_plan = $_POST['floor_plan'];
$num_bedrooms = $_POST['num_bedrooms'];
$base_value = $_POST['base_value'];
$image = $_FILES['image']['name'] ?? null;

if ($image) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = "images/uploads/";
    $image_path = $upload_dir . basename($image);
    move_uploaded_file($image_tmp, $image_path);
}

$sql = "UPDATE property SET location = ?, floor_plan = ?, num_bedrooms = ?, base_value = ?, image = IFNULL(?, image) WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssidsi", $location, $floor_plan, $num_bedrooms, $base_value, $image_path, $property_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Property updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating property']);
}

$conn->close();
?>
