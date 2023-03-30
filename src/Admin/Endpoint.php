<?php

namespace ADB\MailchimpMarketing\Admin;

use ADB\MailchimpMarketing\Admin\Contact;
use ADB\MailchimpMarketing\Admin\Woocommerce\CouponGenerator;
use ADB\MailchimpMarketing\Plugin;
use Exception;
use GuzzleHttp\Exception\ClientException;

class Endpoint
{
    public $logger;

    public $request;

    public function __construct()
    {
        $this->logger = Plugin::getLogger();

        add_action('rest_api_init', function () {
            $this->initEndpoints();
        });
    }

    public function initEndpoints()
    {
        register_rest_route('webhook', '/generate-coupon/', array(
            'methods' => ['POST', 'GET'],
            'callback' => [$this, 'handleCouponGenerationRequest'],
        ));
    }

    public function handleCouponGenerationRequest($request)
    {
        $this->logger->debug("Received a new coupon generation request");

        $this->validateRequest();

        $this->request = $request;

        $parameters = json_decode($this->request->get_body());

        $couponCode = (new CouponGenerator)
            ->setAmount($parameters->amount)
            ->setDiscountType($parameters->discount_type)
            ->setIndividualUse($parameters->individual_use)
            ->setUsageLimit($parameters->usage_limit)
            ->setDaysTillExpiration($parameters->days_till_expiration)
            ->setEmail($parameters->email)
            ->setCreationalType($parameters->creational_type)
            ->setExtras($parameters->extras)
            ->generateCoupon();

        $this->sendCodeToMailchimp($couponCode);

        return 'Coupon created';
    }

    public function sendCodeToMailchimp($couponCode)
    {
        $client = Plugin::$mailchimpClient;

        $this->logger->debug('Request body ' . json_encode($this->request->get_body()));
        $reqBody = json_decode($this->request->get_body(), true);

        $email = isset($reqBody['email']) ?  $reqBody['email'] : 'riane.vancamp@key2info.be';
        $audienceId = '2872c7dd79';
        $subscriber_hash = md5(strtolower(trim($email)));

        try {
            $this->logger->debug("Checking to see if user exists in Mailchimp");
            $check_member = json_encode($client->lists->getListMember($audienceId, $subscriber_hash));

            $this->logger->debug("User found");

            (new Contact)->addCouponCode($email, $audienceId, $subscriber_hash, $couponCode);
        } catch (ClientException $e) {
            $this->logger->debug("Something went wrong, user is not a member or doesnt have a status");
            $this->logger->debug($e->getMessage());
        }
    }

    private function validateRequest()
    {
        $this->logger->debug("Validating request");
        $requestHeaders = apache_request_headers();
        $passedAuthToken = $requestHeaders['Authorization'];
        $correctAuthToken = base64_encode('letters:sincere');

        if (!$passedAuthToken == "Basic {$correctAuthToken}") {
            $this->logger->debug("Invalid request, throwing Exception");
            throw new Exception("Invalid login credentials provided");
        }

        $this->logger->debug("Valid request");
        return true;
    }
}
