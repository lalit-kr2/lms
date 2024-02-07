<?php
require_once(__DIR__ . '../../../../config.php');
require_once(__DIR__ . '../../lib.php');
require_once($CFG->dirroot.'/user/lib.php');
global $DB, $OUTPUT, $PAGE ,$SITE;
$courses = $DB->get_records_sql("SELECT * FROM {course} WHERE format != 'site' ORDER BY id DESC");
foreach ($courses as $course) {
    $obj = new stdClass();
    $obj->id = $course->id;
    $obj->fullname = $course->fullname;
    $obj->duration = ($course->enddate - $course->startdate) / (24 * 60 * 60);
    $obj->price = $DB->get_record('customfield_data',['instanceid'=>$course->id,'fieldid'=>1])->value;
    $url = get_course_image($course->id);
    if ($url) {
        $obj->imageurl = $url;
    } else {
        $obj->imageurl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsNGGjrfSqqv8UjL18xS4YypbK-q7po_8oVQ&usqp=CAU';
    }
    $course_arr[] = $obj;
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'course_arr' => $course_arr,
];
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_payment/all_course', $templatecontext);
echo $OUTPUT->footer();