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
        <div class="flex flex-wrap gap-2 mb-6" data-aos="fade-up" data-aos-delay="50">
            <button class="filter-btn active">All (6)</button>
            <button class="filter-btn">Connected (4)</button>
            <button class="filter-btn">Disconnected (1)</button>
            <button class="filter-btn">Demo (1)</button>
            <button class="filter-btn">Real (5)</button>
        </div>

        <!-- Accounts Grid -->
        <div class="accounts-grid" id="accountsGrid">
            <!-- Card 1 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#MT4-123456</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>
                            Live
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">Quantum Scalper</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-blue-600 font-bold">12</span> (2.5 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-red-600 font-bold">8</span> (1.8 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$1,234</span>
                            <span class="text-[0.6rem] text-gray-500">(12.4%)</span>
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$12.5k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$13.9k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$234</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy" onclick="openBuyModal('MT4-123456', 'Quantum Scalper', '12.5k', 'Live')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="openSellModal('MT4-123456', 'Quantum Scalper', '12.5k', 'Live')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="openCloseModal('MT4-123456', 'Quantum Scalper')">
                            <i class="fas fa-times text-[0.6rem]"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="150">
                <div class="card-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#MT4-789012</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>
                            Live
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">Forex Master EA</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-blue-600 font-bold">24</span> (5.2 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-red-600 font-bold">15</span> (3.4 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$3,450</span>
                            <span class="text-[0.6rem] text-gray-500">(18.2%)</span>
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$22.9k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$26.3k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$890</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy" onclick="openBuyModal('MT4-789012', 'Forex Master EA', '22.9k', 'Live')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="openSellModal('MT4-789012', 'Forex Master EA', '22.9k', 'Live')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="openCloseModal('MT4-789012', 'Forex Master EA')">
                            <i class="fas fa-times text-[0.6rem]"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#MT4-345678</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>
                            Live
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">Trend Hunter</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-blue-600 font-bold">6</span> (1.2 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-red-600 font-bold">18</span> (4.5 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-negative font-bold">-$890</span>
                            <span class="text-[0.6rem] text-gray-500">(5.8%)</span>
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$15.5k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$14.6k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-negative font-bold">-$890</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy" onclick="openBuyModal('MT4-345678', 'Trend Hunter', '15.5k', 'Live')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="openSellModal('MT4-345678', 'Trend Hunter', '15.5k', 'Live')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="openCloseModal('MT4-345678', 'Trend Hunter')">
                            <i class="fas fa-times text-[0.6rem]"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="250">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#DEMO-9012</h3>
                        </div>
                        <span class="status-badge" style="background: #8b5cf6;">
                            <i class="fas fa-flask text-[0.4rem] mr-1"></i>
                            Demo
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">AI Scalper</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-blue-600 font-bold">45</span> (9.8 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-red-600 font-bold">32</span> (7.2 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$5,670</span>
                            <span class="text-[0.6rem] text-gray-500">(22.7%)</span>
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$30k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$35.7k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$5.7k</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy" onclick="openBuyModal('DEMO-9012', 'AI Scalper', '30k', 'Demo')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i>
                            Buy
                        </button>
                        <button class="action-sell" onclick="openSellModal('DEMO-9012', 'AI Scalper', '30k', 'Demo')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i>
                            Sell
                        </button>
                        <button class="action-close" onclick="openCloseModal('DEMO-9012', 'AI Scalper')">
                            <i class="fas fa-times text-[0.6rem]"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 5 - Disconnected -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-header" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#MT4-567890</h3>
                        </div>
                        <span class="status-badge disconnected">
                            <i class="fas fa-circle text-[0.4rem] mr-1"></i>
                            Offline
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">Grid Master</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-gray-500 font-bold">0</span> (0 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-gray-500 font-bold">0</span> (0 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value text-gray-500 font-bold">$0</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$8.5k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$8.5k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value text-gray-500 font-bold">$0</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy opacity-40 cursor-not-allowed" disabled>
                            Buy
                        </button>
                        <button class="action-sell opacity-40 cursor-not-allowed" disabled>
                            Sell
                        </button>
                        <button class="action-close opacity-40 cursor-not-allowed" disabled>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="account-card" data-aos="fade-up" data-aos-delay="350">
                <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white opacity-75 text-xs">Account ID</p>
                            <h3 class="text-white font-bold account-id">#MT4-112233</h3>
                        </div>
                        <span class="status-badge connected">
                            <i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>
                            Live
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name">Gold Hunter</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-up text-blue-600"></i>
                            Buy
                        </span>
                        <span class="metric-value">
                            <span class="text-blue-600 font-bold">32</span> (8.5 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-arrow-down text-red-600"></i>
                            Sell
                        </span>
                        <span class="metric-value">
                            <span class="text-red-600 font-bold">28</span> (7.2 lot)
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Profit
                        </span>
                        <span class="metric-value">
                            <span class="profit-positive font-bold">+$8,945</span>
                            <span class="text-[0.6rem] text-gray-500">(32.8%)</span>
                        </span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-wallet text-green-600"></i>
                            Balance
                        </span>
                        <span class="metric-value font-bold">$27.3k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Equity
                        </span>
                        <span class="metric-value font-bold">$36.2k</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">
                            <i class="fas fa-water text-blue-600"></i>
                            Floating
                        </span>
                        <span class="metric-value floating-positive font-bold">+$8.9k</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="flex gap-1.5 mt-2">
                        <button class="action-buy" onclick="openBuyModal('MT4-112233', 'Gold Hunter', '27.3k', 'Live')">
                            Buy
                        </button>
                        <button class="action-sell" onclick="openSellModal('MT4-112233', 'Gold Hunter', '27.3k', 'Live')">
                            Sell
                        </button>
                        <button class="action-close" onclick="openCloseModal('MT4-112233', 'Gold Hunter')">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6" data-aos="fade-up" data-aos-delay="400">
            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Accounts</p>
                <p class="text-xl font-bold text-gray-800">6</p>
                <p class="text-[0.6rem] text-green-600 mt-1">+2 this month</p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Balance</p>
                <p class="text-xl font-bold text-gray-800">$116.5k</p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Total Profit</p>
                <p class="text-xl font-bold text-green-600">+$18.4k</p>
            </div>

            <div class="summary-card">
                <p class="text-gray-500 text-xs mb-1">Active Bots</p>
                <p class="text-xl font-bold text-gray-800">5/6</p>
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
        // Initialize AOS
        AOS.init({
            duration: 600,
            once: true
        });

        // Auth check
        (function() {
            try {
                const token = localStorage.getItem("jwt");
                const user = localStorage.getItem("user");

                if (!token || !user) {
                    console.log("Not authenticated, redirecting to login");
                    window.location.href = "/login.php";
                    return;
                }
            } catch (error) {
                console.error("Auth check error:", error);
                window.location.href = "/login.php";
            }
        })();

        // Sidebar Functions
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('overlay').classList.toggle('hidden');
        }

        function toggleSidebarCollapse() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }

        // Navigation
        function navigateTo(page) {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');

            const pages = {
                'dashboard': '#',//'dashboard.html',
                'accounts': '#',//'accounts.html',
                'trading': '#',//'trading.html',
                'history': '#',//'history.html'
            };
            if (pages[page]) window.location.href = pages[page];
        }

        // Logout
        function logout() {
            Swal.fire({
                title: 'Sign Out?',
                text: 'Are you sure you want to sign out?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FDA300',
                cancelButtonColor: '#2C5282',
                confirmButtonText: 'Yes, sign out',
                cancelButtonText: 'Cancel',
                background: document.documentElement.classList.contains('light-mode') ? '#FFFFFF' : '#2C3E50',
                color: document.documentElement.classList.contains('light-mode') ? '#2C5282' : '#EDF2F7'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "info",
                        title: "Signing Out",
                        timer: 1500,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('light-mode') ? '#FFFFFF' : '#2C3E50',
                        color: document.documentElement.classList.contains('light-mode') ? '#2C5282' : '#EDF2F7'
                    });

                    setTimeout(() => {
                        localStorage.clear();
                        window.location.href = "/login.php";
                    }, 1500);
                }
            });
        }

        // Add New Account
        function addNewAccount() {
            showToast('info', 'Redirecting to add account page...');
            setTimeout(() => {
                // window.location.href = 'add-account.html';
            }, 1500);
        }

        // Buy Modal Functions
        function openBuyModal(accountId, botName, balance, type) {
            const modal = document.getElementById('buyModal');
            const accountInfo = document.getElementById('buyAccountInfo');

            accountInfo.innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: $${balance} • ${type}</div>
            `;

            modal.style.display = 'flex';
        }

        function closeBuyModal() {
            document.getElementById('buyModal').style.display = 'none';
        }

        function confirmBuyTrade() {
            closeBuyModal();
            showToast('success', 'Buy order placed successfully!');

            // Simulate updating buy count
            setTimeout(() => {
                // In real app, you would update the UI here
            }, 500);
        }

        // Sell Modal Functions
        function openSellModal(accountId, botName, balance, type) {
            const modal = document.getElementById('sellModal');
            const accountInfo = document.getElementById('sellAccountInfo');

            accountInfo.innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: $${balance} • ${type}</div>
            `;

            modal.style.display = 'flex';
        }

        function closeSellModal() {
            document.getElementById('sellModal').style.display = 'none';
        }

        function confirmSellTrade() {
            closeSellModal();
            showToast('success', 'Sell order placed successfully!');
        }

        // Close Positions Modal Functions
        function openCloseModal(accountId, botName) {
            const modal = document.getElementById('closeModal');
            const accountInfo = document.getElementById('closeAccountInfo');

            accountInfo.innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName}</div>
            `;

            modal.style.display = 'flex';
        }

        function closeCloseModal() {
            document.getElementById('closeModal').style.display = 'none';
        }

        function confirmClosePositions() {
            closeCloseModal();
            showToast('warning', 'All positions closed successfully!');
        }

        // Toast Notification
        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const icon = toast.querySelector('.text-green-600, .text-red-600, .text-yellow-600');

            toast.className = 'toast';

            if (type === 'success') {
                toast.classList.add('show');
                toastTitle.textContent = 'Success';
                toastMessage.textContent = message;
                icon.className = 'text-green-600';
                icon.innerHTML = '<i class="fas fa-check-circle text-xl"></i>';
            } else if (type === 'error') {
                toast.classList.add('show', 'error');
                toastTitle.textContent = 'Error';
                toastMessage.textContent = message;
                icon.className = 'text-red-600';
                icon.innerHTML = '<i class="fas fa-exclamation-circle text-xl"></i>';
            } else if (type === 'warning') {
                toast.classList.add('show', 'warning');
                toastTitle.textContent = 'Warning';
                toastMessage.textContent = message;
                icon.className = 'text-yellow-600';
                icon.innerHTML = '<i class="fas fa-exclamation-triangle text-xl"></i>';
            } else {
                toast.classList.add('show');
                toastTitle.textContent = 'Info';
                toastMessage.textContent = message;
                icon.className = 'text-blue-600';
                icon.innerHTML = '<i class="fas fa-info-circle text-xl"></i>';
            }

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.textContent.trim();
                showToast('info', `Filtering by: ${filter}`);
            });
        });

        // Search functionality
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.account-card').forEach(card => {
                const id = card.querySelector('h3').textContent.toLowerCase();
                const bot = card.querySelector('.bot-name').textContent.toLowerCase();
                card.style.display = (id.includes(term) || bot.includes(term)) ? 'block' : 'none';
            });
        });

        // Real-time updates simulation
        setInterval(() => {
            document.querySelectorAll('.floating-positive, .floating-negative').forEach(el => {
                if (Math.random() > 0.8) {
                    const val = parseFloat(el.textContent.replace(/[^0-9.-]+/g, ''));
                    const change = (Math.random() * 30 - 15).toFixed(0);
                    const newVal = (val + parseFloat(change)).toFixed(0);
                    el.className = newVal > 0 ? 'metric-value floating-positive font-bold' : 'metric-value floating-negative font-bold';
                    el.textContent = (newVal > 0 ? '+' : '') + '$' + Math.abs(newVal);
                }
            });
        }, 10000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + K for search
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-bar input').focus();
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                closeBuyModal();
                closeSellModal();
                closeCloseModal();
            }

            // Escape to close sidebar on mobile
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('mobile-open')) {
                    toggleSidebar();
                }
            }
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const buyModal = document.getElementById('buyModal');
            const sellModal = document.getElementById('sellModal');
            const closeModal = document.getElementById('closeModal');

            if (e.target === buyModal) closeBuyModal();
            if (e.target === sellModal) closeSellModal();
            if (e.target === closeModal) closeCloseModal();
        });
    </script>
</body>

</html>