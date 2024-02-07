<?php

require_once("../../config.php");

global $CFG;


require_once("$CFG->dirroot/blocks/coupon_pay/classes/form/coupon_pay.php");
require_once("lib.php");

if (!is_siteadmin()) {
    echo "You do not have the authorization";
    exit;
}

$mform = new coupon_pay_form();

$PAGE->set_title(get_string('pluginname', 'block_coupon_pay'));
$PAGE->set_heading(get_string('pluginname', 'block_coupon_pay'));

echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/my/");
} else if ($data = $mform->get_data()) {
    $starttime = $data->start_date;
    $expirytime = $data->expiry_date;

    $coupon = generateCoupon();

    $obj = new stdClass();
    $obj->coupon = $coupon;
    $obj->discount = $data->discount_percentage;
    $obj->start_date = $data->start_date;
    $obj->expiry_date = $data->expiry_date;
    $obj->timecreated = time();

    $id = $DB->insert_record('coupon_pay', $obj);

    if ($id) {
        redirect("$CFG->wwwroot/blocks/coupon_pay/index.php", get_string('coupon_created', 'block_coupon_pay'));
    }
} else {
    $mform->display();
}

echo $OUTPUT->footer();
