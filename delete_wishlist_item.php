<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$buyer_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'] ?? null;

if (!$property_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid property ID.']);
    exit;
}

$servername = "localhost";
$username = "skaushik5";
$password = "skaushik5";
$dbname = "skaushik5";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

$sql = "DELETE FROM user_property WHERE user_id = ? AND property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $buyer_id, $property_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Property removed from wishlist.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove property from wishlist.']);
}

$stmt->close();
$conn->close();
?>
