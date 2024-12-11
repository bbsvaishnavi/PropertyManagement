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

$conn->autocommit(false);

try {
    $delete_user_property = "DELETE FROM user_property WHERE property_id = ?";
    $stmt1 = $conn->prepare($delete_user_property);
    $stmt1->bind_param("i", $property_id);
    $stmt1->execute();

    $delete_property = "DELETE FROM property WHERE id = ?";
    $stmt2 = $conn->prepare($delete_property);
    $stmt2->bind_param("i", $property_id);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Property deleted successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error deleting property']);
}

$conn->close();
?>
