<?php
require_once('../../../../vendor/autoload.php');
require_once('config.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/lib/classes/user.php");

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$amount = optional_param('amount', 0, PARAM_INT);
$token = optional_param('token', '', PARAM_TEXT);

$user = \core_user::get_user($userid);

$client = new \GuzzleHttp\Client();

$data['success'] = false;

try {
    $authorize = $client->request('POST', 'https://api.tap.company/v2/authorize/', [
        'body' => '{
            "amount":' . $amount . ',
            "currency":"SAR",
            "customer":{
                "first_name":"' . $user->firstname . '",
                "last_name":"' . $user->lastname . '",
                "email":"' . $user->email . '"
            },
            "source":{"id":"src_all"},
            "redirect":{"url":"' . BASE_URL . '/local/payment/tap/success.php?userid=' . $userid . '&courseid=' . $courseid . '&couponid=' . $couponid . '"}}',
        'headers' => [
            'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ],
    ]);

    $authorizeResp = json_decode($authorize->getBody());

    $response = $client->request('POST', 'https://api.tap.company/v2/charges/', [
        'body' => '{
            "amount":' . $amount . ',
            "currency":"SAR",
            "customer":{
                "first_name":"' . $user->firstname . '",
                "last_name":"' . $user->lastname . '",
                "email":"' . $user->email . '"
            },
            "source":{"id":"src_all"},
            "redirect":{"url":"' . BASE_URL . '/local/payment/tap/success.php?userid=' . $userid . '&courseid=' . $courseid . '&couponid=' . $couponid . '"}}',
        'headers' => [
            'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ],
    ]);
    // $invoice = $client->request('POST', 'https://api.tap.company/v2/invoices/', [
    //     'body' => '{
    //         "due":1706965306,
    //         "expiry":1707965306,
    //         "currencies":["SAR"],
    //         "customer":{"first_name":"' . $user->firstname . '","last_name":"' . $user->lastname . '","email":"' . $user->email . '"},
    //         "order":{"items":[{"amount":' . $amount . ',"currency":"SAR","description":"test"}]},
    //         "redirect":{"url":"' . BASE_URL . '/local/payment/tap/success.php?userid=' . $userid . '&courseid=' . $courseid . '&couponid=' . $couponid . '"}}',
    //         'headers' => [
    //           'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
    //           'accept' => 'application/json',
    //           'content-type' => 'application/json',
    //         ],
    // ]);

   
    var_dump($invoiceResp);
    die();


    if ($invoiceResp->success) {
        $data['success'] = true;
        $data['token'] = $id_token;
        $data['url'] = $invoiceResp->url;
        $data['transaction'] = $invoiceResp->transactionNo;
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo 'Guzzle Request Exception: ' . $e->getMessage();
    if ($e->hasResponse()) {
        echo 'Response: ' . $e->getResponse()->getBody();
    }
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage();
}

echo json_encode($data);
exit();
