<?php

require_once("../../config.php");
require_once "classes/forms/paymentform.php";
require_once "$CFG->dirroot/user/lib.php";
$mform = new user_form();
// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
    
    
} else if ($fromform = $mform->get_data()) {
   
    $id = user_create_user($fromform);

    
} else {
    
    $mform->set_data($toform);
    
    
    // Display the form.
    echo $OUTPUT->header();
    echo $mform->display();
    echo $OUTPUT->footer();
    
}
?>