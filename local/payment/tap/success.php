<?php
require_once('config.php');
require_once("../../../config.php");
require_once("$CFG->dirroot/local/payment/lib.php");
require_once("$CFG->dirroot/lib/enrollib.php");
include_once('connection.php');

global $DB;

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$amount = optional_param('amount', 0, PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);
$authId = optional_param('tap_id', '', PARAM_TEXT);

try {
    $payload = [
        'amount' => $amount,
        'currency' => 'SAR',
        'customer' => [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email
        ],
        'source' => ['id' => $authId],
        'redirect' => [
            'url' => BASE_URL . '/local/payment/tap/success.php?userid=' . $userid . '&courseid=' . $courseid . '&couponid=' . $couponid
        ]
    ];

    $response = $client->request('POST', 'https://api.tap.company/v2/charges/', [
        'json' => $payload,
        'headers' => [
            'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ],
    ]);

    $chargeResp = json_decode($response->getBody());

    $obj = new stdClass();
    $obj->userid = $userid;
    $obj->courseid = $courseid;
    $obj->orderid = ORDER_RECIEPT_ID;
    $obj->status = strtolower($chargeResp->status);
    $obj->amount = $chargeResp->amount;
    $obj->currency = $chargeResp->currency;
    $obj->message = $chargeResp->gateway->message;
    $obj->couponid = $couponid;
    $obj->gatewayid = get_gate_id('tap');
    $obj->timecreated = time();
    $obj->timemodified = time();

    if ($chargeResp->status) {

        if (!$DB->record_exists('new_payment', ['userid' => $userid, 'courseid' => $courseid])) {
            $id = $DB->insert_record('new_payment', $obj);
            if ($id && $obj->status == "captured") {
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
            $newOrder->orderid = ORDER_RECIEPT_ID;
            $newOrder->status = strtolower($chargeResp->status);
            $newOrder->amount = $chargeResp->amount;
            $newOrder->currency = $chargeResp->currency;
            $newOrder->message = $chargeResp->gateway->message;
            $newOrder->couponid = $couponid;
            $newOrder->gatewayid = get_gate_id('tap');
            $newOrder->timemodified = time();

            $DB->update_record('new_payment', $newOrder);
            if ($newOrder->status == "captured") {
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
