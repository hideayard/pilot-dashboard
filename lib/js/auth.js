// auth.js - Updated to use proxy
const API_BASE = window.location.origin; // Your domain
const PROXY_ENDPOINT = "/proxy.php"; // Your proxy file

// FIXED requireAuth - Only clears session when absolutely necessary
async function requireAuth(role = null) {
  try {
    const token = localStorage.getItem("jwt");
    const user = localStorage.getItem("user");

    console.log("requireAuth: Checking token and user...");

    // Basic check - if no token/user, redirect to login
    if (!token || !user) {
      console.log("requireAuth: No token or user found");
      // Don't call clearSession here - just return false
      // Let the caller decide what to do
      return false;
    }

    // Parse user data
    let userObj;
    try {
      userObj = JSON.parse(user);
    } catch (e) {
      console.error("requireAuth: Failed to parse user data");
      return false;
    }

    // Check token expiration using isExpired function
    if (typeof isExpired === "function") {
      if (isExpired(token)) {
        console.log("requireAuth: Token expired, attempting refresh...");

        if (typeof refreshToken === "function") {
          const ok = await refreshToken();
          if (!ok) {
            console.log("requireAuth: Token refresh failed");
            // Only clear session if refresh fails
            clearSession();
            return false;
          }
          console.log("requireAuth: Token refreshed successfully");

          // Update user object after refresh
          const updatedUser = localStorage.getItem("user");
          if (updatedUser) {
            try {
              userObj = JSON.parse(updatedUser);
            } catch (e) {
              console.error("requireAuth: Failed to parse updated user data");
            }
          }
        } else {
          console.log("requireAuth: refreshToken function not available");
          // Don't clear session just because we can't refresh
          // The token might still be valid
          return true;
        }
      } else {
        console.log("requireAuth: Token is valid (not expired)");
      }
    } else {
      console.log(
        "requireAuth: isExpired function not available, skipping expiration check",
      );
    }

    // Check role if specified
    if (role && userObj.user_tipe !== role) {
      console.log(
        `requireAuth: Role mismatch. Required: ${role}, User has: ${userObj.user_tipe}`,
      );
      // Don't clear session for role mismatch - just return false
      // User might want to navigate to a different page
      return false;
    }

    console.log("requireAuth: User authenticated successfully");
    return true;
  } catch (error) {
    console.error("requireAuth error:", error);
    // Don't clear session on unexpected errors
    // Could be network issues, etc.
    return false;
  }
}

// Also add this function to check login state
function isLoggedIn() {
  try {
    const token = localStorage.getItem("jwt");
    const user = localStorage.getItem("user");

    if (!token || !user) return false;

    // Quick check without validation
    return true;
  } catch {
    return false;
  }
}

/* ========= SESSION MANAGEMENT ========= */
function saveSession(token, user) {
  localStorage.setItem("jwt", token);
  localStorage.setItem("authToken", token);
  localStorage.setItem("user", JSON.stringify(user));
  localStorage.setItem("lastLogin", new Date().toISOString());

  // Store expiration time (24 hours from now)
  const expiresAt = Date.now() + 24 * 60 * 60 * 1000;
  localStorage.setItem("jwt_expires", expiresAt.toString());

  // Also set as cookie for compatibility
  document.cookie = `jwt=${token}; path=/; max-age=${
    7 * 24 * 60 * 60
  }; SameSite=Lax`;
  document.cookie = `user_id=${user.id}; path=/; max-age=${
    7 * 24 * 60 * 60
  }; SameSite=Lax`;

  console.log(
    "Session saved, expires at:",
    new Date(expiresAt).toLocaleString(),
  );
}

// FIXED clearSession - Only redirects if called from login page
function clearSession() {
  console.log("clearSession: Clearing user session...");

  // Remove localStorage items
  localStorage.removeItem("jwt");
  localStorage.removeItem("user");
  localStorage.removeItem("lastLogin");
  localStorage.removeItem("jwt_expires");

  // Keep remembered user (optional)
  // localStorage.removeItem("rememberedUser");

  // Clear cookies
  document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

  console.log("clearSession: Session cleared");

  // Only redirect if we're not already on the login page
  if (!window.location.pathname.includes("login")) {
    console.log("clearSession: Redirecting to login page");
    setTimeout(() => {
      window.location.href = "/login.php";
    }, 500);
  }
}

/* ========= LOGIN FUNCTION (USING PROXY) ========= */
async function login(credentials) {
  try {
    const { username, password, rememberMe } = credentials;

    console.log("Attempting login via proxy...");

    const formData = new FormData();
    formData.append("action", "login"); // Added action parameter
    formData.append("username", username);
    formData.append("password", password);

    const res = await fetch(PROXY_ENDPOINT, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    console.log("Proxy response status:", res.status);

    if (!res.ok) {
      const errorText = await res.text();
      console.error("Login failed via proxy:", errorText);

      try {
        const errorData = JSON.parse(errorText);
        throw new Error(errorData.message || `Login failed: ${res.status}`);
      } catch {
        throw new Error(`Login failed: ${res.status} - Server error`);
      }
    }

    const data = await res.json();
    console.log("Login response:", data);

    // Check if action matches
    if (data.action !== "login") {
      console.warn("Unexpected action in response:", data.action);
    }

    if (!data.success) {
      throw new Error(data.message || "Invalid username or password");
    }

    // ✅ Save session
    saveSession(data.token, data.user);

    // ✅ Save remembered user
    if (rememberMe) {
      localStorage.setItem("rememberedUser", username);
    } else {
      localStorage.removeItem("rememberedUser");
    }

    // ✅ Show success
    Swal.fire({
      icon: "success",
      title: "Login Successful",
      text: "Welcome! Redirecting to dashboard...",
      timer: 1500,
      showConfirmButton: false,
    });

    // ✅ Redirect based on user type
    setTimeout(() => {
      const userType = data.user?.user_tipe;
      let redirectUrl = "../accounts.php";
// localStorage
      switch (userType) {
        case "admin":
          redirectUrl = "../accounts.php";
          break;
        case "user":
          redirectUrl = "../accounts.php";
          break;
        default:
          redirectUrl = "../accounts.php";
      }

      console.log("Redirecting to:", redirectUrl);
      window.location.href = redirectUrl;
    }, 1500);

    return data;
  } catch (err) {
    console.error("Login error:", err);

    let errorMessage = err.message;
    if (
      err.message.includes("Network") ||
      err.message.includes("Failed to fetch")
    ) {
      errorMessage =
        "Cannot connect to server. Please check your internet connection.";
    } else if (err.message.includes("CORS")) {
      errorMessage =
        "Cross-origin request blocked. Please contact administrator.";
    }

    Swal.fire({
      icon: "error",
      title: "Login Failed",
      text: errorMessage,
      confirmButtonText: "Try Again",
    });

    throw err;
  }
}

async function register(userData) {
  try {
    const {
      firstName,
      lastName,
      email,
      username,
      password,
      confirmPassword,
      acceptTerms,
    } = userData;

    console.log("Attempting registration via proxy...");

    // Client-side validation
    if (password !== confirmPassword) {
      throw new Error("Passwords do not match");
    }

    if (!acceptTerms) {
      throw new Error("You must accept the terms and conditions");
    }

    const formData = new FormData();
    formData.append("action", "register"); // Added action parameter
    formData.append("first_name", firstName);
    formData.append("last_name", lastName);
    formData.append("email", email);
    formData.append("username", username);
    formData.append("password", password);
    formData.append("confirm_password", confirmPassword);
    formData.append("user_type", "user");
    formData.append("accept_terms", acceptTerms ? "1" : "0");

    const res = await fetch(PROXY_ENDPOINT, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    console.log("Registration proxy response status:", res.status);

    if (!res.ok) {
      const errorText = await res.text();
      console.error("Registration failed via proxy:", errorText);

      try {
        const errorData = JSON.parse(errorText);
        throw new Error(
          errorData.message || `Registration failed: ${res.status}`,
        );
      } catch {
        throw new Error(`Registration failed: ${res.status} - Server error`);
      }
    }

    const data = await res.json();
    console.log("Registration response:", data);

    // Check if action matches
    if (data.action !== "register") {
      console.warn("Unexpected action in response:", data.action);
    }

    if (!data.success) {
      // Check for validation errors
      if (data.errors && Array.isArray(data.errors)) {
        throw new Error(data.errors.join(", "));
      }
      throw new Error(data.message || "Registration failed. Please try again.");
    }

    // ✅ Save session if auto-login is enabled
    if (data.token && data.user) {
      saveSession(data.token, data.user);
    }

    // ✅ Show success
    const successMessage =
      data.message ||
      `Welcome ${firstName}! Your account has been created successfully.`;

    Swal.fire({
      icon: "success",
      title: "Registration Successful",
      html:
        successMessage +
        "<br><br><small>You will be redirected shortly...</small>",
      timer: 3000,
      showConfirmButton: false,
      willClose: () => {
        if (data.token && data.user) {
          const userType = data.user?.user_tipe || data.user?.user_type;
          let redirectUrl = "../accounts.php";

          switch (userType) {
            case "admin":
              redirectUrl = "../accounts.php";
              break;
            case "user":
              redirectUrl = "../accounts.php";
              break;
            default:
              redirectUrl = "../accounts.php";
          }

          window.location.href = redirectUrl;
        } else {
          window.location.href = "login.php";
        }
      },
    });

    return data;
  } catch (err) {
    console.error("Registration error:", err);

    let errorMessage = err.message;
    if (
      err.message.includes("Network") ||
      err.message.includes("Failed to fetch")
    ) {
      errorMessage =
        "Cannot connect to server. Please check your internet connection.";
    } else if (err.message.includes("CORS")) {
      errorMessage =
        "Cross-origin request blocked. Please contact administrator.";
    }

    Swal.fire({
      icon: "error",
      title: "Registration Failed",
      text: errorMessage,
      confirmButtonText: "Try Again",
    });

    throw err;
  }
}
/* ========= AUTH CHECK ========= */
function checkAuth() {
  const token = localStorage.getItem("jwt");
  const user = localStorage.getItem("user");

  console.log("checkAuth: Checking token and user...");
  console.log("Token exists:", !!token);
  console.log("User exists:", !!user);

  if (!token || !user) {
    console.log("checkAuth: Missing token or user data");
    return false;
  }

  try {
    const userObj = JSON.parse(user);
    console.log("checkAuth: User parsed successfully:", userObj.username);

    // Check expiration using stored timestamp (if available)
    const expiresAt = localStorage.getItem("jwt_expires");
    if (expiresAt) {
      const expirationTime = parseInt(expiresAt);
      const now = Date.now();

      if (now > expirationTime) {
        console.log("checkAuth: Token expired based on stored timestamp");
        // Don't clear here - let requireAuth handle refresh
        return false;
      } else {
        console.log("checkAuth: Token is valid (not expired)");
        return true;
      }
    }

    // If no expiration timestamp, return true (let requireAuth handle JWT parsing)
    console.log("checkAuth: No expiration timestamp, assuming valid");
    return true;
  } catch (error) {
    console.error("checkAuth: Error parsing user data:", error);
    return false;
  }
}

/* ========= INITIALIZE ========= */
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOMContentLoaded");

  // Check if user is already logged in
  if (checkAuth() && !window.location.pathname.includes("login")) {
    const user = JSON.parse(localStorage.getItem("user"));
    console.log("User already logged in:", user.username);
    // You could auto-redirect here if needed
  }

  // Initialize login page UI
  initLoginUI();
});

// FIXED initLoginUI - Only redirects if token is valid
function initLoginUI() {
  // Only run on login page
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return;

  console.log("Initializing login UI...");

  // Check if already logged in
  if (isLoggedIn()) {
    console.log("User already logged in, checking token...");

    // First check with simple validation
    const token = localStorage.getItem("jwt");
    const userStr = localStorage.getItem("user");

    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);

        // Check expiration using timestamp first (less aggressive)
        const expiresAt = localStorage.getItem("jwt_expires");
        let shouldRedirect = false;

        if (expiresAt) {
          const expirationTime = parseInt(expiresAt);
          const now = Date.now();

          if (now <= expirationTime) {
            console.log("Token not expired, redirecting...");
            shouldRedirect = true;
          } else if (typeof isExpired === "function" && !isExpired(token)) {
            // Double-check with JWT parsing
            console.log("Token still valid (JWT check), redirecting...");
            shouldRedirect = true;
          }
        } else {
          // No expiration stored, be cautious
          console.log("No expiration timestamp, proceeding cautiously");
        }

        if (shouldRedirect) {
          redirectLoggedInUser();
          return; // Stop further initialization
        }
      } catch (e) {
        console.error("Error checking logged in status:", e);
      }
    }
  }

  // Password toggle
  const togglePasswordBtn = document.getElementById("togglePassword");
  if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener("click", function () {
      const passwordInput = document.getElementById("password");
      const eyeIcon = this.querySelector("i");

      if (passwordInput && eyeIcon) {
        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          eyeIcon.className = "fas fa-eye-slash";
        } else {
          passwordInput.type = "password";
          eyeIcon.className = "fas fa-eye";
        }
      }
    });
  }

  // Add this helper function
  function redirectLoggedInUser() {
    try {
      const user = JSON.parse(localStorage.getItem("user") || "{}");
      const userType = user.user_tipe || "";
      let redirectUrl = "/accounts.php";

      if (userType.toUpperCase() === "ADMIN") {
        redirectUrl = "/accounts.php"; // "/admin/dashboard.php";
      } else if (userType.toUpperCase() === "USER") {
        redirectUrl = "/accounts.php"; // "/user/dashboard.php";
      }

      console.log("Auto-redirecting logged-in user to:", redirectUrl);

      // Show message before redirect
      Swal.fire({
        icon: "info",
        title: "Already Logged In",
        text: `Redirecting to dashboard...`,
        timer: 1500,
        showConfirmButton: false,
      });

      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 1500);
    } catch (error) {
      console.error("Error in redirectLoggedInUser:", error);
    }
  }

  // Form submission
  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const username = document.getElementById("username")?.value.trim();
    const password = document.getElementById("password")?.value;
    const rememberMe = document.getElementById("remember")?.checked;

    // Validation
    if (!username || !password) {
      Swal.fire({
        icon: "error",
        title: "Missing Fields",
        text: "Please enter both username and password",
      });
      return;
    }

    if (password.length < 6) {
      Swal.fire({
        icon: "error",
        title: "Invalid Password",
        text: "Password must be at least 6 characters",
      });
      return;
    }

    // Show loading state
    const loginButton = document.getElementById("loginButton");
    if (loginButton) {
      const originalText = loginButton.innerHTML;
      loginButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Signing In...';
      loginButton.disabled = true;

      try {
        await login({ username, password, rememberMe });
      } catch (error) {
        // Error is already shown by login function
        loginButton.innerHTML = originalText;
        loginButton.disabled = false;
      }
    }
  });

  // Auto-fill remembered user
  const rememberedUser = localStorage.getItem("rememberedUser");
  if (rememberedUser) {
    const usernameInput = document.getElementById("username");
    const rememberCheckbox = document.getElementById("remember");
    if (usernameInput) usernameInput.value = rememberedUser;
    if (rememberCheckbox) rememberCheckbox.checked = true;
  }

  // Add focus effects
  const inputs = document.querySelectorAll(".form-input");
  inputs.forEach((input) => {
    if (!input) return;

    input.addEventListener("focus", function () {
      const icon = this.parentElement.querySelector(".input-icon");
      if (icon) {
        icon.style.color = "#667eea";
      }
    });

    input.addEventListener("blur", function () {
      const icon = this.parentElement.querySelector(".input-icon");
      if (icon) {
        icon.style.color = "#a0aec0";
      }
    });
  });

  console.log("Login UI initialized");
}

// Direct form submission handler to ensure it works
document.addEventListener("DOMContentLoaded", function () {
  console.log("Login page loaded, initializing direct handler...");

  const loginForm = document.getElementById("loginForm");
  const togglePasswordBtn = document.getElementById("togglePassword");

  // Password toggle
  if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener("click", function () {
      const passwordInput = document.getElementById("password");
      const icon = this.querySelector("i");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.className = "fas fa-eye-slash";
      } else {
        passwordInput.type = "password";
        icon.className = "fas fa-eye";
      }
    });
  }

  // Form submission
  if (loginForm) {
    // Remove any existing listeners to avoid duplicates
    loginForm.removeEventListener("submit", handleFormSubmit);
    loginForm.addEventListener("submit", handleFormSubmit);
  }

  // Input focus effects
  document.querySelectorAll(".form-input").forEach((input) => {
    input.addEventListener("focus", function () {
      const icon = this.parentElement.querySelector(".input-icon");
      if (icon) icon.style.color = "#FDA300";
    });

    input.addEventListener("blur", function () {
      if (!this.value) {
        const icon = this.parentElement.querySelector(".input-icon");
        if (icon) icon.style.color = "#2C5282";
      }
    });
  });

  // Auto-fill remembered user
  const rememberedUser = localStorage.getItem("rememberedUser");
  if (rememberedUser) {
    const usernameInput = document.getElementById("username");
    const rememberCheckbox = document.getElementById("remember");
    if (usernameInput) usernameInput.value = rememberedUser;
    if (rememberCheckbox) rememberCheckbox.checked = true;
  }
});

// Separate handler function
async function handleFormSubmit(event) {
  event.preventDefault();
  console.log("Form submitted directly");

  const username = document.getElementById("username")?.value.trim();
  const password = document.getElementById("password")?.value;
  const rememberMe = document.getElementById("remember")?.checked;
  const loginButton = document.getElementById("loginButton");

  // Validation
  if (!username || !password) {
    Swal.fire({
      icon: "error",
      title: "Missing Fields",
      text: "Please enter both username and password",
      confirmButtonColor: "#FDA300",
    });
    return;
  }

  if (password.length < 6) {
    Swal.fire({
      icon: "error",
      title: "Invalid Password",
      text: "Password must be at least 6 characters",
      confirmButtonColor: "#FDA300",
    });
    return;
  }

  // Show loading state
  const originalText = loginButton.innerHTML;
  loginButton.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Signing In...';
  loginButton.disabled = true;

  try {
    // Try to use the login function from auth.js first
    if (typeof window.login === "function") {
      console.log("Using auth.js login function");
      await window.login({ username, password, rememberMe });
    } else {
      // Fallback direct login
      console.log("Using fallback login");
      // await fallbackLogin(username, password, rememberMe);
    }
  } catch (error) {
    console.error("Login error:", error);
    Swal.fire({
      icon: "error",
      title: "Login Failed",
      text: error.message || "An error occurred during login",
      confirmButtonColor: "#FDA300",
    });
    loginButton.innerHTML = originalText;
    loginButton.disabled = false;
  }
}

// Fallback login function in case auth.js isn't loaded properly
async function fallbackLogin(username, password, rememberMe) {
  console.log("Using fallback login with username:", username);

  // Simulate API call - replace with your actual API endpoint
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      // Demo credentials check
      if (username === "admin" && password === "password123") {
        const userData = {
          name: "Admin User",
          username: "admin",
          user_email: "admin@example.com",
          user_tipe: "ADMIN",
        };

        // Save to localStorage
        localStorage.setItem("jwt", "demo-token-12345");
        localStorage.setItem("authToken", "demo-token-12345");
        localStorage.setItem("user", JSON.stringify(userData));
        localStorage.setItem("lastLogin", new Date().toISOString());

        if (rememberMe) {
          localStorage.setItem("rememberedUser", username);
        }

        Swal.fire({
          icon: "success",
          title: "Login Successful",
          text: "Welcome! Redirecting to dashboard...",
          timer: 1500,
          showConfirmButton: false,
          background: "#FFFFFF",
          color: "#2C5282",
          iconColor: "#FDA300",
        }).then(() => {
          window.location.href = "accounts.php";
        });

        resolve();
      } else {
        reject(new Error("Invalid username or password"));
      }
    }, 1000);
  });
}

// Add this to your auth.js file

/* ========= FORGOT PASSWORD FUNCTION ========= */
async function forgotPassword(email) {
  try {
    console.log("Sending password reset email to:", email);

    const formData = new FormData();
    formData.append("action", "forgot_password");
    formData.append("email", email);

    const response = await fetch(PROXY_ENDPOINT, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    console.log("Forgot password response status:", response.status);

    if (!response.ok) {
      const errorText = await response.text();
      console.error("Forgot password failed:", errorText);
      throw new Error(`Gagal menghantar email: ${response.status}`);
    }

    const data = await response.json();
    console.log("Forgot password response:", data);

    if (data.action !== "forgot_password") {
      console.warn("Unexpected action in response:", data.action);
    }

    if (!data.success) {
      throw new Error(data.message || "Email tidak dijumpai");
    }

    // Show success message
    Swal.fire({
      icon: "success",
      title: "Email Dihantar!",
      text:
        data.message ||
        "Pautan reset kata laluan telah dihantar ke email anda.",
      confirmButtonColor: "#FDA300",
      background: "#FFFFFF",
      color: "#2C5282",
    });

    return data;
  } catch (error) {
    console.error("Forgot password error:", error);

    let errorMessage = error.message;
    if (
      error.message.includes("Network") ||
      error.message.includes("Failed to fetch")
    ) {
      errorMessage =
        "Tidak dapat menyambung ke pelayan. Sila periksa sambungan internet anda.";
    } else if (error.message.includes("CORS")) {
      errorMessage = "Permintaan lintas asal disekat. Sila hubungi pentadbir.";
    }

    Swal.fire({
      icon: "error",
      title: "Ralat",
      text: errorMessage,
      confirmButtonColor: "#FDA300",
      background: "#FFFFFF",
      color: "#2C5282",
    });

    throw error;
  }
}

// Also add reset password function
async function resetPassword(token, newPassword, confirmPassword) {
  try {
    console.log("Resetting password with token");

    if (newPassword !== confirmPassword) {
      throw new Error("Kata laluan tidak sepadan");
    }

    if (newPassword.length < 8) {
      throw new Error("Kata laluan mesti sekurang-kurangnya 8 aksara");
    }

    const formData = new FormData();
    formData.append("action", "reset_password");
    formData.append("token", token);
    formData.append("new_password", newPassword);
    formData.append("confirm_password", confirmPassword);

    const response = await fetch(PROXY_ENDPOINT, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error("Reset password failed:", errorText);
      throw new Error(
        `Gagal menetapkan semula kata laluan: ${response.status}`,
      );
    }

    const data = await response.json();
    console.log("Reset password response:", data);

    if (!data.success) {
      throw new Error(data.message || "Gagal menetapkan semula kata laluan");
    }

    Swal.fire({
      icon: "success",
      title: "Berjaya!",
      text: data.message || "Kata laluan anda telah berjaya ditetapkan semula.",
      confirmButtonColor: "#FDA300",
      background: "#FFFFFF",
      color: "#2C5282",
    }).then(() => {
      window.location.href = "login.php";
    });

    return data;
  } catch (error) {
    console.error("Reset password error:", error);

    Swal.fire({
      icon: "error",
      title: "Ralat",
      text: error.message,
      confirmButtonColor: "#FDA300",
      background: "#FFFFFF",
      color: "#2C5282",
    });

    throw error;
  }
}

// Show notification
function showNotification(message, type) {
  const toast = document.getElementById("toast");
  const icon = toast.querySelector("i");
  const title = toast.querySelector(".font-semibold");
  const desc = toast.querySelector(".text-sm");

  if (type === "success") {
    icon.className = "fas fa-check-circle text-green-600 text-xl";
    title.textContent = "Welcome back!";
    desc.textContent = message;
  } else {
    icon.className = "fas fa-exclamation-circle text-red-600 text-xl";
    title.textContent = "Error";
    desc.textContent = message;
  }

  toast.classList.remove("translate-x-full");

  setTimeout(() => {
    toast.classList.add("translate-x-full");
  }, 3000);
}
