<?php
require_once('../../../../vendor/autoload.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/local/payment/lib.php");
require_once("$CFG->dirroot/enrol/payhub/lib.php");
require_once("$CFG->dirroot/lib/enrollib.php");
global $DB;

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$transactionNo = optional_param('transactionNo', 0, PARAM_INT);

$payhub = new enrol_payhub_plugin();

$apiKey = $DB->get_field('new_payment_gateways', 'apikey', ['state' => 1]);
$apiSecret = $DB->get_field('new_payment_gateways', 'apisecret', ['state' => 1]);

$client = new \GuzzleHttp\Client();


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
    $obj->currency = $invoiceResp->gatewayOrderRequest->currency;
    $obj->message = $invoiceResp->paymentErrors[0]->errorMessage ? $invoiceResp->paymentErrors[0]->errorMessage : 'APPROVED';
    $obj->couponid = $couponid;
    $obj->gatewayid = get_gate_id('paylink');
    $obj->timecreated = time();
    $obj->timemodified = time();

    if ($invoiceResp->success) {

        if (!$DB->record_exists('new_payment', ['userid' => $userid, 'courseid' => $courseid])) {
            $id = $DB->insert_record('new_payment', $obj);
            
            if ($id && $obj->status == "paid") {
                if ($couponid) {
                    $coupon = $DB->get_record('coupon_pay', ['id' => $couponid]);
                    $coupon->count = $coupon->count + 1;
                    $coupon->timemodified = time();
                    $DB->update_record('coupon_pay', $coupon);
                }
                $studentRoleId = $DB->get_field('role', 'id', ['shortname' => 'student']);
                enrol_try_internal_enrol($courseid, $userid, $studentRoleId);
                redirect("$CFG->wwwroot/course/view.php?id=$courseid", 'User successfully enrolled in the course!');
            } else {
                redirect("$CFG->wwwroot/my", 'Something Went Wrong! Try again');
            }
        } else if ($order = get_failed_payment($courseid, $userid)) {
            $newOrder = new stdClass();
            $newOrder->id = $order->id;
            $newOrder->orderid = $invoiceResp->gatewayOrderRequest->orderNumber;
            $newOrder->status = strtolower($invoiceResp->orderStatus);
            $newOrder->amount = $invoiceResp->amount;
            $newOrder->currency = $invoiceResp->gatewayOrderRequest->currency;
            $newOrder->message = $invoiceResp->paymentErrors[0]->errorMessage ? $invoiceResp->paymentErrors[0]->errorMessage : 'APPROVED';
            $newOrder->couponid = $couponid;
            $newOrder->gatewayid = get_gate_id('paylink');
            $newOrder->timemodified = time();

            $DB->update_record('new_payment', $newOrder);
            if ($newOrder->status == "paid") {
                if ($couponid) {
                    $coupon = $DB->get_record('coupon_pay', ['id' => $couponid]);
                    $coupon->count = $coupon->count + 1;
                    $coupon->timemodified = time();
                    $DB->update_record('coupon_pay', $coupon);
                }
                $studentRoleId = $DB->get_field('role', 'id', ['shortname' => 'student']);
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
