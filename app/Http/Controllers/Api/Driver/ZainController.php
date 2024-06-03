<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Services\ZainCashService;
use Illuminate\Http\Request;

class ZainController extends Controller
{
    protected $zainCashService;

    public function __construct(ZainCashService $zainCashService)
    {
        $this->zainCashService = $zainCashService;
    }

    public function createTransaction(Request $request)
    {
        $amount = $request->input('amount');
        $serviceType = $request->input('serviceType');
        $orderId = $request->input('orderId');

        $transaction = $this->zainCashService->createTransaction($amount, $serviceType, $orderId);

        $redirectUrl = 'https://test.zaincash.iq/transaction/pay?id=' . $transaction['id'];

        return response()->json([
            'transaction_id' => $transaction['id'],
            'payment_url' => env('ZAINCASH_API_URL') . '/transaction/pay?id=' . $transaction['id'],
        ]);
    }

    public function handleRedirect(Request $request)
    {

        $token = $request->query('token');
        $result = $this->zainCashService->handleRedirect($token);

        return response()->json($result);
    }

    public function checkTransactionStatus($transactionId)
    {
        $status = $this->zainCashService->checkTransaction($transactionId);

        return response()->json($status);
    }
}