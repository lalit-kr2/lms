<?php

/**
 * Add Payment Plugin to the navigation
 */
function local_payment_extend_navigation(global_navigation $navigation)
{
    global $CFG, $PAGE;

    $icon = new pix_icon('key', '', 'local_payment', array('class' => 'icon pluginicon', 'style' => 'width: 22px; height: 30px; object-fit: contain;'));

    $navigation->add(
        "Payment Gate",
        new moodle_url($CFG->wwwroot . '/local/payment/payment_report.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_payment',
        null
    )->showinflatnavigation = true;
}

/**
 * Get the course image url
 * @param int $courseid: Course id
 * @return string
 */
function get_course_image($courseid): string
{
    $url = '';
    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false);
        }
    }
    return $url;
}

/**
 * Get the id of payment gateway
 * @param string $name: Shortname of the payment gateway
 * @return int
 */
function get_gate_id($name): int
{
    global $DB;
    return $DB->get_record('new_payment_gateways', ['shortname' => $name], 'id')->id;
}

/**
 * Get the failed payment object occurred while using tap company
 * @param int $courseid
 * @param int $userid
 * @return object|bool
 */
function get_failed_tap($courseid, $userid)
{
    global $DB;
    $gateId = get_gate_id('tap');
    $order = $DB->get_record_sql("SELECT * FROM {new_payment} WHERE userid = $userid AND courseid = $courseid AND status != 'captured' AND gatewayId = $gateId");
    return $order;
}

/**
 * Get the failed payment object occurred while using paylink or moyasar
 * @param int $courseid
 * @param int $userid
 * @param string $name
 * @return object|bool
 */
function get_failed_payment($courseid, $userid)
{
    global $DB;
    $order = $DB->get_record_sql("SELECT * FROM {new_payment} WHERE userid = $userid AND courseid = $courseid AND status NOT IN ('paid', 'captured')");
    return $order;
}

// function generateCoupon($length = 6) {
//     do {
//         $randomBytes = bin2hex(random_bytes(ceil($length / 2)));
//         $randomString = strtoupper(substr($randomBytes, 0, $length));
//         $isUnique = isCouponUnique($randomString);

//     } while (!$isUnique);

//     return $randomString;
// }

// function isCouponUnique($randomString) {
//     return true;
// }
