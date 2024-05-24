<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\ManualGatewayResource;
use App\Http\Resources\Api\Driver\DriverDepositResource;
use App\Models\AgentDeposit;
use App\Models\BookedTicket;
use App\Models\Deposit;
use App\Models\DriverDeposit;
use App\Models\DriverDetails;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverDepositController extends Controller
{
    protected $depoRelations = ['user', 'gateway', 'driver'];

    public function getAmount()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        return response()->json(['status' => 'success', 'data' => ManualGatewayResource::collection($gatewayCurrency), 'message' => ''])->setStatusCode(200);
    }

    // add favorite driver amount in driver_details
    public function addFavoriteDriverAmount(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric',
            'gateway' => 'required|numeric|exists:gateways,id',
        ]);

        $details = DriverDetails::query()->where('user_id', auth()->id())
            ->update([
            'gateway' => $request->gateway,
            'mobile' => $request->mobile,
        ]);
        $item = DriverDetails::query()->where('user_id', auth()->id())->first();
        $data = [
            'gateway' => $item->gateway,
            'mobile' => $item->mobile,
        ];


        return response()->json(['status' => 'success', 'data' => $data, 'message' => 'successfully added'])->setStatusCode(200);
    }

    public function getFavoriteDriverAmount(Request $request)
    {
        $details = DriverDetails::query()->where('user_id', auth()->id())->first(['gateway', 'mobile']);
       $data = [
            'gateway' => $details->gateway,
            'mobile' => $details->mobile,
        ];
        return response()->json(['status' => 'success', 'data' => $data, 'message' => 'successfully added'])->setStatusCode(200);
    }

    public function index()
    {
        $deposits = DriverDeposit::where('driver_id', auth()->id())->with($this->depoRelations)->latest()->paginate(15);
        return response()->json(['status' => 'success', 'data' => DriverDepositResource::collection($deposits), 'message' => null])->setStatusCode(200);
    }

    public function approve(Request $request)
    {


        $request->validate(['id' => 'required|integer']);

        $deposit = DriverDeposit::where('id', $request->id)->where('status', 2)->firstOrFail();
        $deposit->status = 1;
        $deposit->save();
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.driver_deposit_accept')])->setStatusCode(200);

    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|max:250'
        ]);
        $deposit = DriverDeposit::where('id', $request->id)->where('status', 2)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = 3;
        $deposit->save();


        $general = GeneralSetting::first();
        notify($deposit->user, 'PAYMENT_REJECT', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amo),
            'amount' => showAmount($deposit->amount),
            'charge' => showAmount($deposit->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($deposit->rate),
            'trx' => $deposit->trx,
            'rejection_message' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.driver_deposit_reject')])->setStatusCode(200);


    }
}
