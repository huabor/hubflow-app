<?php

return [
    /** Settings applied to every coupon. Can be overridden per coupon. */
    'defaults' => [
        /**
         * The class responsible for validating and applying the coupon discount.
         * Must extend \Cashier\Discount\BaseCouponHandler
         */
        //'handler' => '\SomeHandler',

        /**
         * The number of times this coupon will be applied. I.e. If you'd like to prove 6 months discount on a
         * monthly subscription:
         *
         * @example 6
         */
        'times' => 1,

        /** Any context you want to pass to the handler */
        'context' => [],
    ],

    /** Available coupons */
    'coupons' => [],
];
