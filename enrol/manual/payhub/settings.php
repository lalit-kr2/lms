<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $config = get_config('enrol_payhub');
    
    
    $optionsyesno = array(
        ENROL_INSTANCE_ENABLED => get_string('yes'),
        ENROL_INSTANCE_DISABLED => get_string('no')
    );
    


    $settings->add(new admin_setting_configselect(
        'enrol_payhub/status',
        get_string('status', 'enrol_payhub'),
        get_string('status_desc', 'enrol_payhub'),
        ENROL_INSTANCE_DISABLED,
        $optionsyesno
    ));

    $settings->add(new admin_setting_configtext(
        'enrol_payhub/cost',
        get_string('cost', 'enrol_payhub'),
        '',
        10.00,
        PARAM_FLOAT,
        4
    ));
    $settings->add(new admin_setting_configtext(
        'enrol_payhub/vat',
        get_string('vat', 'enrol_payhub'),
        get_string('vat_help', 'enrol_payhub'),
        21,
        PARAM_INT,
        4
    ));

    $classicpaycurrencies = enrol_get_plugin('classicpay')->get_currencies();
    $settings->add(new admin_setting_configselect('enrol_classicpay/currency',
            get_string('currency', 'enrol_classicpay'), '', 'EUR', $classicpaycurrencies));

    // add taxation number

    $settings->add(new admin_setting_configtext(
        'enrol_payhub/vatnumber',
        get_string('vatnumber', 'enrol_payhub'),
        get_string('vatnumber_help', 'enrol_payhub'),
        '9999999999',
        PARAM_TEXT,
        4
    ));

    // $payhubcurrencies = enrol_get_plugin('payhub')->get_currencies();
    $settings->add(new admin_setting_configselect(
        'enrol_payhub/currency',
        get_string('currency', 'enrol_payhub'),
        '',
        'EUR',
        $payhubcurrencies
    ));


    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect(
            'enrol_payhub/roleid',
            get_string('defaultrole', 'enrol_payhub'),
            get_string('defaultrole_desc', 'enrol_payhub'),
            $student->id,
            $options
        ));
    }
    $settings->add(new admin_setting_configduration(
        'enrol_payhuby/enrolperiod',
        get_string('enrolperiod', 'enrol_payhub'),
        get_string('enrolperiod_desc', 'enrol_payhub'),
        0
    ));

    $options = array(
        0 => get_string('no'),
        1 => get_string('expirynotifyenroller', 'core_enrol'),
        2 => get_string('expirynotifyall', 'core_enrol')
    );

    
}


