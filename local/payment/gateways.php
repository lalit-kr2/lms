<?php
require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/payment/classes/tables/gateway_listing.php');
require_once($CFG->dirroot . '/user/lib.php');

global $CFG, $DB, $USER;

require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_title('Manage Payment Gateways');
$PAGE->set_heading('Manage Payment Gateways');
$PAGE->requires->css(new moodle_url('/local/payment/amd/style/payment.css'));

echo $OUTPUT->header();
$PAGE->navbar->add('Payment Report', new moodle_url('/local/payment/payment_report.php'));
$PAGE->navbar->add('Add Gateway', new moodle_url('/local/payment/add_gateway.php'));
echo '<a href="' . $CFG->wwwroot . '/local/payment/add_gateway.php" > <button class="btn btn-primary mb-5 ml-5  float-end">Add a new payment gateway</button></a>';

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action','', PARAM_TEXT);

if ($id != 0) {
    $gateways = $DB->get_records_sql("SELECT * FROM {new_payment_gateways}");
    foreach ($gateways as $gateway) {
        if ($gateway->id == $id) {
            $DB->set_field('new_payment_gateways', 'state', 1, ['id' => $id]);
        } else {
            $DB->set_field('new_payment_gateways', 'state', 0, ['id' => $gateway->id]);
        }
    }
}

if($action=='delete' && $id!=0){
    if( $DB->delete_records_select('new_payment_gateways','id='.$id)){
        redirect("$CFG->wwwroot/local/payment/gateways.php","Gateway deleted successsfully!");
    }
   
}
$where .= "1=1";

$table = new test_table('uniqueid');
$fields = '(@row_number:=@row_number + 1) as sr,pg.id,pg.name as name,pg.state as state,pg.logo as logo,pg.timemodified ';

$from = '{new_payment_gateways} AS pg';
$data = $table->set_sql($fields, $from, $where);
$perpage = 10;

$DB->execute('SET @row_number = ' . (($perpage * $page)), array());
$table->sortable(true, 'sr', SORT_DESC);

$table->define_baseurl($CFG->wwwroot . "/local/payment/gateway_listing.php?search=" . $search);
$table->out($perpage, true);

echo $OUTPUT->footer();
