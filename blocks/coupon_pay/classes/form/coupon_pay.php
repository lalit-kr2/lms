<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class coupon_pay_form extends moodleform
{
    public function definition()
    {
        global $CFG;

        $form = $this->_form;

        $form->addElement('header', 'Select', get_string('createcoupon', 'block_coupon_pay'));

        $form->addElement('date_selector', 'start_date', get_string('start_date', 'block_coupon_pay'));
        $form->addHelpButton('start_date', 'start_date', 'block_coupon_pay');
        $form->setType('start_date', PARAM_RAW);
        $form->addRule('start_date', get_string('required'), 'required');

        $form->addElement('date_selector', 'expiry_date', get_string('expiry_date', 'block_coupon_pay'));
        $form->addHelpButton('expiry_date', 'expiry_date', 'block_coupon_pay');
        $form->setType('expiry_date', PARAM_RAW);
        $form->addRule('expiry_date', get_string('required'), 'required');

        $form->addElement('text', 'discount_percentage', get_string('discountpercentage', 'block_coupon_pay'));
        $form->setType('discount_percentage', PARAM_INT);
        $form->addRule('discount_percentage', get_string('required'), 'required');

        

        $this->add_action_buttons(true, get_string('generate', 'block_coupon_pay'));
    }

    public function validation($data, $files)
    {
        $errors = array();
        $errors = parent::validation($data, $files);

        if ($data['start_date'] >= $data['expiry_date']) {
            $errors['start_date'] = get_string('start_date_error', 'block_coupon_pay');
        }
        if ($data['expiry_date'] <= $data['start_date']) {
            $errors['expiry_date'] = get_string('expiry_date_error', 'block_coupon_pay');
        }
        return $errors;
    }
}
