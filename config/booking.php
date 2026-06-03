<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Direct card payment (DPO / gateway)
    |--------------------------------------------------------------------------
    |
    | When false, "Book and Pay Now" shows a coming-soon notice on the booking
    | form and guests must use WhatsApp or other channels.
    |
    */
    'direct_pay_enabled' => (bool) env('BOOKING_DIRECT_PAY_ENABLED', false),

];
