<?php
// proxy.php - Dynamic proxy with improved debugging
// Place this in the same directory as your login/register pages

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all incoming requests
error_log("=== New Proxy Request ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Headers received:");
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        error_log("  $key: $value");
    }
}
error_log("POST Data: " . json_encode($_POST));
error_log("GET Data: " . json_encode($_GET));

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Action, Accept');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    error_log("OPTIONS preflight handled");
    exit();
}

// Define API endpoints based on action
$apiEndpoints = [
    'login' => 'https://itrust-tech.id/web/mobile/login',
    'register' => 'https://itrust-tech.id/web/mobile/register',
    'logout' => 'https://itrust-tech.id/web/mobile/logout',
    'forgot_password' => 'https://itrust-tech.id/web/mobile/forgot-password',
    'get_devices' => 'https://itrust-tech.id/web/mobile/get-devices',
    'get_devices_with_data' => 'https://itrust-tech.id/web/mobile/get-devices-with-data',
    'local_get_devices_with_data' => 'https://itrust.local/mobile/get-devices-with-data',
    'get_scrape_data' => 'https://itrust-tech.id/web/mobile/get-latest-scrape-data',
    'get_scrape_data_v2' => 'https://itrust-tech.id/web/mobile/get-latest-scrape-data-v2', // Optional

];

// Get the action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? 'login';

// Determine the API endpoint based on action
$apiUrl = $apiEndpoints[$action] ?? $apiEndpoints['login'];

// Check if action requires authentication
$requiresAuth = in_array($action, ['get_devices', 'get_scrape_data']);

// Get Authorization header
$authHeader = null;

// Method 1: Check standard Authorization header
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    error_log("Found Authorization header (HTTP_AUTHORIZATION): " . $authHeader);
}
// Method 2: Check REDIRECT_HTTP_AUTHORIZATION (common with some PHP configs)
elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    error_log("Found Authorization header (REDIRECT_HTTP_AUTHORIZATION): " . $authHeader);
}
// Method 3: Check for Authorization in other possible locations
elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        error_log("Found Authorization header (apache_request_headers): " . $authHeader);
    }
}

// Method 4: Check POST/GET data for token
if (!$authHeader) {
    $token = $_POST['token'] ?? $_GET['token'] ?? null;
    if ($token) {
        $authHeader = 'Bearer ' . $token;
        error_log("Created Authorization header from token param: " . $authHeader);
    }
}

// Debug: Log what we found
error_log("Action: $action");
error_log("API URL: $apiUrl");
error_log("Requires Auth: " . ($requiresAuth ? 'Yes' : 'No'));
error_log("Auth Header: " . ($authHeader ? 'Present' : 'Missing'));

// If auth is required but no header, return 401 immediately
if ($requiresAuth && !$authHeader) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Authorization required for this action',
        'action' => $action,
        'debug' => [
            'requires_auth' => true,
            'auth_header_found' => false,
            'headers_received' => array_filter($_SERVER, function ($key) {
                return strpos($key, 'HTTP_') === 0;
            }, ARRAY_FILTER_USE_KEY)
        ]
    ]);
    error_log("401 Unauthorized - No auth header found for action: $action");
    exit();
}

// Prepare data to send
$postData = $_POST;
unset($postData['action']);
unset($postData['token']); // Remove token from POST data if it's in header

error_log("Prepared POST data: " . json_encode($postData));

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);

// Set request method
if (in_array($action, ['login', 'register', 'forgot_password', 'get_devices', 'get_scrape_data'])) {
    curl_setopt($ch, CURLOPT_POST, true);

    // Set POST data appropriately
    if (in_array($action, ['get_scrape_data', 'get_devices']) && !empty($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
} else {
    curl_setopt($ch, CURLOPT_HTTPGET, true);
}

// cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Get headers for debugging
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Set headers for the remote request
$headers = [
    'Accept: application/json',
];

// Set Content-Type based on action
if (in_array($action, ['get_scrape_data', 'get_devices']) && !empty($postData)) {
    $headers[] = 'Content-Type: application/json';
} else {
    $headers[] = 'Content-Type: multipart/form-data';
}

// Add Authorization header if we have one
if ($authHeader) {
    $headers[] = 'Authorization: ' . $authHeader;
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$curlErrorNo = curl_errno($ch);

// Get header size and separate headers from body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$responseHeaders = substr($response, 0, $headerSize);
$responseBody = substr($response, $headerSize);

curl_close($ch);

// Log the response details
error_log("Remote API Response Code: $httpCode");
error_log("Remote API Error: $error");
error_log("Remote API Headers: " . $responseHeaders);
error_log("Remote API Body (first 500 chars): " . substr($responseBody, 0, 500));

// Handle cURL errors
if ($curlErrorNo) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => "cURL Error ($curlErrorNo): $error",
        'action' => $action,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug' => [
            'api_url' => $apiUrl,
            'auth_header_sent' => $authHeader,
            'curl_error' => $error
        ]
    ]);
    exit();
}

// Set response headers
header('Content-Type: application/json');

// Pass through the response
http_response_code($httpCode);

// Try to decode JSON response
$responseData = json_decode($responseBody, true);
if (json_last_error() === JSON_ERROR_NONE) {
    // Add debug info if not in production
    $responseData['action'] = $action;
    $responseData['proxy_debug'] = [
        'auth_header_received' => !empty($authHeader),
        'action' => $action,
        'requires_auth' => $requiresAuth
    ];
    echo json_encode($responseData);
} else {
    // Response is not JSON
    echo $responseBody;
}

error_log("Proxy response sent with code: $httpCode");
exit();
