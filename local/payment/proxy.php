<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Extract the target URL from the query parameters
$targetUrl = isset($_GET['url']) ? $_GET['url'] : '';

// Check if the target URL is provided
if (empty($targetUrl)) {
    http_response_code(400);
    echo json_encode(['error' => 'Target URL not provided']);
    exit();
}

// Create context options to ignore SSL verification (useful in some cases)
$contextOptions = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
];

$context = stream_context_create($contextOptions);

// Make a request to the external server
$response = file_get_contents($targetUrl, false, $context);

// Check if the request was successful
if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data from the external server']);
} else {
    // Return the response from the external server
    echo $response;
}
