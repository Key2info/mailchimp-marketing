<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce;

/* This class overrides the standaard Woocommerce WC_Coupon class adding extra functionaltiy */

class WC_Coupon_Custom extends \WC_Coupon
{
    public function set_email($email)
    {
        $this->add_meta_data('email', $email);
        $this->save();
    }

    public function get_email()
    {
        return $this->get_meta('email');
    }

    public function set_creational_type($email)
    {
        $this->add_meta_data('creational_type', $email);
        $this->save();
    }

    public function get_creational_type()
    {
        return $this->get_meta('creational_type');
    }

    public function set_extras($extras)
    {
        $this->add_meta_data('extras', serialize($extras));
        $this->save();
    }

    public function get_extras()
    {
        return unserialize($this->get_meta('extras'));
    }
}
