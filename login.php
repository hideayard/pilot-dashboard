<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT4 Trading Hub - Login</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }
        
        .bg-animation .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
        }
        
        .shape-1 {
            width: 500px;
            height: 500px;
            top: -250px;
            right: -100px;
            animation-delay: 0s;
        }
        
        .shape-2 {
            width: 400px;
            height: 400px;
            bottom: -200px;
            left: -100px;
            animation-delay: 5s;
        }
        
        .shape-3 {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-30px, 30px) rotate(240deg); }
        }
        
        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }
        
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: gradient 3s linear infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        
        /* Input Styles */
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .input-group input:focus + i {
            color: #667eea;
        }
        
        /* Button Styles */
        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
        }
        
        /* Floating Labels */
        .float-label {
            position: absolute;
            left: 3rem;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 0.25rem;
            color: #a0aec0;
            transition: all 0.3s ease;
            pointer-events: none;
            font-size: 1rem;
        }
        
        .input-group input:focus ~ .float-label,
        .input-group input:not(:placeholder-shown) ~ .float-label {
            top: 0;
            transform: translateY(-50%) scale(0.85);
            color: #667eea;
        }
        
        /* Particle Canvas */
        #particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }
        
        /* Market Ticker */
        .market-ticker {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.5rem 0;
            z-index: 20;
            overflow: hidden;
        }
        
        .ticker-content {
            display: flex;
            animation: ticker 30s linear infinite;
            white-space: nowrap;
        }
        
        .ticker-item {
            display: inline-flex;
            align-items: center;
            padding: 0 2rem;
            font-size: 0.9rem;
        }
        
        .ticker-item i {
            margin-right: 0.5rem;
        }
        
        .ticker-item.up {
            color: #10b981;
        }
        
        .ticker-item.down {
            color: #ef4444;
        }
        
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .glass-card {
                margin: 1rem;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Success Check Animation */
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #fff;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #7ac142;
            animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
        }
        
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #7ac142;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        
        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        
        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 30px #7ac142; }
        }
    </style>
</head>
<body>
    <!-- Particles Canvas -->
    <canvas id="particles"></canvas>
    
    <!-- Animated Background Shapes -->
    <div class="bg-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <!-- Market Ticker -->
    <div class="market-ticker">
        <div class="ticker-content" id="ticker">
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> EUR/USD 1.09234 <span class="ml-2">+0.23%</span>
            </div>
            <div class="ticker-item down">
                <i class="fas fa-arrow-down"></i> GBP/USD 1.26789 <span class="ml-2">-0.12%</span>
            </div>
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> USD/JPY 148.456 <span class="ml-2">+0.45%</span>
            </div>
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> AUD/USD 0.65789 <span class="ml-2">+0.08%</span>
            </div>
            <div class="ticker-item down">
                <i class="fas fa-arrow-down"></i> XAU/USD 2034.50 <span class="ml-2">-0.34%</span>
            </div>
            <!-- Duplicate for seamless loop -->
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> EUR/USD 1.09234 <span class="ml-2">+0.23%</span>
            </div>
            <div class="ticker-item down">
                <i class="fas fa-arrow-down"></i> GBP/USD 1.26789 <span class="ml-2">-0.12%</span>
            </div>
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> USD/JPY 148.456 <span class="ml-2">+0.45%</span>
            </div>
            <div class="ticker-item up">
                <i class="fas fa-arrow-up"></i> AUD/USD 0.65789 <span class="ml-2">+0.08%</span>
            </div>
            <div class="ticker-item down">
                <i class="fas fa-arrow-down"></i> XAU/USD 2034.50 <span class="ml-2">-0.34%</span>
            </div>
        </div>
    </div>
    
    <!-- Login Container -->
    <div class="relative w-full max-w-6xl" data-aos="fade-up" data-aos-duration="1000">
        <div class="glass-card">
            <div class="flex flex-col lg:flex-row">
                <!-- Left Side - Branding -->
                <div class="lg:w-1/2 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 p-12 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    
                    <div class="relative z-10 h-full flex flex-col">
                        <!-- Logo -->
                        <div class="mb-12" data-aos="fade-right" data-aos-delay="200">
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center transform rotate-3 hover:rotate-0 transition-transform duration-300">
                                    <i class="fas fa-chart-line text-3xl text-blue-600"></i>
                                </div>
                                <div>
                                    <span class="text-2xl font-bold block">MT4 Trading</span>
                                    <span class="text-sm opacity-90">Professional Trading Hub</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Main Content -->
                        <div class="flex-1 flex flex-col justify-center">
                            <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight" data-aos="fade-right" data-aos-delay="300">
                                Trade Smarter,
                                <span class="text-yellow-300 block">Not Harder</span>
                            </h1>
                            
                            <p class="text-lg opacity-90 mb-8" data-aos="fade-right" data-aos-delay="400">
                                Professional MT4 trading platform with advanced analytics, real-time monitoring, and powerful trading tools.
                            </p>
                            
                            <!-- Feature List with Icons -->
                            <div class="space-y-4 mb-12" data-aos="fade-right" data-aos-delay="500">
                                <div class="flex items-center space-x-3 group cursor-pointer">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:bg-opacity-30 transition-all">
                                        <i class="fas fa-bolt text-yellow-300"></i>
                                    </div>
                                    <span>Real-time MT4 synchronization</span>
                                </div>
                                <div class="flex items-center space-x-3 group cursor-pointer">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:bg-opacity-30 transition-all">
                                        <i class="fas fa-chart-pie text-yellow-300"></i>
                                    </div>
                                    <span>Advanced trading analytics</span>
                                </div>
                                <div class="flex items-center space-x-3 group cursor-pointer">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:bg-opacity-30 transition-all">
                                        <i class="fas fa-wallet text-yellow-300"></i>
                                    </div>
                                    <span>Multi-account management</span>
                                </div>
                                <div class="flex items-center space-x-3 group cursor-pointer">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:bg-opacity-30 transition-all">
                                        <i class="fas fa-bell text-yellow-300"></i>
                                    </div>
                                    <span>Price alerts & notifications</span>
                                </div>
                            </div>
                            
                            <!-- Testimonial Card -->
                            <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-lg transform hover:scale-105 transition-all duration-300 cursor-pointer" data-aos="fade-up" data-aos-delay="600">
                                <div class="flex items-center space-x-4 mb-4">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="w-14 h-14 rounded-full border-3 border-white">
                                    <div>
                                        <p class="font-semibold text-lg">Ahmad Razak</p>
                                        <p class="text-sm opacity-75">Professional Trader • 5 years</p>
                                    </div>
                                </div>
                                <p class="text-sm italic">"Best MT4 dashboard I've ever used. The analytics and insights have completely transformed my trading strategy."</p>
                                <div class="flex mt-3 text-yellow-300">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats Footer -->
                        <div class="grid grid-cols-3 gap-4 mt-8" data-aos="fade-up" data-aos-delay="700">
                            <div class="text-center">
                                <div class="text-2xl font-bold">50K+</div>
                                <div class="text-xs opacity-75">Active Traders</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">$2B+</div>
                                <div class="text-xs opacity-75">Trading Volume</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-xs opacity-75">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Login Form -->
                <div class="lg:w-1/2 p-12 bg-white">
                    <div class="h-full flex flex-col justify-center max-w-md mx-auto">
                        <!-- Header -->
                        <div class="text-center mb-10" data-aos="fade-left" data-aos-delay="200">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back! 👋</h2>
                            <p class="text-gray-600">Sign in to access your trading dashboard</p>
                        </div>
                        
                        <!-- Login Form -->
                        <form id="loginForm" class="space-y-6" data-aos="fade-left" data-aos-delay="300">
                            <!-- Username Field -->
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="username" name="username" placeholder=" " required>
                                <label for="username" class="float-label">Username or Email</label>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder=" " required>
                                <label for="password" class="float-label">Password</label>
                                <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 z-10" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            
                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-800">Remember me</span>
                                </label>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                    Forgot Password?
                                </a>
                            </div>
                            
                            <!-- Login Button -->
                            <button type="submit" class="btn-primary" id="loginBtn">
                                <span class="relative z-10 flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Sign In
                                </span>
                            </button>
                            
                            <!-- Demo Credentials -->
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">Demo Credentials</span>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 text-sm border border-blue-100">
                                <p class="text-gray-600 mb-2 flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    Use these for testing:
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white p-2 rounded-lg">
                                        <span class="text-xs text-gray-500">Username</span>
                                        <p class="font-mono font-semibold text-gray-800">demo_trader</p>
                                    </div>
                                    <div class="bg-white p-2 rounded-lg">
                                        <span class="text-xs text-gray-500">Password</span>
                                        <p class="font-mono font-semibold text-gray-800">demo123</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Sign Up Link -->
                        <p class="mt-8 text-center text-sm text-gray-600" data-aos="fade-up" data-aos-delay="400">
                            Don't have an account? 
                            <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold group">
                                Create free account 
                                <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </p>
                        
                        <!-- Social Login -->
                        <div class="mt-6" data-aos="fade-up" data-aos-delay="500">
                            <div class="relative mb-4">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-center space-x-4">
                                <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transform hover:scale-110 transition-all duration-300 group">
                                    <i class="fab fa-google text-xl"></i>
                                </button>
                                <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transform hover:scale-110 transition-all duration-300 group">
                                    <i class="fab fa-facebook-f text-xl"></i>
                                </button>
                                <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transform hover:scale-110 transition-all duration-300 group">
                                    <i class="fab fa-apple text-xl"></i>
                                </button>
                                <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transform hover:scale-110 transition-all duration-300 group">
                                    <i class="fab fa-microsoft text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 bg-white rounded-xl shadow-2xl p-4 transform translate-x-full transition-transform duration-300 z-50 max-w-sm">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Welcome back!</p>
                <p class="text-sm text-gray-600">Redirecting to dashboard...</p>
            </div>
        </div>
    </div>
    
    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 50
        });
        
        // Particles.js Background
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const particles = [];
        const particleCount = 50;
        
        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: Math.random() * 2 + 1,
                speedX: (Math.random() - 0.5) * 0.5,
                speedY: (Math.random() - 0.5) * 0.5,
                color: `rgba(255, 255, 255, ${Math.random() * 0.3})`
            });
        }
        
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(particle => {
                particle.x += particle.speedX;
                particle.y += particle.speedY;
                
                if (particle.x < 0 || particle.x > canvas.width) particle.speedX *= -1;
                if (particle.y < 0 || particle.y > canvas.height) particle.speedY *= -1;
                
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                ctx.fillStyle = particle.color;
                ctx.fill();
            });
            
            requestAnimationFrame(animateParticles);
        }
        
        animateParticles();
        
        // Handle window resize
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
        
        // Toggle Password Visibility
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                password.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            
            // Simple validation
            if (!username || !password) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            
            // Show loading state
            const originalText = loginBtn.innerHTML;
            loginBtn.innerHTML = '<span class="loading"></span> Signing In...';
            loginBtn.disabled = true;
            
            // Simulate login
            setTimeout(() => {
                if ((username === 'demo_trader' && password === 'demo123') ||(username === 'admin' && password === 'admin')) {
                    // Success
                    showNotification('Login successful! Redirecting...', 'success');
                    
                    // Save to localStorage
                    localStorage.setItem('isLoggedIn', 'true');
                    localStorage.setItem('username', username);
                    
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    // Error
                    showNotification('Invalid username or password', 'error');
                    loginBtn.innerHTML = originalText;
                    loginBtn.disabled = false;
                }
            }, 1500);
        });
        
        // Show notification
        function showNotification(message, type) {
            const toast = document.getElementById('toast');
            const icon = toast.querySelector('i');
            const title = toast.querySelector('.font-semibold');
            const desc = toast.querySelector('.text-sm');
            
            if (type === 'success') {
                icon.className = 'fas fa-check-circle text-green-600 text-xl';
                title.textContent = 'Welcome back!';
                desc.textContent = message;
            } else {
                icon.className = 'fas fa-exclamation-circle text-red-600 text-xl';
                title.textContent = 'Error';
                desc.textContent = message;
            }
            
            toast.classList.remove('translate-x-full');
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        }
        
        // Auto-fill remembered user
        const rememberedUser = localStorage.getItem('rememberedUser');
        if (rememberedUser) {
            document.getElementById('username').value = rememberedUser;
            document.querySelector('input[type="checkbox"]').checked = true;
        }
        
        // Add ripple effect to buttons
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function(e) {
                const x = e.clientX - e.target.offsetLeft;
                const y = e.clientY - e.target.offsetTop;
                
                const ripple = document.createElement('span');
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.className = 'ripple';
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
    
    <style>
        /* Ripple Effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Toast Animation */
        #toast {
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        /* Input Autofill Styles */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px white inset;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</body>
</html>