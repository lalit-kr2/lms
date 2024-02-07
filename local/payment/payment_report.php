<?php

require_once('../../config.php');
global $DB, $CFG;
require "$CFG->libdir/tablelib.php";
require "classes/tables/test_table.php";
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/payment/payment_report.php');

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$download = optional_param('download', '', PARAM_ALPHA);


$table = new test_table('uniqueid');
$table->is_downloading($download, 'test', 'testing123');



if (!$table->is_downloading()) {
    $PAGE->set_title('Payment Report');
    $PAGE->set_pagelayout('standard');

    $PAGE->set_heading(get_string('createcoupon', 'local_payment'));
    echo $OUTPUT->header();
    $PAGE->navbar->add('Payment Report', new moodle_url('/local/payment/payment_report.php'));

    echo '<a href="' . $CFG->wwwroot . '/local/payment/manage_coupons.php" > <button class="btn btn-primary mb-5 ml-5  float-end">Manage Coupons</button></a>';

    echo '<a href="' . $CFG->wwwroot . '/local/payment/gateways.php" > <button class="btn btn-primary mb-5 ml-2 mr-5 float-end">Primary Gateways</button></a>';

    $fields = '(@row_number:=@row_number + 1) as sno, mu.firstname, mu.lastname, np.status, c.fullname AS coursename, cfd.value, np.amount, np.timecreated, np.message, np.couponid,np.gatewayid';
    $from = "{user} AS mu
    JOIN {new_payment} AS np ON np.userid = mu.id
    JOIN {course} AS c ON c.id = np.courseid
    JOIN {customfield_data} AS cfd ON cfd.instanceid = c.id";
    $where = "1=1 ";
    $perpage = 10;
    $page = optional_param('page', 0, PARAM_INT);
    $DB->execute('SET @row_number = ' . (($perpage * $page)), array());

    $table->set_sql($fields, $from, $where);
}

$table->define_baseurl("$CFG->wwwroot/local/payment/payment_report.php");

$table->out($perpage, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
