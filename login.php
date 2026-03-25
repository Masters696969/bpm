<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microfinance Login</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="img/logo.png">
</head>
<body>
    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
        <i data-lucide="sun" class="sun-icon"></i>
        <i data-lucide="moon" class="moon-icon"></i>
    </button>

    <!-- Main Container -->
    <div class="container">
        <!-- Auth Card -->
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <a href="index.php" class="logo-wrapper" style="text-decoration: none; cursor: pointer; display: flex; flex-direction: column; align-items: center;">
                    <img src="img/logo.png" alt="Logo" class="logo">
                    <span class="logo-text">Microfinance</span>
                </a>
            </div>

            <!-- Login Form -->
            <div class="form-wrapper active" id="loginForm">
                <input type="hidden" id="loginPortal" name="login_portal" value="workforce">
                <div class="form-header">
                    <h1 class="form-title">Welcome back</h1>
                    <p class="form-subtitle">Sign in to continue to your account</p>
                </div>

                <form class="form" onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <label for="loginEmail" class="input-label">Email Address</label>
                        <div class="input-wrapper">
                            <i data-lucide="mail" class="input-icon"></i>
                           <input 
                                type="email" 
                                id="loginEmail" 
                                name="email" 
                                class="input-field" 
                                placeholder="Enter your email address" 
                                required
                           >
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="label-row">
                            <label for="loginPassword" class="input-label">Password</label>
                        </div>
                        <div class="input-wrapper">
                            <i data-lucide="lock" class="input-icon"></i>
                            <input 
                                type="password" 
                                id="loginPassword" 
                                name="password" 
                                class="input-field" 
                                placeholder="Enter your password" 
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('loginPassword')">
                                <i data-lucide="eye" class="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="captcha" class="input-label">Security Question</label>
                        <div class="captcha-container">
                            <div class="captcha-question" id="captchaQuestion">
                                What is <span id="mathProblem">7 + 3</span>?
                            </div>
                            <div class="input-wrapper">
                                <i data-lucide="shield" class="input-icon"></i>
                                <input 
                                    type="text" 
                                    id="captcha" 
                                    name="captcha" 
                                    class="input-field" 
                                    placeholder="Enter answer" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span>Sign in</span>
                        <i data-lucide="arrow-right" class="btn-icon"></i>
                    </button>
                    
                    <div class="form-footer">
                        <a href="#" id="portalSwitchLink" class="link">Switch to Employee Portal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OTP Popup -->
    <div class="otp-overlay" id="otpOverlay">
        <div class="otp-popup">
            <button class="close-otp" id="closeOtpPopup">
                <i data-lucide="x"></i>
            </button>
            <div class="otp-header">
                <h2>Verify Your Email</h2>
                <p>Enter the 6-digit code sent to your email</p>
            </div>
            <form id="otpForm">
                <div class="otp-inputs" id="otpInputs">
                    <!-- OTP inputs will be generated by JavaScript -->
                </div>
                <div class="otp-actions">
                    <button type="submit" class="btn btn-primary" id="verifyOtpBtn">
                        <span>Verify OTP</span>
                        <i data-lucide="check" class="btn-icon"></i>
                    </button>
                    <div class="resend-otp">
                        Didn't receive the code? 
                        <a href="#" id="resendOtp">Resend OTP</a>
                        <span id="otpTimer" class="otp-timer"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="js/login.js?v=<?php echo time(); ?>"></script>
    <script>
        // Generate random math captcha
        function generateCaptcha() {
            const num1 = Math.floor(Math.random() * 90) + 10; // 2-digit numbers (10-99)
            const num2 = Math.floor(Math.random() * 90) + 10; // 2-digit numbers (10-99)
            const operators = ['+', '-', '×'];
            const operator = operators[Math.floor(Math.random() * operators.length)];
            
            let answer;
            switch(operator) {
                case '+':
                    answer = num1 + num2;
                    break;
                case '-':
                    answer = num1 - num2;
                    break;
                case '×':
                    answer = num1 * num2;
                    break;
            }
            
            document.getElementById('mathProblem').textContent = `${num1} ${operator} ${num2}`;
            document.getElementById('captchaQuestion').setAttribute('data-answer', answer);
        }

        // Initialize captcha on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateCaptcha();
        });
    </script>
</body>
</html>
