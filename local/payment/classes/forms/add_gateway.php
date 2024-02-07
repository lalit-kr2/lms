<?php

require_once("$CFG->libdir/formslib.php");

class add_gateway extends moodleform
{

    public $gateId;
    public function __construct($id = 0)
    {
        parent::__construct();
        $this->gateId = $id;
    }
    public function definition()
    {

        $mform = $this->_form; // Don't forget the underscore!

        $id = optional_param('id', 0, PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->addElement('header', '', get_string('addpaymentgateway', 'local_payment'));

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // $mform->addElement('text', 'shortname', get_string('shortname'));
        // $mform->setType('shortname', PARAM_TEXT);
        // $mform->addRule('shortname', null, 'required', null, 'client');

        if ($id > 0) {
            $mform->addElement('checkbox', 'show_filepicker', 'Update Logo');
            $mform->setType('show_filepicker', PARAM_INT);
        }
        $mform->addElement('filepicker', 'logo', get_string('logo', 'local_payment'));

        if ($id > 0) {
            $mform->hideIf('logo', 'show_filepicker', 'notchecked');
            $mform->setDefault('show_filepicker', 0);
        }


        $mform->addElement('text', 'apikey', get_string('apikey', 'local_payment'));
        $mform->setType('apikey', PARAM_TEXT);

        $mform->addElement('text', 'apisecret', get_string('apisecret', 'local_payment'));
        $mform->setType('apisecret', PARAM_TEXT);

        if($id > 0){
            $this->add_action_buttons(true, get_string('update', 'local_payment'));
        } else {
            $this->add_action_buttons(true, get_string('add', 'local_payment'));
        }
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
