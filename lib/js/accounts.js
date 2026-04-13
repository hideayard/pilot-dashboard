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
  let totalBalance = 0,
    totalProfit = 0,
    activeBots = 0;

  accounts.forEach((acc) => {
    const status = (acc.status || "").toLowerCase();
    const isActive =
      status === "active" || status === "connected" || status === "live";

    totalBalance += parseFloat(acc.account_balance || 0);
    totalProfit += parseFloat(acc.total_profit || 0);
    if (isActive) activeBots++;
  });

  const set = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  };

  set("stat-total", accounts.length);
  set("stat-balance", formatMoney(totalBalance));
  set("stat-active", `${activeBots}/${accounts.length}`);

  const profitEl = document.getElementById("stat-profit");
  if (profitEl) {
    profitEl.textContent = formatMoney(totalProfit);
    profitEl.className =
      "text-xl font-bold " +
      (totalProfit >= 0 ? "text-green-600" : "text-red-600");
  }

  // update filter counts based on the full unfiltered data
  // so the badge numbers on filter buttons always show totals
  updateFilterCounts(accountsData);
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
  const safeId = String(accountId).replace(/['"]/g, "");
  const safeBot = botName.replace(/['"]/g, "");
  const safeBalance = formatMoney(balance).replace(/['"]/g, "");

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
  const buyStatus = acc.buy_status;
  const sellStatus = acc.sell_status;
  const safeMinLot = parseFloat(acc.min_lot || 0.01).toFixed(2); // ← add this
  console.log(buyStatus, acc.buy_status);

  const buyLabel =
    buyStatus == 0
      ? `<i class="fas fa-arrow-up text-blue-600"></i> Buy <span style="font-size:0.65rem; font-weight:700; color:#ef4444;">(disabled)</span>`
      : `<i class="fas fa-arrow-up text-blue-600"></i> Buy `;

  const sellLabel =
    sellStatus == 0
      ? `<i class="fas fa-arrow-down text-red-600"></i> Sell <span style="font-size:0.65rem; font-weight:700; color:#ef4444;">(disabled)</span>`
      : `<i class="fas fa-arrow-down text-red-600"></i> Sell `;
  // Auto trade status (default false if not provided)
  const autoTradeEnabled = parseInt(acc.disabled_ea || 0) === 0;

  const isActive =
    status === "active" || status === "connected" || status === "live";
  const isDemo = accountType === "demo";
  const isOffline = !isActive;
  const hasOrders = totalOrders > 0;
  const safeType = isDemo ? "Demo" : "Live";
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
  const autoTradeClass = autoTradeEnabled ? "auto-trade-on" : "auto-trade-off";

  const autoTradeButton = `
  <button class="action-auto ${autoTradeClass} ${btnOpacity}" ${btnDisabled}
      onclick="toggleAutoTrade('${safeId}','${safeBot}', ${!autoTradeEnabled})"
      style="flex-direction:column; gap:0.1rem; padding:0.4rem 0.25rem;">
      <span style="font-size:0.65rem; font-weight:700; line-height:1;">
          ${autoTradeEnabled ? "EA:ENABLED" : "EA:DISABLED"}
      </span>
      <span style="font-size:0.55rem; font-weight:400; opacity:0.85; line-height:1;">
          click to ${autoTradeEnabled ? "disable" : "enable"}
      </span>
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
                        <p class="text-white font-semibold bot-name text-sm">
                            ${botName}
                            ${
                              !autoTradeEnabled
                                ? `<span style="font-size:0.65rem; font-weight:400; opacity:0.75;" class="text-red-300">(EA disabled)</span>`
                                : ""
                            }
                        </p>
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
                        <span class="metric-label">${buyLabel}</span>
                        <span class="metric-value ${buyCount > 0 ? "text-blue-600" : ""} font-bold">${buyCount} (${buyLot} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">${sellLabel}</span>
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
                        <button class="action-orders ${btnOpacity}" ${btnDisabled}
                            onclick="viewAccountOrders('${safeId}','${safeBot}')">
                            <i class="fas fa-list text-gray-400"></i> Orders
                        </button>
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
                            onclick="openBuyModal('${safeId}','${safeBot}','${safeBalance}','${safeType}','${safeMinLot}')">
                            <i class="fas fa-arrow-up text-[0.6rem]"></i> Buy
                        </button>
                        <button class="action-sell ${btnOpacity}" ${btnDisabled}
                            onclick="openSellModal('${safeId}','${safeBot}','${safeBalance}','${safeType}','${safeMinLot}')">
                            <i class="fas fa-arrow-down text-[0.6rem]"></i> Sell
                        </button>
                        ${closeButton}
                    </div>
                </div>
            </div>`;
}

// ─── TOGGLE BUY/SELL STATUS ───────────────────────────────────────────────────
async function toggleBuySellStatus(accountId, type, enable) {
  // enable: 1 = enable, 0 = disable
  const label = type === "buy" ? "Buy" : "Sell";
  const actionText = enable ? "enable" : "disable";

  const result = await Swal.fire({
    title: `${enable ? "Enable" : "Disable"} ${label}?`,
    text: `Are you sure you want to ${actionText} ${label} orders for account ${accountId}?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: enable ? "#10b981" : "#ef4444",
    cancelButtonColor: "#6b7280",
    confirmButtonText: `Yes, ${actionText}`,
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) return;

  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    Swal.fire({
      title: "Processing...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: "toggle_buy_sell_status",
        user_id: userId,
        account_id: accountId,
        type: type, // "buy" or "sell"
        status: enable, // 1 = enable, 0 = disable
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      // Update local accountsData
      const account = accountsData.find((acc) => acc.account_id == accountId);
      if (account) {
        if (type === "buy") account.buy_status = enable;
        if (type === "sell") account.sell_status = enable;
      }

      await refreshAccountCard(accountId);

      Swal.fire({
        icon: "success",
        title: "Success!",
        text: `${label} orders ${enable ? "enabled" : "disabled"} for account ${accountId}`,
        timer: 2000,
        showConfirmButton: false,
      });

      showToast(
        "success",
        `${label} ${enable ? "enabled" : "disabled"} for ${accountId}`,
      );
    } else {
      throw new Error(data.message || `Failed to toggle ${label} status`);
    }
  } catch (error) {
    console.error(`Error toggling ${type} status:`, error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message || "Failed to update. Please try again.",
    });
    showToast("error", `Failed to toggle ${label} status`);
  }
}

// ─── AUTO TRADE TOGGLE ───────────────────────────────────────────────────────
async function toggleAutoTrade(accountId, botName, enable) {
  // enable=true means user wants auto ON → disabled_ea=0
  // enable=false means user wants auto OFF → disabled_ea=1

  const result = await Swal.fire({
    title: `${enable ? "Enable" : "Disable"} Auto Trading?`,
    text: `Are you sure you want to ${enable ? "enable" : "disable"} auto trading for account ${accountId}?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: enable ? "#10b981" : "#ef4444",
    cancelButtonColor: "#6b7280",
    confirmButtonText: `Yes, ${enable ? "enable" : "disable"} EA`,
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) return;

  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    Swal.fire({
      title: "Processing...",
      text: `${enable ? "Enabling" : "Disabling"} auto trading...`,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

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
        disabled_ea: enable ? "0" : "1", // ← send disabled_ea instead of enable
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      // Update local accountsData to reflect new disabled_ea value
      const account = accountsData.find((acc) => acc.account_id == accountId);
      if (account) {
        account.disabled_ea = enable ? 0 : 1;
      }

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
      text: error.message || "Failed to toggle auto trading. Please try again.",
    });
    showToast("error", "Failed to toggle auto trading");
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
  const visibleIds = [];

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

    const visible = statusMatch && searchMatch;
    card.style.display = visible ? "" : "none";
    if (visible) visibleIds.push(card.dataset.id);
  });

  // re-calculate summary for only the visible accounts
  const visibleAccounts = accountsData.filter((acc) =>
    visibleIds.includes(String(acc.account_id).toLowerCase()),
  );
  computeSummaryLocal(visibleAccounts);
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
    applyFilterAndSearch(); // instead of computeSummaryLocal(data)
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

// ─── AUTO REFRESH ─────────────────────────────────────────────────────────────
let autoRefreshInterval = null;

async function refreshAccounts() {
  try {
    const data = await fetchAccountsFromServer(); // updates accountsData globally
    renderCards(data);
    applyFilterAndSearch(); // re-apply current filter + search + recalculate summary
    console.log("Auto-refreshed at", new Date().toLocaleTimeString());
  } catch (err) {
    console.warn("Auto-refresh failed:", err.message);
  }
}

function startAutoRefresh(intervalMs = 60000) {
  stopAutoRefresh(); // clear any existing interval first
  autoRefreshInterval = setInterval(refreshAccounts, intervalMs);
  console.log("Auto-refresh started — every", intervalMs / 1000, "seconds");
}

function stopAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
    autoRefreshInterval = null;
  }
}

// document.addEventListener("DOMContentLoaded", initAccounts);

// Start after first load, pause when tab is hidden to avoid wasted requests
document.addEventListener("DOMContentLoaded", () => {
  initAccounts().then(() => startAutoRefresh());
});

document.addEventListener("visibilitychange", () => {
  if (document.hidden) {
    stopAutoRefresh();
  } else {
    refreshAccounts(); // immediate refresh when tab becomes visible again
    startAutoRefresh();
  }
});

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
function openBuyModal(accountId, botName, balance, type, minLot = "0.01") {
  document.getElementById("buyAccountInfo").innerHTML = `
    <div class="modal-account-id">${accountId}</div>
    <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;

  const lotInput = document.getElementById("buyLotInput");
  if (lotInput) {
    lotInput.value = minLot;
    lotInput.min = minLot; // enforce min_lot as minimum
    lotInput.step = 0.01; // step by min_lot increments
  }

  document.getElementById("buyModal").style.display = "flex";
}

function closeBuyModal() {
  document.getElementById("buyModal").style.display = "none";
}

// ─── BUY MODAL ───────────────────────────────────────────────────────────────
async function confirmBuyTrade() {
  const lot = parseFloat(document.getElementById("buyLotInput")?.value || 0.01);
  const accountId = document.querySelector(
    "#buyAccountInfo .modal-account-id",
  )?.textContent;
  const botName = document
    .querySelector("#buyAccountInfo .modal-bot-name")
    ?.textContent.split(" • ")[0];

  if (!accountId) {
    showToast("error", "Account information missing");
    closeBuyModal();
    return;
  }

  const result = await Swal.fire({
    title: "Confirm Buy Order",
    text: `Place BUY order for ${lot} lot on account ${accountId}?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#10b981",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, place order",
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) {
    closeBuyModal();
    return;
  }

  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    Swal.fire({
      title: "Processing...",
      text: "Placing buy order...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: "order_buy",
        user_id: userId,
        account_id: accountId,
        lot: lot,
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      await refreshAccountCard(accountId);

      Swal.fire({
        icon: "success",
        title: "Success!",
        text: `Buy order placed: ${lot} lot on ${botName || accountId}`,
        timer: 2000,
        showConfirmButton: false,
      });

      showToast("success", `Buy order placed: ${lot} lot`);
    } else {
      throw new Error(data.message || "Failed to place buy order");
    }
  } catch (error) {
    console.error("Error placing buy order:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message || "Failed to place buy order. Please try again.",
    });
    showToast("error", "Failed to place buy order");
  }

  closeBuyModal();
}

// ─── SELL MODAL ──────────────────────────────────────────────────────────────
async function confirmSellTrade() {
  const lot = parseFloat(
    document.getElementById("sellLotInput")?.value || 0.01,
  );
  const accountId = document.querySelector(
    "#sellAccountInfo .modal-account-id",
  )?.textContent;
  const botName = document
    .querySelector("#sellAccountInfo .modal-bot-name")
    ?.textContent.split(" • ")[0];

  if (!accountId) {
    showToast("error", "Account information missing");
    closeSellModal();
    return;
  }

  const result = await Swal.fire({
    title: "Confirm Sell Order",
    text: `Place SELL order for ${lot} lot on account ${accountId}?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, place order",
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) {
    closeSellModal();
    return;
  }

  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    Swal.fire({
      title: "Processing...",
      text: "Placing sell order...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: "order_sell",
        user_id: userId,
        account_id: accountId,
        lot: lot,
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      await refreshAccountCard(accountId);

      Swal.fire({
        icon: "success",
        title: "Success!",
        text: `Sell order placed: ${lot} lot on ${botName || accountId}`,
        timer: 2000,
        showConfirmButton: false,
      });

      showToast("success", `Sell order placed: ${lot} lot`);
    } else {
      throw new Error(data.message || "Failed to place sell order");
    }
  } catch (error) {
    console.error("Error placing sell order:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message || "Failed to place sell order. Please try again.",
    });
    showToast("error", "Failed to place sell order");
  }

  closeSellModal();
}

// ─── SELL MODAL ──────────────────────────────────────────────────────────────
function openSellModal(accountId, botName, balance, type, minLot = "0.01") {
  document.getElementById("sellAccountInfo").innerHTML = `
    <div class="modal-account-id">${accountId}</div>
    <div class="modal-bot-name">${botName} • Balance: ${balance} • ${type}</div>`;

  const lotInput = document.getElementById("sellLotInput");
  if (lotInput) {
    lotInput.value = minLot;
    lotInput.min = minLot;
    lotInput.step = 0.01;
  }

  document.getElementById("sellModal").style.display = "flex";
}

function closeSellModal() {
  document.getElementById("sellModal").style.display = "none";
}

// ─── CLOSE POSITIONS MODAL ───────────────────────────────────────────────────
function openCloseModal(accountId, botName) {
  // Find the account data
  const account = accountsData.find((acc) => acc.account_id == accountId);

  if (!account) {
    showToast("error", "Account data not found");
    return;
  }

  // Extract position data from account
  const buyCount = parseInt(account.buy_order_count || 0);
  const sellCount = parseInt(account.sell_order_count || 0);
  const totalOrders = buyCount + sellCount;

  const buyLot = parseFloat(account.total_buy_lot || 0);
  const sellLot = parseFloat(account.total_sell_lot || 0);
  const totalVolume = (buyLot + sellLot).toFixed(2);

  const floating = parseFloat(account.floating_value || 0);
  const floatingFormatted = formatMoney(Math.abs(floating));
  const floatingSign = floating >= 0 ? "+" : "-";
  const floatingClass = floating >= 0 ? "text-green-600" : "text-red-600";

  // Update modal content
  document.getElementById("closeAccountInfo").innerHTML = `
    <div class="modal-account-id">${accountId}</div>
    <div class="modal-bot-name">${botName}</div>`;

  // Update trade summary with real data
  document.getElementById("openPositionsCount").innerHTML = `
    <span class="font-semibold">${totalOrders}</span>
    <span class="text-xs text-gray-500 ml-1">(${buyCount} buy / ${sellCount} sell)</span>`;

  document.getElementById("totalVolume").innerHTML = `
    <span class="font-semibold">${totalVolume} lot</span>
    <span class="text-xs text-gray-500 ml-1">(Buy: ${buyLot} / Sell: ${sellLot})</span>`;

  document.getElementById("currentFloating").innerHTML = `
    <span class="font-semibold ${floatingClass}">${floatingSign}${floatingFormatted}</span>
    <span class="text-xs text-gray-500 ml-1">(${(account.total_profit_percentage || 0).toFixed(1)}%)</span>`;

  // Store account ID for confirm function
  document
    .getElementById("closeModal")
    .setAttribute("data-account-id", accountId);
  document.getElementById("closeModal").setAttribute("data-bot-name", botName);

  document.getElementById("closeModal").style.display = "flex";
}

function closeCloseModal() {
  document.getElementById("closeModal").style.display = "none";
}

// ─── CLOSE POSITIONS MODAL ───────────────────────────────────────────────────
async function confirmClosePositions() {
  const modal = document.getElementById("closeModal");
  const accountId = modal?.getAttribute("data-account-id");
  const botName = modal?.getAttribute("data-bot-name");

  // Fallback to DOM query if attribute not found
  const fallbackAccountId = document.querySelector(
    "#closeAccountInfo .modal-account-id",
  )?.textContent;

  const finalAccountId = accountId || fallbackAccountId;

  if (!finalAccountId) {
    showToast("error", "Account information missing");
    closeCloseModal();
    return;
  }

  // Find account to show additional info in confirmation
  const account = accountsData.find((acc) => acc.account_id == finalAccountId);
  const totalOrders =
    parseInt(account?.buy_order_count || 0) +
    parseInt(account?.sell_order_count || 0);

  if (totalOrders === 0) {
    Swal.fire({
      icon: "info",
      title: "No Positions",
      text: "This account has no open positions to close.",
      timer: 2000,
      showConfirmButton: false,
    });
    closeCloseModal();
    return;
  }

  const result = await Swal.fire({
    title: "Close All Positions?",
    html: `Are you sure you want to close ALL positions for account <strong>${finalAccountId}</strong>?<br><br>
           <div style="text-align: left; background: #f3f4f6; padding: 10px; border-radius: 8px; font-size: 13px;">
             <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
               <span>📊 Total Positions:</span>
               <span class="font-semibold">${totalOrders}</span>
             </div>
             <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
               <span>📈 Total Volume:</span>
               <span class="font-semibold">${document.getElementById("totalVolume")?.textContent || "—"}</span>
             </div>
             <div style="display: flex; justify-content: space-between;">
               <span>💵 Current Floating:</span>
               <span class="font-semibold">${document.getElementById("currentFloating")?.innerHTML || "—"}</span>
             </div>
           </div><br>
           <span class="text-red-600 text-sm">⚠️ This action cannot be undone!</span>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, close all positions",
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) {
    closeCloseModal();
    return;
  }

  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    Swal.fire({
      title: "Processing...",
      text: "Closing all positions...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: "close_all_positions",
        user_id: userId,
        account_id: finalAccountId,
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      // Refresh account data after closing positions
      await refreshAccounts(); // Refresh all accounts
      // Or specifically refresh this account:
      // await refreshAccountCard(finalAccountId);

      Swal.fire({
        icon: "success",
        title: "Success!",
        text: `All positions closed for account ${finalAccountId}`,
        timer: 2000,
        showConfirmButton: false,
      });

      showToast(
        "success",
        `All positions closed for ${botName || finalAccountId}`,
      );
    } else {
      throw new Error(data.message || "Failed to close positions");
    }
  } catch (error) {
    console.error("Error closing positions:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message || "Failed to close positions. Please try again.",
    });
    showToast("error", "Failed to close positions");
  }

  closeCloseModal();
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

// ─── ORDER FETCHING FUNCTIONS ────────────────────────────────────────────────

/**
 * Get all orders for an account
 */
async function getAccountOrders(accountId, status = "all") {
  try {
    const authToken = localStorage.getItem("jwt");
    const userId = getUserIdFromJwt();

    let action = "get_orders_by_account";
    if (status === "open") action = "get_open_orders";
    if (status === "closed") action = "get_closed_orders";

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: action,
        user_id: userId,
        account_id: accountId,
      }),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (data.status === "success") {
      return data.data;
    } else {
      throw new Error(data.message || "Failed to fetch orders");
    }
  } catch (error) {
    console.error("Error fetching orders:", error);
    showToast("error", "Failed to fetch orders: " + error.message);
    return null;
  }
}

/**
 * Get account data from local accountsData by account ID
 */
function getAccountDataById(accountId) {
  return accountsData.find((acc) => acc.account_id == accountId);
}

/**
 * Display open orders popup with Show History button
 * Uses totalLots and totalFloating from parent card data
 */
async function viewAccountOrders(accountId, botName) {
  // Get account data from local storage
  const accountData = getAccountDataById(accountId);

  if (!accountData) {
    showToast("error", "Account data not found");
    return;
  }

  // Extract data from account card
  const totalLots = (
    parseFloat(accountData.total_buy_lot || 0) +
    parseFloat(accountData.total_sell_lot || 0)
  ).toFixed(2);
  const totalFloating = parseFloat(accountData.floating_value || 0);
  const buyCount = parseInt(accountData.buy_order_count || 0);
  const sellCount = parseInt(accountData.sell_order_count || 0);
  const openOrdersCount = buyCount + sellCount;

  // Show loading
  Swal.fire({
    title: `Open Orders - ${botName || accountId}`,
    html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading open orders...</p></div>',
    showConfirmButton: false,
    allowOutsideClick: false,
  });

  try {
    // Fetch open orders from API (for detailed list)
    const ordersData = await getAccountOrders(accountId, "open");

    if (!ordersData) {
      throw new Error("No order data received");
    }

    const orders = ordersData.orders || [];
    const apiOpenOrdersCount = ordersData.open_orders || orders.length;

    // Build HTML for open orders using card data for totals
    const floatingFormatted = formatMoney(totalFloating);
    const floatingClass =
      totalFloating >= 0 ? "color: #a7f3d0;" : "color: #fecaca;";
    const floatingSign = totalFloating > 0 ? "+" : "";

    ordersHtml = `
  <div style="max-height: 500px; overflow-y: auto;">
    <!-- Summary Stats from Card Data -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 12px; margin-bottom: 20px; color: white;">
      <div style="display: flex; justify-content: space-between; text-align: center;">
        <div style="flex: 1;">
          <div style="font-size: 24px; font-weight: bold;">${openOrdersCount}</div>
          <div style="font-size: 11px; opacity: 0.8;">Open Orders</div>
          <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">Buy: ${buyCount} | Sell: ${sellCount}</div>
        </div>
        <div style="flex: 1;">
          <div style="font-size: 24px; font-weight: bold;">${totalLots}</div>
          <div style="font-size: 11px; opacity: 0.8;">Total Lots</div>
          <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">Buy: ${parseFloat(accountData.total_buy_lot || 0).toFixed(2)} | Sell: ${parseFloat(accountData.total_sell_lot || 0).toFixed(2)}</div>
        </div>
        <div style="flex: 1;">
          <div style="font-size: 24px; font-weight: bold; ${floatingClass}">
            ${floatingSign}${floatingFormatted}
          </div>
          <div style="font-size: 11px; opacity: 0.8;">Floating P&L</div>
        </div>
      </div>
    </div>
`;

    // Open Orders Section
    if (apiOpenOrdersCount > 0 && orders.length > 0) {
      ordersHtml += `
        <div style="margin-bottom: 16px;">
          ${orders
            .map(
              (order) => `
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; margin-bottom: 10px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div>
                  <span style="font-weight: 700; font-size: 14px;">${order.symbol}</span>
                  <span style="margin-left: 8px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; ${order.type === 0 ? "background: #d1fae5; color: #065f46;" : "background: #fee2e2; color: #991b1b;"}">
                    ${order.type_desc}
                  </span>
                </div>
                <div>
                  <span style="font-weight: 700; font-size: 14px; ${order.profit >= 0 ? "color: #10b981;" : "color: #ef4444;"}">
                    ${order.profit >= 0 ? "+" : ""}${order.profit.toFixed(2)}
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-bottom: 6px;">
                <span><i class="fas fa-ticket-alt"></i> Ticket: ${order.ticket}</span>
                <span><i class="fas fa-weight-hanging"></i> Lots: ${order.lots}</span>
                <span><i class="fas fa-dollar-sign"></i> Open: ${order.open_price}</span>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af;">
                <span><i class="far fa-clock"></i> Opened: ${order.open_time_formatted}</span>
                <span><i class="fas fa-magic"></i> Magic: ${order.magic || 0}</span>
              </div>
              ${order.comment ? `<div style="margin-top: 6px; font-size: 11px; color: #6b7280; background: #f9fafb; padding: 4px 8px; border-radius: 6px;"><i class="fas fa-comment"></i> ${order.comment}</div>` : ""}
            </div>
          `,
            )
            .join("")}
        </div>
      `;
    } else {
      ordersHtml += `
        <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
          <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; display: block;"></i>
          <p>No open orders found for this account</p>
        </div>
      `;
    }

    ordersHtml += `</div>`;

    // Show popup with Show History button
    Swal.fire({
      title: `Open Orders - ${botName || accountId}`,
      html: ordersHtml,
      width: "700px",
      showConfirmButton: true,
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-history"></i> Show Trade History',
      cancelButtonText: "Close",
      confirmButtonColor: "#6b7280",
      cancelButtonColor: "#9ca3af",
      preConfirm: () => {
        // Call function to show closed orders history
        viewClosedOrdersHistory(accountId, botName);
        return false; // Prevent closing current modal
      },
    });
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonColor: "#ef4444",
    });
  }
}

/**
 * Display closed orders history popup
 */
async function viewClosedOrdersHistory(
  accountId,
  botName,
  page = 1,
  itemsPerPage = 20,
) {
  // Show loading
  Swal.fire({
    title: `Trade History - ${botName || accountId}`,
    html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading trade history...</p></div>',
    showConfirmButton: false,
    allowOutsideClick: false,
  });

  try {
    // Fetch closed orders
    const ordersData = await getAccountOrders(accountId, "closed");

    if (!ordersData) {
      throw new Error("No order data received");
    }

    const orders = ordersData.orders || [];
    const totalClosed = ordersData.total_closed || orders.length;
    const totalProfit = ordersData.total_profit || 0;
    const winRate = ordersData.win_rate || 0;
    const wins = ordersData.wins || 0;
    const losses = ordersData.losses || 0;

    // Calculate pagination
    const totalPages = Math.ceil(totalClosed / itemsPerPage);
    const startIndex = (page - 1) * itemsPerPage;
    const paginatedOrders = orders.slice(startIndex, startIndex + itemsPerPage);

    // Build HTML for closed orders
    let historyHtml = `
      <div style="max-height: 550px; overflow-y: auto;">
        <!-- Performance Summary -->
        <div style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); padding: 15px; border-radius: 12px; margin-bottom: 20px; color: white;">
          <div style="display: flex; justify-content: space-between; text-align: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 22px; font-weight: bold;">${totalClosed}</div>
              <div style="font-size: 10px; opacity: 0.8;">Total Trades</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 22px; font-weight: bold; ${totalProfit >= 0 ? "color: #a7f3d0" : "color: #fecaca"}">${totalProfit >= 0 ? "+" : ""}$${Math.abs(totalProfit).toFixed(2)}</div>
              <div style="font-size: 10px; opacity: 0.8;">Net Profit</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 22px; font-weight: bold; color: #a7f3d0">${winRate}%</div>
              <div style="font-size: 10px; opacity: 0.8;">Win Rate</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 22px; font-weight: bold; color: #34d399">${wins}</div>
              <div style="font-size: 10px; opacity: 0.8;">Wins</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 22px; font-weight: bold; color: #f87171">${losses}</div>
              <div style="font-size: 10px; opacity: 0.8;">Losses</div>
            </div>
          </div>
        </div>
    `;

    // Closed Orders List
    if (totalClosed > 0) {
      historyHtml += `
        <div style="margin-bottom: 16px;">
          ${paginatedOrders
            .map(
              (order) => `
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; margin-bottom: 10px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <div>
                  <span style="font-weight: 700; font-size: 14px;">${order.symbol}</span>
                  <span style="margin-left: 8px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; ${order.type === 0 ? "background: #d1fae5; color: #065f46;" : "background: #fee2e2; color: #991b1b;"}">
                    ${order.type_desc}
                  </span>
                </div>
                <div>
                  <span style="font-weight: 700; font-size: 16px; ${order.profit >= 0 ? "color: #10b981;" : "color: #ef4444;"}">
                    ${order.profit >= 0 ? "+" : ""}$${order.profit.toFixed(2)}
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                <span><i class="fas fa-ticket-alt"></i> Ticket: ${order.ticket}</span>
                <span><i class="fas fa-weight-hanging"></i> Lots: ${order.lots}</span>
                <span><i class="fas fa-chart-line"></i> ${order.open_price} → ${order.close_price}</span>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; flex-wrap: wrap; gap: 8px;">
                <span><i class="far fa-calendar-plus"></i> Open: ${order.open_time_formatted}</span>
                <span><i class="far fa-calendar-check"></i> Close: ${order.close_time_formatted}</span>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 6px; flex-wrap: wrap; gap: 8px;">
                <span><i class="fas fa-percentage"></i> Swap: $${order.swap.toFixed(2)}</span>
                <span><i class="fas fa-hand-holding-usd"></i> Commission: $${order.commission.toFixed(2)}</span>
                <span><i class="fas fa-magic"></i> Magic: ${order.magic || 0}</span>
              </div>
              ${order.comment ? `<div style="margin-top: 6px; font-size: 11px; color: #6b7280; background: #f9fafb; padding: 4px 8px; border-radius: 6px;"><i class="fas fa-comment"></i> ${order.comment}</div>` : ""}
            </div>
          `,
            )
            .join("")}
        </div>
      `;

      // Pagination controls
      if (totalPages > 1) {
        historyHtml += `
          <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px; padding: 12px 0; border-top: 1px solid #e5e7eb;">
            <button class="history-pagination-btn" data-page="${page - 1}" ${page === 1 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ""} style="padding: 6px 12px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;">
              ← Previous
            </button>
            <span style="padding: 6px 12px; font-size: 12px; color: #6b7280;">Page ${page} of ${totalPages}</span>
            <button class="history-pagination-btn" data-page="${page + 1}" ${page === totalPages ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ""} style="padding: 6px 12px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;">
              Next →
            </button>
          </div>
        `;
      }
    } else {
      historyHtml += `
        <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
          <i class="fas fa-history" style="font-size: 48px; margin-bottom: 12px; display: block;"></i>
          <p>No trade history found for this account</p>
        </div>
      `;
    }

    historyHtml += `</div>`;

    // Show history popup
    Swal.fire({
      title: `Trade History - ${botName || accountId}`,
      html: historyHtml,
      width: "800px",
      showConfirmButton: true,
      confirmButtonText:
        '<i class="fas fa-arrow-left"></i> Back to Open Orders',
      confirmButtonColor: "#667eea",
      showCancelButton: true,
      cancelButtonText: "Close",
      cancelButtonColor: "#9ca3af",
      preConfirm: () => {
        // Go back to open orders
        viewAccountOrders(accountId, botName);
        return false;
      },
      didRender: () => {
        // Attach pagination event handlers
        document.querySelectorAll(".history-pagination-btn").forEach((btn) => {
          btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const newPage = parseInt(btn.getAttribute("data-page"));
            if (!isNaN(newPage) && newPage >= 1 && newPage <= totalPages) {
              viewClosedOrdersHistory(
                accountId,
                botName,
                newPage,
                itemsPerPage,
              );
            }
          });
        });
      },
    });
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonColor: "#ef4444",
    });
  }
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
