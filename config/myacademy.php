<?php

return [
    'school_name' => env('MYACADEMY_SCHOOL_NAME', env('APP_NAME', 'MyAcademy')),
    'tagline' => env('MYACADEMY_SCHOOL_TAGLINE', "Here's what's happening in your school today."),
    'current_term' => env('MYACADEMY_CURRENT_TERM', 'Term 1'),
    'current_week' => env('MYACADEMY_CURRENT_WEEK', 'Week 1'),
    'currency_symbol' => env('MYACADEMY_CURRENCY_SYMBOL', '₦'),
];
