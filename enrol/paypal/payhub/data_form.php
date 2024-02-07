<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
die("hi");


class enrol_payhub_data_form extends moodleform {

    public function definition() {
        global $DB;
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_payhub'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $options = array(
            ENROL_INSTANCE_ENABLED => get_string('yes'),
            ENROL_INSTANCE_DISABLED => get_string('no')
        );
        $mform->addElement('select', 'status', get_string('status', 'enrol_payhub'), $options);
        $mform->setDefault('status', $plugin->get_config('status'));

        $mform->addElement('text', 'cost', get_string('cost', 'enrol_payhub'), array('size' => 4));
        $mform->setType('cost', PARAM_RAW); // Use unformat_float to get real value.
        $mform->setDefault('cost', format_float($plugin->get_config('cost'), 2, true));

        $mform->addElement('text', 'customint1', get_string('vat', 'enrol_payhub'), array('size' => 4));
        $mform->setType('customint1', PARAM_RAW);
        $mform->setDefault('customint1', intval($plugin->get_config('vat')));
        $mform->addHelpButton('customint1', 'vat', 'enrol_payhub');

        $mform->addElement('text', 'customint1', get_string('vatnumber', 'enrol_payhub'), array('size' => 4));
        $mform->setType('customint1', PARAM_RAW);
        $mform->setDefault('customint1', intval($plugin->get_config('vatnumber')));
        $mform->addHelpButton('customint1', 'vatnumber', 'enrol_payhub');
        $mform->addElement('text', 'customint1', get_string('vatnumber', 'enrol_payhub'), array('size' => 4));

        $payhubcurrencies = $plugin->get_currencies();
        $mform->addElement('select', 'currency', get_string('currency', 'enrol_payhub'), $payhubcurrencies);
        $mform->setDefault('currency', $plugin->get_config('currency'));

       

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

        $cohorts = array(0 => get_string('no'));
        $allcohorts = cohort_get_available_cohorts($context, 0, 0, 0);
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5]) &&
                ($c = $DB->get_record('cohort', array('id' => $instance->customint5),
                        'id, name, idnumber, contextid, visible', IGNORE_MISSING))) {
            // Current cohort was not found because current user can not see it. Still keep it.
            $allcohorts[$instance->customint5] = $c;
        }
        foreach ($allcohorts as $c) {
            $cohorts[$c->id] = format_string($c->name, true, array('context' => context::instance_by_id($c->contextid)));
            if ($c->idnumber) {
                $cohorts[$c->id] .= ' ['.s($c->idnumber).']';
            }
        }
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5])) {
            // Somebody deleted a cohort, better keep the wrong value so that random ppl can not enrol.
            $cohorts[$instance->customint5] = get_string('unknowncohort', 'cohort', $instance->customint5);
        }
        if (count($cohorts) > 1) {
            $mform->addElement('select', 'customint5', get_string('cohortonly', 'enrol_self'), $cohorts);
            $mform->addHelpButton('customint5', 'cohortonly', 'enrol_self');
        } else {
            $mform->addElement('hidden', 'customint5');
            $mform->setType('customint5', PARAM_INT);
            $mform->setConstant('customint5', 0);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $options = array(
            0 => get_string('no'),
            1 => get_string('expirynotifyenroller', 'core_enrol'),
            2 => get_string('expirynotifyall', 'core_enrol')
        );
        
        if (enrol_accessing_via_instance($instance)) {
            $mform->addElement('static', 'selfwarn', get_string('instanceeditselfwarning', 'core_enrol'),
                    get_string('instanceeditselfwarningtext', 'core_enrol'));
        }

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
