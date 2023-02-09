<?php

namespace ADB\MailchimpMarketing\Command;

use ADB\MailchimpMarketing\Command\MailchimpCommand;
use ADB\MailchimpMarketing\Plugin;
use ADB\MailchimpMarketingClient\Api\CampaignApi;

class Campaign extends MailchimpCommand
{
    public const COMMAND_NAME = 'mma campaign';

    /**
     * Get multiple campaigns from Mailchimp API.
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     *     wp mma campaign list
     *
     * @when after_wp_load
     */
    public function list(array $args = [])
    {
        $client = Plugin::$mailchimpClient;

        $campaigns = $client->campaigns->list();

        file_put_contents("campaigns.json", json_encode($campaigns));

        echo json_encode($campaigns);
        \WP_CLI::success('Done processing campaigns.');
    }

    /**
     * Get single campaign from Mailchimp API.
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     *     wp mma campaign get
     *
     * @when after_wp_load
     */
    public function get(array $args = [])
    {
        $client = Plugin::$mailchimpClient;

        $campaign = $client->campaigns->get($args[0]); // args here is campagin ID

        file_put_contents("single-campaign.json", json_encode($campaign));

        echo json_encode($campaign);
        \WP_CLI::success('Done processing single campaign.');
    }
}
