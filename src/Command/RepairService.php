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
        \WP_CLI::success('Doing action');
        $client = Plugin::$mailchimpTransactionalClient;

        /*
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

        if ($results) {
            foreach ($results as $result) {
                $order_id = $result->order_id;
                $order_date = $result->order_date;
                // doe iets met de order_id en order_date
            }
        } else {
            // geen resultaten gevonden
        }
        */

        $response = $client->messages->sendTemplate([
            "template_name" => "testtemplate",
            "template_content" => [
                [
                    "name" => "header",
                    "content" => "<h2>Arne</h2>"
                ],
                [
                    "name" => "main",
                    "content" => "TestTest"
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

        dd($response);

        file_put_contents("reponse.json", json_encode($response));

        echo json_encode($response);
        \WP_CLI::success('Finishing action');
    }
}
