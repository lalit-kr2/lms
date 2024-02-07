<?php
require_once("../../config.php");
require_once("$CFG->dirroot/local/payment/lib.php");
require_once("$CFG->dirroot/enrol/payhub/lib.php");
require_once("$CFG->dirroot/lib/enrollib.php");

global $DB;

$orderid = required_param('id', PARAM_TEXT);
$status = required_param('status', PARAM_TEXT);
$amount = required_param('amount', PARAM_TEXT);
$message = required_param('message', PARAM_TEXT);
$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$couponid = optional_param('couponid', 0, PARAM_INT);

$payhub = new enrol_payhub_plugin();

$currency = 'SAR';
$gateId = get_gate_id('moyasar');

$obj = new stdClass();
$obj->userid = $userid;
$obj->courseid = $courseid;
$obj->orderid = $orderid;
$obj->status = $status;
$obj->amount = $amount;
$obj->currency = $currency;
$obj->message = $message;
$obj->couponid = $couponid;
$obj->gatewayid = $gateId;
$obj->timecreated = time();
$obj->timemodified = time();

$instance = $DB->get_record('enrol', ['enrol' => $payhub->get_name(), 'courseid' => $courseid]);

if (!$DB->record_exists('new_payment', ['userid' => $userid, 'courseid' => $courseid])) {
    $id = $DB->insert_record('new_payment', $obj);

    if ($id && $status == "paid") {
        if ($couponid) {
            $coupon = $DB->get_record('coupon_pay', ['id' => $couponid]);
            $coupon->count = $coupon->count + 1;
            $coupon->timemodified = time();
            $DB->update_record('coupon_pay', $coupon);
        }
        $studentRoleId = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $payhub->enrol_user($instance, $userid, $studentRoleId);
        // enrol_try_internal_enrol($courseid, $userid, $studentRoleId);
        redirect("$CFG->wwwroot/course/view.php?id=$courseid", 'User successfully enrolled in the course!');
    } else {
        redirect("$CFG->wwwroot/my", 'Something Went Wrong! Try again');
    }
} else if ($order = get_failed_payment($courseid, $userid)) {
    $newOrder = new stdClass();
    $newOrder->id = $order->id;
    $newOrder->orderid = $orderid;
    $newOrder->status = $status;
    $newOrder->amount = $amount;
    $newOrder->currency = $currency;
    $newOrder->message = $message;
    $newOrder->couponid = $couponid;
    $newOrder->gatewayid = $gateId;
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
        $payhub->enrol_user($instance, $userid, $studentRoleId);
        // enrol_try_internal_enrol($courseid, $userid, $studentRoleId);
        redirect("$CFG->wwwroot/course/view.php?id=$courseid", 'User successfully enrolled in the course!');
    }
} else {
    redirect("$CFG->wwwroot/my", 'User already enrolled in the course!');
}
