<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce\Contracts;

interface CouponGeneratorContract
{
    public function generateCoupon(): void;

    public function createCouponCode(): void;
}
