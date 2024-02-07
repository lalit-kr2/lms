<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => 'core\event\course_viewed',
        'callback' => '\local_payment\observers::handlee',
    ),
);