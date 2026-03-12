<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT4 Trading Hub - Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f3f4f6;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: white;
            box-shadow: 10px 0 30px -15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 50;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .logo-text {
            display: none;
        }
        
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 1rem 0;
        }
        
        .sidebar.collapsed .menu-item i {
            margin-right: 0;
            font-size: 1.25rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
            padding: 2rem;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        /* Glass Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Stat Card */
        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Progress Bar */
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 1s ease;
        }
        
        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Avatar */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Notification Badge */
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            min-width: 20px;
            text-align: center;
        }
        
        /* Menu Item */
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #4a5568;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            margin: 0.25rem 1rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .menu-item i {
            margin-right: 1rem;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        
        .menu-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(102, 126, 234, 0.3);
        }
        
        /* Header */
        .header {
            background: white;
            border-radius: 1.5rem;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Search Bar */
        .search-bar {
            background: #f7fafc;
            border-radius: 2rem;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            width: 300px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .search-bar:focus-within {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            flex: 1;
            padding: 0.25rem 0.5rem;
        }
        
        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            text-align: left;
            padding: 1rem;
            color: #4a5568;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:hover {
            background: #f7fafc;
        }
        
        /* Status Badge */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-badge.success {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-badge.warning {
            background: #feebc8;
            color: #744210;
        }
        
        .status-badge.error {
            background: #fed7d7;
            color: #742a2a;
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.5s ease forwards;
        }
        
        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .search-bar {
                width: 100%;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        /* Dark Mode Toggle */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: rotate(45deg);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <span class="logo-text text-xl font-bold gradient-text">MT4 Hub</span>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="avatar">JD</div>
                <div class="sidebar-text">
                    <p class="font-semibold text-gray-800">John Doe</p>
                    <p class="text-xs text-gray-500">Premium Trader</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-6">
            <div class="menu-item active" onclick="navigateTo('dashboard')">
                <i class="fas fa-home"></i>
                <span class="sidebar-text">Dashboard</span>
            </div>
            <div class="menu-item" onclick="navigateTo('accounts')">
                <i class="fas fa-wallet"></i>
                <span class="sidebar-text">MT4 Accounts</span>
            </div>
            <div class="menu-item" onclick="navigateTo('trading')">
                <i class="fas fa-chart-line"></i>
                <span class="sidebar-text">Trading</span>
                <span class="badge">3</span>
            </div>
            <div class="menu-item" onclick="navigateTo('history')">
                <i class="fas fa-history"></i>
                <span class="sidebar-text">Trade History</span>
            </div>
            <div class="menu-item" onclick="navigateTo('analytics')">
                <i class="fas fa-chart-pie"></i>
                <span class="sidebar-text">Analytics</span>
            </div>
            <div class="menu-item" onclick="navigateTo('alerts')">
                <i class="fas fa-bell"></i>
                <span class="sidebar-text">Price Alerts</span>
            </div>
            
            <div class="border-t border-gray-200 my-4"></div>
            
            <div class="menu-item" onclick="navigateTo('profile')">
                <i class="fas fa-user"></i>
                <span class="sidebar-text">Profile</span>
            </div>
            <div class="menu-item" onclick="navigateTo('settings')">
                <i class="fas fa-cog"></i>
                <span class="sidebar-text">Settings</span>
            </div>
            <div class="menu-item" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Logout</span>
            </div>
        </nav>
        
        <!-- Upgrade Banner -->
        <div class="absolute bottom-4 left-4 right-4 p-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white sidebar-text">
            <p class="text-sm font-semibold mb-1">Upgrade to Pro</p>
            <p class="text-xs opacity-90 mb-2">Get advanced features and analytics</p>
            <button class="bg-white text-blue-600 text-xs px-3 py-1 rounded-full font-semibold hover:bg-opacity-90">
                Upgrade Now
            </button>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header" data-aos="fade-down">
            <div class="flex items-center space-x-4">
                <button class="md:hidden text-gray-600" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <button class="hidden md:block text-gray-600" onclick="toggleSidebarCollapse()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <div class="search-bar">
                    <i class="fas fa-search text-gray-400"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </div>
                <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-xl cursor-pointer"></i>
                    <span class="badge">5</span>
                </div>
                <div class="relative">
                    <i class="fas fa-envelope text-gray-600 text-xl cursor-pointer"></i>
                    <span class="badge">2</span>
                </div>
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="w-10 h-10 rounded-full cursor-pointer border-2 border-transparent hover:border-blue-600 transition-all">
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="mb-8" data-aos="fade-up">
            <h1 class="text-3xl font-bold text-gray-800">Welcome back, John! 👋</h1>
            <p class="text-gray-600 mt-2">Here's what's happening with your trading accounts today.</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-wallet text-2xl text-blue-600"></i>
                    </div>
                    <span class="text-sm text-gray-500">+12.5%</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">$45,230.50</h3>
                <p class="text-sm text-gray-600 mt-1">Total Balance</p>
                <div class="progress-bar mt-4">
                    <div class="progress-fill" style="width: 75%"></div>
                </div>
            </div>
            
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl text-green-600"></i>
                    </div>
                    <span class="text-sm text-green-600">+$1,234</span>
                </div>
                <h3 class="text-2xl font-bold text-green-600">+$2,450.30</h3>
                <p class="text-sm text-gray-600 mt-1">Today's Profit</p>
                <div class="flex items-center mt-4 text-sm">
                    <i class="fas fa-arrow-up text-green-600 mr-1"></i>
                    <span class="text-green-600">8.5%</span>
                    <span class="text-gray-500 ml-2">vs yesterday</span>
                </div>
            </div>
            
            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-server text-2xl text-purple-600"></i>
                    </div>
                    <span class="text-sm text-gray-500">Active</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">3/5</h3>
                <p class="text-sm text-gray-600 mt-1">Active Accounts</p>
                <div class="flex -space-x-2 mt-4">
                    <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">MT4</div>
                    <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">MT5</div>
                    <div class="w-8 h-8 rounded-full bg-yellow-500 border-2 border-white flex items-center justify-center text-white text-xs">cTrader</div>
                </div>
            </div>
            
            <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-bar text-2xl text-yellow-600"></i>
                    </div>
                    <span class="text-sm text-gray-500">Today</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">24</h3>
                <p class="text-sm text-gray-600 mt-1">Total Trades</p>
                <div class="flex justify-between mt-4 text-sm">
                    <span class="text-green-600">12 Wins</span>
                    <span class="text-red-600">8 Losses</span>
                    <span class="text-gray-600">4 Pending</span>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Performance Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg" data-aos="fade-right">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800">Account Performance</h2>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>Last 90 Days</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <!-- Account Distribution -->
            <div class="bg-white rounded-2xl p-6 shadow-lg" data-aos="fade-left">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Account Distribution</h2>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="distributionChart"></canvas>
                </div>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-sm">Standard Account</span>
                        </div>
                        <span class="font-semibold">$45,230</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm">ECN Account</span>
                        </div>
                        <span class="font-semibold">$32,890</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                            <span class="text-sm">Islamic Account</span>
                        </div>
                        <span class="font-semibold">$12,450</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MT4 Accounts Section -->
        <div class="mb-8" data-aos="fade-up">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Your MT4 Accounts</h2>
                <button class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 flex items-center" onclick="addAccount()">
                    <i class="fas fa-plus mr-2"></i>
                    Add Account
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Account Card 1 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white opacity-75 text-sm">MT4 Account</p>
                                <p class="text-white font-bold text-xl">12345678</p>
                            </div>
                            <span class="px-3 py-1 bg-green-500 rounded-full text-white text-xs">Connected</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Balance</span>
                                <span class="font-bold text-gray-800">$15,230.50</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Equity</span>
                                <span class="font-bold text-gray-800">$15,890.30</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Profit</span>
                                <span class="font-bold text-green-600">+$659.80</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Leverage</span>
                                <span class="font-bold text-gray-800">1:500</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition">
                                Details
                            </button>
                            <button class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition">
                                Trade
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Account Card 2 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                    <div class="bg-gradient-to-r from-green-600 to-blue-600 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white opacity-75 text-sm">ECN Account</p>
                                <p class="text-white font-bold text-xl">87654321</p>
                            </div>
                            <span class="px-3 py-1 bg-green-500 rounded-full text-white text-xs">Connected</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Balance</span>
                                <span class="font-bold text-gray-800">$32,450.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Equity</span>
                                <span class="font-bold text-gray-800">$31,890.75</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Profit</span>
                                <span class="font-bold text-red-600">-$559.25</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Leverage</span>
                                <span class="font-bold text-gray-800">1:200</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition">
                                Details
                            </button>
                            <button class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition">
                                Trade
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Account Card 3 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                    <div class="bg-gradient-to-r from-yellow-600 to-red-600 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white opacity-75 text-sm">Demo Account</p>
                                <p class="text-white font-bold text-xl">98765432</p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-500 rounded-full text-white text-xs">Demo</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Balance</span>
                                <span class="font-bold text-gray-800">$50,000.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Equity</span>
                                <span class="font-bold text-gray-800">$52,340.50</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Profit</span>
                                <span class="font-bold text-green-600">+$2,340.50</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Leverage</span>
                                <span class="font-bold text-gray-800">1:100</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition">
                                Details
                            </button>
                            <button class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition">
                                Trade
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Trades & Market Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Trades -->
            <div class="bg-white rounded-2xl p-6 shadow-lg" data-aos="fade-right">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Trades</h2>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Symbol</th>
                                <th>Type</th>
                                <th>Volume</th>
                                <th>Profit</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-semibold">EUR/USD</td>
                                <td><span class="status-badge success">Buy</span></td>
                                <td>0.5</td>
                                <td class="text-green-600">+$45.20</td>
                                <td>2 min ago</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">GBP/USD</td>
                                <td><span class="status-badge error">Sell</span></td>
                                <td>1.0</td>
                                <td class="text-red-600">-$23.50</td>
                                <td>15 min ago</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">USD/JPY</td>
                                <td><span class="status-badge success">Buy</span></td>
                                <td>0.3</td>
                                <td class="text-green-600">+$12.80</td>
                                <td>32 min ago</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">XAU/USD</td>
                                <td><span class="status-badge warning">Pending</span></td>
                                <td>0.1</td>
                                <td class="text-gray-600">-</td>
                                <td>1 hour ago</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">AUD/USD</td>
                                <td><span class="status-badge success">Buy</span></td>
                                <td>0.8</td>
                                <td class="text-green-600">+$34.60</td>
                                <td>2 hours ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <a href="#" class="block text-center text-blue-600 hover:text-blue-800 mt-4">
                    View All Trades <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- Market Overview -->
            <div class="bg-white rounded-2xl p-6 shadow-lg" data-aos="fade-left">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Market Overview</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-blue-600">EUR</span>
                            </div>
                            <div>
                                <p class="font-semibold">EUR/USD</p>
                                <p class="text-xs text-gray-500">1.09234</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-green-600 font-semibold">+0.23%</span>
                            <p class="text-xs text-gray-500">+0.0023</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-green-600">GBP</span>
                            </div>
                            <div>
                                <p class="font-semibold">GBP/USD</p>
                                <p class="text-xs text-gray-500">1.26789</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-red-600 font-semibold">-0.12%</span>
                            <p class="text-xs text-gray-500">-0.0015</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-yellow-600">JPY</span>
                            </div>
                            <div>
                                <p class="font-semibold">USD/JPY</p>
                                <p class="text-xs text-gray-500">148.456</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-green-600 font-semibold">+0.45%</span>
                            <p class="text-xs text-gray-500">+0.67</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-purple-600">AUD</span>
                            </div>
                            <div>
                                <p class="font-semibold">AUD/USD</p>
                                <p class="text-xs text-gray-500">0.65789</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-green-600 font-semibold">+0.08%</span>
                            <p class="text-xs text-gray-500">+0.0005</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-red-600">XAU</span>
                            </div>
                            <div>
                                <p class="font-semibold">Gold</p>
                                <p class="text-xs text-gray-500">$2,034.50</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-red-600 font-semibold">-0.34%</span>
                            <p class="text-xs text-gray-500">-$6.90</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                                <span class="font-bold text-gray-600">BTC</span>
                            </div>
                            <div>
                                <p class="font-semibold">BTC/USD</p>
                                <p class="text-xs text-gray-500">$43,245.00</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-green-600 font-semibold">+2.34%</span>
                            <p class="text-xs text-gray-500">+$987</p>
                        </div>
                    </div>
                </div>
                
                <a href="#" class="block text-center text-blue-600 hover:text-blue-800 mt-4">
                    View Full Market <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>© 2024 MT4 Trading Hub. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Check if logged in
        if (!localStorage.getItem('isLoggedIn')) {
            window.location.href = 'login.html';
        }
        
        // Performance Chart
        const ctx1 = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Account 1',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Account 2',
                    data: [8000, 12000, 10000, 18000, 15000, 22000, 20000],
                    borderColor: '#764ba2',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#764ba2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#667eea',
                        borderWidth: 2
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
        
        // Distribution Chart
        const ctx2 = document.getElementById('distributionChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Standard', 'ECN', 'Islamic'],
                datasets: [{
                    data: [45230, 32890, 12450],
                    backgroundColor: ['#667eea', '#764ba2', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return label + ': $' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Sidebar Functions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('hidden');
        }
        
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
        
        // Navigation
        function navigateTo(page) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item
            event.currentTarget.classList.add('active');
            
            // Navigate to page
            switch(page) {
                case 'dashboard':
                    window.location.href = 'dashboard.html';
                    break;
                case 'accounts':
                    window.location.href = 'accounts.html';
                    break;
                case 'trading':
                    window.location.href = 'trading.html';
                    break;
                case 'history':
                    window.location.href = 'history.html';
                    break;
                case 'analytics':
                    window.location.href = 'analytics.html';
                    break;
                default:
                    console.log('Page not found');
            }
        }
        
        // Add Account
        function addAccount() {
            window.location.href = 'accounts.html';
        }
        
        // Logout
        function logout() {
            // Clear localStorage
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('username');
            
            // Redirect to login
            window.location.href = 'login.html';
        }
        
        // Theme Toggle
        function toggleTheme() {
            const icon = document.querySelector('.theme-toggle i');
            
            if (icon.classList.contains('fa-moon')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                // Enable dark mode
                document.body.style.background = '#1a202c';
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                // Disable dark mode
                document.body.style.background = '#f3f4f6';
            }
        }
        
        // Real-time updates simulation
        setInterval(() => {
            // Update stats randomly
            const stats = document.querySelectorAll('.stat-card h3');
            stats.forEach(stat => {
                if (Math.random() > 0.7) {
                    const currentValue = parseFloat(stat.textContent.replace(/[^0-9.-]+/g, ''));
                    const change = (Math.random() * 100 - 50).toFixed(2);
                    const newValue = (currentValue + parseFloat(change)).toFixed(2);
                    
                    if (stat.textContent.includes('$')) {
                        stat.textContent = '$' + newValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    } else {
                        stat.textContent = newValue;
                    }
                }
            });
        }, 5000);
        
        // Market ticker update
        setInterval(() => {
            const tickerItems = document.querySelectorAll('.market-ticker .ticker-item');
            tickerItems.forEach(item => {
                const priceSpan = item.querySelector('span:last-child');
                if (priceSpan) {
                    const currentValue = parseFloat(priceSpan.textContent);
                    const change = (Math.random() * 0.5 - 0.25).toFixed(2);
                    const newValue = (currentValue + parseFloat(change)).toFixed(2);
                    priceSpan.textContent = (change > 0 ? '+' : '') + newValue + '%';
                    
                    if (change > 0) {
                        item.classList.remove('down');
                        item.classList.add('up');
                    } else {
                        item.classList.remove('up');
                        item.classList.add('down');
                    }
                }
            });
        }, 3000);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + K for search
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-bar input').focus();
            }
            
            // Escape to close sidebar on mobile
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('mobile-open')) {
                    toggleSidebar();
                }
            }
        });
        
        // Loading skeleton effect
        window.addEventListener('load', function() {
            document.querySelectorAll('.stat-card').forEach(card => {
                card.classList.remove('skeleton');
            });
        });
    </script>
</body>
</html>