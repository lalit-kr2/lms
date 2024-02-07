<?php
require_once("../../config.php");
require_once("../../lib/moodlelib.php");
require_once("../../user/lib.php");

$courseid = optional_param('id', 0, PARAM_INT);
$formType = optional_param('formType', "", PARAM_TEXT);
$couponCode = optional_param('coupon', '', PARAM_TEXT);

$amount = $DB->get_record_sql("SELECT mcfd.value FROM {customfield_data} mcfd JOIN {customfield_field} mcff ON mcfd.fieldid = mcff.id WHERE mcff.shortname = 'price' AND mcfd.instanceid = $courseid")->value;
$vat = $DB->get_record_sql("SELECT mcfd.value FROM {customfield_data} mcfd JOIN {customfield_field} mcff ON mcfd.fieldid = mcff.id WHERE mcff.shortname = 'vat' AND mcfd.instanceid = $courseid")->value;
$moyasar = $DB->get_record('new_payment_gateways', ['name' => 'Moyasar']);
$paylink = $DB->get_record('new_payment_gateways', ['name' => 'Paylink']);
$tap = $DB->get_record('new_payment_gateways', ['name' => 'Tap Pay']);

$moyasar = $moyasar->state == 1 ? true : false;
$paylink = $paylink->state == 1 ? true : false;
$tap = $tap->state == 1 ? true : false;

if ($vat) {
    $totalAmount = ($amount * (100 + $vat)) / 100;
}

if ($couponCode) {
    $coupon = $DB->get_record('coupon_pay', ['coupon' => $couponCode]);
    $response['success'] = false;
    $response['amount'] = $totalAmount ? $totalAmount : $amount;
    if ($coupon->start_date <= time() && $coupon->expiry_date >= time() && $coupon->count < $coupon->maxusage) {
        $response['success'] = true;
        $response['couponid'] = $coupon->id;
        $discount = $coupon->discount;
        if($totalAmount){
            $finalAmount = round(($totalAmount * (100 - $discount)) / 100, 2);
        } else {
            $finalAmount = round(($amount * (100 - $discount)) / 100, 2);
        }
        $response['discount'] = $discount;
        $response['finalAmount'] = $finalAmount;
    }
    echo json_encode($response);
    exit();
}

if ($formType === 'login') {
    $formData = $_POST['formData'];
    foreach ($formData as $data) {
        ${$data['name']} = $data['value'];
    }
    $response = ['login' => false, 'error' => 'Invalid Credentials!'];

    if ($user = authenticate_user_login($username, $password)) {
        $response['login'] = true;
        $response['userid'] = $user->id;
        $response['username'] = "$user->firstname $user->lastname";
        unset($response['error']);
        $response['paid'] = false;
        if ($DB->record_exists('new_payment', ['userid' => $user->id, 'courseid' => $courseid, 'status' => 'paid'])) {
            $response['paid'] = true;
        }
        complete_user_login($user);
    }

    echo json_encode($response);
    exit();
} else if ($formType === 'registration') {
    $username = optional_param('username', '', PARAM_TEXT);
    $firstname = optional_param('firstname', '', PARAM_TEXT);
    $lastname = optional_param('lastname', '', PARAM_TEXT);
    $email = optional_param('email', '', PARAM_TEXT);
    $password = optional_param('password', '', PARAM_TEXT);
    $phone = optional_param('phone', '', PARAM_INT);

    $data = [];
    if ($DB->record_exists('user', ['username' => $username])) {
        $data['username_error'] = true;
    }
    if ($DB->record_exists('user', ['email' => $email])) {
        $data['email_error'] = true;
    }
    if (empty($data)) {
        $obj = new stdClass();
        $obj->username = $username;
        $obj->firstname = $firstname;
        $obj->lastname = $lastname;
        $obj->email = $email;
        $obj->confirmed = 1;
        $obj->mnethostid = 1;
        $obj->password = $password;
        $obj->phone1 = $phone;

        $id = user_create_user($obj);
        $user = $DB->get_record('user', ['id' => $id]);
        complete_user_login($user);

        if ($id) {
            $data['created'] = true;
            $data['userid'] = $id;
            $data['username'] = "$firstname $lastname";
        }
    }
    echo json_encode($data);
    exit();
}

$coursename = $DB->get_field('course', 'fullname', ['id' => $courseid]);

$alreadyPaid = $DB->record_exists_sql("SELECT id FROM {new_payment} WHERE userid = $USER->id AND courseid = $courseid AND status IN ('paid', 'captured')")->id;

$templatecontext = [
    'courseid' => $courseid,
    'userid' => $USER->id,
    'coursename' => $coursename,
    'amount' => $amount,
    'vat' => $vat,
    'totalAmount' => $totalAmount,
    'isloggedin' => isloggedin(),
    'alreadyPaid' => $alreadyPaid,
    'moyasar' => $moyasar,
    'paylink' => $paylink,
    'tap' => $tap
];

echo $OUTPUT->render_from_template("local_payment/index", $templatecontext);
