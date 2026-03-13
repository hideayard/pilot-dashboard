<?php
// proxy.php - Dynamic proxy for both login and register
// Place this in the same directory as your login/register pages

// CORS headers
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Action');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Define API endpoints based on action
$apiEndpoints = [
    'login' => 'https://itrust-tech.id/web/mobile/login',
    'register' => 'https://itrust-tech.id/web/mobile/register', // Adjust this to your actual register endpoint
    'logout' => 'https://itrust-tech.id/web/mobile/logout', // Optional
    'forgot_password' => 'https://itrust-tech.id/web/mobile/forgot-password', // Optional
    'get_devices' => 'https://itrust-tech.id/web/mobile/get-devices', // Optional
    'get_scrape_data' => 'https://itrust-tech.id/web/mobile/get-latest-scrape-data', // Optional
    'get_scrape_data_v2' => 'https://itrust-tech.id/web/mobile/get-latest-scrape-data-v2',
    'get_scrape_data_v3' => 'https://itrust-tech.id/web/mobile/get-latest-scrape-data-v3',

];

// Get the action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? 'login';

// Determine the API endpoint based on action
$apiUrl = $apiEndpoints[$action] ?? $apiEndpoints['login'];

// Get all POST data
$postData = $_POST;

// Remove the action parameter from data sent to API (if it shouldn't be sent)
unset($postData['action']);

// Debug logging (optional - enable only in development)
error_log("Proxy Action: $action | API URL: $apiUrl | Data: " . json_encode($postData));

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production with proper cert
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Set headers
$headers = [
    'Accept: application/json',
    'Content-Type: multipart/form-data',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$curlErrorNo = curl_errno($ch);

curl_close($ch);

// Handle cURL errors
if ($curlErrorNo) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "cURL Error ($curlErrorNo): $error",
        'action' => $action,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Set response headers
header('Content-Type: application/json');

// If API returns an error HTTP code, modify response to include action
if ($httpCode >= 400) {
    http_response_code($httpCode);

    // Try to parse the error response
    $errorData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $errorData['action'] = $action;
        echo json_encode($errorData);
    } else {
        // If response is not JSON, create a JSON error
        echo json_encode([
            'success' => false,
            'message' => "API returned HTTP $httpCode: " . substr($response, 0, 100),
            'action' => $action,
            'http_code' => $httpCode
        ]);
    }
} else {
    // Success - pass through the response
    http_response_code($httpCode);

    // Try to decode and enhance the response
    $responseData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        // Add action to response for client-side reference
        $responseData['action'] = $action;
        echo json_encode($responseData);
    } else {
        // Response is not JSON, pass it through as-is
        echo $response;
    }
}

// Log for debugging (optional - enable only in development)
error_log("Proxy Response - Action: $action | Status: $httpCode | Response: " . substr($response, 0, 200));

exit();
