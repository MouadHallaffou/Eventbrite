<?php
// app/controllers/PaymentController.php

namespace App\controllers;

use App\core\Controller;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController extends Controller {
    private $client;

    public function __construct() {
        $config = require __DIR__ . "/../../config/PayPalConfig.php";
        $environment = new SandboxEnvironment($config['client_id'], $config['client_secret']);
        $this->client = new PayPalHttpClient($environment);
    }

    public function showPaymentPage() {
        // Afficher la page de paiement
        $this->render('Payment.twig');
    }

    public function createOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        $cart = $data['cart'];

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => "100.00"
                ]
            ]]
        ];

        try {
            $response = $this->client->execute($request);
            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function captureOrder($orderID) {
        $request = new OrdersCaptureRequest($orderID);
        $request->prefer('return=representation');

        try {
            $response = $this->client->execute($request);
            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}