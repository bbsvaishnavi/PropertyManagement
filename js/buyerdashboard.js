function showPropertyDetails(propertyId) {
    fetch(`get_property_details.php?property_id=${propertyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const detailsModal = document.getElementById('propertyDetailsModal');
                const content = document.getElementById('propertyDetailsContent');
                const property = data.property;

                content.innerHTML = `
                    <p><strong>Location:</strong>${property.location}</p>
                    <p><strong>Floor Plan:</strong> ${property.floor_plan}</p>
                    <p><strong>Bedrooms:</strong> ${property.num_bedrooms}</p>
                    <p><strong>Base Value:</strong> $${property.base_value}</p>
                    <p><strong>Amenities:</strong> ${property.amenities}</p>
                    <img src="${property.image}" alt="Property Image" style="max-width: 100%; height: auto;">
                    
                                    
                `;

                detailsModal.style.display = 'block';
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error fetching property details:', error));
}

function togglePropertyDetails() {
    document.getElementById('propertyDetailsModal').style.display = 'none';
}
function openRatingModal(propertyId) {
    const ratingModal = document.getElementById('ratingModal');
    const existingRating = document.getElementById(`rating_${propertyId}`).value;

    // Show the modal
    ratingModal.style.display = 'block';
    document.getElementById('propertyToRate').value = propertyId;

    // Reset stars
    document.querySelectorAll('#starRating .star').forEach(star => {
        star.style.color = 'gray';
    });

    // Highlight existing rating stars
    if (existingRating && parseInt(existingRating) > 0) {
        document.querySelectorAll('#starRating .star').forEach(star => {
            if (parseInt(star.getAttribute('data-rating')) <= parseInt(existingRating)) {
                star.style.color = 'gold';
            }
        });

        // Set the hidden input with the current rating
        document.getElementById('ratingValue').value = existingRating;
    }
}

document.querySelectorAll('#starRating .star').forEach(star => {
    star.onclick = function () {
        const rating = this.getAttribute('data-rating');
        document.querySelectorAll('#starRating .star').forEach(s => {
            s.style.color = parseInt(s.getAttribute('data-rating')) <= rating ? 'gold' : 'gray';
        });

        // Update the hidden input with the selected rating
        document.getElementById('ratingValue').value = rating;
    };
});



function submitRating() {
    const propertyId = document.getElementById('propertyToRate').value;
    const rating = document.getElementById('ratingValue').value;

    if (!rating) {
        alert('Please select a rating before submitting.');
        return;
    }

    fetch('submit_rating.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: propertyId, rating: rating }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Rating submitted successfully!');
                toggleRatingModal();
                // Update the rating value on the page if needed
                document.getElementById(`rating_${propertyId}`).value = rating;
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error submitting rating:', error));
}


function toggleRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
}

function deleteFromWishlist(propertyId) {
    if (!confirm('Are you sure you want to remove this property from your wishlist?')) return;

    fetch('delete_wishlist_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ property_id: propertyId }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); // Reload to update the wishlist
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error deleting wishlist item:', error));
}

function addToWishlist(propertyId) {

    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ property_id: propertyId }), 
    })
        .then(response => response.json())
        .then(data => {
            console.log("Response:", data); 
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); 
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error adding to wishlist:', error);
        });
}


