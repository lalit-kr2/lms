<?php
require_once '../../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'];
    // Retrieve card information from the form
    $cardNumber = $source['cardNumber'];
    $expiryMonth = $source['expiryMonth'];
    $expiryYear = $source['expiryYear'];
    $cvv = $source['cvv'];

    // Set your Moyasar API key
    \Moyasar\Moyasar::setApiKey('sk_test_t1HHz7GyveyfTUmEjTQ8dubv3FCqCvv22WxXEJjk');

    try {
        // Create a payment object
        $payment = \Moyasar\Payment::create([
            'amount' => 1000, // Replace with the actual payment amount in cents
            'currency' => 'SAR', // Replace with the desired currency code
            'source' => [
                'type' => 'creditcard',
                'number' => $cardNumber,
                'cvc' => $cvv,
                'month' => $expiryMonth,
                'year' => $expiryYear,
            ],
            'description' => 'Payment description', // Replace with your payment description
        ]);
    } catch (\Exception $e) {
        error_log('Caught exception: ' . $e->getMessage());
        http_response_code(500);
        echo 'Internal Server Error';
    }


    var_dump($payment);
    die();

    // Handle the payment response
    if ($payment->status === 'paid') {
        // Payment was successful
        echo 'Payment successful! Transaction ID: ' . $payment->id;
    } else {
        // Payment failed
        echo 'Payment failed. Error: ' . $payment->error->message;
    }
} else {
    // Redirect back to the form if accessed directly
    header('Location: index.html');
    exit;
}
