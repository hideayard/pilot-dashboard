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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            width: 260px;
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
            margin-left: 260px;
            transition: all 0.3s ease;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Account Card */
        .account-card {
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid rgba(102, 126, 234, 0.1);
            height: fit-content;
        }

        .account-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(102, 126, 234, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
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
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .card-body {
            padding: 1rem;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
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
            padding: 0.4rem 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.85rem;
        }

        .metric-item:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: #6b7280;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .metric-label i {
            font-size: 0.7rem;
        }

        .metric-value {
            font-weight: 600;
            font-size: 0.85rem;
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
            height: 1px;
            background: linear-gradient(90deg, transparent, #667eea, #764ba2, #667eea, transparent);
            margin: 0.75rem 0;
        }

        /* Action Buttons */
        .action-buy,
        .action-sell,
        .action-close {
            padding: 0.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .action-buy {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .action-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px -3px rgba(59, 130, 246, 0.5);
        }

        .action-sell {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .action-sell:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px -3px rgba(239, 68, 68, 0.5);
        }

        .action-close {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }

        .action-close:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px -3px rgba(139, 92, 246, 0.5);
        }

        /* Disabled button */
        .action-buy:disabled,
        .action-sell:disabled,
        .action-close:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Menu Item */
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            color: #4a5568;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            margin: 0.2rem 0.8rem;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .menu-item i {
            margin-right: 0.8rem;
            font-size: 1rem;
            width: 20px;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(3px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Header */
        .header {
            background: white;
            border-radius: 1.25rem;
            padding: 0.75rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        /* Search Bar */
        .search-bar {
            background: #f7fafc;
            border-radius: 1.5rem;
            padding: 0.4rem 1rem;
            display: flex;
            align-items: center;
            width: 250px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .search-bar input {
            font-size: 0.85rem;
            padding: 0.2rem 0.4rem;
            background: transparent;
            border: none;
            outline: none;
            flex: 1;
        }

        .search-bar:focus-within {
            border-color: #667eea;
            background: white;
        }

        /* Filter Buttons */
        .filter-btn {
            padding: 0.35rem 0.9rem;
            border-radius: 1.5rem;
            font-size: 0.8rem;
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
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Badge */
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 9999px;
            min-width: 18px;
            text-align: center;
        }

        /* Summary Cards */
        .summary-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        /* Grid for cards */
        .accounts-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .accounts-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .accounts-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .accounts-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (min-width: 1536px) {
            .accounts-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            backdrop-filter: blur(4px);
        }

        .modal-container {
            background: white;
            border-radius: 1.25rem;
            max-width: 400px;
            width: 100%;
            padding: 1.5rem;
            transform: scale(0.9);
            animation: modalPop 0.3s ease forwards;
        }

        @keyframes modalPop {
            to {
                transform: scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .modal-close:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .modal-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .modal-icon.buy {
            background: #dbeafe;
            color: #3b82f6;
        }

        .modal-icon.sell {
            background: #fee2e2;
            color: #ef4444;
        }

        .modal-icon.close {
            background: #ede9fe;
            color: #8b5cf6;
        }

        .modal-account-info {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 0.75rem;
            margin: 1rem 0;
            text-align: center;
        }

        .modal-account-id {
            font-weight: 700;
            color: #1f2937;
        }

        .modal-bot-name {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .modal-btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px -3px rgba(102, 126, 234, 0.5);
        }

        .modal-btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
        }

        .modal-btn-secondary:hover {
            background: #e5e7eb;
        }

        .modal-btn-buy {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .modal-btn-sell {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .modal-btn-close {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }

        .trade-summary {
            background: #f3f4f6;
            border-radius: 0.75rem;
            padding: 0.75rem;
            margin: 1rem 0;
            font-size: 0.875rem;
        }

        .trade-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #d1d5db;
        }

        .trade-summary-item:last-child {
            border-bottom: none;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 200;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 300px;
            border-left: 4px solid #10b981;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
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
                gap: 0.75rem;
            }

            .search-bar {
                width: 100%;
            }

            .accounts-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        /* Loading Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <span class="logo-text text-base font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">MT4 Hub</span>
            </div>
        </div>

        <!-- User Info -->
        <div class="p-3 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <div class="avatar">JD</div>
                <div class="sidebar-text">
                    <p class="font-semibold text-gray-800 text-sm">John Doe</p>
                    <p class="text-xs text-gray-500">Premium Trader</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-4">
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

            <div class="border-t border-gray-200 my-3"></div>

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
        <div class="absolute bottom-3 left-3 right-3 p-3 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white sidebar-text">
            <p class="text-xs font-semibold mb-1">Upgrade to Pro</p>
            <p class="text-xs opacity-90 mb-1">Get advanced features</p>
            <button class="bg-white text-blue-600 text-xs px-2 py-1 rounded-full font-semibold">
                Upgrade
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header" data-aos="fade-down">
            <div class="flex items-center space-x-3">
                <button class="md:hidden text-gray-600" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <button class="hidden md:block text-gray-600" onclick="toggleSidebarCollapse()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="search-bar">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search accounts...">
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-base cursor-pointer"></i>
                    <span class="badge">5</span>
                </div>
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="w-8 h-8 rounded-full cursor-pointer border-2 border-transparent hover:border-blue-600">
            </div>
        </div>

        <!-- Page Title -->
        <div class="mb-6" data-aos="fade-up">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">MT4 Accounts</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage and trade on all your accounts</p>
                </div>
                <button class="mt-3 md:mt-0 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 flex items-center gap-2 text-sm font-semibold" onclick="addNewAccount()">
                    <i class="fas fa-plus"></i>
                    Add Account
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2 mb-6" data-aos="fade-up" data-aos-delay="50" id="filterBar">
            <button class="filter-btn active" data-filter="all">All (<span id="fc-all">0</span>)</button>
            <button class="filter-btn" data-filter="connected">Connected (<span id="fc-connected">0</span>)</button>
            <button class="filter-btn" data-filter="disconnected">Disconnected (<span id="fc-disconnected">0</span>)</button>
            <button class="filter-btn" data-filter="demo">Demo (<span id="fc-demo">0</span>)</button>
            <button class="filter-btn" data-filter="real">Real (<span id="fc-real">0</span>)</button>
        </div>

        <!-- Accounts Grid -->
        <div class="accounts-grid" id="accountsGrid">
            <!-- Loading skeleton -->
            <div id="accounts-loading" class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                <div class="spinner mb-3" style="width:36px;height:36px;border-width:3px;"></div>
                <p class="text-sm">Loading accounts from server...</p>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6" data-aos="fade-up" data-aos-delay="400">
            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Accounts</p>
                <p class="text-xl font-bold text-gray-800" id="stat-total">–</p>
                <p class="text-[0.6rem] text-green-600 mt-1" id="stat-total-sub"></p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Balance</p>
                <p class="text-xl font-bold text-gray-800" id="stat-balance">–</p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Profit</p>
                <p class="text-xl font-bold" id="stat-profit">–</p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Active Bots</p>
                <p class="text-xl font-bold text-gray-800" id="stat-active">–</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-gray-400 text-xs">
            <p>© 2024 MT4 Trading Hub. All rights reserved.</p>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- Buy Modal -->
    <div id="buyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Open Buy Trade</h3>
                <button class="modal-close" onclick="closeBuyModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-icon buy">
                <i class="fas fa-arrow-up"></i>
            </div>

            <div class="modal-account-info" id="buyAccountInfo">
                <!-- Will be filled by JavaScript -->
            </div>

            <div class="trade-summary">
                <div class="trade-summary-item">
                    <span class="text-gray-600">Order Type:</span>
                    <span class="font-semibold text-blue-600">BUY (Market)</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Volume:</span>
                    <span class="font-semibold">0.1 lot (default)</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Est. Margin:</span>
                    <span class="font-semibold">$50.00</span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeBuyModal()">
                    Cancel
                </button>
                <button class="modal-btn modal-btn-buy" onclick="confirmBuyTrade()">
                    <i class="fas fa-check mr-1"></i>
                    Confirm Buy
                </button>
            </div>
        </div>
    </div>

    <!-- Sell Modal -->
    <div id="sellModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Open Sell Trade</h3>
                <button class="modal-close" onclick="closeSellModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-icon sell">
                <i class="fas fa-arrow-down"></i>
            </div>

            <div class="modal-account-info" id="sellAccountInfo">
                <!-- Will be filled by JavaScript -->
            </div>

            <div class="trade-summary">
                <div class="trade-summary-item">
                    <span class="text-gray-600">Order Type:</span>
                    <span class="font-semibold text-red-600">SELL (Market)</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Volume:</span>
                    <span class="font-semibold">0.1 lot (default)</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Est. Margin:</span>
                    <span class="font-semibold">$50.00</span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeSellModal()">
                    Cancel
                </button>
                <button class="modal-btn modal-btn-sell" onclick="confirmSellTrade()">
                    <i class="fas fa-check mr-1"></i>
                    Confirm Sell
                </button>
            </div>
        </div>
    </div>

    <!-- Close Positions Modal -->
    <div id="closeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Close All Positions</h3>
                <button class="modal-close" onclick="closeCloseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-icon close">
                <i class="fas fa-times"></i>
            </div>

            <div class="modal-account-info" id="closeAccountInfo">
                <!-- Will be filled by JavaScript -->
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                This will close ALL open positions for this account. This action cannot be undone.
            </div>

            <div class="trade-summary">
                <div class="trade-summary-item">
                    <span class="text-gray-600">Open Positions:</span>
                    <span class="font-semibold" id="openPositionsCount">20</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Total Volume:</span>
                    <span class="font-semibold" id="totalVolume">4.3 lot</span>
                </div>
                <div class="trade-summary-item">
                    <span class="text-gray-600">Current Floating:</span>
                    <span class="font-semibold text-green-600" id="currentFloating">+$234</span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeCloseModal()">
                    Cancel
                </button>
                <button class="modal-btn modal-btn-close" onclick="confirmClosePositions()">
                    <i class="fas fa-check mr-1"></i>
                    Close All
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="flex items-start gap-3">
            <div class="text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800" id="toastTitle">Success</p>
                <p class="text-sm text-gray-600" id="toastMessage">Trade executed successfully</p>
            </div>
        </div>
    </div>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // ─── GLOBALS ────────────────────────────────────────────────────────────────
        let accountsData  = [];   // raw server data
        let activeFilter  = 'all';
        let searchTerm    = '';

        // Card header gradient palette (cycles through accounts)
        const CARD_GRADIENTS = [
            'linear-gradient(135deg,#667eea 0%,#764ba2 100%)',
            'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)',
            'linear-gradient(135deg,#ef4444 0%,#dc2626 100%)',
            'linear-gradient(135deg,#10b981 0%,#059669 100%)',
            'linear-gradient(135deg,#6b7280 0%,#4b5563 100%)',
            'linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)',
            'linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)',
            'linear-gradient(135deg,#ec4899 0%,#be185d 100%)',
        ];

        // ─── JWT UTILS ───────────────────────────────────────────────────────────────
        function parseJwt(token) {
            try {
                const base64Url = token.split('.')[1];
                const base64    = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                return JSON.parse(atob(base64));
            } catch (e) {
                return null;
            }
        }

        function getUserIdFromJwt() {
            const token   = localStorage.getItem('jwt');
            if (!token) return null;
            const payload = parseJwt(token);
            // Support common JWT claim names for user id
            return payload?.data?.user_id ?? payload?.data?.id ?? payload?.data?.sub ?? null;
        }

        // ─── AUTH CHECK ──────────────────────────────────────────────────────────────
        (function() {
            try {
                const token = localStorage.getItem('jwt');
                const user  = localStorage.getItem('user');
                if (!token || !user) {
                    window.location.href = '/auth/login.php';
                    return;
                }
            } catch (e) {
                window.location.href = '/auth/login.php';
            }
        })();

        // ─── INIT AOS ────────────────────────────────────────────────────────────────
        AOS.init({ duration: 600, once: true });

        // ─── POPULATE SIDEBAR USER ───────────────────────────────────────────────────
        (function() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const name = user.name || user.username || 'User';
            function initials(n) { return n.split(' ').map(w => w[0]).join('').toUpperCase().substring(0,2); }
            const av = document.querySelector('.avatar');
            if (av) av.textContent = initials(name);
            const nm = document.querySelector('.sidebar-text .font-semibold');
            if (nm) nm.textContent = name;
            const rl = document.querySelector('.sidebar-text .text-xs');
            if (rl) rl.textContent = user.user_tipe || 'Trader';
        })();

        // ─── SERVER FETCH ────────────────────────────────────────────────────────────
        async function fetchAccountsFromServer() {
            const authToken = localStorage.getItem('jwt');
            if (!authToken) {
                console.error('No authentication token available (key: jwt)');
                throw new Error('Please login first');
            }

            // Decode user_id from JWT payload
            const userId = getUserIdFromJwt();
            if (!userId) {
                console.warn('Could not decode user_id from JWT');
            }

            console.log('Fetching accounts from server for user_id:', userId);

            const body = new URLSearchParams({ action: 'get_accounts_by_user'});//, user_id: userId });
            if (userId) body.append('user_id', userId);

            const response = await fetch('/proxy2.php', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': `Bearer ${authToken}`,
                },
                body,
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch accounts: ${response.status}`);
            }

            const result = await response.json();

            // Response shape: { status:"success", data:{ user:{}, summary:{}, accounts:[] } }
            if (result.status === 'success' && result.data) {
                const accounts = result.data.accounts || [];
                const summary  = result.data.summary  || null;

                console.log(`Fetched ${accounts.length} accounts from server`);
                accountsData = accounts;

                if (summary) updateSummaryFromServer(summary);
                return accounts;
            } else {
                throw new Error(result.message || result.error || 'Failed to fetch accounts from server');
            }
        }

        // ─── UPDATE SUMMARY STATS ────────────────────────────────────────────────────
        function updateSummaryFromServer(summary) {
            // summary fields: total_accounts, total_balance, total_profit,
            //                 avg_profit_percentage, active_accounts
            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

            const total      = summary.total_accounts  ?? accountsData.length;
            const balance    = summary.total_balance   ?? null;
            const profit     = summary.total_profit    ?? null;
            const activeBots = summary.active_accounts ?? null;

            set('stat-total',   total);

            if (balance !== null) set('stat-balance', formatMoney(balance));

            if (profit !== null) {
                const el = document.getElementById('stat-profit');
                if (el) {
                    el.textContent = (profit >= 0 ? '+' : '') + formatMoney(profit);
                    el.className   = 'text-xl font-bold ' + (profit >= 0 ? 'text-green-600' : 'text-red-600');
                }
            }

            if (activeBots !== null) set('stat-active', `${activeBots}/${total}`);

            // Update filter counts from live account list
            updateFilterCounts(accountsData);
        }

        // ─── FILTER COUNT HELPER ─────────────────────────────────────────────────────
        function updateFilterCounts(accounts) {
            let connected = 0, disconnected = 0, demo = 0, real = 0;
            accounts.forEach(acc => {
                const status = (acc.status || '').toLowerCase();
                const type   = (acc.account_type || '').toLowerCase();
                const isConn = status === 'active' || status === 'connected' || status === 'live';
                if (isConn) connected++; else disconnected++;
                if (type === 'demo') demo++; else real++;
            });
            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            set('fc-all',          accounts.length);
            set('fc-connected',    connected);
            set('fc-disconnected', disconnected);
            set('fc-demo',         demo);
            set('fc-real',         real);
        }

        // ─── COMPUTE SUMMARY FROM LOCAL DATA ────────────────────────────────────────
        function computeSummaryLocal(accounts) {
            // Use exact field names from proxy2 response
            let totalBalance = 0, totalProfit = 0, activeBots = 0;

            accounts.forEach(acc => {
                const status     = (acc.status || '').toLowerCase();
                const balance    = parseFloat(acc.account_balance || 0);
                const profit     = parseFloat(acc.total_profit    || 0);
                const isActive   = status === 'active' || status === 'connected' || status === 'live';

                totalBalance += balance;
                totalProfit  += profit;
                if (isActive) activeBots++;
            });

            updateFilterCounts(accounts);

            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            set('stat-total',   accounts.length);
            set('stat-balance', formatMoney(totalBalance));
            set('stat-active',  `${activeBots}/${accounts.length}`);

            const profitEl = document.getElementById('stat-profit');
            if (profitEl) {
                profitEl.textContent = (totalProfit >= 0 ? '+' : '') + formatMoney(totalProfit);
                profitEl.className   = 'text-xl font-bold ' + (totalProfit >= 0 ? 'text-green-600' : 'text-red-600');
            }
        }

        // ─── FORMAT HELPERS ──────────────────────────────────────────────────────────
        function formatMoney(val) {
            const n = parseFloat(val) || 0;
            if (Math.abs(n) >= 1000000) return '$' + (n / 1000000).toFixed(2) + 'M';
            if (Math.abs(n) >= 1000)    return '$' + (n / 1000).toFixed(1) + 'k';
            return '$' + n.toFixed(2);
        }

        // ─── RENDER CARDS ────────────────────────────────────────────────────────────
        function buildAccountCard(acc, index) {
            // Exact field names from proxy2 response
            const accountId   = acc.account_id   || `ACC-${index + 1}`;
            const botName     = acc.bot_name      || 'N/A';
            const balance     = parseFloat(acc.account_balance         || 0);
            const equity      = parseFloat(acc.account_equity          || 0);
            const profit      = parseFloat(acc.total_profit            || 0);
            const profitPct   = parseFloat(acc.total_profit_percentage || 0);
            const floating    = parseFloat(acc.floating_value          || 0);
            const buyCount    = parseInt  (acc.buy_order_count         || 0);
            const sellCount   = parseInt  (acc.sell_order_count        || 0);
            const buyLot      = parseFloat(acc.total_buy_lot           || 0);
            const sellLot     = parseFloat(acc.total_sell_lot          || 0);
            const leverage    = acc.leverage     || '–';
            const currency    = acc.currency     || 'USD';
            const broker      = acc.broker       || acc.server || '–';
            const server      = acc.server       || '–';
            const accountType = (acc.account_type || '').toLowerCase();
            const status      = (acc.status       || '').toLowerCase();
            const lastSync    = acc.last_sync     || acc.last_connected || null;
            const totalOrders = acc.total_orders  || (buyCount + sellCount);

            const isActive    = status === 'active' || status === 'connected' || status === 'live';
            const isDemo      = accountType === 'demo';
            const isOffline   = !isActive;

            const gradient    = CARD_GRADIENTS[index % CARD_GRADIENTS.length];

            // Status badge
            let statusBadge;
            if (isDemo) {
                statusBadge = `<span class="status-badge" style="background:#8b5cf6;"><i class="fas fa-flask text-[0.4rem] mr-1"></i>Demo</span>`;
            } else if (isActive) {
                statusBadge = `<span class="status-badge connected"><i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>Live</span>`;
            } else {
                statusBadge = `<span class="status-badge disconnected"><i class="fas fa-circle text-[0.4rem] mr-1"></i>Offline</span>`;
            }

            const profitClass = profit >= 0 ? 'profit-positive' : 'profit-negative';
            const profitSign  = profit >= 0 ? '+' : '';
            const floatClass  = floating >= 0 ? 'floating-positive' : 'floating-negative';
            const floatSign   = floating >= 0 ? '+' : '';

            const btnDisabled = isOffline ? 'disabled' : '';
            const btnOpacity  = isOffline ? 'opacity-40 cursor-not-allowed' : '';

            const safeId      = String(accountId).replace(/['"]/g, '');
            const safeBot     = botName.replace(/['"]/g, '');
            const safeBalance = formatMoney(balance).replace(/['"]/g, '');
            const safeType    = isDemo ? 'Demo' : 'Live';

            const lastSyncStr = lastSync
                ? new Date(lastSync).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                : '–';

            return `
            <div class="account-card"
                 data-status="${isActive ? 'connected' : 'disconnected'}"
                 data-type="${isDemo ? 'demo' : 'real'}"
                 data-id="${safeId.toLowerCase()}"
                 data-bot="${safeBot.toLowerCase()}">
                <div class="card-header" style="background:${gradient};">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id text-sm">#${safeId}</h3>
                        </div>
                        ${statusBadge}
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name text-sm">${botName}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-white opacity-60 text-[0.65rem]">
                        <span><i class="fas fa-building mr-1"></i>${broker}</span>
                        <span>•</span>
                        <span>1:${leverage}</span>
                        <span>•</span>
                        <span>${currency}</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-arrow-up text-blue-600"></i> Buy</span>
                        <span class="metric-value"><span class="text-blue-600 font-bold">${buyCount}</span> (${buyLot.toFixed(1)} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-arrow-down text-red-600"></i> Sell</span>
                        <span class="metric-value"><span class="text-red-600 font-bold">${sellCount}</span> (${sellLot.toFixed(1)} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-chart-line text-purple-600"></i> Profit</span>
                        <span class="metric-value">
                            <span class="${profitClass} font-bold">${profitSign}${formatMoney(profit)}</span>
                            <span class="text-[0.6rem] text-gray-500">(${profitPct.toFixed(1)}%)</span>
                        </span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-wallet text-green-600"></i> Balance</span>
                        <span class="metric-value font-bold">${formatMoney(balance)}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-coins text-yellow-600"></i> Equity</span>
                        <span class="metric-value font-bold">${formatMoney(equity)}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-water text-blue-600"></i> Floating</span>
                        <span class="metric-value ${floatClass} font-bold">${floatSign}${formatMoney(floating)}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-list text-gray-400"></i> Orders</span>
                        <span class="metric-value text-gray-600">${totalOrders} total</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="text-[0.6rem] text-gray-400 text-right mb-1">
                        <i class="fas fa-sync-alt mr-1"></i>Sync: ${lastSyncStr}
                    </div>

                    <div class="flex gap-1.5">
                        <button class="action-buy ${btnOpacity}" ${btnDisabled}
                            onclick="openBuyModal('${safeId}','${safeBot}','${safeBalance}','${safeType}')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i> Buy
                        </button>
                        <button class="action-sell ${btnOpacity}" ${btnDisabled}
                            onclick="openSellModal('${safeId}','${safeBot}','${safeBalance}','${safeType}')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i> Sell
                        </button>
                        <button class="action-close ${btnOpacity}" ${btnDisabled}
                            onclick="openCloseModal('${safeId}','${safeBot}')">
                            <i class="fas fa-times text-[0.6rem]"></i> Close
                        </button>
                    </div>
                </div>
            </div>`;
        }

        function renderCards(accounts) {
            const grid    = document.getElementById('accountsGrid');
            const loading = document.getElementById('accounts-loading');
            if (loading) loading.remove();

            if (!accounts || accounts.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                        <i class="fas fa-wallet text-4xl mb-3"></i>
                        <p class="text-sm">No accounts found</p>
                    </div>`;
                return;
            }

            grid.innerHTML = accounts.map((acc, i) => buildAccountCard(acc, i)).join('');
        }

        // ─── FILTER + SEARCH ────────────────────────────────────────────────────────
        function applyFilterAndSearch() {
            const cards = document.querySelectorAll('.account-card');
            cards.forEach(card => {
                const statusMatch = activeFilter === 'all'
                    || (activeFilter === 'connected'    && card.dataset.status === 'connected')
                    || (activeFilter === 'disconnected' && card.dataset.status === 'disconnected')
                    || (activeFilter === 'demo'         && card.dataset.type   === 'demo')
                    || (activeFilter === 'real'         && card.dataset.type   === 'real');

                const searchMatch = !searchTerm
                    || card.dataset.id.includes(searchTerm)
                    || card.dataset.bot.includes(searchTerm);

                card.style.display = (statusMatch && searchMatch) ? '' : 'none';
            });
        }

        // Filter buttons
        document.getElementById('filterBar').addEventListener('click', function(e) {
            const btn = e.target.closest('.filter-btn');
            if (!btn) return;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeFilter = btn.dataset.filter;
            applyFilterAndSearch();
        });

        // Search
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            searchTerm = e.target.value.toLowerCase().trim();
            applyFilterAndSearch();
        });

        // ─── MAIN INIT ───────────────────────────────────────────────────────────────
        async function initAccounts() {
            try {
                const data = await fetchAccountsFromServer();
                console.log("data",data);
                renderCards(data);
                computeSummaryLocal(data);
            } catch (err) {
                console.error('Failed to load accounts:', err);
                const grid = document.getElementById('accountsGrid');
                grid.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-red-400">
                        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                        <p class="text-sm font-semibold">Failed to load accounts</p>
                        <p class="text-xs mt-1 text-gray-400">${err.message}</p>
                        <button onclick="initAccounts()" class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700 transition">
                            <i class="fas fa-redo mr-1"></i> Retry
                        </button>
                    </div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', initAccounts);

        // ─── SIDEBAR ─────────────────────────────────────────────────────────────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('overlay').classList.toggle('hidden');
        }

        function toggleSidebarCollapse() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }

        // ─── NAVIGATION ──────────────────────────────────────────────────────────────
        function navigateTo(page) {
            document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
            event.currentTarget.classList.add('active');
            const pages = {
                dashboard:  'dashboard.php',
                accounts:   'accounts.php',
                trading:    'trading.php',
                history:    'history.php',
                analytics:  'analytics.php',
                alerts:     'alerts.php',
                profile:    'profile.php',
                settings:   'settings.php',
            };
            if (pages[page]) window.location.href = pages[page];
        }

        // ─── LOGOUT ──────────────────────────────────────────────────────────────────
        function logout() {
            Swal.fire({
                title: 'Sign Out?',
                text: 'Are you sure you want to sign out?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#764ba2',
                confirmButtonText: 'Yes, sign out',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ icon: 'info', title: 'Signing Out', timer: 1500, showConfirmButton: false });
                    setTimeout(() => {
                        localStorage.clear();
                        window.location.href = '/auth/login.php';
                    }, 1500);
                }
            });
        }

        // ─── ADD ACCOUNT ─────────────────────────────────────────────────────────────
        function addNewAccount() {
            showToast('info', 'Redirecting to add account page...');
            setTimeout(() => { /* window.location.href = 'add-account.php'; */ }, 1500);
        }

        // ─── BUY MODAL ───────────────────────────────────────────────────────────────
        function openBuyModal(accountId, botName, balance, type) {
            document.getElementById('buyAccountInfo').innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;
            document.getElementById('buyModal').style.display = 'flex';
        }
        function closeBuyModal()  { document.getElementById('buyModal').style.display  = 'none'; }
        function confirmBuyTrade() { closeBuyModal();  showToast('success', 'Buy order placed successfully!'); }

        // ─── SELL MODAL ──────────────────────────────────────────────────────────────
        function openSellModal(accountId, botName, balance, type) {
            document.getElementById('sellAccountInfo').innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;
            document.getElementById('sellModal').style.display = 'flex';
        }
        function closeSellModal()  { document.getElementById('sellModal').style.display = 'none'; }
        function confirmSellTrade() { closeSellModal(); showToast('success', 'Sell order placed successfully!'); }

        // ─── CLOSE POSITIONS MODAL ───────────────────────────────────────────────────
        function openCloseModal(accountId, botName) {
            document.getElementById('closeAccountInfo').innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName}</div>`;
            document.getElementById('closeModal').style.display = 'flex';
        }
        function closeCloseModal()  { document.getElementById('closeModal').style.display = 'none'; }
        function confirmClosePositions() { closeCloseModal(); showToast('warning', 'All positions closed successfully!'); }

        // ─── TOAST ───────────────────────────────────────────────────────────────────
        function showToast(type, message) {
            const toast     = document.getElementById('toast');
            const titleEl   = document.getElementById('toastTitle');
            const messageEl = document.getElementById('toastMessage');
            const iconEl    = toast.querySelector('[class*="text-"]');

            toast.className = 'toast';
            const cfg = {
                success: { cls: 'text-green-600',  icon: 'fa-check-circle',       title: 'Success',  border: '' },
                error:   { cls: 'text-red-600',    icon: 'fa-exclamation-circle', title: 'Error',    border: 'error' },
                warning: { cls: 'text-yellow-600', icon: 'fa-exclamation-triangle',title: 'Warning', border: 'warning' },
                info:    { cls: 'text-blue-600',   icon: 'fa-info-circle',        title: 'Info',     border: '' },
            }[type] || { cls: 'text-blue-600', icon: 'fa-info-circle', title: 'Info', border: '' };

            titleEl.textContent   = cfg.title;
            messageEl.textContent = message;
            iconEl.className      = cfg.cls;
            iconEl.innerHTML      = `<i class="fas ${cfg.icon} text-xl"></i>`;
            if (cfg.border) toast.classList.add(cfg.border);
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // ─── KEYBOARD SHORTCUTS ──────────────────────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-bar input').focus();
            }
            if (e.key === 'Escape') {
                closeBuyModal(); closeSellModal(); closeCloseModal();
                const sb = document.getElementById('sidebar');
                if (sb.classList.contains('mobile-open')) toggleSidebar();
            }
        });

        // Close modals on backdrop click
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('buyModal'))   closeBuyModal();
            if (e.target === document.getElementById('sellModal'))  closeSellModal();
            if (e.target === document.getElementById('closeModal')) closeCloseModal();
        });
    </script>
</body>

</html>