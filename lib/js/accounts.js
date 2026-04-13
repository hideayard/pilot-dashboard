// ─── GLOBALS ────────────────────────────────────────────────────────────────
let accountsData = []; // raw server data
let activeFilter = "all";
let searchTerm = "";

// Card header gradient palette (cycles through accounts)
const CARD_GRADIENTS = [
  "linear-gradient(135deg,#667eea 0%,#764ba2 100%)",
  "linear-gradient(135deg,#f59e0b 0%,#d97706 100%)",
  "linear-gradient(135deg,#ef4444 0%,#dc2626 100%)",
  "linear-gradient(135deg,#10b981 0%,#059669 100%)",
  "linear-gradient(135deg,#6b7280 0%,#4b5563 100%)",
  "linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)",
  "linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)",
  "linear-gradient(135deg,#ec4899 0%,#be185d 100%)",
];

// ─── JWT UTILS ───────────────────────────────────────────────────────────────
function parseJwt(token) {
  try {
    const base64Url = token.split(".")[1];
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    return JSON.parse(atob(base64));
  } catch (e) {
    return null;
  }
}

function getUserIdFromJwt() {
  const token = localStorage.getItem("jwt");
  if (!token) return null;
  const payload = parseJwt(token);
  // Support common JWT claim names for user id
  return (
    payload?.data?.user_id ?? payload?.data?.id ?? payload?.data?.sub ?? null
  );
}

// ─── AUTH CHECK ──────────────────────────────────────────────────────────────
(function () {
  try {
    const token = localStorage.getItem("jwt");
    const user = localStorage.getItem("user");
    if (!token || !user) {
      window.location.href = "/auth/login.php";
      return;
    }
  } catch (e) {
    window.location.href = "/auth/login.php";
  }
})();

// ─── INIT AOS ────────────────────────────────────────────────────────────────
AOS.init({
  duration: 600,
  once: true,
});

// ─── POPULATE SIDEBAR USER ───────────────────────────────────────────────────
(function () {
  const user = JSON.parse(localStorage.getItem("user") || "{}");
  const name = user.name || user.username || "User";

  function initials(n) {
    return n
      .split(" ")
      .map((w) => w[0])
      .join("")
      .toUpperCase()
      .substring(0, 2);
  }
  const av = document.querySelector(".avatar");
  if (av) av.textContent = initials(name);
  const nm = document.querySelector(".sidebar-text .font-semibold");
  if (nm) nm.textContent = name;
  const rl = document.querySelector(".sidebar-text .text-xs");
  if (rl) rl.textContent = user.user_tipe || "Trader";
})();

// ─── SERVER FETCH ────────────────────────────────────────────────────────────
async function fetchAccountsFromServer() {
  const authToken = localStorage.getItem("jwt");
  if (!authToken) {
    console.error("No authentication token available (key: jwt)");
    throw new Error("Please login first");
  }

  // Decode user_id from JWT payload
  const userId = getUserIdFromJwt();
  if (!userId) {
    console.warn("Could not decode user_id from JWT");
  }

  console.log("Fetching accounts from server for user_id:", userId);

  const body = new URLSearchParams({
    action: "get_accounts_by_user",
  }); //, user_id: userId });
  if (userId) body.append("user_id", userId);

  const response = await fetch("/proxy2.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      Authorization: `Bearer ${authToken}`,
    },
    body,
  });

  if (!response.ok) {
    throw new Error(`Failed to fetch accounts: ${response.status}`);
  }

  const result = await response.json();

  // Response shape: { status:"success", data:{ user:{}, summary:{}, accounts:[] } }
  if (result.status === "success" && result.data) {
    const accounts = result.data.accounts || [];
    const summary = result.data.summary || null;

    console.log(`Fetched ${accounts.length} accounts from server`);
    accountsData = accounts;

    if (summary) updateSummaryFromServer(summary);
    return accounts;
  } else {
    throw new Error(
      result.message || result.error || "Failed to fetch accounts from server",
    );
  }
}

// ─── UPDATE SUMMARY STATS ────────────────────────────────────────────────────
function updateSummaryFromServer(summary) {
  // summary fields: total_accounts, total_balance, total_profit,
  //                 avg_profit_percentage, active_accounts
  const set = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  };

  const total = summary.total_accounts ?? accountsData.length;
  const balance = summary.total_balance ?? null;
  const profit = summary.total_profit ?? null;
  const activeBots = summary.active_accounts ?? null;

  set("stat-total", total);

  if (balance !== null) set("stat-balance", formatMoney(balance));

  if (profit !== null) {
    const el = document.getElementById("stat-profit");
    if (el) {
      el.textContent = (profit >= 0 ? "+" : "") + formatMoney(profit);
      el.className =
        "text-xl font-bold " +
        (profit >= 0 ? "text-green-600" : "text-red-600");
    }
  }

  if (activeBots !== null) set("stat-active", `${activeBots}/${total}`);

  // Update filter counts from live account list
  updateFilterCounts(accountsData);
}

// ─── FILTER COUNT HELPER ─────────────────────────────────────────────────────
function updateFilterCounts(accounts) {
  let connected = 0,
    disconnected = 0,
    demo = 0,
    real = 0;
  accounts.forEach((acc) => {
    const status = (acc.status || "").toLowerCase();
    const type = (acc.account_type || "").toLowerCase();
    const isConn =
      status === "active" || status === "connected" || status === "live";
    if (isConn) connected++;
    else disconnected++;
    if (type === "demo") demo++;
    else real++;
  });
  const set = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  };
  set("fc-all", accounts.length);
  set("fc-connected", connected);
  set("fc-disconnected", disconnected);
  set("fc-demo", demo);
  set("fc-real", real);
}

// ─── COMPUTE SUMMARY FROM LOCAL DATA ────────────────────────────────────────
function computeSummaryLocal(accounts) {
  // Use exact field names from proxy2 response
  let totalBalance = 0,
    totalProfit = 0,
    activeBots = 0;

  accounts.forEach((acc) => {
    const status = (acc.status || "").toLowerCase();
    const balance = parseFloat(acc.account_balance || 0);
    const profit = parseFloat(acc.total_profit || 0);
    const isActive =
      status === "active" || status === "connected" || status === "live";

    totalBalance += balance;
    totalProfit += profit;
    if (isActive) activeBots++;
  });

  updateFilterCounts(accounts);

  const set = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  };
  set("stat-total", accounts.length);
  set("stat-balance", formatMoney(totalBalance));
  set("stat-active", `${activeBots}/${accounts.length}`);

  const profitEl = document.getElementById("stat-profit");
  if (profitEl) {
    profitEl.textContent =
      (totalProfit >= 0 ? "+" : "") + formatMoney(totalProfit);
    profitEl.className =
      "text-xl font-bold " +
      (totalProfit >= 0 ? "text-green-600" : "text-red-600");
  }
}

// ─── FORMAT HELPERS ──────────────────────────────────────────────────────────
function formatMoney(val) {
  const num = parseFloat(val) || 0;
  const n = Math.abs(num);
  const sign = num < 0 ? "-" : ""; // ← preserve the sign

  if (n >= 1000000) return sign + "$" + (n / 1000000).toFixed(2) + "M";
  if (n >= 1000) return sign + "$" + (n / 1000).toFixed(1) + "k";
  return sign + "$" + n.toFixed(2);
}

function formatLastSync(lastSync) {
  if (!lastSync) return "–";
  const date = new Date(lastSync);
  const now = new Date();
  const diffMs = now - date;

  if (diffMs < 0) {
    // date is in the future — show time only
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  const diffSec = Math.floor(diffMs / 1000);
  const diffMin = Math.floor(diffSec / 60);
  const diffHr = Math.floor(diffMin / 60);
  const diffDay = Math.floor(diffHr / 24);
  const diffWk = Math.floor(diffDay / 7);
  const diffMo = Math.floor(diffDay / 30);
  const diffYr = Math.floor(diffDay / 365);

  if (diffSec < 60) return "just now";
  if (diffMin < 60) return diffMin + "m ago";
  if (diffHr < 24) return diffHr + "h ago";
  if (diffDay < 7) return diffDay + "d ago";
  if (diffWk < 4) return diffWk + "w ago";
  if (diffMo < 12) return diffMo + "mo ago";
  return diffYr + "y ago";
}

// ─── RENDER CARDS ────────────────────────────────────────────────────────────
function buildAccountCard(acc, index) {
  console.log(index, "acc", acc);
  // Exact field names from proxy2 response
  const accountId = acc.account_id || `ACC-${index + 1}`;
  const botName = acc.bot_name || "N/A";
  const balance = parseFloat(acc.account_balance || 0);
  const equity = parseFloat(acc.account_equity || 0);
  const profit = parseFloat(acc.total_profit || 0);
  const profitPct = parseFloat(acc.total_profit_percentage || 0);
  const floating = parseFloat(acc.floating_value || 0);
  const buyCount = parseInt(acc.buy_order_count || 0);
  const sellCount = parseInt(acc.sell_order_count || 0);
  const buyLot = parseFloat(acc.total_buy_lot || 0);
  const sellLot = parseFloat(acc.total_sell_lot || 0);
  const leverage = acc.leverage || "–";
  const currency = acc.currency || "USD";
  const broker = acc.broker || acc.server || "–";
  const server = acc.server || "–";
  const accountType = (acc.account_type || "").toLowerCase();
  const status = (acc.status || "").toLowerCase();
  const lastSync = acc.last_sync || acc.last_connected || null;
  const totalOrders = acc.total_orders || buyCount + sellCount;

  // Auto trade status (default false if not provided)
  const autoTradeEnabled =
    acc.auto_trade === true ||
    acc.auto_trade === "enabled" ||
    acc.ea_trade === true;

  const isActive =
    status === "active" || status === "connected" || status === "live";
  const isDemo = accountType === "demo";
  const isOffline = !isActive;
  const hasOrders = totalOrders > 0;

  const gradient = CARD_GRADIENTS[index % CARD_GRADIENTS.length];

  // Status badge
  let statusBadge;
  if (isDemo) {
    statusBadge = `<span class="status-badge" style="background:#8b5cf6;"><i class="fas fa-flask text-[0.4rem] mr-1"></i>Demo</span>`;
  } else if (isActive) {
    statusBadge = `<span class="status-badge connected"><i class="fas fa-circle text-[0.4rem] mr-1 animate-pulse"></i>Live</span>`;
  } else {
    statusBadge = `<span class="status-badge disconnected"><i class="fas fa-circle text-[0.4rem] mr-1"></i>Offline</span>`;
  }

  const profitClass = profit >= 0 ? "profit-positive" : "profit-negative";
  const profitSign = profit >= 0 ? "+" : "";
  const floatClass = floating >= 0 ? "floating-positive" : "floating-negative";
  const floatSign = floating >= 0 ? "+" : "-";

  const btnDisabled = isOffline ? "disabled" : "";
  const btnOpacity = isOffline ? "opacity-40 cursor-not-allowed" : "";

  const safeId = String(accountId).replace(/['"]/g, "");
  const safeBot = botName.replace(/['"]/g, "");
  const safeBalance = formatMoney(balance).replace(/['"]/g, "");
  const safeType = isDemo ? "Demo" : "Live";

  const lastSyncFull = lastSync
    ? new Date(lastSync).toLocaleString([], {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      })
    : "–";

  const lastSyncStr = formatLastSync(lastSync);

  // Auto trade button styling
  const autoTradeStatus = autoTradeEnabled ? "enabled" : "disabled";
  const autoTradeIcon = autoTradeEnabled ? "fa-robot" : "fa-pause-circle";
  const autoTradeText = autoTradeEnabled ? "Auto: ON" : "Auto: OFF";
  const autoTradeClass = autoTradeEnabled ? "auto-trade-on" : "auto-trade-off";

  const autoTradeButton = `
    <button class="action-auto ${autoTradeClass} ${btnOpacity}" ${btnDisabled}
        onclick="toggleAutoTrade('${safeId}','${safeBot}', ${!autoTradeEnabled})">
        <i class="fas ${autoTradeIcon} text-[0.6rem]"></i> ${autoTradeText}
    </button>`;

  // Only show Close button if there are orders
  const closeButton = hasOrders
    ? `
    <button class="action-close ${btnOpacity}" ${btnDisabled}
        onclick="openCloseModal('${safeId}','${safeBot}')">
        <i class="fas fa-times text-[0.6rem]"></i> Close
    </button>`
    : "";

  return `
            <div class="account-card"
                 data-status="${isActive ? "connected" : "disconnected"}"
                 data-type="${isDemo ? "demo" : "real"}"
                 data-id="${safeId.toLowerCase()}"
                 data-bot="${safeBot.toLowerCase()}"
                 data-auto-trade="${autoTradeEnabled}">
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
                        <span class="metric-value ${buyCount > 0 ? "text-blue-600" : ""} font-bold">${buyCount} (${buyLot} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-arrow-down text-red-600"></i> Sell</span>
                        <span class="metric-value ${sellCount > 0 ? "text-red-600" : ""} font-bold">${sellCount} (${sellLot} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-chart-line text-purple-600"></i> Profit</span>
                        <span class="metric-value">
                            <span class="${profitClass} font-bold">${formatMoney(profit)}</span>
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
                        <span class="metric-value ${floatClass} font-bold">${floatSign}${formatMoney(Math.abs(floating))}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label"><i class="fas fa-list text-gray-400"></i> Orders</span>
                        <span class="metric-value text-gray-600">${totalOrders} total</span>
                    </div>

                    <div class="card-divider"></div>

                    <div class="text-[0.6rem] text-gray-400 text-right mb-1 cursor-help"
                        title="${lastSyncFull}">
                        <i class="fas fa-sync-alt mr-1"></i>Sync: ${lastSyncStr}
                    </div>

                    <div class="flex gap-1.5 flex-wrap">
                        ${autoTradeButton}
                        <button class="action-buy ${btnOpacity}" ${btnDisabled}
                            onclick="openBuyModal('${safeId}','${safeBot}','${safeBalance}','${safeType}')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i> Buy
                        </button>
                        <button class="action-sell ${btnOpacity}" ${btnDisabled}
                            onclick="openSellModal('${safeId}','${safeBot}','${safeBalance}','${safeType}')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i> Sell
                        </button>
                        ${closeButton}
                    </div>
                </div>
            </div>`;
}

// ─── AUTO TRADE TOGGLE ───────────────────────────────────────────────────────
async function toggleAutoTrade(accountId, botName, enable) {
  const action = enable ? "enable" : "disable";
  const actionText = enable ? "enable" : "disable";
  const confirmText = enable
    ? `Are you sure you want to enable auto trading (EA) for account ${accountId}?`
    : `Are you sure you want to disable auto trading (EA) for account ${accountId}?`;

  const result = await Swal.fire({
    title: `${enable ? "Enable" : "Disable"} Auto Trading?`,
    text: confirmText,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: enable ? "#10b981" : "#ef4444",
    cancelButtonColor: "#6b7280",
    confirmButtonText: `Yes, ${action} EA`,
    cancelButtonText: "Cancel",
  });

  if (result.isConfirmed) {
    try {
      const authToken = localStorage.getItem("jwt");
      const userId = getUserIdFromJwt();

      // Show loading state
      Swal.fire({
        title: "Processing...",
        text: `${enable ? "Enabling" : "Disabling"} auto trading...`,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      // API call to toggle auto trade
      const response = await fetch("/proxy2.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          Authorization: `Bearer ${authToken}`,
        },
        body: new URLSearchParams({
          action: "toggle_auto_trade",
          user_id: userId,
          account_id: accountId,
          enable: enable ? "1" : "0",
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const data = await response.json();

      if (data.status === "success") {
        // Update the local account data
        const account = accountsData.find((acc) => acc.account_id == accountId);
        if (account) {
          account.auto_trade = enable;
        }

        // Refresh the card UI
        await refreshAccountCard(accountId);

        Swal.fire({
          icon: "success",
          title: "Success!",
          text: `Auto trading ${enable ? "enabled" : "disabled"} for ${botName}`,
          timer: 2000,
          showConfirmButton: false,
        });

        showToast(
          "success",
          `Auto trading ${enable ? "enabled" : "disabled"} for ${botName}`,
        );
      } else {
        throw new Error(data.message || "Failed to toggle auto trading");
      }
    } catch (error) {
      console.error("Error toggling auto trade:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text:
          error.message || "Failed to toggle auto trading. Please try again.",
      });
      showToast("error", "Failed to toggle auto trading");
    }
  }
}

// Helper function to refresh a single account card
async function refreshAccountCard(accountId) {
  try {
    // Find the account in current data
    const account = accountsData.find((acc) => acc.account_id == accountId);
    if (!account) return;

    // Find and update the card in the DOM
    const cards = document.querySelectorAll(".account-card");
    for (let card of cards) {
      if (
        card.querySelector(`[data-id="${accountId.toLowerCase()}"]`) ||
        card.innerHTML.includes(`#${accountId}`)
      ) {
        const index = Array.from(cards).indexOf(card);
        const newCardHtml = buildAccountCard(account, index);
        card.outerHTML = newCardHtml;
        break;
      }
    }

    // Reapply filters after refresh
    applyFilterAndSearch();
  } catch (error) {
    console.error("Error refreshing card:", error);
  }
}

function renderCards(accounts) {
  const grid = document.getElementById("accountsGrid");
  const loading = document.getElementById("accounts-loading");
  if (loading) loading.remove();

  if (!accounts || accounts.length === 0) {
    grid.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                        <i class="fas fa-wallet text-4xl mb-3"></i>
                        <p class="text-sm">No accounts found</p>
                    </div>`;
    return;
  }

  grid.innerHTML = accounts.map((acc, i) => buildAccountCard(acc, i)).join("");
}

// ─── FILTER + SEARCH ────────────────────────────────────────────────────────
function applyFilterAndSearch() {
  const cards = document.querySelectorAll(".account-card");
  cards.forEach((card) => {
    const statusMatch =
      activeFilter === "all" ||
      (activeFilter === "connected" && card.dataset.status === "connected") ||
      (activeFilter === "disconnected" &&
        card.dataset.status === "disconnected") ||
      (activeFilter === "demo" && card.dataset.type === "demo") ||
      (activeFilter === "real" && card.dataset.type === "real");

    const searchMatch =
      !searchTerm ||
      card.dataset.id.includes(searchTerm) ||
      card.dataset.bot.includes(searchTerm);

    card.style.display = statusMatch && searchMatch ? "" : "none";
  });
}

// Filter buttons
document.getElementById("filterBar").addEventListener("click", function (e) {
  const btn = e.target.closest(".filter-btn");
  if (!btn) return;
  document
    .querySelectorAll(".filter-btn")
    .forEach((b) => b.classList.remove("active"));
  btn.classList.add("active");
  activeFilter = btn.dataset.filter;
  applyFilterAndSearch();
});

// Search
document
  .querySelector(".search-bar input")
  .addEventListener("input", function (e) {
    searchTerm = e.target.value.toLowerCase().trim();
    applyFilterAndSearch();
  });

// ─── MAIN INIT ───────────────────────────────────────────────────────────────
async function initAccounts() {
  try {
    const data = await fetchAccountsFromServer();
    console.log("data", data);
    renderCards(data);
    computeSummaryLocal(data);
  } catch (err) {
    console.error("Failed to load accounts:", err);
    const grid = document.getElementById("accountsGrid");
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

document.addEventListener("DOMContentLoaded", initAccounts);

// ─── SIDEBAR ─────────────────────────────────────────────────────────────────
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("mobile-open");
  document.getElementById("overlay").classList.toggle("hidden");
}

function toggleSidebarCollapse() {
  document.getElementById("sidebar").classList.toggle("collapsed");
  document.getElementById("mainContent").classList.toggle("expanded");
}

// ─── NAVIGATION ──────────────────────────────────────────────────────────────
function navigateTo(page) {
  document
    .querySelectorAll(".menu-item")
    .forEach((i) => i.classList.remove("active"));
  event.currentTarget.classList.add("active");
  const pages = {
    dashboard: "dashboard.php",
    accounts: "accounts.php",
    trading: "trading.php",
    history: "history.php",
    analytics: "analytics.php",
    alerts: "alerts.php",
    profile: "profile.php",
    settings: "settings.php",
  };
  if (pages[page]) window.location.href = pages[page];
}

// ─── LOGOUT ──────────────────────────────────────────────────────────────────
function logout() {
  Swal.fire({
    title: "Sign Out?",
    text: "Are you sure you want to sign out?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#667eea",
    cancelButtonColor: "#764ba2",
    confirmButtonText: "Yes, sign out",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        icon: "info",
        title: "Signing Out",
        timer: 1500,
        showConfirmButton: false,
      });
      setTimeout(() => {
        localStorage.clear();
        window.location.href = "/login.php";
      }, 1500);
    }
  });
}

// ─── ADD ACCOUNT ─────────────────────────────────────────────────────────────
function addNewAccount() {
  showToast("info", "Redirecting to add account page...");
  setTimeout(() => {
    /* window.location.href = 'add-account.php'; */
  }, 1500);
}

// ─── BUY MODAL ───────────────────────────────────────────────────────────────
function openBuyModal(accountId, botName, balance, type) {
  document.getElementById("buyAccountInfo").innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;
  document.getElementById("buyModal").style.display = "flex";
}

function closeBuyModal() {
  document.getElementById("buyModal").style.display = "none";
}

function confirmBuyTrade() {
  closeBuyModal();
  showToast("success", "Buy order placed successfully!");
}

// ─── SELL MODAL ──────────────────────────────────────────────────────────────
function openSellModal(accountId, botName, balance, type) {
  document.getElementById("sellAccountInfo").innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;
  document.getElementById("sellModal").style.display = "flex";
}

function closeSellModal() {
  document.getElementById("sellModal").style.display = "none";
}

function confirmSellTrade() {
  closeSellModal();
  showToast("success", "Sell order placed successfully!");
}

// ─── CLOSE POSITIONS MODAL ───────────────────────────────────────────────────
function openCloseModal(accountId, botName) {
  document.getElementById("closeAccountInfo").innerHTML = `
                <div class="modal-account-id">${accountId}</div>
                <div class="modal-bot-name">${botName}</div>`;
  document.getElementById("closeModal").style.display = "flex";
}

function closeCloseModal() {
  document.getElementById("closeModal").style.display = "none";
}

function confirmClosePositions() {
  closeCloseModal();
  showToast("warning", "All positions closed successfully!");
}

// ─── TOAST ───────────────────────────────────────────────────────────────────
function showToast(type, message) {
  const toast = document.getElementById("toast");
  const titleEl = document.getElementById("toastTitle");
  const messageEl = document.getElementById("toastMessage");
  const iconEl = toast.querySelector('[class*="text-"]');

  toast.className = "toast";
  const cfg = {
    success: {
      cls: "text-green-600",
      icon: "fa-check-circle",
      title: "Success",
      border: "",
    },
    error: {
      cls: "text-red-600",
      icon: "fa-exclamation-circle",
      title: "Error",
      border: "error",
    },
    warning: {
      cls: "text-yellow-600",
      icon: "fa-exclamation-triangle",
      title: "Warning",
      border: "warning",
    },
    info: {
      cls: "text-blue-600",
      icon: "fa-info-circle",
      title: "Info",
      border: "",
    },
  }[type] || {
    cls: "text-blue-600",
    icon: "fa-info-circle",
    title: "Info",
    border: "",
  };

  titleEl.textContent = cfg.title;
  messageEl.textContent = message;
  iconEl.className = cfg.cls;
  iconEl.innerHTML = `<i class="fas ${cfg.icon} text-xl"></i>`;
  if (cfg.border) toast.classList.add(cfg.border);
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 3000);
}

// ─── KEYBOARD SHORTCUTS ──────────────────────────────────────────────────────
document.addEventListener("keydown", function (e) {
  if (e.ctrlKey && e.key === "k") {
    e.preventDefault();
    document.querySelector(".search-bar input").focus();
  }
  if (e.key === "Escape") {
    closeBuyModal();
    closeSellModal();
    closeCloseModal();
    const sb = document.getElementById("sidebar");
    if (sb.classList.contains("mobile-open")) toggleSidebar();
  }
});

// Close modals on backdrop click
window.addEventListener("click", function (e) {
  if (e.target === document.getElementById("buyModal")) closeBuyModal();
  if (e.target === document.getElementById("sellModal")) closeSellModal();
  if (e.target === document.getElementById("closeModal")) closeCloseModal();
});
