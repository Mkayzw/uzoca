<?php
// Get current year for copyright
$currentYear = date('Y');
?>

        </div> <!-- Close main content div -->
    </main> <!-- Close main tag -->
</div> <!-- Close grid container -->

<footer class="mt-8 p-4 lg:px-[2.5%] lg:py-2.5 bg-white dark:bg-slate-900 dark:text-slate-100 border-t border-slate-200 dark:border-slate-700">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="text-center md:text-left">
            <p class="text-slate-600 dark:text-slate-400">
                &copy; <?= $currentYear ?> UZOCA. All rights reserved.
            </p>
        </div>
        <div class="flex items-center gap-4">
            <a href="/uzoca/privacy-policy" class="text-slate-600 hover:text-sky-500 dark:text-slate-400 dark:hover:text-sky-400">
                Privacy Policy
            </a>
            <a href="/uzoca/terms" class="text-slate-600 hover:text-sky-500 dark:text-slate-400 dark:hover:text-sky-400">
                Terms of Service
            </a>
            <a href="/uzoca/contact" class="text-slate-600 hover:text-sky-500 dark:text-slate-400 dark:hover:text-sky-400">
                Contact Us
            </a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="/uzoca/assets/js/script.js"></script>
<script>
    // Theme toggle functionality
    const modeToggle = document.querySelector('.mode-toggle');
    if (modeToggle) {
        modeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);
            modeToggle.innerHTML = isDark ? '<i class="fr fi-rr-sun"></i>' : '<i class="fr fi-rr-moon"></i>';
        });

        // Check for saved theme preference
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'true') {
            document.documentElement.classList.add('dark');
            modeToggle.innerHTML = '<i class="fr fi-rr-sun"></i>';
        }
    }

    // Notification dropdown functionality
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllRead = document.getElementById('markAllRead');

    if (notificationButton && notificationDropdown) {
        notificationButton.addEventListener('click', () => {
            notificationDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Mark all notifications as read
        if (markAllRead) {
            markAllRead.addEventListener('click', async () => {
                try {
                    const response = await fetch('/uzoca/includes/mark_notifications_read.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        // Remove notification count badge
                        const badge = document.querySelector('.notification-count');
                        if (badge) badge.remove();
                        
                        // Update notification items
                        const items = document.querySelectorAll('.notification-item');
                        items.forEach(item => item.classList.remove('bg-slate-50', 'dark:bg-slate-700'));
                    }
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                }
            });
        }
    }
</script>
</body>
</html> 