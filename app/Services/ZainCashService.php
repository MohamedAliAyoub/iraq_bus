<?php

namespace App\Services;

use App\Models\Transaction;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZainCashService
{
    protected $secret;
    protected $msisdn;
    protected $merchantId;
    protected $redirectUrl;

    public function __construct()
    {
        $this->secret = env('ZAINCASH_MERCHANT_SECRET');
        $this->msisdn = env('ZAINCASH_MSISDN');
        $this->merchantId = env('ZAINCASH_MERCHANT_ID');
        $this->redirectUrl = env('APP_URL') . 'api/v1/driver/zain/callback';
    }


    public function createTransaction($amount, $serviceType, $orderId)
    {
        $payload = [
            'amount' => $amount,
            'serviceType' => $serviceType,
            'msisdn' => $this->msisdn,
            'orderId' => $orderId,
            'redirectUrl' => $this->redirectUrl,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 4
        ];

        if (!is_string($this->secret)) {
            throw new \InvalidArgumentException('ZAINCASH_SECRET must be a string.');
        }
        $token = JWT::encode($payload, $this->secret, 'HS256');

        $response = Http::asForm()->post('https://test.zaincash.iq/transaction/init', [
            'token' => $token,
            'merchantId' => $this->merchantId,
            'lang' => 'en'
        ]);

        return json_decode($response->body(), true);
    }

    public function checkTransaction($transactionId)
    {
        $payload = [
            'id' => $transactionId,
            'msisdn' => $this->msisdn,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 4
        ];

        $token = JWT::encode($payload, $this->secret, 'HS256');

        $response = Http::asForm()->post('https://test.zaincash.iq/transaction/get', [
            'token' => $token,
            'merchantId' => $this->merchantId
        ]);

        return json_decode($response->body(), true);
    }

    public function handleRedirect($token)
    {

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $result = (array)$decoded;

            if (isset($result['status'])) {
                if ($result['status'] === 'success') {
                    // Handle successful transaction
                    if (isset($result['transaction_id'])) {
                        Transaction::where('id', $result['transaction_id'])->update(['status' => 'success']);
                    } else {
                        Log::error('Transaction ID not found in the decoded token.');
                    }
                } elseif ($result['status'] === 'failed') {
                    // Handle failed transaction
                    $reason = $result['msg'] ?? 'No failure message provided';
                    Log::error('Transaction failed: ' . $reason);
                }
            } else {
                Log::error('Status not found in the decoded token.');
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error decoding token: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Token decoding failed.'];
        }
    }

    public function decodeToken($token)
    {
        $algorithms = ['HS256'];
        return (array)JWT::decode($token, new Key($this->secret, $algorithms));
    }
}
