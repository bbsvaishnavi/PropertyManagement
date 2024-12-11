<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
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

// Fetch properties created by the seller
$seller_id = $_SESSION['user_id'];
$sql = "SELECT p.*, 
        GROUP_CONCAT(a.name SEPARATOR ', ') AS amenities,
        ROUND(p.base_value * 1.07, 2) AS value_after_tax
        FROM property p 
        LEFT JOIN amenity a ON p.id = a.property_id 
        JOIN user_property up ON p.id = up.property_id 
        WHERE up.user_id = ? 
        GROUP BY p.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">

    <style>
        /* Modal Styles */
        .modal {
            display: none; /* Ensure it's hidden by default */
            position: fixed; /* Stay fixed on the screen */
            top: 50%; /* Vertically center */
            left: 50%; /* Horizontally center */
            transform: translate(-50%, -50%); /* Center using transform */
            z-index: 2000; /* Make sure it appears on top */
            background-color: rgba(0, 0, 0, 0.5); /* Overlay background */
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            position: relative;
            z-index: 3000;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .modal-content .close:hover {
            color: #000;
        }

        .modal-content h2 {
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .modal-content label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .modal-content input {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            font-family: var(--font-family);
        }

        .modal-content button {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .modal-content button:hover {
            background: #0056b3;
        }

        /* Show Amenities Button */
        .show-amenities-btn {
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .show-amenities-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Amenities Modal Grid Styling */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            padding: 10px;
        }

        .amenity-item {
            display: inline-block;
            background-color: var(--primary-color);
            color: #fff;
            padding: 8px 15px;
            margin: 5px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .amenity-item:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }


        /* Animation for Modal */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

</head>
<body>
<div class="navbar">
    <div class="navbar-content">
        <h2>Seller Dashboard</h2>
        <div class="nav-actions">
            <button class="add-property-btn" onclick="openModal()">+ Add Property</button>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <h2>Your Properties</h2>
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
                            <td>
                            <!-- Show Amenities Button -->
                                <button class="show-amenities-btn" onclick="openShowAmenitiesModal(<?= $row['id'] ?>)">Show Amenities</button>
                            </td>
                            <td class="actions">
                                <div class="action-icons">
                                    <a href="javascript:void(0)" onclick="openEditModal(<?= $row['id'] ?>)">
                                        <img src="icons/edit-icon.png" alt="Edit">
                                    </a>
                                    <a href="javascript:void(0)" onclick="openDeleteModal(<?= $row['id'] ?>)">
                                        <img src="icons/delete-icon.png" alt="Delete">
                                    </a>
                                    <a href="javascript:void(0)" onclick="openAddAmenityModal(<?= $row['id'] ?>)">
                                        <img src="icons/add-icon.png" alt="Add Amenities" title="Add Amenities">
                                    </a>
                                </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-properties">
                <p>No properties found. Click "+ Add Property" to create one.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Property Modal -->
<div id="addPropertyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add Property</h2>
        <form id="addPropertyForm" enctype="multipart/form-data">
            <label>Location</label>
            <input type="text" name="location" required>

            <label>Floor Plan</label>
            <input type="text" name="floor_plan" required>

            <label>Number of Bedrooms</label>
            <input type="number" name="num_bedrooms" required min="1">

            <label>Base Value ($)</label>
            <input type="number" name="base_value" required min="0" step="0.01">

            <label>Property Image</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="button" onclick="addProperty()">Add Property</button>
        </form>
    </div>
</div>

<!-- Edit Property Modal -->
<div id="editPropertyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Property</h2>
        <form id="editPropertyForm" enctype="multipart/form-data">
            <input type="hidden" name="property_id" id="edit_property_id">
            
            <label>Location</label>
            <input type="text" name="location" id="edit_location" required>

            <label>Floor Plan</label>
            <input type="text" name="floor_plan" id="edit_floor_plan" required>

            <label>Number of Bedrooms</label>
            <input type="number" name="num_bedrooms" id="edit_num_bedrooms" required min="1">

            <label>Base Value ($)</label>
            <input type="number" name="base_value" id="edit_base_value" required min="0" step="0.01">

            <label>Property Image</label>
            <input type="file" name="image" id="edit_image" accept="image/*">
            
            <button type="button" onclick="updateProperty()">Update Property</button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete this property?</p>
        <button onclick="confirmDelete()" class="delete-btn">Delete</button>
        <button onclick="closeDeleteModal()" class="cancel-btn">Cancel</button>
        <input type="hidden" id="delete_property_id">
    </div>
</div>

<div id="addAmenityModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeAddAmenityModal()">&times;</span>
        <h2>Add Amenity</h2>
        <form id="addAmenityForm">
            <input type="hidden" id="property_id_for_amenity" name="property_id">
            
            <label for="amenity_name">Amenity Name</label>
            <input type="text" name="amenity_name" id="amenity_name" required>

            <button type="button" onclick="addAmenity()">Add Amenity</button>
        </form>
    </div>
</div>

<!-- Show Amenities Modal -->
<div id="showAmenitiesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeShowAmenitiesModal()">&times;</span>
        <h2>Amenities</h2>
        <div id="amenities-container" class="amenities-grid">
            <!-- Amenities will be dynamically loaded here -->
        </div>
    </div>
</div>


<script src="js/dashboard.js"></script>
</body>
</html>
