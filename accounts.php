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

    <link rel="stylesheet" href="lib/css/accounts.css">

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
                <span class="menu-badge">3</span>
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
        <!-- <div class="absolute bottom-3 left-3 right-3 p-3 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white sidebar-text">
            <p class="text-xs font-semibold mb-1">Upgrade to Pro</p>
            <p class="text-xs opacity-90 mb-1">Get advanced features</p>
            <button class="bg-white text-blue-600 text-xs px-2 py-1 rounded-full font-semibold">
                Upgrade
            </button>
        </div> -->
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
                <!-- Bell hidden for now -->
                <!-- <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-base cursor-pointer"></i>
                    <span class="badge">5</span>
                </div> -->

                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrapper" id="profileDropdownWrapper">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile"
                        id="profileAvatar"
                        class="w-8 h-8 rounded-full cursor-pointer border-2 border-transparent hover:border-blue-600 transition-all"
                        onclick="toggleProfileDropdown()">
                </div>
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
                    <div class="flex items-center gap-1">
                        <input type="number" id="buyLotInput" disabled
                            step="0.01" min="0.01"
                            value="0.01"
                            class="w-20 border border-gray-300 rounded-lg px-2 py-1 text-sm font-semibold text-center focus:outline-none focus:border-blue-500">
                        <span class="text-gray-500 text-sm">lot</span>
                    </div>
                </div>
                <!-- <div class="trade-summary-item">
                    <span class="text-gray-600">Est. Margin:</span>
                    <span class="font-semibold">$50.00</span>
                </div> -->
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
                    <div class="flex items-center gap-1">
                        <input type="number" id="sellLotInput" disabled
                            step="0.01" min="0.01"
                            value="0.01"
                            class="w-20 border border-gray-300 rounded-lg px-2 py-1 text-sm font-semibold text-center focus:outline-none focus:border-red-500">
                        <span class="text-gray-500 text-sm">lot</span>
                    </div>
                </div>
                <!-- <div class="trade-summary-item">
                    <span class="text-gray-600">Est. Margin:</span>
                    <span class="font-semibold">$50.00</span>
                </div> -->
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

    <script src="lib/js/accounts.js"></script>

    <script>
        // ── Profile dropdown (portal - rendered on body to escape all stacking contexts) ──
        const dropdownHTML = `
            <div id="profileDropdown" class="profile-dropdown">
                <div class="profile-dropdown-header">
                    <div class="profile-dropdown-name" id="dropdownUserName">User</div>
                    <div class="profile-dropdown-role" id="dropdownUserRole">Trader</div>
                </div>
                <button class="profile-dropdown-item" onclick="navigateTo('profile'); closeProfileDropdown()">
                    <i class="fas fa-user"></i> My Profile
                </button>
                <button class="profile-dropdown-item" onclick="navigateTo('settings'); closeProfileDropdown()">
                    <i class="fas fa-cog"></i> Settings
                </button>
                <button class="profile-dropdown-item logout" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', dropdownHTML);

        function positionDropdown() {
            const avatar = document.getElementById('profileAvatar');
            const dropdown = document.getElementById('profileDropdown');
            if (!avatar || !dropdown) return;
            const rect = avatar.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
            dropdown.style.left = (rect.right + window.scrollX - dropdown.offsetWidth) + 'px';
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const isOpen = dropdown.classList.contains('open');
            if (isOpen) {
                closeProfileDropdown();
            } else {
                positionDropdown();
                dropdown.classList.add('open');
            }
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.remove('open');
        }

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            const avatar = document.getElementById('profileAvatar');
            const dropdown = document.getElementById('profileDropdown');
            if (!dropdown) return;
            if (!dropdown.contains(e.target) && e.target !== avatar) {
                closeProfileDropdown();
            }
        });

        // Reposition on scroll/resize
        window.addEventListener('scroll', function() {
            if (document.getElementById('profileDropdown').classList.contains('open')) {
                positionDropdown();
            }
        });
        window.addEventListener('resize', function() {
            if (document.getElementById('profileDropdown').classList.contains('open')) {
                positionDropdown();
            }
        });

        // Populate name/role from localStorage
        (function() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const name = user.name || user.username || 'User';
            const role = user.user_tipe || 'Trader';
            const nameEl = document.getElementById('dropdownUserName');
            const roleEl = document.getElementById('dropdownUserRole');
            if (nameEl) nameEl.textContent = name;
            if (roleEl) roleEl.textContent = role;
        })();
    </script>
</body>

</html>