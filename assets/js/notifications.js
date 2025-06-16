document.addEventListener('DOMContentLoaded', function() {
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllReadButton = document.getElementById('markAllRead');

    if (notificationButton && notificationDropdown) {
        // Toggle notification dropdown
        notificationButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationButton.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Mark all notifications as read
        if (markAllReadButton) {
            markAllReadButton.addEventListener('click', function() {
                fetch('/uzoca/notifications/mark-all-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove unread styling from all notifications
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('bg-slate-50', 'dark:bg-slate-700');
                        });
                        // Remove notification count badge
                        const badge = document.querySelector('.notification-count');
                        if (badge) {
                            badge.remove();
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
    }
}); 