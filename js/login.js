// Initialize Lucide icons
console.log('Login.js loaded');
lucide.createIcons();

// Theme Toggle
// Theme Toggle
const themeToggle = document.getElementById("themeToggle");
const body = document.body;

// Check for saved theme preference
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") {
    body.classList.add("dark-mode");
}

if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        const isDark = body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDark ? "dark" : "light");
    });
}




// Password Toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentElement.querySelector(".toggle-password");
    const icon = button.querySelector(".eye-icon");

    if (input.type === "password") {
        input.type = "text";
        icon.setAttribute("data-lucide", "eye-off");
    } else {
        input.type = "password";
        icon.setAttribute("data-lucide", "eye");
    }

    window.lucide.createIcons();
}

// OTP Functionality
let otpTimerInterval;
let isSubmitting = false;  // Prevent double submission

// Generate OTP inputs
function generateOtpInputs() {
    const otpInputsContainer = document.getElementById('otpInputs');
    if (!otpInputsContainer) return;

    otpInputsContainer.innerHTML = '';
    for (let i = 0; i < 6; i++) {
        const input = document.createElement('input');
        input.type = 'text';
        input.maxLength = '1';
        input.className = 'otp-input';
        input.dataset.index = i;
        input.inputMode = 'numeric';
        input.autocomplete = 'off';

        // Handle input
        input.addEventListener('input', (e) => {
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');

            if (e.target.value.length === 1) {
                e.target.classList.add('filled');
                const nextInput = e.target.nextElementSibling;
                if (nextInput && nextInput.classList.contains('otp-input')) {
                    nextInput.focus();
                }
            } else if (e.target.value.length === 0) {
                e.target.classList.remove('filled');
            }
        });

        // Handle backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (e.target.value === '') {
                    e.preventDefault();
                    const prevInput = e.target.previousElementSibling;
                    if (prevInput && prevInput.classList.contains('otp-input')) {
                        prevInput.focus();
                        prevInput.value = '';
                        prevInput.classList.remove('filled');
                    }
                } else {
                    e.target.value = '';
                    e.target.classList.remove('filled');
                }
            } else if (e.key === 'ArrowLeft') {
                const prevInput = e.target.previousElementSibling;
                if (prevInput && prevInput.classList.contains('otp-input')) {
                    prevInput.focus();
                }
            } else if (e.key === 'ArrowRight') {
                const nextInput = e.target.nextElementSibling;
                if (nextInput && nextInput.classList.contains('otp-input')) {
                    nextInput.focus();
                }
            }
        });

        // Handle keypress to allow only numbers
        input.addEventListener('keypress', (e) => {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Paste handling
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            if (pasteData) {
                const inputs = document.querySelectorAll('.otp-input');
                let currentIndex = parseInt(input.dataset.index);
                for (let j = 0; j < pasteData.length && currentIndex < 6; j++, currentIndex++) {
                    inputs[currentIndex].value = pasteData[j];
                    inputs[currentIndex].classList.add('filled');
                    if (currentIndex < 5) {
                        inputs[currentIndex + 1].focus();
                    }
                }
            }
        });

        otpInputsContainer.appendChild(input);
    }

    // Focus first input
    setTimeout(() => {
        const firstInput = otpInputsContainer.querySelector('.otp-input');
        if (firstInput) {
            firstInput.focus();
        }
    }, 100);
}

// Show OTP popup
function showOtpPopup() {
    const otpOverlay = document.getElementById('otpOverlay');
    if (!otpOverlay) return;

    isSubmitting = false;  // Reset flag when showing OTP popup
    otpOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    generateOtpInputs();
    startOtpTimer();
}

// Hide OTP popup
function hideOtpPopup() {
    const otpOverlay = document.getElementById('otpOverlay');
    if (!otpOverlay) return;

    isSubmitting = false;  // Reset flag when hiding OTP popup
    otpOverlay.classList.remove('active');
    document.body.style.overflow = '';

    if (otpTimerInterval) {
        clearInterval(otpTimerInterval);
    }
}

// Start OTP timer
function startOtpTimer() {
    const otpTimer = document.getElementById('otpTimer');
    const resendOtp = document.getElementById('resendOtp');
    if (!otpTimer || !resendOtp) return;

    if (otpTimerInterval) {
        clearInterval(otpTimerInterval);
    }

    let timeLeft = 300; // 5 minutes = 300 seconds
    otpTimer.textContent = `(05:00)`;
    resendOtp.style.display = 'none';

    otpTimerInterval = setInterval(() => {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        otpTimer.textContent = `(${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')})`;

        if (timeLeft <= 0) {
            clearInterval(otpTimerInterval);
            otpTimer.textContent = '';
            resendOtp.style.display = 'inline';
        }
    }, 1000);
}

// Handle Login
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    const captcha = document.getElementById('captcha').value.trim();
    const expectedAnswer = document.getElementById('captchaQuestion').getAttribute('data-answer');
    
    console.log('Login attempt:', { email, captcha, expectedAnswer }); // Debug log
    
    // Validate captcha
    if (captcha !== expectedAnswer) {
        await Swal.fire({
            icon: "error",
            title: "Security Question Incorrect",
            text: "Please answer the security question correctly.",
            confirmButtonColor: "#2ca078"
        });
        generateCaptcha(); // Regenerate captcha on wrong answer
        document.getElementById('captcha').value = '';
        return;
    }
    
    if (!email || !password) {
        await Swal.fire({
            icon: "error",
            title: "Missing Information",
            text: "Please enter both email and password.",
            confirmButtonColor: "#2ca078"
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: "Signing in...",
        text: "Please wait",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Use URLSearchParams instead of FormData for better compatibility
        const params = new URLSearchParams();
        params.append('action', 'login');
        params.append('email', email);
        params.append('password', password);
        params.append('captcha', captcha);
        params.append('login_portal', document.getElementById('loginPortal').value);

        console.log('Sending request to login_action.php'); // Debug log
        console.log('Request data:', params.toString()); // Debug log

        const response = await fetch('login_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        });

        console.log('Response status:', response.status); // Debug log
        console.log('Response headers:', response.headers); // Debug log
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Response result:', result); // Debug log

        if (result.success) {
            if (result.requires_otp) {
                // Close any open SweetAlert first
                Swal.close();
                // Small delay to ensure SweetAlert is fully closed
                setTimeout(() => {
                    showOtpPopup();
                }, 100);
            } else {
                // Redirect based on portal preference
                const portal = document.getElementById('loginPortal').value;
                if (portal === 'workforce') {
                    window.location.href = 'modules/admin/dashboard.php';
                } else {
                    window.location.href = 'modules/employee/dashboard.php';
                }
            }
        } else {
            if (result.banned) {
                if (result.ban_type === 'permanent') {
                    await Swal.fire({
                        icon: "error",
                        title: "Account Banned",
                        text: result.message || "Your account has been banned. Please contact the administrator.",
                        confirmButtonColor: "#2ca078",
                        showConfirmButton: true,
                        confirmButtonText: "OK"
                    });
                } else if (result.ban_type === 'temporary') {
                    let remainingSeconds = (result.remaining_seconds || (result.remaining_minutes || 5) * 60);
                    
                    // Create countdown timer function
                    function updateCountdown() {
                        const minutes = Math.floor(remainingSeconds / 60);
                        const seconds = remainingSeconds % 60;
                        const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                        
                        return Swal.fire({
                            icon: "warning",
                            title: "Login Temporarily Blocked",
                            html: `
                                <div style="text-align: center;">
                                    <p>Too many failed login attempts. Please try again in:</p>
                                    <div id="countdown-timer" style="font-size: 2rem; font-weight: bold; color: #e53e3e; margin: 20px 0;">
                                        ${timeString}
                                    </div>
                                    <p style="font-size: 0.9rem; color: #718096;">
                                        You can close this window and try again after the timer expires.
                                    </p>
                                </div>
                            `,
                            confirmButtonColor: "#2ca078",
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                            didOpen: () => {
                                const timerElement = document.getElementById('countdown-timer');
                                const timerInterval = setInterval(() => {
                                    remainingSeconds--;
                                    
                                    if (remainingSeconds <= 0) {
                                        clearInterval(timerInterval);
                                        timerElement.textContent = "0:00";
                                        setTimeout(() => {
                                            Swal.close();
                                        }, 1000);
                                    } else {
                                        const mins = Math.floor(remainingSeconds / 60);
                                        const secs = remainingSeconds % 60;
                                        timerElement.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
                                    }
                                }, 1000);
                            }
                        });
                    }
                    
                    updateCountdown();
                }
            } else {
                // Regular login failed
                await Swal.fire({
                    icon: "error",
                    title: "Login failed",
                    text: result.message || "Invalid credentials",
                    confirmButtonColor: "#2ca078"
                });
            }
            // Regenerate captcha on failed login
            generateCaptcha();
            document.getElementById('captcha').value = '';
        }
    } catch (error) {
        console.error('Login error:', error);
        await Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Unable to connect to the server. Please try again.\n\nError: " + error.message,
            confirmButtonColor: "#2ca078"
        });
    }
}

// Handle OTP Verification
async function handleOtpVerification(e) {
    e.preventDefault();

    // Prevent double submission
    if (isSubmitting) {
        return;
    }
    isSubmitting = true;

    const otpInputsContainer = document.getElementById('otpInputs');
    if (!otpInputsContainer) {
        isSubmitting = false;
        return;
    }

    const inputs = otpInputsContainer.querySelectorAll('.otp-input');
    let otpCode = '';
    let isValid = true;

    inputs.forEach(input => {
        if (input.value === '') {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
            otpCode += input.value;
        }
    });

    if (!isValid) {
        isSubmitting = false;
        Swal.fire({
            icon: 'error',
            title: 'Invalid OTP',
            text: 'Please fill in all digits',
            confirmButtonColor: '#2ca078'
        });
        return;
    }

    try {
        const formData = new FormData();
        formData.append("action", "verify_otp");
        formData.append("otp", otpCode);

        console.log("Submitting OTP:", otpCode, "Length:", otpCode.length);

        const response = await fetch('login_action.php', {
            method: "POST",
            body: formData
        });

        const result = await response.json();
        console.log("OTP verification response:", result);

        if (result.success) {
            isSubmitting = false;
            hideOtpPopup();
            await Swal.fire({
                icon: "success",
                title: "Login successful!",
                text: "Redirecting...",
                timer: 1000,
                showConfirmButton: false
            });
            window.location.href = result.redirect;
        } else {
            isSubmitting = false;
            await Swal.fire({
                icon: "error",
                title: "Verification failed",
                text: result.message || "Invalid OTP",
                confirmButtonColor: "#2ca078"
            });
        }
    } catch (error) {
        isSubmitting = false;
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Something went wrong. Please try again.",
            confirmButtonColor: "#2ca078"
        });
    }
}

// Handle Resend OTP
async function handleResendOtp(e) {
    e.preventDefault();

    // Prevent double submission
    if (isSubmitting) {
        return;
    }
    isSubmitting = true;

    try {
        const formData = new FormData();
        formData.append("action", "resend_otp");

        const response = await fetch('login_action.php', {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            isSubmitting = false;
            startOtpTimer();
            generateOtpInputs();

            Swal.fire({
                icon: "success",
                title: "OTP Resent",
                text: "A new verification code has been sent to your email",
                confirmButtonColor: "#2ca078",
                timer: 3000,
                timerProgressBar: true
            });

            // Show debug OTP in development
            if (result.debug_otp) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Debug OTP',
                        text: `Your new OTP code is: ${result.debug_otp}`,
                        confirmButtonColor: '#2ca078',
                        timer: 5000,
                        timerProgressBar: true
                    });
                }, 3500);
            }
        } else {
            isSubmitting = false;
            await Swal.fire({
                icon: "error",
                title: "Resend failed",
                text: result.message || "Failed to resend OTP",
                confirmButtonColor: "#2ca078"
            });
        }
    } catch (error) {
        isSubmitting = false;
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Something went wrong. Please try again.",
            confirmButtonColor: "#2ca078"
        });
    }
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    window.lucide.createIcons();

    // OTP form
    const otpForm = document.getElementById('otpForm');
    if (otpForm) {
        otpForm.addEventListener('submit', handleOtpVerification);
    }

    // Close OTP popup
    const closeOtpPopup = document.getElementById('closeOtpPopup');
    if (closeOtpPopup) {
        closeOtpPopup.addEventListener('click', hideOtpPopup);
    }

    // Resend OTP
    const resendOtp = document.getElementById('resendOtp');
    if (resendOtp) {
        resendOtp.addEventListener('click', handleResendOtp);
    }


    // Portal Switcher Link Logic (Directly inline)
    const portalSwitchLink = document.getElementById('portalSwitchLink');
    if (portalSwitchLink) {
        console.log('Portal switch link found, attaching listener');
        portalSwitchLink.addEventListener('click', function (e) {
            e.preventDefault();
            console.log("Portal link clicked!");

            const loginPortalInput = document.getElementById('loginPortal');
            const formTitle = document.querySelector('.form-title');
            const formSubtitle = document.querySelector('.form-subtitle');

            if (!loginPortalInput) {
                console.error("Login portal input not found!");
                return;
            }

            // Toggle state
            const isCurrentlyWorkforce = loginPortalInput.value === 'workforce';
            const newPortal = isCurrentlyWorkforce ? 'ess' : 'workforce';
            console.log(`Switching to: ${newPortal}`);

            // Update input
            loginPortalInput.value = newPortal;

            // Update UI
            if (newPortal === 'ess') {
                if (formTitle) formTitle.textContent = 'Employee Portal';
                if (formSubtitle) formSubtitle.textContent = 'Sign in to access self-service features';
                this.textContent = 'Switch to Workforce System';
            } else {
                if (formTitle) formTitle.textContent = 'Welcome back';
                if (formSubtitle) formSubtitle.textContent = 'Sign in to continue to your account';
                this.textContent = 'Switch to Employee Portal';
            }
        });
    } else {
        console.error('Portal switch link NOT found in DOM');
    }
});



