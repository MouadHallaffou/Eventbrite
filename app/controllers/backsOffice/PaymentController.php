<?php

namespace App\Controllers;

use App\Core\Controller;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSDKClientBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingOptionBuilder;
use PaypalServerSdkLib\Models\ShippingType;

class PaymentController extends Controller
{
    private $client;

    public function __construct()
    {
        // Charger la configuration PayPal
        $config = require __DIR__ . '/../../config/PayPalConfig.php';

        // Configurer le client PayPal
        $this->client = PaypalServerSDKClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $config['client_id'],
                    $config['client_secret']
                )
            )
            ->environment(Environment::SANDBOX)
            ->build();
    }

    public function createOrder()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $cart = $data["cart"];

        $orderBody = [
            "body" => OrderRequestBuilder::init("CAPTURE", [
                PurchaseUnitRequestBuilder::init(
                    AmountWithBreakdownBuilder::init("USD", "100")->build()
                )->build(),
            ])->build(),
        ];

        $apiResponse = $this->client->getOrdersController()->ordersCreate($orderBody);

        $jsonResponse = json_decode($apiResponse->getBody(), true);
        echo json_encode($jsonResponse);
    }

    public function captureOrder($orderID)
    {
        $captureBody = [
            "id" => $orderID,
        ];

        $apiResponse = $this->client->getOrdersController()->ordersCapture($captureBody);

        $jsonResponse = json_decode($apiResponse->getBody(), true);
        echo json_encode($jsonResponse);
    }
    
}