<?php
require_once('../../../../vendor/autoload.php');
require_once('config.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/lib/classes/user.php");

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$amount = optional_param('amount', 0, PARAM_INT);
$source = optional_param('source', 0, PARAM_RAW);

$user = \core_user::get_user($userid);

$client = new \GuzzleHttp\Client();

$data['success'] = false;

try {
    $response = $client->request('POST', 'https://restpilot.paylink.sa/api/auth', [
        'body' => '{"apiId":"' . API_KEY . '","persistToken":true,"secretKey":"' . API_SECRET . '"}',
        'headers' => [
            'accept' => '*/*',
            'content-type' => 'application/json',
        ],
    ]);

    $id_token = json_decode($response->getBody())->id_token;
    $data['success'] = true;
    $data['token'] = $id_token;
    $invoice = $client->request('POST', 'https://restpilot.paylink.sa/api/addInvoice', [
        'body' => '{
            "amount": ' . $amount . ',
            "callBackUrl": "' . BASE_URL . '/local/payment/paylink/success.php?userid='.$userid.'&courseid='.$courseid.'&couponid='.$couponid.'",
            "cancelUrl": "' . BASE_URL . '/local/payment/paylink/success.php",
            "clientEmail": "' . $user->email . '",
            "clientMobile": "' . $user->phone1 . '",
            "clientName": "' . "$user->firstname $user->lastname" . '",
            "note": "This invoice is for VIP client.",
            "orderNumber": "' . ORDER_RECIEPT_ID . '",
            "products": [
              {
                "price": ' . $amount . ',
                "qty": 1,
                "title": "Course Payment"
              }
            ]
          }',
        'headers' => [
            'Authorization' => 'Bearer ' . $id_token . '',
            'content-type' => 'application/json',
        ]
    ]);
    
    $invoiceResp = json_decode($invoice->getBody());
    
    
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
