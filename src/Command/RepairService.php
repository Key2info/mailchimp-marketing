<?php

namespace ADB\MailchimpMarketing\Command;

use ADB\MailchimpMarketing\Command\MailchimpCommand;
use ADB\MailchimpMarketing\Plugin;

class RepairService extends MailchimpCommand
{
    public const COMMAND_NAME = 'mma repair';

    /**
     * Get multiple campaigns from Mailchimp API.
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     *     wp mma repair sendMail
     *
     * @when after_wp_load
     */
    public function sendMail(array $args = [])
    {
        \WP_CLI::success('Preparingg email action');
        $client = Plugin::$mailchimpTransactionalClient;

        global $wpdb;

        $results = $wpdb->get_results("
            SELECT *
            FROM {$wpdb->prefix}posts p
            INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON p.ID = oi.order_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
            WHERE p.post_type = 'shop_order'
            AND oim.meta_key = 'pa_optie'
            AND oim.meta_value = 'basic-deal'
        ");

        $firstResult = $results[0];

        $order = wc_get_order($firstResult->order_id);

        $content = '<div class="mcnTextContent" style="min-width: 100%; padding: 18px;">';
        $content .= 'Toestel: ' . $firstResult->order_item_name . '<br />';
        $content .= 'Aangekocht op: ' . $firstResult->post_date;
        $content .= '</div>';

        $response = $client->messages->sendTemplate([
            "template_name" => "test-onderhoud",
            "template_content" => [
                [
                    "name" => "main",
                    "content" => $content,
                ]
            ],
            "message" => [
                "subject" => 'Test',
                "from_email" => "no-reply@vanrooy.be",
                "to" => [
                    ["email" => "test@vanrooy.be"]
                ]
            ],
        ]);

        echo json_encode($response);
        \WP_CLI::success('Email sent');
    }
}
