<?php

require_once("../../config.php");
require_once("$CFG->dirroot/local/payment/classes/forms/add_gateway.php");

$PAGE->set_pagelayout('standard');
$PAGE->set_title('Add a new payment gateway');
$PAGE->set_heading('Add a new payment gateway');

$id = optional_param('id', 0, PARAM_INT);

// Instantiate the myform form from within the plugin.
$mform = new add_gateway($id);


if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/payment/gateways.php'));
    
} else if ($fromform = $mform->get_data()) {
    
    $ins = new stdClass();
    $ins->name = $fromform->name;
    $ins->shortname = $fromform->shortname;
    $ins->logo = "$CFG->wwwroot/local/payment/images/" . $mform->get_new_filename('logo');
    $ins->apikey = $fromform->apikey;
    $ins->apisecret = $fromform->apisecret;
    $ins->state = '0';
    $ins->timemodified = time();
    
    $table = 'new_payment_gateways';
    $location = "$CFG->dirroot/local/payment/images/";
    $success = $mform->save_file('logo', $location.$mform->get_new_filename('logo'), true);

    
    if ($id!= 0) {
        // Update existing record
        $ins->id = $id;
        $DB->update_record($table, $ins);
        redirect(new moodle_url('/local/payment/gateways.php'), 'Gateway successfully updated!');
    } else {    
        $DB->insert_record($table, $ins);
        redirect(new moodle_url('/local/payment/gateways.php'), 'A new gateway successfully added.');
      
    }
} else {
    
    
    if ($id > 0) {
        global $DB;
        $gate = $DB->get_record_sql('SELECT * FROM {new_payment_gateways} WHERE id = ?', array($id));
        $toform = new stdClass();
        $toform->id=$id;
        $toform->name = $gate->name;
        $toform->shortname = $gate->shortname;
        $toform->apikey = $gate->apikey;
        $toform->apisecret = $gate->apisecret;
        
        $mform->set_data($toform);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
