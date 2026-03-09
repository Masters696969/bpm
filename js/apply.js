lucide.createIcons();

document.getElementById('applyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerText;

    btn.disabled = true;
    btn.innerText = 'Submitting...';

    const formData = new FormData(e.target);

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
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'System Error',
            text: error.message || 'An unexpected error occurred. Please try again later.',
            confirmButtonColor: '#ef4444'
        });
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
    }
});
