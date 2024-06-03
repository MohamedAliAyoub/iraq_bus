<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ZianCashController extends Controller
{
    public function initTransaction(Request $request)
    {
        $data = [
            'amount' => $request->input('amount'),
            'serviceType' => $request->input('serviceType'),
            'msisdn' => env('ZAINCASH_MSISDN'),
            'orderId' => $request->input('orderId'),
            'redirectUrl' => env('ZAINCASH_REDIRECT_URL'),
            'iat' => time(),
            'exp' => time() + 60 * 60 * 60,
        ];

        $token = JWT::encode($data, env('ZAINCASH_MERCHANT_SECRET'), 'HS256');

        Log::info('Initiating transaction with data:', $data);
        try {
            $response = Http::timeout(600) // Set timeout to 600 seconds
            ->asForm()
                ->post(env('ZAINCASH_API_URL') . '/transaction/init', [
                    'token' => $token,
                    'merchantId' => env('ZAINCASH_MERCHANT_ID'),
                    'lang' => env('ZAINCASH_LANG'),
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Retry the operation
            $response = Http::timeout(600) // Set timeout to 600 seconds
            ->asForm()
                ->post(env('ZAINCASH_API_URL') . '/transaction/init', [
                    'token' => $token,
                    'merchantId' => env('ZAINCASH_MERCHANT_ID'),
                    'lang' => env('ZAINCASH_LANG'),
                ]);
        }

        $responseData = $response->json();

        Log::info('Response from ZainCash:', $responseData);

        if ($response->failed()) {
            Log::error('Failed to initiate transaction', ['response' => $responseData]);
            return response()->json(['error' => 'Failed to initiate transaction'], 500);
        }

        return response()->json([
            'transaction_id' => $responseData['id'],
            'payment_url' => env('ZAINCASH_API_URL') . '/transaction/pay?id=' . $responseData['id'],
        ]);
    }


    public function handleRedirect(Request $request)
    {

        $token = $request->query('token');
        $decoded = JWT::decode($token, new Key(env('ZAINCASH_MERCHANT_SECRET'), 'HS256'));
        $result = (array)$decoded;


        if ($result['status'] == 'success') {
            // Handle successful transaction
            // For example, update a transaction record in your database
            Transaction::where('id', $result['transaction_id'])->update(['status' => 'success']);
        } elseif ($result['status'] == 'failed') {
            // Handle failed transaction
            if (isset($result['transaction_id'])) {
                Log::error('Transaction failed: ' . $result['transaction_id']);
            } else {
                Log::error('Transaction failed: Transaction ID not found in the decoded token.');
            }
        }

        return response()->json($result);
    }

    public function checkStatus(Request $request)
    {
        $data = [
            'id' => $request->input('transaction_id'),
            'msisdn' => env('ZAINCASH_MSISDN'),
            'iat' => time(),
            'exp' => time() + 60 * 60 * 4,
        ];

        $token = JWT::encode($data, env('ZAINCASH_MERCHANT_SECRET'), 'HS256');

        $response = Http::asForm()->post(env('ZAINCASH_API_URL') . '/transaction/get', [
            'token' => $token,
            'merchantId' => env('ZAINCASH_MERCHANT_ID'),
        ]);

        return response()->json($response->json());
    }
}
