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
//
/**
 * @package   plugintype_pluginname
 * @copyright 2020, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/payment/classes/tables/manage_coupons.php');


$tenantId = optional_param('tenantData', '', PARAM_TEXT);


global $CFG, $DB, $USER;


require_login();

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Coupon Management');
echo $OUTPUT->header();

// $search = optional_param('search', '', PARAM_TEXT);
// $page = optional_param('page', 0, PARAM_INT);
// $PAGE->requires->js(new moodle_url("/local/tenant/amd/src/modal.js"));

echo '<a href="' . $CFG->wwwroot . '/local/payment/create_coupon.php" > <button class="btn btn-primary mb-5 ml-5  float-end">Create Coupon</button></a>';

        




echo "
<style>
.generaltable {
   
    color: #0f6cbf !important;
}
</style>
";
// echo "<div class='d-flex p-2'>";
// echo "<form method='post' action='user_listing.php'>
// <input type='text' class='form-control withclear rounded' name='search' value='$search' placeholder='Search'>
// <input type='submit' name='submit' class='btn btn-primary mt-2' value='Search'>
// <a href='$CFG->wwwroot/local/tenant/user_listing.php'>
// <input type='button' name='reset' class='btn btn-secondary mt-2' value='Clear'></a>
// </form>
// </div>";



$where.="1=1";

$table = new test_table('uniqueid');
$fields = '(@row_number:=@row_number + 1) as sr,cp.id,c.fullname as coursename,cp.coupon as coupon,cp.courseid,cp.discount as discount,cp.type as type,cp.maxusage as maxusage,cp.timecreated,cp.start_date,cp.expiry_date';

$from = '{coupon_pay} AS cp JOIN {course} AS c ON cp.courseid=c.id';
$data = $table->set_sql($fields, $from, $where);
$perpage = 10;

$DB->execute('SET @row_number = ' . (($perpage * $page)), array());
$table->sortable(true, 'sr', SORT_DESC);

$table->define_baseurl($CFG->wwwroot . "/local/payment/manage_coupon.php?search=" . $search);
$table->out($perpage, true);


echo $OUTPUT->footer();
