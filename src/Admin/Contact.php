<?php

namespace ADB\MailchimpMarketing\Admin;

use ADB\MailchimpMarketing\Plugin;
use League\Container\Container;

class Contact
{
    public $logger;

    public $client;

    public function __construct()
    {
        $this->client = Plugin::$mailchimpClient;
        $this->logger = Plugin::getLogger();
    }

    public function addCouponCode($email, $audienceId, $subscriber_hash, $couponCode)
    {
        $this->logger->debug("Merging coupon code value to user in Mailchimp.");
        $this->logger->debug("Email: {$email}");
        $this->logger->debug("Audience ID: {$audienceId}");
        $this->logger->debug("Subscriber Hash: {$subscriber_hash}");
        $this->logger->debug("Coupon code: {$couponCode}");

        $response = $this->client->lists->setListMember($audienceId, $subscriber_hash, [
            "email_address" => $email,
            'status'        => 'subscribed',
            'merge_fields' => [
                'COUPON' => $couponCode,
            ]
        ]);
    }
}
