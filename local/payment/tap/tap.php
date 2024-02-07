<?php
require_once('config.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/lib/classes/user.php");
include_once('connection.php');

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$amount = optional_param('amount', 0, PARAM_INT);
$token = optional_param('token', '', PARAM_TEXT);

$user = \core_user::get_user($userid);

$data['success'] = false;

try {
    // Create an authorize
    $authorize = $client->request('POST', 'https://api.tap.company/v2/authorize/', [
        'body' => '{
            "amount":' . $amount . ',
            "currency":"SAR",
            "customer":{
                "first_name":"' . $user->firstname . '",
                "last_name":"' . $user->lastname . '",
                "email":"' . $user->email . '"
            },
            "source":{"id":"' . $token . '"},
            "redirect":{"url":"' . BASE_URL . '/local/payment/tap/success.php?userid=' . $userid . '&courseid=' . $courseid . '&couponid=' . $couponid . '&amount='.$amount.'"}}',
        'headers' => [
            'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ],
    ]);

    $authorizeResp = json_decode($authorize->getBody());

    if ($authorizeResp->customer_initiated) {
        $data['success'] = true;
        $data['url'] = $authorizeResp->transaction->url;
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
