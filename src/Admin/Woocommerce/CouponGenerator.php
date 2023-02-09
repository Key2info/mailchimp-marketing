<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce;

use ADB\MailchimpMarketing\Admin\Woocommerce\Contracts\CouponGeneratorContract;
use Exception;

class CouponGenerator implements CouponGeneratorContract
{
    public $code;

    public $amount;

    public $discountType;

    public $individualUse;

    public $usageLimit;

    public function generateCoupon(): void
    {
        $this->code = self::_random(16);

        $this->createCouponCode();
    }

    public function createCouponCode(): void
    {
        try {
            $coupon = new \WC_Coupon();
            $coupon->set_code($this->code);
            $coupon->set_amount($this->amount);
            $coupon->set_discount_type($this->discountType);
            $coupon->set_individual_use($this->individualUse);
            $coupon->set_usage_limit($this->usageLimit);
            $coupon->save();
        } catch (Exception $e) {
            throw new Exception("Something went wrong when creating the coupon: " . $this->code . " Coupon might already exist");
        }
    }

    private static function _random($n): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of discountType
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * Set the value of discountType
     *
     * @return  self
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get the value of individualUse
     */
    public function getIndividualUse()
    {
        return $this->individualUse;
    }

    /**
     * Set the value of individualUse
     *
     * @return  self
     */
    public function setIndividualUse($individualUse)
    {
        $this->individualUse = $individualUse;

        return $this;
    }

    /**
     * Get the value of usageLimit
     */
    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    /**
     * Set the value of usageLimit
     *
     * @return  self
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;

        return $this;
    }
}
