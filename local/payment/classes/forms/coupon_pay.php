<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class coupon_pay_form extends moodleform
{
    public function definition()
    {
        global $CFG,$DB;

        $form = $this->_form;

        $form->addElement('header', 'Select', get_string('createcoupon', 'local_payment'));

        $data = $DB->get_records_sql('SELECT c.id,c.shortname,c.fullname FROM {course} AS c ORDER BY c.shortname');

        $courses = [];
        if ($data) {
            $courses[0] = 'All';
            foreach ($data as $course) {
                $courses[$course->id] = $course->fullname;
            }
        } else {
            $courses['name'] = 'No course found';
        }

        $form->addElement('select', 'course', get_string('course', ''), $courses);
        $form->setDefault('course', 'Select');
        $form->addRule('course', get_string('course', 'local_payment'), 'required', null, 'client');
    
        $form->addElement('date_selector', 'start_date', get_string('start_date', 'local_payment'));
        $form->addHelpButton('start_date', 'start_date', 'local_payment');
        $form->setType('start_date', PARAM_RAW);
        $form->addRule('start_date', get_string('required'), 'required');
        

        $form->addElement('date_selector', 'expiry_date', get_string('expiry_date', 'local_payment'));
        $form->addHelpButton('expiry_date', 'expiry_date', 'local_payment');
        $form->setType('expiry_date', PARAM_RAW);
        $form->addRule('expiry_date', get_string('required'), 'required');

        $types = array(
            'percentage' => get_string('coupontype:percentage', 'local_payment'),
            'value' => get_string('coupontype:value', 'local_payment')
        );
        $form->addElement('select', 'type', get_string('coupontype', 'local_payment'), $types);
        $form->setDefault('type', 'percentage');
        $form->setType('type', PARAM_ALPHA);

        $form->addElement('text', 'discount_percentage', get_string('discountpercentage', 'local_payment'),['maxlength="3" ' ,'placeholder'=>"Percentage"]);
        $form->setType('discount_percentage', PARAM_INT);
        $form->addRule('discount_percentage', get_string('required'), 'required');
        
        $form->addElement('text', 'maxusage', get_string('maxusage', 'local_payment'), 'maxlength="6" size="6"');
        $form->addRule('maxusage', null, 'numeric', null, 'client');
        $form->addRule('maxusage', null, 'rangelength', array(1, 6), 'client');
        $form->setType('maxusage', PARAM_TEXT);
        $form->setDefault('maxusage', 0);
        $form->addHelpButton('maxusage', 'maxusage', 'local_payment');

        

        $this->add_action_buttons(true, get_string('generate', 'local_payment'));
    }

    public function validation($data, $files)
    {
        // var_dump($data);
        $errors = array();
        $errors = parent::validation($data, $files);

        if ($data['start_date'] >= $data['expiry_date']) {
            $errors['start_date'] = get_string('start_date_error', 'local_payment');
        }

        if ($data['expiry_date'] <= $data['start_date']) {
            $errors['expiry_date'] = get_string('expiry_date_error', 'local_payment');
        }

        if ($data['course'] == 0) {
            $errors['course'] = get_string('course', 'local_payment'); 
        }

        // if ($data['discount_percentage']>100) {
           
        //     $errors['discount_percentage'] = get_string('invalidpercentage', 'local_payment');  
        // }

        if ($data['type'] === 'percentage') {
            if ((float)$data['discount_percentage'] < 0.00) {
                $errors['discount_percentage'] = get_string('err:percentage-negative', 'enrol_classicpay');
            }
            if ((float)$data['discount_percentage'] > 100.00) {
                $errors['discount_percentage'] = get_string('err:percentage-exceed', 'enrol_classicpay');
            }
        } else if ($data['discount_percentage'] === 'value') {
            if ((float)$data['value'] < 0.00) {
                $errors['discount_percentage'] = get_string('err:value-negative', 'enrol_classicpay');
            }
        }
        return $errors;
    }
}
