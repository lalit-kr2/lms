<?php
require_once('../../../../vendor/autoload.php');

try {
    $client = new \GuzzleHttp\Client();
} catch (Exception $e) {
    error_log("Failed to connect to Guzzle Http!", 0);
    echo 'Exception: ' . $e->getMessage();
}
