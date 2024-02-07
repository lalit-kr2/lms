<?php 
// defined('MOODLE_INTERNAL' || die());
include("../../config.php");

global $DB;
$keys = $DB->get_record('new_payment_gateways');
die("S");
// $apiKey = $DB->get_field('new_payment_gateways', 'apikey', ['state' => 1]);
// $apiSecret = $DB->get_field('new_payment_gateways', 'apisecret', ['state' => 1]);

define('BASE_URL', "$CFG->wwwroot");
define('API_KEY', "$apiKey");
define('API_SECRET', "$apiSecret");
define('COMPANY_NAME', 'LMS');
define('COMPANY_LOGO_URL', $CFG->wwwroot.'/local/payment/images/lms.png');
define('ORDER_RECIEPT_ID', uniqid());
