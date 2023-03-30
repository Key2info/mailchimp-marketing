<?php

namespace ADB\MailchimpMarketing\Admin;

class Order
{
    public $order;

    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
            $this->order = $order;
            $this->displayMailchimpMarketingInfo();
        }, 10, 1);
    }

    protected function displayMailchimpMarketingInfo(): void
    {
        $kennisgeving_levering = get_post_meta($this->order->get_id(), 'kennisgeving_levering', true);

        if ($kennisgeving_levering == 1) {
            echo '<p><strong>Kennisgeving Levering: </strong> <span style="color:red;">Ingeschakeld</span></p>';
        }
    }
}
