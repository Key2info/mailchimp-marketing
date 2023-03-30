<?php

namespace ADB\MailchimpMarketing\Admin;

use ADB\MailchimpMarketing\Admin\Woocommerce\CouponGenerator;
use Exception;

class Endpoint
{
    public function __construct()
    {
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
        $this->validateRequest();

        $parameters = json_decode($request->get_body());

        (new CouponGenerator)
            ->setAmount($parameters->amount)
            ->setDiscountType($parameters->discount_type)
            ->setIndividualUse($parameters->individual_use)
            ->setUsageLimit($parameters->usage_limit)
            ->setDaysTillExpiration($parameters->days_till_expiration)
            ->setEmail($parameters->email)
            ->setCreationalType($parameters->creational_type)
            ->setExtras($parameters->extras)
            ->generateCoupon();
    }

    private function validateRequest()
    {
        $requestHeaders = apache_request_headers();
        $passedAuthToken = $requestHeaders['Authorization'];
        $correctAuthToken = base64_encode('newsletter:bouncy');

        if (!$passedAuthToken == "Basic {$correctAuthToken}") {
            throw new Exception("Invalid login credentials provided");
        }

        return true;
    }
}
