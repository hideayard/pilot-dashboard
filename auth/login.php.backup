<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Energy Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="login-title">Energy Monitor</h1>
            <p class="login-subtitle">Sign in to access your dashboard</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text"
                    id="username"
                    class="form-input"
                    placeholder="Enter your username"
                    required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <div class="password-container">
                    <input type="password"
                        id="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required>
                    <button type="button"
                        class="toggle-password"
                        id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" id="remember">
                    Remember me
                </label>
                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="login-button" id="loginButton">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>

        <!-- <div class="divider">
            <span>Or continue with</span>
        </div> -->

        <!-- <div class="social-login">
            <button type="button" class="social-button" id="googleLogin">
                <i class="fab fa-google"></i>
                Google
            </button>
            <button type="button" class="social-button" id="githubLogin">
                <i class="fab fa-github"></i>
                GitHub
            </button>
        </div> -->

        <p class="register-link">
            Don't have an account?
            <a href="register.php">Create one now</a>
        </p>
    </div>

    <div class="copyright">
        © 2024 <a href="#">Energy Monitoring System</a>. All rights reserved.
        <br>
        <span style="font-size: 12px; opacity: 0.7;">
            Version 5.0 | Made with <i class="fas fa-heart" style="color: #ff6b6b;"></i> for a sustainable future
        </span>
    </div>

    <!-- Load auth.js -->
    <script src="../assets/js/auth.js"></script>
    
    <!-- Simple social login handlers -->
    <script>
        document.getElementById('googleLogin')?.addEventListener('click', () => {
            Swal.fire('Info', 'Google login coming soon!', 'info');
        });
        
        document.getElementById('githubLogin')?.addEventListener('click', () => {
            Swal.fire('Info', 'GitHub login coming soon!', 'info');
        });
    </script>
</body>
</html>