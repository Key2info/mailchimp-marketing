<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce;

use ADB\MailchimpMarketing\Admin\Woocommerce\Contracts\CouponGeneratorContract;
use ADB\MailchimpMarketing\Admin\Woocommerce\WC_Coupon_Custom;
use Exception;

class CouponGenerator implements CouponGeneratorContract
{
    public $code;

    public $amount;

    public $discountType;

    public $individualUse;

    public $usageLimit;

    public $daysTillExpiration;

    public $email;

    public $creationalType;

    public $extras;

    public function generateCoupon(): void
    {
        $this->code = self::_random(16);

        $this->createCouponCode();
    }

    public function createCouponCode(): void
    {
        try {
            $coupon = new WC_Coupon_Custom();
            $coupon->set_code($this->code);
            $coupon->set_amount($this->amount);
            $coupon->set_discount_type($this->discountType);
            $coupon->set_individual_use($this->individualUse);
            $coupon->set_usage_limit($this->usageLimit);
            $coupon->set_date_expires($this->daysTillExpiration);
            $coupon->set_email($this->email);
            $coupon->set_creational_type($this->creationalType);
            $coupon->set_extras($this->extras);
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

    /**
     * Get the value of dates till expiration
     */
    public function getDaysTillExpiration()
    {
        return $this->daysTillExpiration;
    }

    /**
     * Set the value of dates till expiration
     *
     * @return  self
     */
    public function setDaysTillExpiration($daysTillExpiration)
    {
        $this->daysTillExpiration = strtotime("+" . $daysTillExpiration . " day");

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of usageLimit
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of creationalType
     */
    public function getCreationalType()
    {
        return $this->creationalType;
    }

    /**
     * Set the value of creationalType
     *
     * @return  self
     */
    public function setCreationalType($creationalType)
    {
        $this->creationalType = $creationalType;

        return $this;
    }

    /**
     * Get the value of extras
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set the value of creationalType
     *
     * @return  self
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;

        return $this;
    }
}
