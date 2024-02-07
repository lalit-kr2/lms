<?php

$capabilities = [
    'block/coupon_pay:myaddinstance' => [
        'captype' => 'write',
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ],
    // 'block/coupon_pay:addinstance' => [
    //     'riskbitmask' => RISK_SPAM | RISK_XSS,
    //     'captype' => 'write',
    //     'contextlevel' => CONTEXT_BLOCK,
    //     'archetypes' => [
    //         'manager' => CAP_ALLOW
    //     ],
    //     'clonepermissionsfrom' => 'moodle/site:manageblocks'
    // ],
];