function approveLandlord(landlordId) {
    if (confirm('Are you sure you want to approve this landlord?')) {
        fetch('/admin/approve-landlord.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ landlordId: landlordId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Landlord approved successfully!');
                location.reload();
            } else {
                alert('Error approving landlord: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the landlord.');
        });
    }
}

function rejectLandlord(landlordId) {
    if (confirm('Are you sure you want to reject this landlord?')) {
        fetch('/admin/reject-landlord.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ landlordId: landlordId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Landlord rejected successfully!');
                location.reload();
            } else {
                alert('Error rejecting landlord: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting the landlord.');
        });
    }
}

function viewRoomDetails(roomId) {
    // Open a modal or navigate to room details page
    window.location.href = `/admin/room-details.php?id=${roomId}`;
} 