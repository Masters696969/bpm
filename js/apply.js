lucide.createIcons();

document.getElementById('applyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerText;

    btn.disabled = true;
    btn.innerText = 'Submitting...';

    const formData = new FormData(e.target);

    // 1. Show the Loading Spinner
    Swal.fire({
        title: 'Applying...',
        text: 'Uploading your documents and details. Please wait.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 2. Set the 10-second Timeout Check
    const timeoutCheck = setTimeout(() => {
        Swal.fire({
            icon: 'error',
            title: 'Request Timed Out',
            text: 'The server took longer than 10 seconds to respond. Please try again.',
            confirmButtonColor: '#ef4444'
        });
        btn.disabled = false;
        btn.innerText = originalText;
    }, 10000); // 10,000 milliseconds = 10s

    try {
        const response = await fetch('apply_external_action.php', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error('Server response was not JSON:', text);
            throw new Error('Server returned an invalid response. Please check the server logs.');
        }

        clearTimeout(timeoutCheck); // Request finished in time! Clear the timeout.

        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Application Received!',
                text: result.message,
                confirmButtonColor: '#2ca078'
            }).then(() => {
                window.location.href = 'jobposting.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: result.message,
                confirmButtonColor: '#ef4444'
            });
            btn.disabled = false;
            btn.innerText = originalText;
        }
    } catch (error) {
        clearTimeout(timeoutCheck); // Clear timeout even on fetch errors
        console.error('Error:', error);
        
        // Prevent overwriting the timeout error if it was triggered
        if (Swal.getTitle()?.textContent !== 'Request Timed Out') {
            Swal.fire({
                icon: 'error',
                title: 'System Error',
                text: error.message || 'An unexpected error occurred. Please try again later.',
                confirmButtonColor: '#ef4444'
            });
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }
});
