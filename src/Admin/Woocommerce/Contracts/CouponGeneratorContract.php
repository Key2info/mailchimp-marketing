<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce\Contracts;

interface CouponGeneratorContract
{
    public function generateCoupon();

    public function createCouponCode();
}
