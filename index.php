<!DOCTYPE html>
<html>
<head>
  <title>Pasar Malam Hijau</title>
</head>
<body>
  <div id="loading" style="
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
    font-family: Arial, sans-serif;
  ">
    <div style="
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    "></div>
    <p>Checking authentication...</p>
  </div>

  <script>
  (async () => {
    try {
      console.log('App Loader: Checking authentication...');
      
      // Simple check without auth.js dependencies
      const token = localStorage.getItem("jwt");
      const userStr = localStorage.getItem("user");
      
      if (!token || !userStr) {
        console.log('No token or user found, redirecting to login');
        location.replace("/login.php");
        return;
      }
      
      let user;
      try {
        user = JSON.parse(userStr);
      } catch (e) {
        console.log('Invalid user data');
        localStorage.clear();
        location.replace("/login.php");
        return;
      }
      
      console.log('User found:', user.username);
      console.log('User type:', user.user_tipe);
      
      // Check token expiration (basic check without auth.js)
      // You can store token expiration time in localStorage during login
      const tokenExpires = localStorage.getItem("jwt_expires");
      if (tokenExpires) {
        const expiresAt = parseInt(tokenExpires);
        const now = Date.now();
        
        if (now > expiresAt) {
          console.log('Token expired (based on stored timestamp)');
          
          // Try to load auth.js for refresh
          try {
            const script = document.createElement('script');
            script.src = '/lib/js/auth.js';
            script.onload = async () => {
              if (typeof refreshToken !== 'undefined') {
                const ok = await refreshToken();
                if (!ok) {
                  localStorage.clear();
                  location.replace("/login.php");
                  return;
                }
                redirectUser();
              } else {
                localStorage.clear();
                location.replace("/login.php");
              }
            };
            script.onerror = () => {
              console.log('Failed to load auth.js');
              localStorage.clear();
              location.replace("/login.php");
            };
            document.head.appendChild(script);
            return;
          } catch (error) {
            console.error('Error loading auth.js:', error);
            localStorage.clear();
            location.replace("/login.php");
            return;
          }
        }
      }
      
      // Redirect immediately if token seems valid
      redirectUser();
      
    } catch (error) {
      console.error('Auth check error:', error);
      location.replace("/login.php");
    }
    
    function redirectUser() {
      const userStr = localStorage.getItem("user");
      if (!userStr) {
        location.replace("/login.php");
        return;
      }
      
      let user;
      try {
        user = JSON.parse(userStr);
      } catch {
        location.replace("/login.php");
        return;
      }
      
      let redirectUrl = '/accounts.php';
      
      if (user.user_tipe === "ADMIN") {
        redirectUrl = '/accounts.php';// '/admin/dashboard.php';
      } else if (user.user_tipe === "USER") {
        redirectUrl = '/accounts.php';// '/user/dashboard.php';
      }
      
      console.log('Redirecting to:', redirectUrl);
      setTimeout(() => {
        location.replace(redirectUrl);
      }, 300);
    }
  })();

  // CSS for spinner
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
  </script>
</body>
</html>