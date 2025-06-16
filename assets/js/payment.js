document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitButton.disabled = true;

            // Send payment request
            fetch('/lib/src/ajax/process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Your payment has been processed and your tenant account has been created.',
                        confirmButtonText: 'View Booking Details'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/booking-details.php?reference=${data.reference_number}`;
                        }
                    });
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            })
            .catch(error => {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: error.message || 'An error occurred while processing your payment. Please try again.'
                });
            })
            .finally(() => {
                // Reset button state
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            });
        });
    }
}); 