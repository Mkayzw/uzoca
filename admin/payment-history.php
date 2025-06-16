<?php
$page_title = "UZOCA | Payment History";
require_once './includes/Header.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../lib/src/BookingPayment.php';

use app\config\Database;
use app\src\BookingPayment;

$database = new Database();
$conn = $database->getConnection();
$bookingPayment = new BookingPayment($conn);

// First, let's check if the bookings table exists and create it if it doesn't
$checkBookingsTable = "SHOW TABLES LIKE 'bookings'";
$bookingsTableExists = $conn->query($checkBookingsTable)->rowCount() > 0;

if (!$bookingsTableExists) {
    // Create the bookings table
    $createBookingsTable = "CREATE TABLE IF NOT EXISTS bookings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        property_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
    )";
    $conn->exec($createBookingsTable);
}

// Then check if the booking_payments table exists and create it if it doesn't
$checkPaymentsTable = "SHOW TABLES LIKE 'booking_payments'";
$paymentsTableExists = $conn->query($checkPaymentsTable)->rowCount() > 0;

if (!$paymentsTableExists) {
    // Create the booking_payments table
    $createPaymentsTable = "CREATE TABLE IF NOT EXISTS booking_payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT NOT NULL,
        reference_number VARCHAR(50) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        qr_code VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
    )";
    $conn->exec($createPaymentsTable);
}

// Now proceed with the query to fetch payment data
$query = "SELECT bp.*, b.property_id, p.title as property_title, u.name as tenant_name, u.email as tenant_email
          FROM booking_payments bp
          JOIN bookings b ON bp.booking_id = b.id
          JOIN properties p ON b.property_id = p.id
          JOIN users u ON b.user_id = u.id
          ORDER BY bp.created_at DESC";
$result = $conn->query($query);
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-4rem)]">
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm h-full">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Payment History</h3>
            </div>
            <div class="p-4 h-[calc(100%-4rem)] overflow-y-auto">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Reference</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Property</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Tenant</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Amount</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Method</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Status</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Date</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->rowCount() > 0): ?>
                                <?php while ($payment = $result->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <td class="py-4 text-slate-900 dark:text-slate-100"><?php echo $payment['reference_number']; ?></td>
                                        <td class="py-4">
                                            <a href="property-details.php?id=<?php echo $payment['property_id']; ?>" class="text-sky-500 hover:text-sky-600 dark:text-sky-400 dark:hover:text-sky-300">
                                                <?php echo $payment['property_title']; ?>
                                            </a>
                                        </td>
                                        <td class="py-4">
                                            <div>
                                                <span class="font-medium text-slate-900 dark:text-slate-100"><?php echo $payment['tenant_name']; ?></span>
                                                <br>
                                                <span class="text-sm text-slate-500 dark:text-slate-400"><?php echo $payment['tenant_email']; ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 text-slate-900 dark:text-slate-100">$<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td class="py-4 text-slate-900 dark:text-slate-100"><?php echo ucfirst($payment['payment_method']); ?></td>
                                        <td class="py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php 
                                                echo $payment['status'] === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                    ($payment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                    'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200'); 
                                            ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-slate-900 dark:text-slate-100"><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                                        <td class="py-4">
                                            <div class="flex gap-2">
                                                <button type="button" 
                                                        class="p-2 text-sky-500 hover:bg-sky-50 dark:hover:bg-slate-800 rounded-lg" 
                                                        onclick="viewPayment(<?php echo $payment['id']; ?>)">
                                                    <i class="fr fi-rr-eye"></i>
                                                </button>
                                                <?php if ($payment['status'] === 'pending'): ?>
                                                    <button type="button" 
                                                            class="p-2 text-green-500 hover:bg-green-50 dark:hover:bg-slate-800 rounded-lg" 
                                                            onclick="markAsCompleted(<?php echo $payment['id']; ?>)">
                                                        <i class="fr fi-rr-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-slate-500 dark:text-slate-400">No payment records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-lg">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Payment Details</h5>
                <button type="button" class="close text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="p-4" id="paymentDetails">
                <!-- Payment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
function viewPayment(paymentId) {
    $.get('../lib/src/ajax/get_payment.php', { id: paymentId }, function(response) {
        if (response.success) {
            const payment = response.data;
            let html = `
                <div class="payment-details">
                    <p><strong>Reference Number:</strong> ${payment.reference_number}</p>
                    <p><strong>Amount:</strong> $${payment.amount}</p>
                    <p><strong>Payment Method:</strong> ${payment.payment_method}</p>
                    <p><strong>Status:</strong> ${payment.status}</p>
                    <p><strong>Date:</strong> ${payment.created_at}</p>
                    <p><strong>Property:</strong> ${payment.property_title}</p>
                    <p><strong>Tenant:</strong> ${payment.tenant_name}</p>
                    <p><strong>Tenant Email:</strong> ${payment.tenant_email}</p>
                    ${payment.qr_code ? `
                        <div class="mt-3">
                            <h6>Payment QR Code</h6>
                            <img src="${payment.qr_code}" class="img-fluid">
                        </div>
                    ` : ''}
                </div>
            `;
            $('#paymentDetails').html(html);
            $('#viewPaymentModal').modal('show');
        } else {
            alert('Error loading payment details');
        }
    });
}

function markAsCompleted(paymentId) {
    if (confirm('Are you sure you want to mark this payment as completed?')) {
        $.post('../lib/src/ajax/update_payment_status.php', { 
            id: paymentId, 
            status: 'completed' 
        }, function(response) {
            if (response.success) {
                alert('Payment marked as completed');
                location.reload();
            } else {
                alert(response.message || 'Error updating payment status');
            }
        });
    }
}
</script>

<?php require_once '../includes/Footer.php'; ?>