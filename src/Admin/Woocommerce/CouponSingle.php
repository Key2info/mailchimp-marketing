<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce;

use ADB\MailchimpMarketing\Admin\Woocommerce\WC_Coupon_Custom;

class CouponSingle
{
    public function __construct()
    {
        add_filter('woocommerce_coupon_data_tabs', [$this, 'add_coupon_custom_tab']);
        add_action('woocommerce_coupon_data_panels', [$this, 'add_coupon_custom_tab_fields']);
        add_action('admin_head', [$this, 'custom_coupon_tab_icon']);
    }

    public  function add_coupon_custom_tab($tabs)
    {
        $tabs['mailchimp_marketing'] = array(
            'label' => __('Marketing Mailchimp', 'adb-mailchimp-marketing'),
            'target' => 'custom_tab_data',
            'class' => '',
            'icon' => 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMTQgMTQiIGZpbGw9IiNmZmYiIHdpZHRoPSIxNCIgaGVpZ2h0PSIxNCIgdmlld0JveD0iMCAwIDE0IDE0IiB4bWw6c3BhY2U9InByZXNlcnZlIj48cGF0aCBkPSJNMCAwdjE0aDE0VjB6Ii8+PC9zdmc+',
        );
        return $tabs;
    }

    function custom_coupon_tab_icon()
    {
        $image_url = plugin_dir_url(MMA_PATH_RELATIVE) . 'mailchimp-marketing/public/img/mailchimp-marketing.svg';
?>
        <style>
            #woocommerce-coupon-data ul.wc-tabs .mailchimp_marketing_tab a::before {
                content: url(<?php echo $image_url ?>) !important;
                margin-right: -3px;
            }
        </style>
    <?php
    }

    public function add_coupon_custom_tab_fields()
    {
        global $post;
        $coupon = new WC_Coupon_Custom($post->ID);
    ?>
        <div id="custom_tab_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <div class="intro" style="padding: 5px 20px 5px 2px!important;">
                    <p><?php echo __('This data comes from mailchimp', 'adb-mailchimp-marketing') ?></p>
                </div>
                <?php
                woocommerce_wp_text_input(array(
                    'id' => 'custom_email',
                    'label' => __('Email', 'adb-mailchimp-marketing'),
                    'value' => $coupon->get_meta('email'),
                    'custom_attributes' => array('readonly' => 'readonly'),
                ));
                woocommerce_wp_text_input(array(
                    'id' => 'custom_creational_type',
                    'label' => __('Creational Type', 'adb-mailchimp-marketing'),
                    'value' => $coupon->get_meta('creational_type'),
                    'custom_attributes' => array('readonly' => 'readonly'),
                ));
                ?>
                <div class="extras" style="padding: 5px 20px 5px 162px!important;">
                    <?php foreach ($coupon->get_extras('extras') as $extra) : ?>
                        <span><?php echo $extra ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    }
}
