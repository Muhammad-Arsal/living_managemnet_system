<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portal modules
    |--------------------------------------------------------------------------
    |
    | Shared auth/email configuration for Admin, Staff, and Council panels.
    |
    */

    'admin' => [
        'label' => 'Admin',
        'guard' => 'admin',
        'password_broker' => 'admins',
        'login_route' => 'admin.login',
        'password_reset_route' => 'admin.password.reset',
        'verification_notice_route' => 'admin.verification.notice',
        'templates' => [
            'welcome' => 'admin_welcome_email',
            'password_setup' => 'admin_password_setup_email',
            'password_reset' => 'admin_password_reset_email',
            'email_verification' => 'admin_email_verification',
        ],
    ],

    'staff' => [
        'label' => 'Staff',
        'guard' => 'staff',
        'password_broker' => 'staff',
        'login_route' => 'staff.login',
        'password_reset_route' => 'staff.password.reset',
        'verification_notice_route' => 'staff.verification.notice',
        'templates' => [
            'welcome' => 'staff_welcome_email',
            'password_setup' => 'staff_password_setup_email',
            'password_reset' => 'staff_password_reset_email',
            'email_verification' => 'staff_email_verification',
        ],
    ],

    'council' => [
        'label' => 'Council',
        'guard' => 'council',
        'password_broker' => 'councils',
        'login_route' => 'council.login',
        'password_reset_route' => 'council.password.reset',
        'verification_notice_route' => 'council.verification.notice',
        'templates' => [
            'welcome' => 'council_welcome_email',
            'password_setup' => 'council_password_setup_email',
            'password_reset' => 'council_password_reset_email',
            'email_verification' => 'council_email_verification',
        ],
    ],

];
