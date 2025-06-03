<?php
session_start();

define('API_BASE_URL', 'https://tugas-tekweb.uc.r.appspot.com');

function makeApiCall($endpoint, $method = 'GET', $data = null, $token = null) {
    $url = API_BASE_URL . $endpoint;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Handle curl errors
    if ($error) {
        return [
            'data' => ['message' => 'Connection error: ' . $error],
            'code' => 0
        ];
    }
    
    // Handle empty response
    if (empty($response)) {
        return [
            'data' => ['message' => 'Empty response from server'],
            'code' => $httpCode
        ];
    }
    
    $decodedResponse = json_decode($response, true);
    
    // Handle JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'data' => ['message' => 'Invalid JSON response: ' . json_last_error_msg()],
            'code' => $httpCode
        ];
    }
    
    return [
        'data' => $decodedResponse,
        'code' => $httpCode
    ];
}

function isLoggedIn() {
    return isset($_SESSION['token']) && isset($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>
