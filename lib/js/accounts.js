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
      window.location.href = "/login.php";
      return;
    }
  } catch (e) {
    window.location.href = "/login.php";
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
  const safeMinLot = parseFloat(acc.min_lot || 0.01).toFixed(2);
  const mt4LastSync = acc.mt4_last_sync || null;

  // MT4 Login (account_id is the MT4 login number)
  const mt4Login = accountId;

  const buyLabel =
    buyStatus == 0
      ? `<i class="fas fa-arrow-up text-blue-600"></i> Buy <span style="font-size:0.65rem; font-weight:700; color:#ef4444;">(disabled)</span>`
      : `<i class="fas fa-arrow-up text-blue-600"></i> Buy `;

  const sellLabel =
    sellStatus == 0
      ? `<i class="fas fa-arrow-down text-red-600"></i> Sell <span style="font-size:0.65rem; font-weight:700; color:#ef4444;">(disabled)</span>`
      : `<i class="fas fa-arrow-down text-red-600"></i> Sell `;

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

  const mt4LastSyncStr = mt4LastSync ? formatLastSync(mt4LastSync) : "never";

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
                        <div class="flex flex-col items-end gap-1">
                            ${statusBadge}
                            <button class="sync-mt4-btn sync-btn-small ${btnOpacity}" 
                                    data-account-id="${safeId}"
                                    data-mt4-login="${mt4Login}"
                                    ${btnDisabled}
                                    onclick="syncWithMT4('${safeId}','${safeBot}','${mt4Login}', '')"
                                    style="background: rgba(255,255,255,0.2); border: none; border-radius: 20px; padding: 4px 10px; font-size: 10px; color: white; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                <i class="fas fa-sync-alt" style="font-size: 9px;"></i>
                                <span>MT4 Sync</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-white opacity-75 text-xs">Bot Name</p>
                        <p class="text-white font-semibold bot-name text-sm">
                            ${botName}
                            ${!autoTradeEnabled ? `<span style="font-size:0.65rem; font-weight:400; opacity:0.75;" class="text-red-300">(EA disabled)</span>` : ""}
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
                        <span class="metric-value ${buyCount > 0 ? "text-blue-600" : ""} font-bold">${buyCount} (${buyLot.toFixed(2)} lot)</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">${sellLabel}</span>
                        <span class="metric-value ${sellCount > 0 ? "text-red-600" : ""} font-bold">${sellCount} (${sellLot.toFixed(2)} lot)</span>
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

                    <div class="text-[0.6rem] text-gray-400 text-right mb-1 cursor-help flex justify-between items-center"
                        title="${lastSyncFull}">
                        <span><i class="fas fa-database mr-1"></i>Local: ${lastSyncStr}</span>
                        <span><i class="fas fa-cloud mr-1"></i>MT4: ${mt4LastSyncStr}</span>
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

    // Preserve MT4 tokens for accounts that still exist
    const newAccountsData = data;
    const validAccountIds = new Set(
      newAccountsData.map((acc) => String(acc.account_id)),
    );
    for (let [accountId, token] of mt4Tokens.entries()) {
      if (!validAccountIds.has(accountId)) {
        mt4Tokens.delete(accountId);
      }
    }

    // Preserve mt4_password for existing accounts
    for (const newAcc of newAccountsData) {
      const oldAcc = accountsData.find(
        (acc) => acc.account_id == newAcc.account_id,
      );
      if (oldAcc && oldAcc.mt4_password) {
        newAcc.mt4_password = oldAcc.mt4_password;
      }
    }

    accountsData = newAccountsData;
    renderCards(accountsData);
    applyFilterAndSearch();
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
  // initAccounts().then(() => startAutoRefresh());
  initAccounts().then(() => {
    startAutoRefresh();
    initAutoSync(); // Add this line
  });
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

// Store MT4 tokens per account
const mt4Tokens = new Map();

/**
 * Get MT4 token for an account
 */
async function getMT4Token(login, password) {
  try {
    const response = await fetch("https://mt4-dashboard.pages.dev/api/auth", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ login, password }),
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: Authentication failed`);
    }

    const data = await response.json();

    if (data.token) {
      return data.token;
    } else {
      throw new Error(data.error || "Failed to get token");
    }
  } catch (error) {
    console.error("Error getting MT4 token:", error);
    throw error;
  }
}

/**
 * Get account status from MT4 dashboard API
 */
async function getMT4AccountStatus(accountId, token) {
  try {
    const response = await fetch(
      `https://mt4-dashboard.pages.dev/api/status/${accountId}?token=${token}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      },
    );

    if (response.status === 401 || response.status === 403) {
      throw new Error(
        `Authentication failed: ${response.status} - Unauthorized`,
      );
    }

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: Failed to fetch status`);
    }

    const data = await response.json();

    if (data.success && data.data) {
      return data.data;
    } else {
      throw new Error(data.error || "Failed to fetch account status");
    }
  } catch (error) {
    console.error("Error fetching MT4 account status:", error);
    throw error;
  }
}

/**
 * Get open orders from MT4 dashboard API
 */
async function getMT4OpenOrders(accountId, token, limit = 50) {
  try {
    const response = await fetch(
      `https://mt4-dashboard.pages.dev/api/orders/open/${accountId}?token=${token}&limit=${limit}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      },
    );

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: Failed to fetch orders`);
    }

    const data = await response.json();

    if (data.success && data.data) {
      return {
        orders: data.data,
        count: data.count,
        login: data.login,
      };
    } else {
      throw new Error(data.error || "Failed to fetch orders");
    }
  } catch (error) {
    console.error("Error fetching MT4 open orders:", error);
    throw error;
  }
}

/**
 * Sync account with MT4 - fetch latest status and update card
 */
async function syncWithMT4(accountId, botName, mt4Login, mt4Password) {
  // Show loading state on the sync button
  const syncButtons = document.querySelectorAll(
    `.sync-mt4-btn[data-account-id="${accountId}"]`,
  );
  syncButtons.forEach((btn) => {
    btn.disabled = true;
    btn.innerHTML =
      '<i class="fas fa-spinner fa-spin" style="font-size: 9px;"></i> <span>Syncing...</span>';
  });

  try {
    // Get token
    let token = mt4Tokens.get(accountId);
    let tokenValid = false;

    if (token) {
      // Quick validation - try to fetch status with current token
      try {
        const testResponse = await fetch(
          `https://mt4-dashboard.pages.dev/api/status/${mt4Login}?token=${token}&limit=1`,
        );
        if (testResponse.ok) {
          tokenValid = true;
        } else if (testResponse.status === 401 || testResponse.status === 403) {
          // Token is invalid or expired
          console.log("Token invalid, removing from cache");
          mt4Tokens.delete(accountId);
          tokenValid = false;
        }
      } catch (e) {
        tokenValid = false;
      }
    }

    if (!tokenValid) {
      // Prompt for password if not provided or token was invalid
      let password = mt4Password;

      // Clear stored password if token was invalid
      if (!password || mt4Tokens.get(accountId) === undefined) {
        const account = accountsData.find((acc) => acc.account_id == accountId);
        if (account) {
          delete account.mt4_password; // Remove stored invalid password
        }
      }

      if (!password) {
        const result = await Swal.fire({
          title: "MT4 Password Required",
          text: `Please enter MT4 password for account ${accountId}`,
          input: "password",
          inputPlaceholder: "Enter your MT4 password",
          showCancelButton: true,
          confirmButtonText: "Submit",
          cancelButtonText: "Cancel",
          inputValidator: (value) => {
            if (!value) {
              return "Password is required!";
            }
          },
        });

        if (!result.isConfirmed || !result.value) {
          // Reset button state
          syncButtons.forEach((btn) => {
            btn.disabled = false;
            btn.innerHTML =
              '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
          });
          return;
        }
        password = result.value;
      }

      try {
        token = await getMT4Token(mt4Login, password);
        mt4Tokens.set(accountId, token);

        // Store password temporarily for this session
        const account = accountsData.find((acc) => acc.account_id == accountId);
        if (account) {
          account.mt4_password = password;
        }
      } catch (authError) {
        console.error("Authentication failed:", authError);

        // Remove any stored token/password
        mt4Tokens.delete(accountId);
        const account = accountsData.find((acc) => acc.account_id == accountId);
        if (account) {
          delete account.mt4_password;
        }

        // Show authentication error with retry option
        const retryResult = await Swal.fire({
          icon: "error",
          title: "Authentication Failed",
          html: `<p>Failed to authenticate for account ${accountId}</p>
                 <p style="font-size: 12px; color: #6b7280;">${authError.message || "Invalid credentials"}</p>`,
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "Retry",
          cancelButtonText: "Cancel",
          confirmButtonColor: "#667eea",
          cancelButtonColor: "#6b7280",
        });

        if (retryResult.isConfirmed) {
          // Retry sync with fresh password prompt
          syncButtons.forEach((btn) => {
            btn.disabled = false;
            btn.innerHTML =
              '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
          });
          await syncWithMT4(accountId, botName, mt4Login, null);
        } else {
          syncButtons.forEach((btn) => {
            btn.disabled = false;
            btn.innerHTML =
              '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
          });
        }
        return;
      }
    }

    // Fetch account status and account info in parallel
    let statusData, accountInfo;
    try {
      [statusData, accountInfo] = await Promise.all([
        getMT4AccountStatus(mt4Login, token),
        getMT4AccountInfo(mt4Login, token),
      ]);
    } catch (fetchError) {
      console.error("Failed to fetch data:", fetchError);

      // If fetch fails with 401/403, token might be invalid
      if (
        fetchError.message.includes("401") ||
        fetchError.message.includes("403") ||
        fetchError.message.includes("Unauthorized") ||
        fetchError.message.includes("Forbidden")
      ) {
        // Remove invalid token
        mt4Tokens.delete(accountId);
        const account = accountsData.find((acc) => acc.account_id == accountId);
        if (account) {
          delete account.mt4_password;
        }

        // Retry with fresh authentication
        const retryResult = await Swal.fire({
          icon: "warning",
          title: "Session Expired",
          text: `Your session for account ${accountId} has expired. Please re-enter your password.`,
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "Retry",
          cancelButtonText: "Cancel",
          confirmButtonColor: "#667eea",
        });

        if (retryResult.isConfirmed) {
          syncButtons.forEach((btn) => {
            btn.disabled = false;
            btn.innerHTML =
              '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
          });
          await syncWithMT4(accountId, botName, mt4Login, null);
        } else {
          syncButtons.forEach((btn) => {
            btn.disabled = false;
            btn.innerHTML =
              '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
          });
        }
        return;
      }
      throw fetchError;
    }

    if (statusData && statusData.authenticated) {
      // Update account data with MT4 status and account info
      const account = accountsData.find((acc) => acc.account_id == accountId);
      if (account) {
        // Update from account info endpoint (balance, equity, margin, etc.)
        if (accountInfo) {
          account.account_balance =
            accountInfo.balance || account.account_balance || 0;
          account.account_equity =
            accountInfo.equity || account.account_equity || 0;
          account.margin = accountInfo.margin || account.margin || 0;
          account.free_margin =
            accountInfo.freeMargin || account.free_margin || 0;
          account.margin_level =
            accountInfo.marginLevel || account.margin_level || 0;
          account.leverage = accountInfo.leverage || account.leverage || "100";
          account.currency = accountInfo.currency || account.currency || "USD";
          account.volume = accountInfo.volume || account.volume || 0;
        }

        // Update from status endpoint (open trades, closed trades)
        const openTrades = statusData.openTrades || [];
        const closedTrades = statusData.closedTrades || [];
        const userInfo = statusData.userInfo || {};

        // Calculate totals from open trades
        let totalBuyVolume = 0;
        let totalSellVolume = 0;
        let buyCount = 0;
        let sellCount = 0;
        let floatingProfit = 0;

        openTrades.forEach((trade) => {
          const volume = parseFloat(trade.Volume || 0);
          const profit = parseFloat(trade.Profit || 0);
          floatingProfit += profit;

          // Type: 0=Buy, 1=Sell, 2=Buy Limit, 3=Sell Limit, 4=Buy Stop, 5=Sell Stop
          if (trade.Type === 0 || trade.Type === 2 || trade.Type === 4) {
            buyCount++;
            totalBuyVolume += volume;
          } else {
            sellCount++;
            totalSellVolume += volume;
          }
        });

        // Calculate closed trades profit
        let closedProfit = 0;
        closedTrades.forEach((trade) => {
          closedProfit += parseFloat(trade.Profit || 0);
        });

        // Calculate total profit (could be from account info or from trades)
        let totalProfit = closedProfit;
        if (accountInfo && accountInfo.balance && account.account_balance) {
          // Calculate profit from balance and equity difference
          const balanceFromInfo = parseFloat(accountInfo.balance || 0);
          const equityFromInfo = parseFloat(accountInfo.equity || 0);
          totalProfit = equityFromInfo - balanceFromInfo;
        }

        // Update account object with all MT4 data
        account.buy_order_count = buyCount;
        account.sell_order_count = sellCount;
        account.total_buy_lot = totalBuyVolume;
        account.total_sell_lot = totalSellVolume;
        account.floating_value = floatingProfit;
        account.total_profit = totalProfit;
        account.total_orders = openTrades.length;
        account.mt4_last_sync = new Date().toISOString();
        account.mt4_status = "synced";

        if (userInfo.Name) account.bot_name = userInfo.Name;
        if (userInfo.OpenCount !== undefined)
          account.open_orders_count = userInfo.OpenCount;
        if (userInfo.ClosedCount !== undefined)
          account.closed_orders_count = userInfo.ClosedCount;
        if (userInfo.OpenVolume !== undefined)
          account.open_volume = userInfo.OpenVolume;
        if (userInfo.ClosedVolume !== undefined)
          account.closed_volume = userInfo.ClosedVolume;

        // Calculate profit percentage if we have balance
        if (account.account_balance && account.account_balance > 0) {
          const balanceVal = parseFloat(account.account_balance);
          const profitVal = parseFloat(account.total_profit || 0);
          account.total_profit_percentage = (profitVal / balanceVal) * 100;
        }
      }

      // Refresh the account card
      await refreshAccountCard(accountId);

      // Show success message with account info
      const openTradesCount = statusData.openTrades?.length || 0;
      const floatingPL =
        statusData.openTrades?.reduce(
          (sum, t) => sum + parseFloat(t.Profit || 0),
          0,
        ) || 0;
      const balanceFormatted = accountInfo
        ? formatMoney(accountInfo.balance)
        : "N/A";
      const equityFormatted = accountInfo
        ? formatMoney(accountInfo.equity)
        : "N/A";

      Swal.fire({
        icon: "success",
        title: "MT4 Sync Complete",
        html: `
          <div style="text-align: left;">
            <p><strong>Account:</strong> ${accountId}</p>
            <p><strong>Balance:</strong> ${balanceFormatted}</p>
            <p><strong>Equity:</strong> ${equityFormatted}</p>
            <p><strong>Open Trades:</strong> ${openTradesCount}</p>
            <p><strong>Floating P&L:</strong> <span class="${floatingPL >= 0 ? "text-green-600" : "text-red-600"}">${floatingPL >= 0 ? "+" : ""}$${Math.abs(floatingPL).toFixed(2)}</span></p>
            <p><strong>Leverage:</strong> ${accountInfo?.leverage || "N/A"}</p>
            <p><strong>Last Sync:</strong> ${new Date().toLocaleString()}</p>
          </div>
        `,
        timer: 4000,
        showConfirmButton: false,
      });

      showToast("success", `MT4 sync completed for account ${accountId}`);
    } else {
      throw new Error("Authentication failed - invalid credentials");
    }
  } catch (error) {
    console.error("Error syncing with MT4:", error);

    // Remove invalid token on any authentication error
    mt4Tokens.delete(accountId);
    const account = accountsData.find((acc) => acc.account_id == accountId);
    if (account) {
      delete account.mt4_password;
    }

    // Check if it's an authentication error
    const isAuthError =
      error.message.toLowerCase().includes("auth") ||
      error.message.toLowerCase().includes("credential") ||
      error.message.toLowerCase().includes("invalid");

    if (isAuthError) {
      const retryResult = await Swal.fire({
        icon: "error",
        title: "Authentication Failed",
        html: `<p>Failed to authenticate for account ${accountId}</p>
               <p style="font-size: 12px; color: #6b7280;">${error.message}</p>
               <p style="font-size: 12px; margin-top: 8px;">Please check your MT4 credentials and try again.</p>`,
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: "Retry",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#667eea",
        cancelButtonColor: "#6b7280",
      });

      if (retryResult.isConfirmed) {
        // Reset button state before retry
        syncButtons.forEach((btn) => {
          btn.disabled = false;
          btn.innerHTML =
            '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
        });
        await syncWithMT4(accountId, botName, mt4Login, null);
        return;
      }
    } else {
      Swal.fire({
        icon: "error",
        title: "Sync Failed",
        html: `<p>Failed to sync account ${accountId}</p>
               <p style="font-size: 12px; color: #6b7280;">${error.message}</p>`,
        confirmButtonColor: "#ef4444",
      });
    }

    showToast("error", `MT4 sync failed: ${error.message}`);
  } finally {
    // Reset button state if not already reset
    syncButtons.forEach((btn) => {
      if (btn.disabled) {
        btn.disabled = false;
        btn.innerHTML =
          '<i class="fas fa-sync-alt" style="font-size: 9px;"></i> <span>MT4 Sync</span>';
      }
    });
  }
}

/**
 * Get account information from MT4 dashboard API
 */
async function getMT4AccountInfo(accountId, token) {
  try {
    const response = await fetch(
      `https://mt4-dashboard.pages.dev/api/account/${accountId}?token=${token}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      },
    );

    if (response.status === 401 || response.status === 403) {
      throw new Error(
        `Authentication failed: ${response.status} - Unauthorized`,
      );
    }

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: Failed to fetch account info`);
    }

    const data = await response.json();

    if (data.success && data.data) {
      return data.data;
    } else {
      throw new Error(data.error || "Failed to fetch account info");
    }
  } catch (error) {
    console.error("Error fetching MT4 account info:", error);
    throw error;
  }
}

/**
 * Display open orders popup with Show History button
 * Uses MT4 Dashboard API with token authentication
 */
async function viewAccountOrders(accountId, botName) {
  // Get account data from local storage
  const accountData = getAccountDataById(accountId);

  if (!accountData) {
    showToast("error", "Account data not found");
    return;
  }

  // Extract login credentials from account data
  // You may need to adjust these field names based on your actual data structure
  const mt4Login = accountData.account_id || accountId; // This is the MT4 login number
  const mt4Password = accountData.mt4_password || accountData.password; // You'll need to store this securely

  if (!mt4Password) {
    // If password is not stored, prompt user for it
    const result = await Swal.fire({
      title: "MT4 Password Required",
      text: `Please enter MT4 password for account ${accountId}`,
      input: "password",
      inputPlaceholder: "Enter your MT4 password",
      showCancelButton: true,
      confirmButtonText: "Submit",
      cancelButtonText: "Cancel",
      inputValidator: (value) => {
        if (!value) {
          return "Password is required!";
        }
      },
    });

    if (!result.isConfirmed || !result.value) {
      return;
    }

    accountData.mt4_password = result.value; // Store temporarily (consider security implications)
  }

  const mt4PasswordFinal = mt4Password || accountData.mt4_password;

  // Check if we already have a token for this account and if it's still valid
  let token = mt4Tokens.get(accountId);
  let tokenValid = false;

  if (token) {
    // Quick validation - try to fetch orders with current token
    try {
      const testResponse = await fetch(
        `https://mt4-dashboard.pages.dev/api/orders/open/${mt4Login}?token=${token}&limit=1`,
      );
      if (testResponse.ok) {
        tokenValid = true;
      }
    } catch (e) {
      tokenValid = false;
    }
  }

  // Show loading
  Swal.fire({
    title: `Open Orders - ${botName || accountId}`,
    html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Authenticating and loading open orders...</p></div>',
    showConfirmButton: false,
    allowOutsideClick: false,
  });

  try {
    // Get token if not valid
    if (!tokenValid) {
      token = await getMT4Token(mt4Login, mt4PasswordFinal);
      mt4Tokens.set(accountId, token);
    }

    // Fetch open orders
    const ordersData = await getMT4OpenOrders(mt4Login, token, 100);

    const orders = ordersData.orders || [];
    const openOrdersCount = ordersData.count || orders.length;

    // Calculate totals from orders
    let totalVolume = 0;
    let totalProfit = 0;
    let buyCount = 0;
    let sellCount = 0;
    let buyVolume = 0;
    let sellVolume = 0;

    orders.forEach((order) => {
      const volume = parseFloat(order.volume || 0);
      const profit = parseFloat(order.profit || 0);
      totalVolume += volume;
      totalProfit += profit;

      // Type: 0=Buy, 1=Sell, 2=Buy Limit, 3=Sell Limit, 4=Buy Stop, 5=Sell Stop
      if (order.type === 0 || order.type === 2 || order.type === 4) {
        buyCount++;
        buyVolume += volume;
      } else {
        sellCount++;
        sellVolume += volume;
      }
    });

    const profitClass =
      totalProfit >= 0 ? "color: #a7f3d0;" : "color: #fecaca;";
    const profitSign = totalProfit > 0 ? "+" : "";

    // Format orders HTML
    let ordersHtml = `
      <div style="max-height: 550px; overflow-y: auto;">
        <!-- Summary Stats -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 12px; margin-bottom: 20px; color: white;">
          <div style="display: flex; justify-content: space-between; text-align: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 24px; font-weight: bold;">${openOrdersCount}</div>
              <div style="font-size: 11px; opacity: 0.8;">Open Orders</div>
              <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">Buy: ${buyCount} | Sell: ${sellCount}</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 24px; font-weight: bold;">${totalVolume.toFixed(2)}</div>
              <div style="font-size: 11px; opacity: 0.8;">Total Lots</div>
              <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">Buy: ${buyVolume.toFixed(2)} | Sell: ${sellVolume.toFixed(2)}</div>
            </div>
            <div style="flex: 1; min-width: 80px; margin-bottom: 8px;">
              <div style="font-size: 24px; font-weight: bold; ${profitClass}">
                ${profitSign}$${Math.abs(totalProfit).toFixed(2)}
              </div>
              <div style="font-size: 11px; opacity: 0.8;">Floating P&L</div>
            </div>
          </div>
        </div>
    `;

    // Type mapping for display
    const typeMap = {
      0: "Buy",
      1: "Sell",
      2: "Buy Limit",
      3: "Sell Limit",
      4: "Buy Stop",
      5: "Sell Stop",
    };

    // Open Orders Section
    if (openOrdersCount > 0 && orders.length > 0) {
      ordersHtml += `
        <div style="margin-bottom: 16px;">
          ${orders
            .map((order) => {
              const typeLabel = typeMap[order.type] || "Unknown";
              const isBuy =
                order.type === 0 || order.type === 2 || order.type === 4;
              const profitVal = parseFloat(order.profit || 0);

              // Format open time
              let openTimeFormatted = order.openTime;
              if (order.openTime) {
                const d = new Date(order.openTime);
                if (!isNaN(d.getTime())) {
                  openTimeFormatted = d.toLocaleString();
                }
              }

              return `
                  <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; margin-bottom: 10px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                      <div>
                        <span style="font-weight: 700; font-size: 14px;">${order.symbol}</span>
                        <span style="margin-left: 8px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; ${isBuy ? "background: #d1fae5; color: #065f46;" : "background: #fee2e2; color: #991b1b;"}">
                          ${typeLabel}
                        </span>
                      </div>
                      <div>
                        <span style="font-weight: 700; font-size: 14px; ${profitVal >= 0 ? "color: #10b981;" : "color: #ef4444;"}">
                          ${profitVal >= 0 ? "+" : ""}$${profitVal.toFixed(2)}
                        </span>
                      </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                      <span><i class="fas fa-ticket-alt"></i> Ticket: ${order.ticket}</span>
                      <span><i class="fas fa-weight-hanging"></i> Lots: ${order.volume.toFixed(2)}</span>
                      <span><i class="fas fa-dollar-sign"></i> Open: ${order.openPrice.toFixed(5)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; flex-wrap: wrap; gap: 8px;">
                      <span><i class="fas fa-chart-line"></i> Current: ${order.currentPrice.toFixed(5)}</span>
                      <span><i class="far fa-clock"></i> Opened: ${openTimeFormatted}</span>
                    </div>
                    ${order.swap ? `<div style="margin-top: 6px; font-size: 11px; color: #6b7280;"><i class="fas fa-percentage"></i> Swap: $${order.swap.toFixed(2)}</div>` : ""}
                    ${order.comment ? `<div style="margin-top: 6px; font-size: 11px; color: #6b7280; background: #f9fafb; padding: 4px 8px; border-radius: 6px;"><i class="fas fa-comment"></i> ${order.comment}</div>` : ""}
                  </div>
                `;
            })
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
      width: "800px",
      showConfirmButton: true,
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-history"></i> Show Trade History',
      cancelButtonText: "Close",
      confirmButtonColor: "#6b7280",
      cancelButtonColor: "#9ca3af",
      preConfirm: () => {
        // Call function to show closed orders history
        viewClosedOrdersHistory(accountId, botName);
        return false;
      },
    });
  } catch (error) {
    console.error("Error in viewAccountOrders:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      html: `<p>Failed to load orders: ${error.message}</p>
             <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Please check your MT4 credentials and try again.</p>`,
      confirmButtonColor: "#ef4444",
    });
  }
}

/**
 * Get closed orders history from MT4 Dashboard API
 */
async function getMT4ClosedOrders(accountId, token, limit = 200) {
  try {
    const response = await fetch(
      `https://mt4-dashboard.pages.dev/api/orders/closed/${accountId}?token=${token}&limit=${limit}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      },
    );

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: Failed to fetch closed orders`);
    }

    const data = await response.json();

    if (data.success && data.data) {
      return {
        orders: data.data,
        count: data.count,
        login: data.login,
      };
    } else {
      throw new Error(data.error || "Failed to fetch closed orders");
    }
  } catch (error) {
    console.error("Error fetching MT4 closed orders:", error);
    throw error;
  }
}

/**
 * Display closed orders history popup (updated to use MT4 API)
 */
async function viewClosedOrdersHistory(
  accountId,
  botName,
  page = 1,
  itemsPerPage = 20,
) {
  // Get account data
  const accountData = getAccountDataById(accountId);
  if (!accountData) {
    showToast("error", "Account data not found");
    return;
  }

  const mt4Login = accountData.account_id || accountId;
  const token = mt4Tokens.get(accountId);

  if (!token) {
    Swal.fire({
      icon: "error",
      title: "Session Expired",
      text: "Please go back to open orders to re-authenticate.",
      confirmButtonColor: "#ef4444",
    });
    return;
  }

  // Show loading
  Swal.fire({
    title: `Trade History - ${botName || accountId}`,
    html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading trade history...</p></div>',
    showConfirmButton: false,
    allowOutsideClick: false,
  });

  try {
    // Fetch closed orders
    const ordersData = await getMT4ClosedOrders(mt4Login, token, 500);
    const orders = ordersData.orders || [];
    const totalClosed = ordersData.count || orders.length;

    // Calculate statistics
    let totalProfit = 0;
    let wins = 0;
    let losses = 0;
    let totalVolume = 0;

    orders.forEach((order) => {
      const profit = parseFloat(order.profit || 0);
      const volume = parseFloat(order.volume || 0);
      totalProfit += profit;
      totalVolume += volume;
      if (profit > 0) wins++;
      if (profit < 0) losses++;
    });

    const winRate =
      totalClosed > 0 ? ((wins / totalClosed) * 100).toFixed(1) : 0;

    // Calculate pagination
    const totalPages = Math.ceil(totalClosed / itemsPerPage);
    const startIndex = (page - 1) * itemsPerPage;
    const paginatedOrders = orders.slice(startIndex, startIndex + itemsPerPage);

    // Type mapping
    const typeMap = {
      0: "Buy",
      1: "Sell",
      2: "Buy Limit",
      3: "Sell Limit",
      4: "Buy Stop",
      5: "Sell Stop",
    };

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
            .map((order) => {
              const typeLabel = typeMap[order.type] || "Unknown";
              const isBuy =
                order.type === 0 || order.type === 2 || order.type === 4;
              const profitVal = parseFloat(order.profit || 0);

              // Format dates
              let openTimeFormatted = order.openTime;
              let closeTimeFormatted = order.closeTime;
              if (order.openTime) {
                const d = new Date(order.openTime);
                if (!isNaN(d.getTime())) openTimeFormatted = d.toLocaleString();
              }
              if (order.closeTime) {
                const d = new Date(order.closeTime);
                if (!isNaN(d.getTime()))
                  closeTimeFormatted = d.toLocaleString();
              }

              return `
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; margin-bottom: 10px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                    <div>
                      <span style="font-weight: 700; font-size: 14px;">${order.symbol}</span>
                      <span style="margin-left: 8px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; ${isBuy ? "background: #d1fae5; color: #065f46;" : "background: #fee2e2; color: #991b1b;"}">
                        ${typeLabel}
                      </span>
                    </div>
                    <div>
                      <span style="font-weight: 700; font-size: 16px; ${profitVal >= 0 ? "color: #10b981;" : "color: #ef4444;"}">
                        ${profitVal >= 0 ? "+" : ""}$${profitVal.toFixed(2)}
                      </span>
                    </div>
                  </div>
                  <div style="display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                    <span><i class="fas fa-ticket-alt"></i> Ticket: ${order.ticket}</span>
                    <span><i class="fas fa-weight-hanging"></i> Lots: ${order.volume.toFixed(2)}</span>
                    <span><i class="fas fa-chart-line"></i> ${order.openPrice.toFixed(5)} → ${order.closePrice.toFixed(5)}</span>
                  </div>
                  <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; flex-wrap: wrap; gap: 8px;">
                    <span><i class="far fa-calendar-plus"></i> Open: ${openTimeFormatted}</span>
                    <span><i class="far fa-calendar-check"></i> Close: ${closeTimeFormatted}</span>
                  </div>
                  ${order.swap ? `<div style="margin-top: 6px; font-size: 11px;"><i class="fas fa-percentage"></i> Swap: $${order.swap.toFixed(2)}</div>` : ""}
                  ${order.comment ? `<div style="margin-top: 6px; font-size: 11px; color: #6b7280; background: #f9fafb; padding: 4px 8px; border-radius: 6px;"><i class="fas fa-comment"></i> ${order.comment}</div>` : ""}
                </div>
              `;
            })
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
      width: "900px",
      showConfirmButton: true,
      confirmButtonText:
        '<i class="fas fa-arrow-left"></i> Back to Open Orders',
      confirmButtonColor: "#667eea",
      showCancelButton: true,
      cancelButtonText: "Close",
      cancelButtonColor: "#9ca3af",
      preConfirm: () => {
        viewAccountOrders(accountId, botName);
        return false;
      },
      didRender: () => {
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
    console.error("Error in viewClosedOrdersHistory:", error);
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

// ─── AUTO SYNC FUNCTIONALITY ────────────────────────────────────────────────
let autoSyncInterval = null;
let autoSyncEnabled = false;
let autoSyncIntervalMs = 60000; // Default 1 minute

// Load auto sync settings from localStorage
function loadAutoSyncSettings() {
  const saved = localStorage.getItem("autoSyncSettings");
  if (saved) {
    try {
      const settings = JSON.parse(saved);
      autoSyncEnabled = settings.enabled || false;
      autoSyncIntervalMs = settings.intervalMs || 60000;

      // Update UI if modal is open
      const toggle = document.getElementById("autoSyncToggle");
      if (toggle) toggle.checked = autoSyncEnabled;

      const seconds = document.getElementById("syncSeconds");
      const minutes = document.getElementById("syncMinutes");
      const hours = document.getElementById("syncHours");

      if (seconds && minutes && hours) {
        const totalSeconds = Math.floor(autoSyncIntervalMs / 1000);
        hours.value = Math.floor(totalSeconds / 3600);
        minutes.value = Math.floor((totalSeconds % 3600) / 60);
        seconds.value = totalSeconds % 60;
        updateIntervalPreview();
      }

      if (autoSyncEnabled) {
        startAutoSync();
      }
    } catch (e) {
      console.error("Error loading auto sync settings:", e);
    }
  }
}

// Save auto sync settings
function saveAutoSyncSettings() {
  const enabled = document.getElementById("autoSyncToggle").checked;
  const seconds = parseInt(document.getElementById("syncSeconds").value) || 0;
  const minutes = parseInt(document.getElementById("syncMinutes").value) || 0;
  const hours = parseInt(document.getElementById("syncHours").value) || 0;

  let totalSeconds = seconds + minutes * 60 + hours * 3600;

  // Minimum interval 5 seconds
  if (totalSeconds < 5 && enabled) {
    Swal.fire({
      icon: "warning",
      title: "Invalid Interval",
      text: "Minimum sync interval is 5 seconds. Setting to 5 seconds.",
      timer: 2000,
      showConfirmButton: false,
    });
    totalSeconds = 5;
    document.getElementById("syncSeconds").value = 5;
    document.getElementById("syncMinutes").value = 0;
    document.getElementById("syncHours").value = 0;
  }

  const intervalMs = totalSeconds * 1000;

  const settings = {
    enabled: enabled,
    intervalMs: intervalMs,
    intervalSeconds: totalSeconds,
  };

  localStorage.setItem("autoSyncSettings", JSON.stringify(settings));
  autoSyncEnabled = enabled;
  autoSyncIntervalMs = intervalMs;

  if (enabled) {
    startAutoSync();
    showToast(
      "success",
      `Auto sync enabled - Interval: ${formatInterval(totalSeconds)}`,
    );
  } else {
    stopAutoSync();
    showToast("info", "Auto sync disabled");
  }

  // Show status message
  const statusDiv = document.getElementById("autoSyncStatus");
  if (statusDiv) {
    statusDiv.className = `text-sm text-center p-2 rounded-lg mb-4 ${enabled ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-600"}`;
    statusDiv.innerHTML = enabled
      ? `<i class="fas fa-check-circle mr-1"></i> Auto sync enabled - Next sync in ${formatInterval(totalSeconds)}`
      : `<i class="fas fa-ban mr-1"></i> Auto sync disabled`;
    statusDiv.classList.remove("hidden");
    setTimeout(() => {
      statusDiv.classList.add("hidden");
    }, 3000);
  }

  closeAutoSyncModal();
}

// Format interval for display
function formatInterval(seconds) {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  const parts = [];
  if (hours > 0) parts.push(`${hours} hour${hours > 1 ? "s" : ""}`);
  if (minutes > 0) parts.push(`${minutes} minute${minutes > 1 ? "s" : ""}`);
  if (secs > 0 || parts.length === 0)
    parts.push(`${secs} second${secs !== 1 ? "s" : ""}`);

  return parts.join(" ");
}

// Update interval preview text
function updateIntervalPreview() {
  const seconds = parseInt(document.getElementById("syncSeconds").value) || 0;
  const minutes = parseInt(document.getElementById("syncMinutes").value) || 0;
  const hours = parseInt(document.getElementById("syncHours").value) || 0;
  const totalSeconds = seconds + minutes * 60 + hours * 3600;
  const preview = document.getElementById("intervalPreview");
  if (preview) {
    preview.textContent = `Interval: ${formatInterval(totalSeconds)}`;
    if (totalSeconds < 10 && totalSeconds > 0) {
      preview.style.color = "#ef4444";
      preview.innerHTML = `⚠️ Interval: ${formatInterval(totalSeconds)} (minimum 10 seconds)`;
    } else {
      preview.style.color = "#6b7280";
    }
  }
}

// Start auto sync timer
function startAutoSync() {
  stopAutoSync(); // Clear existing interval

  if (autoSyncEnabled && autoSyncIntervalMs >= 10000) {
    autoSyncInterval = setInterval(async () => {
      console.log("Auto sync triggered at", new Date().toLocaleTimeString());
      await performAutoSync();
    }, autoSyncIntervalMs);
    console.log(
      `Auto sync started - Interval: ${autoSyncIntervalMs / 1000} seconds`,
    );
  }
}

// Stop auto sync timer
function stopAutoSync() {
  if (autoSyncInterval) {
    clearInterval(autoSyncInterval);
    autoSyncInterval = null;
    console.log("Auto sync stopped");
  }
}

// Perform auto sync on all accounts that have tokens
async function performAutoSync() {
  const accountsToSync = accountsData.filter((acc) => {
    // Check if account has a token (has been synced before)
    return mt4Tokens.has(String(acc.account_id)) || acc.mt4_password;
  });

  if (accountsToSync.length === 0) {
    console.log("No accounts with tokens to auto sync");
    return;
  }

  console.log(`Auto syncing ${accountsToSync.length} accounts...`);

  // Show mini notification
  showToast("info", `Auto syncing ${accountsToSync.length} account(s)...`);

  // Sync accounts in parallel (limit to 3 at a time to avoid rate limiting)
  const concurrencyLimit = 3;
  const results = [];

  for (let i = 0; i < accountsToSync.length; i += concurrencyLimit) {
    const batch = accountsToSync.slice(i, i + concurrencyLimit);
    const batchResults = await Promise.allSettled(
      batch.map(async (acc) => {
        const accountId = String(acc.account_id);
        const mt4Login = accountId;
        const mt4Password = acc.mt4_password;

        if (!mt4Password) {
          console.log(`Skipping ${accountId} - no password stored`);
          return { accountId, success: false, reason: "no password" };
        }

        try {
          // Get token
          let token = mt4Tokens.get(accountId);

          if (!token) {
            token = await getMT4Token(mt4Login, mt4Password);
            mt4Tokens.set(accountId, token);
          }

          // Fetch status and account info
          const [statusData, accountInfo] = await Promise.all([
            getMT4AccountStatus(mt4Login, token),
            getMT4AccountInfo(mt4Login, token),
          ]);

          if (statusData && statusData.authenticated) {
            // Update account data
            const account = accountsData.find((a) => a.account_id == accountId);
            if (account) {
              if (accountInfo) {
                account.account_balance =
                  accountInfo.balance || account.account_balance || 0;
                account.account_equity =
                  accountInfo.equity || account.account_equity || 0;
                account.margin = accountInfo.margin || account.margin || 0;
                account.free_margin =
                  accountInfo.freeMargin || account.free_margin || 0;
                account.margin_level =
                  accountInfo.marginLevel || account.margin_level || 0;
                account.leverage =
                  accountInfo.leverage || account.leverage || "100";
                account.currency =
                  accountInfo.currency || account.currency || "USD";
              }

              const openTrades = statusData.openTrades || [];
              let totalBuyVolume = 0,
                totalSellVolume = 0,
                buyCount = 0,
                sellCount = 0,
                floatingProfit = 0;

              openTrades.forEach((trade) => {
                const volume = parseFloat(trade.Volume || 0);
                const profit = parseFloat(trade.Profit || 0);
                floatingProfit += profit;

                if (trade.Type === 0 || trade.Type === 2 || trade.Type === 4) {
                  buyCount++;
                  totalBuyVolume += volume;
                } else {
                  sellCount++;
                  totalSellVolume += volume;
                }
              });

              account.buy_order_count = buyCount;
              account.sell_order_count = sellCount;
              account.total_buy_lot = totalBuyVolume;
              account.total_sell_lot = totalSellVolume;
              account.floating_value = floatingProfit;
              account.total_orders = openTrades.length;
              account.mt4_last_sync = new Date().toISOString();
            }

            // Refresh the specific card
            await refreshAccountCard(accountId);
            return { accountId, success: true };
          }
        } catch (error) {
          console.error(`Auto sync failed for ${accountId}:`, error);
          // Remove invalid token
          if (error.message.includes("401") || error.message.includes("403")) {
            mt4Tokens.delete(accountId);
          }
          return { accountId, success: false, reason: error.message };
        }
      }),
    );
    results.push(...batchResults);
  }

  const successCount = results.filter(
    (r) => r.status === "fulfilled" && r.value?.success,
  ).length;
  const failCount = results.filter(
    (r) => r.status === "rejected" || (r.value && !r.value.success),
  ).length;

  if (successCount > 0) {
    showToast(
      "success",
      `Auto sync completed: ${successCount} accounts synced${failCount > 0 ? `, ${failCount} failed` : ""}`,
    );
  }

  // Reapply filters to update visible cards
  applyFilterAndSearch();
}

// Open auto sync modal
function openAutoSyncModal() {
  const modal = document.getElementById("autoSyncModal");
  if (!modal) return;

  // Load current settings into modal
  const toggle = document.getElementById("autoSyncToggle");
  const seconds = document.getElementById("syncSeconds");
  const minutes = document.getElementById("syncMinutes");
  const hours = document.getElementById("syncHours");
  const statusDiv = document.getElementById("autoSyncStatus");

  if (toggle) toggle.checked = autoSyncEnabled;

  const totalSeconds = Math.floor(autoSyncIntervalMs / 1000);
  if (seconds && minutes && hours) {
    hours.value = Math.floor(totalSeconds / 3600);
    minutes.value = Math.floor((totalSeconds % 3600) / 60);
    seconds.value = totalSeconds % 60;
    updateIntervalPreview();
  }

  if (statusDiv) statusDiv.classList.add("hidden");

  modal.style.display = "flex";
}

// Close auto sync modal
function closeAutoSyncModal() {
  const modal = document.getElementById("autoSyncModal");
  if (modal) modal.style.display = "none";
}

// Proceed sync now (manual sync all accounts)
async function proceedSyncNow() {
  closeAutoSyncModal();
  saveAutoSyncSettings();

  Swal.fire({
    title: "Syncing Accounts",
    text: "Please wait while syncing all accounts...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    await performAutoSync();
    Swal.fire({
      icon: "success",
      title: "Sync Complete",
      text: "All accounts have been synced successfully.",
      timer: 2000,
      showConfirmButton: false,
    });
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Sync Failed",
      text: error.message,
      confirmButtonColor: "#ef4444",
    });
  }
}

// Update the DOMContentLoaded event listener to include auto sync initialization
// Add this to your existing DOMContentLoaded or init function
function initAutoSync() {
  loadAutoSyncSettings();

  // Add event listeners for interval inputs
  const secondsInput = document.getElementById("syncSeconds");
  const minutesInput = document.getElementById("syncMinutes");
  const hoursInput = document.getElementById("syncHours");

  if (secondsInput)
    secondsInput.addEventListener("input", updateIntervalPreview);
  if (minutesInput)
    minutesInput.addEventListener("input", updateIntervalPreview);
  if (hoursInput) hoursInput.addEventListener("input", updateIntervalPreview);

  // Add button listeners
  const autoSyncBtn = document.getElementById("autoSyncBtn");
  if (autoSyncBtn) autoSyncBtn.addEventListener("click", openAutoSyncModal);

  const saveBtn = document.getElementById("saveSyncSettingsBtn");
  if (saveBtn) saveBtn.addEventListener("click", saveAutoSyncSettings);

  const syncNowBtn = document.getElementById("proceedSyncNowBtn");
  if (syncNowBtn) syncNowBtn.addEventListener("click", proceedSyncNow);

  // Close modal on escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAutoSyncModal();
    }
  });
}

// Call initAutoSync after accounts are loaded
// Add this line at the end of your initAccounts function or where accounts are initialized
// initAutoSync();
