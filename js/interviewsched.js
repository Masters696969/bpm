document.addEventListener("DOMContentLoaded", () => {
    const scheduleForm = document.getElementById("scheduleForm");
    const interviewMode = document.getElementById("interviewMode");
    const locationLabel = document.getElementById("locationLabel");
    const locationIcon = document.getElementById("locationIcon");
    const locationInput = document.querySelector('input[name="location_link"]');

    // Handle Interview Mode Change
    if (interviewMode) {
        interviewMode.addEventListener("change", () => {
            if (interviewMode.value === "Online") {
                locationLabel.innerText = "Meeting Link (Zoom/Google Meet/etc.)";
                locationIcon.setAttribute("data-lucide", "link");
                locationInput.placeholder = "https://meet.google.com/xxx-xxxx-xxx";
            } else {
                locationLabel.innerText = "Room Location / Office Address";
                locationIcon.setAttribute("data-lucide", "map-pin");
                locationInput.placeholder = "e.g. Conference Room A, 2nd Floor";
            }
            if (window.lucide) window.lucide.createIcons();
        });
    }


    // Handle Form Submission
    if (scheduleForm) {
        scheduleForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(scheduleForm);

            Swal.fire({
                title: 'Sending Invite...',
                text: 'Please wait while we notify the candidate.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch("backend/interview_action.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Interview Scheduled!',
                        text: result.message || 'The candidate has been notified via email.',
                        confirmButtonText: 'Done'
                    }).then(() => {
                        window.location.href = 'applicationmgt.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Wait!',
                        text: result.error || 'Something went wrong. Please try again.'
                    });
                }
            } catch (error) {
                console.error("Submission error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Unable to process the request at this time.'
                });
            }
        });
    }
});
