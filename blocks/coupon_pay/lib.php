<?php

/**
 * Generate a 6 characters random alphanumeric string
 * 
 * @return string
 */
function generateCoupon(){
    do {
        $randomBytes = bin2hex(random_bytes(ceil(3)));
        $coupon_code = strtoupper(substr($randomBytes, 0, 6));
        $isUnique = isCouponUnique($coupon_code);

    } while (!$isUnique);

    return $coupon_code;
}

/**
 * Check if coupon is unique
 * 
 * @param string $coupon_code: The coupon code to check
 * @return bool
 */
function isCouponUnique($coupon_code) {
    global $DB;

    return !$DB->record_exists('coupon_pay', array('coupon' => $coupon_code));
}
