<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Laluan - Geran Komuniti Iskandar Puteri Rendah Karbon 5.0</title>

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

        .forgot-container {
            max-width: 1200px;
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
            min-height: 700px;
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

        /* Right side - Forgot Password Form */
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
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #315492;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .form-header .back-to-login {
            margin-top: 1rem;
        }

        .form-header .back-to-login a {
            color: #FDA300;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-header .back-to-login a:hover {
            text-decoration: underline;
        }

        .forgot-form {
            max-width: 340px;
            margin: 0 auto;
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        .info-box {
            background: #FFF6E1;
            border-left: 4px solid #FDA300;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .info-box p {
            color: #2C5282;
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 0;
        }

        .info-box i {
            color: #FDA300;
            margin-right: 0.5rem;
        }

        .reset-button {
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

        .reset-button:hover {
            background: linear-gradient(135deg, #1A2B3E, #2C5282);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(44, 82, 130, 0.3);
            border-color: #FDA300;
        }

        .reset-button:active {
            transform: translateY(0);
        }

        .reset-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .reset-button i {
            font-size: 1.1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
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
            .forgot-container {
                grid-template-columns: 1.1fr 0.9fr;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .forgot-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .brand-side {
                display: none;
            }

            .form-side {
                padding: 2rem 1.5rem;
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

        .success-message {
            text-align: center;
            padding: 1rem;
            background: #F0FFF4;
            border: 1px solid #2C5282;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .success-message i {
            color: #2C5282;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .success-message p {
            color: #2C5282;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="forgot-container">
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

        <!-- Right Side - Forgot Password Form -->
        <div class="form-side">
            <div class="form-header">
                <h2>Lupa Kata Laluan?</h2>
                <p>Jangan risau, kami akan hantar pautan untuk menetapkan semula kata laluan anda.</p>
                <div class="back-to-login">
                    <a href="login.php">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Log Masuk
                    </a>
                </div>
            </div>

            <div id="successContainer" style="display: none;">
                <div class="success-message">
                    <i class="fas fa-envelope-open-text"></i>
                    <p id="successMessage">Pautan reset kata laluan telah dihantar ke email anda.</p>
                    <p class="text-sm text-[#2C5282]">Sila periksa peti masuk email anda.</p>
                </div>
                <div class="login-link">
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        Kembali ke Log Masuk
                    </a>
                </div>
            </div>

            <form class="forgot-form" id="forgotForm" style="display: block;">
                <div class="info-box">
                    <p>
                        <i class="fas fa-info-circle"></i>
                        Masukkan alamat email yang anda gunakan semasa pendaftaran. Kami akan menghantar pautan reset kata laluan ke email tersebut.
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" required>
                    </div>
                    <div class="error-message" id="emailError">Sila masukkan alamat email yang sah</div>
                </div>

                <button type="submit" class="reset-button" id="resetButton">
                    <i class="fas fa-paper-plane"></i>
                    <span>Hantar Pautan Reset</span>
                </button>

                <div class="login-link">
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        Kembali ke Log Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Load auth.js -->
    <script src="../assets/js/auth.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forgotForm = document.getElementById('forgotForm');
            const successContainer = document.getElementById('successContainer');
            const resetButton = document.getElementById('resetButton');
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');

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

            // Email validation function
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Forgot password function (to be added to auth.js)
            async function forgotPassword(email) {
                try {
                    console.log('Sending password reset email to:', email);

                    const formData = new FormData();
                    formData.append('action', 'forgot_password');
                    formData.append('email', email);

                    const response = await fetch('../proxy.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Forgot password failed:', errorText);
                        throw new Error('Gagal menghantar email. Sila cuba lagi.');
                    }

                    const data = await response.json();
                    console.log('Forgot password response:', data);

                    if (!data.success) {
                        throw new Error(data.message || 'Email tidak dijumpai');
                    }

                    return data;
                } catch (error) {
                    console.error('Forgot password error:', error);
                    throw error;
                }
            }

            // Make forgotPassword available globally
            window.forgotPassword = forgotPassword;

            // Form submission
            forgotForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const email = emailInput.value.trim();

                // Reset error
                emailInput.classList.remove('error');
                emailError.classList.remove('show');

                // Validate email
                if (!email || !isValidEmail(email)) {
                    emailInput.classList.add('error');
                    emailError.classList.add('show');
                    return;
                }

                // Show loading state
                const originalText = resetButton.innerHTML;
                resetButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghantar...';
                resetButton.disabled = true;

                try {
                    // Call forgotPassword function
                    await forgotPassword(email);

                    // Show success message
                    forgotForm.style.display = 'none';
                    successContainer.style.display = 'block';

                    Swal.fire({
                        icon: 'success',
                        title: 'Email Dihantar!',
                        text: 'Pautan reset kata laluan telah dihantar ke email anda.',
                        confirmButtonColor: '#FDA300',
                        background: '#FFFFFF',
                        color: '#2C5282'
                    });

                } catch (error) {
                    console.error('Error:', error);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat',
                        text: error.message || 'Email tidak dijumpai. Sila cuba lagi.',
                        confirmButtonColor: '#FDA300',
                        background: '#FFFFFF',
                        color: '#2C5282'
                    });

                    resetButton.innerHTML = originalText;
                    resetButton.disabled = false;
                }
            });
        });
    </script>
</body>

</html>