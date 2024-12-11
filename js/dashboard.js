function openModal() {
    const modal = document.getElementById('addPropertyModal');
    modal.style.display = 'flex'; // Change to flex for proper centering
}

function closeModal() {
    const modal = document.getElementById('addPropertyModal');
    modal.style.display = 'none';
}

function addProperty() {
    const form = document.getElementById('addPropertyForm');
    const formData = new FormData(form);

    fetch('save_property.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); // Reload to fetch the updated property list
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}


// Open Edit Modal
function openEditModal(propertyId) {
    fetch(`get_property.php?property_id=${propertyId}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                // Populate the form with the property data
                document.getElementById("edit_property_id").value = data.property.id;
                document.getElementById("edit_location").value = data.property.location;
                document.getElementById("edit_floor_plan").value = data.property.floor_plan;
                document.getElementById("edit_num_bedrooms").value = data.property.num_bedrooms;
                document.getElementById("edit_base_value").value = data.property.base_value;
                const modal = document.getElementById("editPropertyModal");
                modal.style.display = "flex"; // Change to flex for proper centering
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}


// Close Edit Modal
function closeEditModal() {
    document.getElementById("editPropertyModal").style.display = "none";
}

// Update Property
function updateProperty() {
    const formData = new FormData(document.getElementById("editPropertyForm"));

    fetch("update_property.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}

// Open Delete Modal
function openDeleteModal(propertyId) {
    document.getElementById("delete_property_id").value = propertyId;
    const modal = document.getElementById("deleteConfirmationModal");
    modal.style.display = "flex"; // Change to flex for proper centering
}
// Close Delete Modal
function closeDeleteModal() {
    document.getElementById("deleteConfirmationModal").style.display = "none";
}

// Confirm Delete
function confirmDelete() {
    const propertyId = document.getElementById("delete_property_id").value;

    fetch(`delete_property.php?property_id=${propertyId}`, {
        method: "GET",
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}


// Open Add Amenity Modal
function openAddAmenityModal(propertyId) {
    document.getElementById("property_id_for_amenity").value = propertyId;
    document.getElementById("addAmenityModal").style.display = "flex";
}

// Close Add Amenity Modal
function closeAddAmenityModal() {
    document.getElementById("addAmenityModal").style.display = "none";
}

// Add Amenity
function addAmenity() {
    const form = document.getElementById("addAmenityForm");
    const formData = new FormData(form);

    fetch("add_amenity.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                alert(data.message);
                closeAddAmenityModal();
                location.reload(); // Refresh to show the updated list
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}


// Open Show Amenities Modal
function openShowAmenitiesModal(propertyId) {
    // Clear previous amenities
    const amenitiesContainer = document.getElementById("amenities-container");
    amenitiesContainer.innerHTML = "";

    // Fetch amenities for the property
    fetch(`get_amenities.php?property_id=${propertyId}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                // Display amenities in grid format
                data.amenities.forEach((amenity) => {
                    const amenityDiv = document.createElement("div");
                    amenityDiv.classList.add("amenity-item");
                    amenityDiv.textContent = `${amenity.name}`;
                    amenitiesContainer.appendChild(amenityDiv);
                });
                document.getElementById("showAmenitiesModal").style.display = "flex";
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}

// Close Show Amenities Modal
function closeShowAmenitiesModal() {
    document.getElementById("showAmenitiesModal").style.display = "none";
}
