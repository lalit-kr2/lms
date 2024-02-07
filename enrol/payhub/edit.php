<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_payhub to specified course
 *
 * File         edit.php
 * Encoding     UTF-8
 *
 * @package     enrol_payhub
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once("$CFG->dirroot/enrol/payhub/edit_form.php");

$courseid = optional_param('courseid', 0, PARAM_INT); // Instanceid.
$instanceid = optional_param('id', 0, PARAM_INT); // Instanceid.

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
// require_capability('enrol/payhub:config', $context);

$PAGE->set_url('/enrol/payhub/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('standard');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('payhub')) {
    redirect($return);
}

$plugin = enrol_get_plugin('payhub');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array(
        'courseid' => $course->id,
        'enrol' => 'payhub',
        'id' => $instanceid
    ), '*', MUST_EXIST);
    $instance->cost = format_float($instance->cost, 2, true);
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));
    $instance = new stdClass();
    $instance->id = null;
    $instance->courseid = $course->id;

    // Force customint5 to prevent notices.
    $instance->customint5 = null;
}

$mform = new enrol_payhub_data_form(null, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);
} else {
    if ($data = $mform->get_data()) {
        if ($DB->record_exists('enrol_payhub', ['instanceid' => $instance->id])) {
            $instance->instanceid = $instance->id;
            $instance->cost = $data->cost ? unformat_float($data->cost) : $plugin->get_config('cost');
            $instance->roleid = $data->roleid;
            $instance->enrolperiod = $data->enrolperiod;
            $instance->timemodified = time();
            $DB->update_record('enrol_payhub', $instance);
        } else {
            $fields = new stdClass();
            $fields->instanceid = $DB->get_field('enrol', 'id', ['courseid' => $data->courseid, 'enrol' => 'payhub']);
            $fields->courseid = $data->courseid;
            $fields->cost = $data->cost ? unformat_float($data->cost) : $plugin->get_config('cost');
            $fields->roleid = $data->roleid;
            $fields->enrolperiod = $data->enrolperiod;
            $fields->timecreated = time();
            $fields->timemodified = time();
            $DB->insert_record('enrol_payhub', $fields);
        }

        redirect($return);
    }
}
$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_payhub'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'enrol_payhub'));
$mform->display();
echo $OUTPUT->footer();
