<?php

namespace ADB\MailchimpMarketing\Admin\Woocommerce;

class CouponOverview
{
    public function __construct()
    {
        add_filter('manage_edit-shop_coupon_columns', [$this, 'add_mailchimp_marketing_coupon_columns']);
        add_filter('manage_edit-shop_coupon_sortable_columns', [$this, 'make_custom_coupon_column_sortable']);
        // add_filter('posts_clauses', [$this, 'sort_coupons_by_expiration_date'], 10, 2);
        add_action('manage_shop_coupon_posts_custom_column', [$this, 'populate_custom_coupon_column']);
    }

    public function add_mailchimp_marketing_coupon_columns($columns)
    {
        $columns['email'] = __('E-mail', 'adb-mailchimp-marketing');
        $columns['creational_type'] = __('Type', 'adb-mailchimp-marketing');
        $columns['extras'] = __('Extras', 'adb-mailchimp-marketing');

        return $columns;
    }

    public function make_custom_coupon_column_sortable($columns)
    {
        $columns['email'] = 'email';
        $columns['creational_type'] = 'creational_type';
        // $columns['expiry_date'] = 'expiry_date';
        // $columns['usage'] = 'usage';

        return $columns;
    }

    public function sort_coupons_by_expiration_date($clauses, $query)
    {
        global $wpdb;
        if (isset($query->query['orderby']) && 'expiry_date' === $query->query['orderby']) {
            $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS expiration_date_meta ON ( {$wpdb->posts}.ID = expiration_date_meta.post_id AND expiration_date_meta.meta_key = '_expiry_date' )";
            $clauses['orderby'] = 'expiration_date_meta.meta_value ASC';
        }
        return $clauses;
    }

    public function populate_custom_coupon_column($column)
    {
        global $post;
        $coupon = new \ADB\MailchimpMarketing\Admin\Woocommerce\WC_Coupon_Custom($post->ID);

        if ('email' === $column) {
            echo $coupon->get_email();
        }

        if ('creational_type' === $column) {
            echo $coupon->get_creational_type();
        }

        if ('extras' === $column) {
            foreach ($coupon->get_extras() as $extra) {
                echo $extra . '<br />';
            }
        }
    }
}
