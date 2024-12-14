<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: signin.php");
    exit;
}

$servername = "localhost";
$username = "skaushik5";
$password = "skaushik5";
$dbname = "skaushik5";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch properties for search
$searchTerm = $_GET['search'] ?? '';
$userId = $_SESSION['user_id']; 

$sql = "SELECT p.*, 
        GROUP_CONCAT(a.name SEPARATOR ', ') AS amenities,
        ROUND(p.base_value * 1.07, 2) AS value_after_tax,
        (SELECT rating FROM property_ratings WHERE property_id = p.id AND user_id = ?) AS user_rating
        FROM property p 
        LEFT JOIN amenity a ON p.id = a.property_id
        WHERE p.location LIKE ? 
        GROUP BY p.id";

$stmt = $conn->prepare($sql);
$searchParam = "%" . $searchTerm . "%";
$stmt->bind_param("is", $userId, $searchParam); 
$stmt->execute();
$result = $stmt->get_result();


// Fetch wishlist properties
$buyer_id = $_SESSION['user_id'];
$wishlistSql = "SELECT p.*, 
        ROUND(p.base_value * 1.07, 2) AS value_after_tax 
        FROM property p 
        JOIN user_property up ON p.id = up.property_id 
        WHERE up.user_id = ?";
$wishlistStmt = $conn->prepare($wishlistSql);
$wishlistStmt->bind_param("i", $buyer_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlistProperties = $wishlistResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/buyerdashboard.css">

</head>
<body>
<div class="navbar">
    <div class="navbar-content">
        <h2>Buyer Dashboard</h2>
        <div class="nav-actions">
            
            <div class="dropdown">
                <button>Wishlist</button>
                <div class="dropdown-content">
                    <?php foreach ($wishlistProperties as $property): ?>
                        <a href="javascript:void(0)" onclick="showPropertyDetails(<?= $property['id'] ?>)" style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?= htmlspecialchars($property['image']) ?>" alt="Property Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <span><?= htmlspecialchars($property['location']) ?></span>
                            <img src="icons/delete-icon.png" id="img" alt="Delete" onclick="deleteFromWishlist(<?= $property['id'] ?>)">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <form method="GET" class="search-form">
                <input type="text" name="search" id="search" placeholder="Search properties by location..." value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit">Search</button>
            </form>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <h2>Available Properties</h2>
    <div id="property-table-container">
        <?php if ($result->num_rows > 0): ?>
            <table class="property-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Location</th>
                        <th>Floor Plan</th>
                        <th>Bedrooms</th>
                        <th>Base Value</th>
                        <th>Value after Tax (7%)</th>
                        <th>Amenities</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($row['image']) ?>" alt="Property Image" class="property-img">
                            </td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><?= htmlspecialchars($row['floor_plan']) ?></td>
                            <td><?= htmlspecialchars($row['num_bedrooms']) ?></td>
                            <td>$<?= htmlspecialchars($row['base_value']) ?></td>
                            <td>$<?= htmlspecialchars($row['value_after_tax']) ?></td>
                            <td><?= htmlspecialchars($row['amenities']) ?></td>
                            <td>
                            <div class="action-buttons">
                            <input type="hidden" id="rating_<?= $row['id'] ?>" value="<?= htmlspecialchars($row['user_rating'] ?? 0) ?>">

                                <button onclick="addToWishlist(<?= $row['id'] ?>)" class="wishlist-btn">Add to Wishlist</button>
                                
                                <button class="rate" onclick="openRatingModal(<?= $row['id'] ?>)">Give Rating</button>
                    </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-properties">
                <p>No properties found. Try a different search term.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Property Details Modal -->
<div id="propertyDetailsModal" class="modal">
    <span class="close" onclick="togglePropertyDetails()">&times;</span>
    <h2>Property Details</h2>
    <div id="propertyDetailsContent">
        <!-- Details will be dynamically loaded here -->
    </div>
</div>
<!-- Rating Modal -->
<div id="ratingModal" class="modal">
    <span class="close" onclick="toggleRatingModal()">&times;</span>
    <h2>Rate Property</h2>
        <input type="hidden" id="propertyToRate">
        <input type="hidden" id="ratingValue">
        <div id="starRating" style="display: flex; gap: 10px;">
            <span class="star" data-rating="1">&#9733;</span>
            <span class="star" data-rating="2">&#9733;</span>
            <span class="star" data-rating="3">&#9733;</span>
            <span class="star" data-rating="4">&#9733;</span>
            <span class="star" data-rating="5">&#9733;</span>
        </div>
        <div class="action-buttons">
        <button class="rate" onclick="submitRating()">Submit Rating</button></div>
    
</div>

<script src="js/buyerdashboard.js"></script>

</body>
</html>