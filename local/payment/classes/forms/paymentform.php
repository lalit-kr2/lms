<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");


class user_form extends moodleform {
    // Add elements to form.
    public function definition() {
        
        $mform = $this->_form;
      
        $mform->setType('button', PARAM_NOTAGS); 
       
        $this->add_action_buttons('button','get_string(set as primery',false);
        $this->add_action_buttons('button','set as primery','deactivate');
        $this->add_action_buttons('button','set as primery','deactivate');
    }
    

}

