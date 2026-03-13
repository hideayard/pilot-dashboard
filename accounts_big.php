<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT4 Accounts - Trading Hub</title>
    
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
        
        /* Account Card */
        .account-card {
            background: white;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .account-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(102, 126, 234, 0.25);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Status Badge */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        
        .status-badge.connected {
            background: #10b981;
            color: white;
        }
        
        .status-badge.disconnected {
            background: #ef4444;
            color: white;
        }
        
        /* Metric Item */
        .metric-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .metric-item:last-child {
            border-bottom: none;
        }
        
        .metric-label {
            color: #6b7280;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .metric-value {
            font-weight: 600;
            font-size: 1rem;
        }
        
        /* Profit Colors */
        .profit-positive {
            color: #10b981;
        }
        
        .profit-negative {
            color: #ef4444;
        }
        
        .floating-positive {
            color: #84cc16;
        }
        
        .floating-negative {
            color: #ef4444;
        }
        
        /* Divider */
        .card-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, #764ba2, #667eea, transparent);
            margin: 1rem 0;
        }
        
        /* Action Buttons */
        .action-buy {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.5);
        }
        
        .action-sell {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-sell:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.5);
        }
        
        .action-close {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-close:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.5);
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
        }
        
        .menu-item i {
            margin-right: 1rem;
            font-size: 1.1rem;
            width: 24px;
        }
        
        .menu-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
        }
        
        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            flex: 1;
            padding: 0.25rem 0.5rem;
        }
        
        /* Filter Buttons */
        .filter-btn {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: white;
            color: #4b5563;
        }
        
        .filter-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
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
        
        /* Badge */
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
        
        /* Pulse Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
                <span class="logo-text text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">MT4 Hub</span>
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
            <div class="menu-item" onclick="navigateTo('dashboard')">
                <i class="fas fa-home"></i>
                <span class="sidebar-text">Dashboard</span>
            </div>
            <div class="menu-item active" onclick="navigateTo('accounts')">
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
            <p class="text-xs opacity-90 mb-2">Get advanced features</p>
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
                    <input type="text" placeholder="Search accounts...">
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-xl cursor-pointer"></i>
                    <span class="badge">5</span>
                </div>
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="w-10 h-10 rounded-full cursor-pointer border-2 border-transparent hover:border-blue-600 transition-all">
            </div>
        </div>
        
        <!-- Page Title -->
        <div class="mb-8" data-aos="fade-up">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">MT4 Accounts</h1>
                    <p class="text-gray-600 mt-2">Manage and monitor all your trading accounts</p>
                </div>
                <button class="mt-4 md:mt-0 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 flex items-center gap-2 font-semibold" onclick="addNewAccount()">
                    <i class="fas fa-plus"></i>
                    Add New Account
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-8" data-aos="fade-up" data-aos-delay="100">
            <button class="filter-btn active">All Accounts</button>
            <button class="filter-btn">Connected</button>
            <button class="filter-btn">Disconnected</button>
            <button class="filter-btn">Demo</button>
            <button class="filter-btn">Real</button>
        </div>
        
        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="accountsGrid">
            <!-- Account Card 1 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#MT4-123456</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-xs mr-1 animate-pulse"></i>
                            Connected
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">Quantum Scalper Pro</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">12</span> orders (2.5 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">8</span> orders (1.8 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$1,234.50</span>
                            <span class="text-sm text-gray-500 ml-1">(+12.4%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$12,450.80</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$13,890.30</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$234.50</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy" onclick="executeTrade('buy', 'MT4-123456')">
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="executeTrade('sell', 'MT4-123456')">
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="closePositions('MT4-123456')">
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Account Card 2 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#MT4-789012</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-xs mr-1 animate-pulse"></i>
                            Connected
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">Forex Master EA</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">24</span> orders (5.2 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">15</span> orders (3.4 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$3,450.75</span>
                            <span class="text-sm text-gray-500 ml-1">(+18.2%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$22,890.45</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$26,341.20</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$890.45</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy" onclick="executeTrade('buy', 'MT4-789012')">
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="executeTrade('sell', 'MT4-789012')">
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="closePositions('MT4-789012')">
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Account Card 3 - Negative Floating -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#MT4-345678</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-xs mr-1 animate-pulse"></i>
                            Connected
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">Trend Hunter Pro</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">6</span> orders (1.2 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">18</span> orders (4.5 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-negative font-bold">-$890.25</span>
                            <span class="text-sm text-gray-500 ml-1">(-5.8%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$15,450.00</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$14,559.75</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-negative font-bold">-$890.25</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy" onclick="executeTrade('buy', 'MT4-345678')">
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="executeTrade('sell', 'MT4-345678')">
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="closePositions('MT4-345678')">
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Account Card 4 - Demo Account -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="500">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#DEMO-901234</h3>
                        </div>
                        <span class="status-badge connected" style="background: #8b5cf6;">
                            <i class="fas fa-flask text-xs mr-1"></i>
                            Demo
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">AI Scalper Test</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">45</span> orders (9.8 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">32</span> orders (7.2 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$5,670.00</span>
                            <span class="text-sm text-gray-500 ml-1">(+22.7%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$30,000.00</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$35,670.00</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$5,670.00</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy" onclick="executeTrade('buy', 'DEMO-901234')">
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="executeTrade('sell', 'DEMO-901234')">
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="closePositions('DEMO-901234')">
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Account Card 5 - Disconnected -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="600">
                <div class="card-header" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#MT4-567890</h3>
                        </div>
                        <span class="status-badge disconnected">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            Disconnected
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">Grid Master Pro</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">0</span> orders (0.0 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">0</span> orders (0.0 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-gray-600">$0.00</span>
                            <span class="text-sm text-gray-500 ml-1">(0.0%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$8,500.00</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$8,500.00</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value font-bold text-gray-600">$0.00</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Account Card 6 - High Profit -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="700">
                <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-sm">Account ID</p>
                            <h3 class="text-white font-bold text-2xl">#MT4-112233</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-xs mr-1 animate-pulse"></i>
                            Connected
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-white opacity-75 text-sm">Bot Name</p>
                        <p class="text-white font-semibold text-lg">Gold Hunter Pro</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Buy Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-blue-600">32</span> orders (8.5 lot)
                        </span>
                    </div>
                    
                    <!-- Sell Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="font-bold text-red-600">28</span> orders (7.2 lot)
                        </span>
                    </div>
                    
                    <!-- Profit Section -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$8,945.30</span>
                            <span class="text-sm text-gray-500 ml-1">(+32.8%)</span>
                        </span>
                    </div>
                    
                    <!-- Balance -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$27,250.00</span>
                    </div>
                    
                    <!-- Equity -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$36,195.30</span>
                    </div>
                    
                    <!-- Floating -->
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$8,945.30</span>
                    </div>
                    
                    <!-- Divider -->
                    <div class="card-divider"></div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-4">
                        <button class="action-buy" onclick="executeTrade('buy', 'MT4-112233')">
                            <i class="fas fa-arrow-up"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="executeTrade('sell', 'MT4-112233')">
                            <i class="fas fa-arrow-down"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="closePositions('MT4-112233')">
                            <i class="fas fa-times"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8" data-aos="fade-up" data-aos-delay="800">
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <p class="text-gray-600 text-sm mb-2">Total Accounts</p>
                <p class="text-3xl font-bold text-gray-800">6</p>
                <p class="text-sm text-green-600 mt-2">+2 this month</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <p class="text-gray-600 text-sm mb-2">Total Balance</p>
                <p class="text-3xl font-bold text-gray-800">$116,541.25</p>
                <p class="text-sm text-green-600 mt-2">Across all accounts</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <p class="text-gray-600 text-sm mb-2">Total Profit</p>
                <p class="text-3xl font-bold text-green-600">+$18,410.30</p>
                <p class="text-sm text-gray-600 mt-2">+15.8% overall</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <p class="text-gray-600 text-sm mb-2">Active Bots</p>
                <p class="text-3xl font-bold text-gray-800">5/6</p>
                <p class="text-sm text-blue-600 mt-2">83% active</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>© 2024 MT4 Trading Hub. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <!-- Trade Confirmation Modal -->
    <div id="tradeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Confirm Trade</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="mb-6">
                <!-- Dynamic content will be inserted here -->
            </div>
            <div class="flex gap-3">
                <button class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition" onclick="closeModal()">
                    Cancel
                </button>
                <button id="confirmBtn" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    
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
        
        // Logout
        function logout() {
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('username');
            window.location.href = 'login.html';
        }
        
        // Add New Account
        function addNewAccount() {
            alert('Redirecting to add account page...');
            // window.location.href = 'add-account.html';
        }
        
        // Execute Trade
        function executeTrade(type, accountId) {
            const modal = document.getElementById('tradeModal');
            const modalContent = document.getElementById('modalContent');
            const confirmBtn = document.getElementById('confirmBtn');
            
            const action = type.charAt(0).toUpperCase() + type.slice(1);
            const color = type === 'buy' ? 'blue' : 'red';
            
            modalContent.innerHTML = `
                <div class="text-center mb-4">
                    <div class="w-16 h-16 mx-auto bg-${color}-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-arrow-${type === 'buy' ? 'up' : 'down'} text-3xl text-${color}-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">${action} Order</h4>
                    <p class="text-gray-600">Account: <span class="font-semibold">${accountId}</span></p>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Volume (Lots)</label>
                        <input type="number" value="0.1" step="0.01" min="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-${color}-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stop Loss</label>
                        <input type="number" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Take Profit</label>
                        <input type="number" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    </div>
                </div>
            `;
            
            confirmBtn.className = `flex-1 bg-gradient-to-r from-${color}-600 to-${color}-700 text-white py-2 rounded-xl hover:from-${color}-700 hover:to-${color}-800 transition`;
            confirmBtn.onclick = () => {
                alert(`${action} order placed successfully!`);
                closeModal();
            };
            
            modal.classList.remove('hidden');
        }
        
        // Close Positions
        function closePositions(accountId) {
            const modal = document.getElementById('tradeModal');
            const modalContent = document.getElementById('modalContent');
            const confirmBtn = document.getElementById('confirmBtn');
            
            modalContent.innerHTML = `
                <div class="text-center mb-4">
                    <div class="w-16 h-16 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-times text-3xl text-purple-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Close All Positions</h4>
                    <p class="text-gray-600">Account: <span class="font-semibold">${accountId}</span></p>
                    <p class="text-sm text-gray-500 mt-2">This will close all open positions for this account</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Are you sure you want to close all positions?
                    </p>
                </div>
            `;
            
            confirmBtn.className = 'flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white py-2 rounded-xl hover:from-purple-700 hover:to-purple-800 transition';
            confirmBtn.onclick = () => {
                alert('All positions closed successfully!');
                closeModal();
            };
            
            modal.classList.remove('hidden');
        }
        
        // Close Modal
        function closeModal() {
            document.getElementById('tradeModal').classList.add('hidden');
        }
        
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter accounts logic here
                const filter = this.textContent.trim();
                console.log('Filtering by:', filter);
            });
        });
        
        // Search functionality
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.account-card');
            
            cards.forEach(card => {
                const accountId = card.querySelector('h3').textContent.toLowerCase();
                const botName = card.querySelector('.text-white.font-semibold').textContent.toLowerCase();
                
                if (accountId.includes(searchTerm) || botName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Real-time updates simulation
        setInterval(() => {
            // Update floating values randomly
            document.querySelectorAll('.floating-positive, .floating-negative').forEach(el => {
                if (Math.random() > 0.7) {
                    const currentValue = parseFloat(el.textContent.replace(/[^0-9.-]+/g, ''));
                    const change = (Math.random() * 100 - 50).toFixed(2);
                    const newValue = (currentValue + parseFloat(change)).toFixed(2);
                    
                    if (newValue > 0) {
                        el.className = 'metric-value floating-positive font-bold';
                        el.textContent = '+' + newValue;
                    } else {
                        el.className = 'metric-value floating-negative font-bold';
                        el.textContent = newValue;
                    }
                }
            });
        }, 5000);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + K for search
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-bar input').focus();
            }
            
            // Escape to close modal
            if (e.key === 'Escape') {
                closeModal();
            }
            
            // Escape to close sidebar on mobile
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('mobile-open')) {
                    toggleSidebar();
                }
            }
        });
    </script>
</body>
</html>