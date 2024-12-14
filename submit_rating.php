<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
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

// Get data from AJAX
$input = json_decode(file_get_contents('php://input'), true);
$property_id = $input['property_id'] ?? null;
$rating = $input['rating'] ?? null;

if (!$property_id || !$rating || $rating < 1 || $rating > 5) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the user has already rated this property
$checkRatingQuery = "SELECT id FROM property_ratings WHERE property_id = ? AND user_id = ?";
$checkRatingStmt = $conn->prepare($checkRatingQuery);
$checkRatingStmt->bind_param("ii", $property_id, $user_id);
$checkRatingStmt->execute();
$checkRatingStmt->store_result();

if ($checkRatingStmt->num_rows > 0) {
    // Update rating if it exists
    $updateRatingQuery = "UPDATE property_ratings SET rating = ? WHERE property_id = ? AND user_id = ?";
    $updateRatingStmt = $conn->prepare($updateRatingQuery);
    $updateRatingStmt->bind_param("iii", $rating, $property_id, $user_id);
    if ($updateRatingStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating updated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update rating.']);
    }
} else {
    // Insert new rating if it doesn't exist
    $insertRatingQuery = "INSERT INTO property_ratings (property_id, user_id, rating) VALUES (?, ?, ?)";
    $insertRatingStmt = $conn->prepare($insertRatingQuery);
    $insertRatingStmt->bind_param("iii", $property_id, $user_id, $rating);
    if ($insertRatingStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating submitted.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit rating.']);
    }
}

$checkRatingStmt->close();
$conn->close();
?>
