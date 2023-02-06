<?php

namespace ADB\MailchimpMarketing\Command;

use ADB\MailchimpMarketing\Api\CampaignApi;
use ADB\MailchimpMarketing\Command\MailchimpCommand;

class Campaign extends MailchimpCommand
{
    public const COMMAND_NAME = 'mma campaign';

    /**
     * Import campaigns from Mailchimp API.
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
        $campaignApi = new CampaignApi();
        $campaignApi->setLogger($this->logger);

        $campaigns = [];

        foreach ($campaignApi->get() as $campaign) {
            $campaigns[] = $campaign;
        }

        echo $this->configuration->getSerializer()->serialize($campaigns, 'csv');
        \WP_CLI::success('Done processing campaigns.');
    }
}
