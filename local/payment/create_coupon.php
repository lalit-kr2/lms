<?php

require_once("../../config.php");

global $CFG;


require_once("$CFG->dirroot/local/payment/classes/forms/coupon_pay.php");
require_once("lib.php");

$PAGE->set_pagelayout('standard');

$PAGE->set_heading(get_string('createcoupon', 'local_payment'));
echo $OUTPUT->header();

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

if (!is_siteadmin()) {
    echo "You do not have the authorization";
    exit;
}

$mform = new coupon_pay_form();

$PAGE->set_title(get_string('createcoupon', 'local_payment'));

// echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/local/payment/payment_report.php");
} else if ($data = $mform->get_data()) {
    $starttime = $data->start_date;
    $expirytime = $data->expiry_date;
    var_dump($data);
    // die;

    $coupon = generateCoupon();

    $obj = new stdClass();
    $obj->courseid = $data->course;
    $obj->coupon = $coupon;
    $obj->type = $data->type;
    $obj->discount = $data->discount_percentage;
    $obj->maxusage = $data->maxusage;
    $obj->start_date = $data->start_date;
    $obj->expiry_date = $data->expiry_date;
    $obj->timecreated = time();

    $id = $DB->insert_record('coupon_pay', $obj);

    if ($id) {
        // die;
        redirect("$CFG->wwwroot/local/payment/manage_coupons.php", get_string('coupon_created', 'local_payment'));
    }
} else {
    $mform->display();
}

echo $OUTPUT->footer();
