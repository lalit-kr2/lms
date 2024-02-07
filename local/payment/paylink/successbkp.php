<?php
require_once('../../../../vendor/autoload.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/lib/enrollib.php");
global $DB;

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$transactionNo = optional_param('transactionNo', 0, PARAM_INT);

$apiKey = $DB->get_field('new_payment_gateways', 'apikey', ['state' => 1]);
$apiSecret = $DB->get_field('new_payment_gateways', 'apisecret', ['state' => 1]);

$client = new \GuzzleHttp\Client();

$data['success'] = false;

try {
    $response = $client->request('POST', 'https://restpilot.paylink.sa/api/auth', [
        'body' => '{"apiId":"' . $apiKey . '","persistToken":true,"secretKey":"' . $apiSecret . '"}',
        'headers' => [
            'accept' => '*/*',
            'content-type' => 'application/json',
        ],
    ]);

    if (json_decode($response->getBody())->id_token) {
        $id_token = json_decode($response->getBody())->id_token;
    } else {
        $headers = $response->getHeaders();
        $authorizationHeader = $headers['authorization'][0];
        $id_token = substr($authorizationHeader, strlen('Bearer '));
    }

    $invoice = $client->request('GET', 'https://restpilot.paylink.sa/api/getInvoice/' . $transactionNo . '', [
        'headers' => [
            'Authorization' => 'Bearer ' . $id_token . '',
            'content-type' => 'application/json',
        ]
    ]);
    $invoiceResp = json_decode($invoice->getBody());

    $obj = new stdClass();
    $obj->userid = $userid;
    $obj->courseid = $courseid;
    $obj->orderid = $invoiceResp->gatewayOrderRequest->orderNumber;
    $obj->status = strtolower($invoiceResp->orderStatus);
    $obj->amount = $invoiceResp->amount;
    $obj->currency = $invoiceResp->currency;
    $obj->message = $invoiceResp->paymentErrors[0]->errorMessage ? $invoiceResp->paymentErrors[0]->errorMessage : 'APPROVED';
    $obj->timecreated = time();
    $obj->timemodified = time();
    $obj->couponid = $couponid;

    if ($invoiceResp->success) {
        $data['success'] = true;
        $data['url'] = $invoiceResp->url;

        if (!$DB->record_exists('new_payment', ['userid' => $userid, 'courseid' => $courseid, 'status' => 'paid'])) {
            $id = $DB->insert_record('new_payment', $obj);

            $studentRoleId = $DB->get_field('role', 'id', ['shortname' => 'student']);

            if ($id && $obj->status == "paid") {
                if($couponid){
                    $coupon = $DB->get_record('coupon_pay', ['id' => $couponid]);
                    $coupon->count = $coupon->count + 1;
                    $coupon->timemodified = time();
                    $DB->update_record('coupon_pay', $coupon);
                }
                enrol_try_internal_enrol($courseid, $userid, $studentRoleId);
                redirect("$CFG->wwwroot/course/view.php?id=$courseid", 'User successfully enrolled in the course!');
            } else {
                redirect("$CFG->wwwroot/my", 'Something Went Wrong! Try again');
            }
        } else {
            redirect("$CFG->wwwroot/my", 'User already enrolled in the course!');
        }
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
