<?php

namespace ADB\MailchimpMarketing\PublicFolder;

use ADB\MailchimpMarketing\Plugin;

class Checkout
{
    public function __construct()
    {
        add_action('woocommerce_review_order_before_submit', function () {
            $this->initMarketingCheckbox();
        });

        add_action('woocommerce_checkout_update_order_meta', function ($orderId) {
            $this->saveMarketingCheckbox($orderId);
        }, 10, 1);

        add_action('woocommerce_checkout_order_processed', function ($orderId) {
            $this->subscribeUserToMailchimp($orderId);
        }, 10, 1);
    }

    protected function initMarketingCheckbox(): void
    {
        echo '<div id="mailchimp_marketing_opt_in">';
        woocommerce_form_field('mailchimp_marketing_opt_in', array(
            'type'      => 'checkbox',
            'class'     => array('input-checkbox'),
            'label'     => __('Ik wil mij inschrijven voor marketing.', 'mailchimp-marketing'),
            'required'  => false,
        ),  WC()->checkout->get_value('mailchimp_marketing_opt_in'));
        echo '</div>';
    }

    protected function saveMarketingCheckbox($orderId): void
    {
        if (!empty($_POST['mailchimp_marketing_opt_in'])) {
            update_post_meta($orderId, 'mailchimp_marketing_opt_in', $_POST['mailchimp_marketing_opt_in']);
        }
    }

    protected function subscribeUserToMailchimp($orderId): void
    {
        if (!empty($_POST['mailchimp_marketing_opt_in'])) {
            $client = Plugin::$mailchimpClient;
        }
    }
}
