<?php 

require_once('../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'block_coupon_pay'));
$PAGE->set_heading(get_string('pluginname', 'block_coupon_pay'));

echo $OUTPUT->header();

echo html_writer::link("$CFG->wwwroot/blocks/coupon_pay/create_coupon.php", get_string('createcoupon', 'block_coupon_pay'), [
    'class' => 'btn btn-primary',
    'id' => 'buttonId',
]);

// Retrieve coupon data from the database.
$coupons = $DB->get_records('coupon_pay', [], 'id DESC');

if ($coupons) {
    $table = new html_table();
    $table->head = array('Coupon Code', 'Discount Percentage', 'Expiry Date', 'Time Created');

    foreach ($coupons as $coupon) {
        $table->data[] = array(
            $coupon->coupon,
            $coupon->discount . '%',
            userdate($coupon->expiry_date, get_string('strftimedate')),
            userdate($coupon->timecreated, get_string('strftimedate')),
        );
    }

    echo html_writer::table($table);
} else {
    echo html_writer::tag('p', get_string('nocoupons', 'block_coupon_pay'), ['class' => 'error']);
}

echo $OUTPUT->footer();
