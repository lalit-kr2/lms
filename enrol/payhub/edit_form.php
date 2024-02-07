<?php

// defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/cohort/lib.php');



class enrol_payhub_data_form extends moodleform {

    public function definition() {
        global $DB;
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_payhub'));

        $mform->addElement('text', 'cost', get_string('cost', 'enrol_payhub'), array('size' => 4));
        $mform->setType('cost', PARAM_INT); // Use unformat_float to get real value.
        $mform->setDefault('cost', $plugin->get_config('cost'));

        $mform->addElement('text', 'customint1', get_string('vat', 'enrol_payhub'), array('size' => 4));
        $mform->setType('customint1', PARAM_RAW);
        $mform->setDefault('customint1', intval($plugin->get_config('vat')));
        $mform->addHelpButton('customint1', 'vat', 'enrol_payhub');       

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_payhub'), $roles);
        $mform->setDefault('roleid', $plugin->get_config('roleid'));

        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_payhub'), array(
            'optional' => true,
            'defaultunit' => 86400
        ));
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));
        $mform->addHelpButton('enrolperiod', 'enrolperiod', 'enrol_payhub');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }


    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        $cost = str_replace(get_string('decsep', 'langconfig'), '.', $data['cost']);
        if (!is_numeric($cost)) {
            $errors['cost'] = get_string('costerror', 'enrol_payhub');
        }

        return $errors;
    }

}
