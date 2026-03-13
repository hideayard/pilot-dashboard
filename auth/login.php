<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Geran Komuniti Iskandar Puteri Rendah Karbon 5.0</title>

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
        /* (keep all your existing styles here - they're perfect) */
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

        .login-container {
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

        .login-form {
            max-width: 340px;
            margin: 0 auto;
            width: 100%;
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

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #2C5282;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #FDA300;
            border-radius: 0.25rem;
        }

        .forgot-password {
            color: #FDA300;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
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

        .login-button:hover {
            background: linear-gradient(135deg, #1A2B3E, #2C5282);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(44, 82, 130, 0.3);
            border-color: #FDA300;
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button i {
            font-size: 1.1rem;
        }

        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .social-login {
            text-align: center;
            margin-top: 1.25rem;
        }

        .social-login p {
            color: #315492;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #EDF2F7;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
        }

        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #F8FAFC;
            border: 2px solid #EDF2F7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2C5282;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-icon:hover {
            background: #2C5282;
            border-color: #FDA300;
            color: white;
            transform: translateY(-2px);
        }

        .demo-credentials {
            margin-top: 1.5rem;
            padding: 0.75rem;
            background: #FFF6E1;
            border-radius: 0.75rem;
            border: 1px solid #FDA300;
            text-align: center;
        }

        .demo-credentials p {
            color: #2C5282;
            font-size: 0.8rem;
            margin: 0.15rem 0;
        }

        .demo-credentials .title {
            font-weight: 700;
            margin-bottom: 0.35rem;
            font-size: 0.85rem;
        }

        @media (max-width: 1024px) {
            .login-container {
                grid-template-columns: 1.1fr 0.9fr;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .login-container {
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
            .form-options {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }

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
    <div class="login-container">
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

        <!-- Right Side - Login Form -->
        <div class="form-side">
            <div class="form-header">
                <h2>Selamat Datang Kembali</h2>
                <p>Atau <a href="register.php">daftar akaun baru</a></p>
            </div>

            <form class="login-form" id="loginForm" method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
                    </div>
                    <div class="error-message" id="usernameError">Sila masukkan username</div>
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
                    <div class="error-message" id="passwordError">Kata laluan diperlukan</div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Lupa kata laluan?</a>
                </div>

                <button type="submit" class="login-button" id="loginButton">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Log Masuk</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Load auth.js -->
    <script src="../assets/js/auth.js"></script>
    
</body>

</html>