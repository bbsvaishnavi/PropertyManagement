<?php
session_start();

// Include database connection
$servername = "localhost";
$username = "skaushik5";
$password = "skaushik5";
$dbname = "skaushik5";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clean up the session from the database
if (isset($_SESSION['session_id'])) {
    $session_id = $_SESSION['session_id']; // Assume session_id is stored in $_SESSION when user logs in
    $stmt = $conn->prepare("DELETE FROM session WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $stmt->close();
}

// Destroy PHP session
session_unset();
session_destroy();

// Correct Location header to redirect
header("Location: signin.php");
exit;

$conn->close();
?>
