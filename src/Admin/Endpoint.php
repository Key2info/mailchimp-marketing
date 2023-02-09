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

    public function handleCouponGenerationRequest()
    {
        $this->validateRequest();

        (new CouponGenerator)
            ->setAmount(10)
            ->setDiscountType('percent')
            ->setIndividualUse(true)
            ->setUsageLimit(1)
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
