<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akaun - Geran Komuniti Iskandar Puteri Rendah Karbon 5.0</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="apple-touch-icon" href="../assets/images/favicon.png">
    <link rel="shortcut icon" href="../assets/images/favicon.png">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Load SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Same styles as before - keep all the existing CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #EDF2F7 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .register-container {
            max-width: 1400px;
            width: 100%;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            background: white;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(44, 82, 130, 0.25);
            border: 2px solid #FDA300;
        }

        /* Left side - Image/Branding */
        .brand-side {
            background: linear-gradient(135deg, #2C5282 0%, #1A2B3E 100%);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            min-height: 800px;
        }

        .brand-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/images/pattern.png') repeat;
            opacity: 0.1;
            pointer-events: none;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .logo-container {
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            max-width: 150px;
            height: auto;
        }

        .brand-title {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .brand-title span {
            color: #FDA300;
        }

        .brand-description {
            color: #EDF2F7;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .image-container {
            width: 100%;
            margin: 0.5rem 0 1rem 0;
            border-radius: 1rem;
            overflow: hidden;
            border: 3px solid #FDA300;
            background: #EDF2F7;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            aspect-ratio: 1024/790;
            position: relative;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: fill;
            display: block;
            transition: transform 0.5s ease;
        }

        .image-container:hover img {
            transform: scale(1.02);
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(44, 82, 130, 0.9), transparent);
            padding: 1rem;
            color: white;
            pointer-events: none;
        }

        .image-overlay p {
            font-size: 0.85rem;
            font-weight: 600;
            color: #FDA300;
            margin: 0;
        }

        .feature-list {
            list-style: none;
            margin-top: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #EDF2F7;
            font-size: 0.9rem;
        }

        .feature-item i {
            color: #FDA300;
            font-size: 1rem;
            width: 20px;
        }

        /* Right side - Register Form */
        .form-side {
            padding: 2.5rem 2rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-header h2 {
            color: #2C5282;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .form-header p {
            color: #315492;
            font-size: 0.875rem;
        }

        .form-header p a {
            color: #FDA300;
            font-weight: 600;
            text-decoration: none;
        }

        .form-header p a:hover {
            text-decoration: underline;
        }

        .register-form {
            max-width: 340px;
            margin: 0 auto;
            width: 100%;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            color: #2C5282;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: #2C5282;
            font-size: 1rem;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 2px solid #EDF2F7;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            color: #2C5282;
            transition: all 0.3s ease;
            background: #F8FAFC;
        }

        .form-input:focus {
            outline: none;
            border-color: #FDA300;
            background: white;
            box-shadow: 0 0 0 4px rgba(253, 163, 0, 0.1);
        }

        .form-input::placeholder {
            color: #94A3B8;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: #2C5282;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #FDA300;
        }

        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #EDF2F7;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background: #EF4444;
            width: 33.33%;
        }

        .strength-medium {
            background: #F59E0B;
            width: 66.66%;
        }

        .strength-strong {
            background: #10B981;
            width: 100%;
        }

        .password-hint {
            font-size: 0.7rem;
            color: #94A3B8;
            margin-top: 0.25rem;
        }

        .terms-group {
            margin: 1.25rem 0;
        }

        .terms-label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #2C5282;
            font-size: 0.85rem;
            line-height: 1.4;
            cursor: pointer;
        }

        .terms-label input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #FDA300;
            border-radius: 0.25rem;
            margin-top: 0.15rem;
        }

        .terms-label a {
            color: #FDA300;
            font-weight: 600;
            text-decoration: none;
        }

        .terms-label a:hover {
            text-decoration: underline;
        }

        .register-button {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #2C5282, #1A2B3E);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            border: 2px solid transparent;
        }

        .register-button:hover {
            background: linear-gradient(135deg, #1A2B3E, #2C5282);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(44, 82, 130, 0.3);
            border-color: #FDA300;
        }

        .register-button:active {
            transform: translateY(0);
        }

        .register-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .register-button i {
            font-size: 1.1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.25rem;
        }

        .login-link a {
            color: #2C5282;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s ease;
        }

        .login-link a:hover {
            color: #FDA300;
        }

        .login-link a i {
            color: #FDA300;
        }

        @media (max-width: 1024px) {
            .register-container {
                grid-template-columns: 1.1fr 0.9fr;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .register-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .brand-side {
                display: none;
            }

            .form-side {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        @media (max-width: 480px) {
            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .form-input.error {
            border-color: #EF4444;
            background: #FEF2F2;
        }

        .error-message {
            color: #EF4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Left Side - Branding with Image -->
        <div class="brand-side">
            <div class="brand-content">
                <div class="logo-container">
                    <img src="../assets/images/favicon.png" alt="Logo" onerror="this.style.display='none'">
                </div>

                <h1 class="brand-title">
                    Geran Komuniti<br>
                    <span>Iskandar Puteri</span><br>
                    Rendah Karbon 5.0
                </h1>

                <p class="brand-description">
                    Sistem pemantauan projek Pasar Malam Hijau yang dikuasakan oleh Power Inverter 2.0
                </p>

                <!-- Image Container -->
                <div class="image-container">
                    <img src="../assets/images/pasarmalamhijau.png" alt="Pasar Malam Hijau" onerror="this.src='https://via.placeholder.com/1024x790/2C5282/FDA300?text=Pasar+Malam+Hijau'">
                    <div class="image-overlay">
                        <p>Pasar Malam Hijau • Inisiatif Rendah Karbon</p>
                    </div>
                </div>

                <ul class="feature-list">
                    <li class="feature-item">
                        <i class="fas fa-leaf"></i>
                        <span>Pemantauan karbon masa nyata</span>
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Lokasi inverter di Johor</span>
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Analisis penjimatan tenaga</span>
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-bolt"></i>
                        <span>Data prestasi inverter</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="form-side">
            <div class="form-header">
                <h2>Daftar Akaun Baru</h2>
                <p>Sudah mempunyai akaun? <a href="login.php">Log Masuk</a></p>
            </div>

            <form class="register-form" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="firstName">Nama Pertama</label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="firstName" name="firstName" class="form-input" placeholder="Ali" required>
                        </div>
                        <div class="error-message" id="firstNameError">Nama pertama diperlukan</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lastName">Nama Keluarga</label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="lastName" name="lastName" class="form-input" placeholder="Abu" required>
                        </div>
                        <div class="error-message" id="lastNameError">Nama keluarga diperlukan</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input" placeholder="ali@email.com" required>
                    </div>
                    <div class="error-message" id="emailError">Sila masukkan email yang sah</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Nama Pengguna</label>
                    <div class="input-group">
                        <i class="fas fa-at input-icon"></i>
                        <input type="text" id="username" name="username" class="form-input" placeholder="aliabu" required>
                    </div>
                    <div class="error-message" id="usernameError">Nama pengguna diperlukan</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Laluan</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrength"></div>
                    </div>
                    <div class="password-hint" id="passwordHint">Minimum 8 aksara dengan huruf besar, kecil dan nombor</div>
                    <div class="error-message" id="passwordError">Kata laluan diperlukan</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPassword">Pengesahan Kata Laluan</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="confirmPasswordError">Kata laluan tidak sepadan</div>
                </div>

                <div class="terms-group">
                    <label class="terms-label">
                        <input type="checkbox" id="acceptTerms" name="acceptTerms" required>
                        <span>
                            Saya bersetuju dengan <a href="terms.php">Terma dan Syarat</a> dan 
                            <a href="privacy.php">Dasar Privasi</a>
                        </span>
                    </label>
                    <div class="error-message" id="termsError">Sila terima terma dan syarat</div>
                </div>

                <button type="submit" class="register-button" id="registerButton">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar Akaun</span>
                </button>

                <div class="login-link">
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        Sudah mempunyai akaun? Log Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Load auth.js -->
    <script src="../assets/js/auth.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.getElementById('registerForm');
            const registerButton = document.getElementById('registerButton');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const strengthBar = document.getElementById('passwordStrength');
            const passwordHint = document.getElementById('passwordHint');
            const acceptTerms = document.getElementById('acceptTerms');

            // Password toggle
            togglePassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });

            toggleConfirmPassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (confirmPasswordInput.type === 'password') {
                    confirmPasswordInput.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    confirmPasswordInput.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });

            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]+/)) strength++;
                if (password.match(/[A-Z]+/)) strength++;
                if (password.match(/[0-9]+/)) strength++;
                if (password.match(/[$@#&!]+/)) strength++;

                return strength;
            }

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                
                strengthBar.className = 'password-strength-bar';
                
                if (password.length === 0) {
                    strengthBar.style.width = '0%';
                    passwordHint.textContent = 'Minimum 8 aksara dengan huruf besar, kecil dan nombor';
                } else if (strength <= 2) {
                    strengthBar.classList.add('strength-weak');
                    passwordHint.textContent = 'Kata laluan lemah - gunakan campuran huruf besar, kecil dan nombor';
                } else if (strength <= 4) {
                    strengthBar.classList.add('strength-medium');
                    passwordHint.textContent = 'Kata laluan sederhana';
                } else {
                    strengthBar.classList.add('strength-strong');
                    passwordHint.textContent = 'Kata laluan kuat';
                }
            });

            // Input focus effects
            document.querySelectorAll('.form-input').forEach(input => {
                input.addEventListener('focus', function() {
                    const icon = this.parentElement.querySelector('.input-icon');
                    if (icon) icon.style.color = '#FDA300';
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        const icon = this.parentElement.querySelector('.input-icon');
                        if (icon) icon.style.color = '#2C5282';
                    }
                });
            });

            // Email validation
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Form submission using auth.js register function
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Get form values
                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const email = document.getElementById('email').value.trim();
                const username = document.getElementById('username').value.trim();
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const termsAccepted = acceptTerms.checked;

                // Reset errors
                document.querySelectorAll('.form-input').forEach(input => {
                    input.classList.remove('error');
                });
                document.querySelectorAll('.error-message').forEach(msg => {
                    msg.classList.remove('show');
                });

                let hasError = false;

                // Validate first name
                if (!firstName) {
                    document.getElementById('firstName').classList.add('error');
                    document.getElementById('firstNameError').classList.add('show');
                    hasError = true;
                }

                // Validate last name
                if (!lastName) {
                    document.getElementById('lastName').classList.add('error');
                    document.getElementById('lastNameError').classList.add('show');
                    hasError = true;
                }

                // Validate email
                if (!email || !isValidEmail(email)) {
                    document.getElementById('email').classList.add('error');
                    document.getElementById('emailError').classList.add('show');
                    hasError = true;
                }

                // Validate username
                if (!username || username.length < 3) {
                    document.getElementById('username').classList.add('error');
                    document.getElementById('usernameError').textContent = 'Nama pengguna mesti sekurang-kurangnya 3 aksara';
                    document.getElementById('usernameError').classList.add('show');
                    hasError = true;
                }

                // Validate password
                if (!password || password.length < 8) {
                    document.getElementById('password').classList.add('error');
                    document.getElementById('passwordError').textContent = 'Kata laluan mesti sekurang-kurangnya 8 aksara';
                    document.getElementById('passwordError').classList.add('show');
                    hasError = true;
                }

                // Validate password match
                if (password !== confirmPassword) {
                    document.getElementById('confirmPassword').classList.add('error');
                    document.getElementById('confirmPasswordError').classList.add('show');
                    hasError = true;
                }

                // Validate terms
                if (!termsAccepted) {
                    document.getElementById('termsError').classList.add('show');
                    hasError = true;
                }

                if (hasError) return;

                // Show loading state
                const originalText = registerButton.innerHTML;
                registerButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendaftar...';
                registerButton.disabled = true;

                try {
                    // Call the register function from auth.js
                    const userData = {
                        firstName,
                        lastName,
                        email,
                        username,
                        password,
                        confirmPassword,
                        acceptTerms: termsAccepted
                    };
                    
                    await register(userData);
                    
                    // Note: The register function in auth.js already handles success message and redirect
                    // So we don't need to do anything here
                    
                } catch (error) {
                    console.error('Registration error:', error);
                    
                    // Error is already shown by auth.js, but we'll show a fallback if needed
                    if (!error.message.includes('Swal')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pendaftaran Gagal',
                            text: error.message || 'Ralat berlaku semasa pendaftaran. Sila cuba lagi.',
                            confirmButtonColor: '#FDA300',
                            background: '#FFFFFF',
                            color: '#2C5282'
                        });
                    }

                    registerButton.innerHTML = originalText;
                    registerButton.disabled = false;
                }
            });
        });
    </script>
</body>

</html>