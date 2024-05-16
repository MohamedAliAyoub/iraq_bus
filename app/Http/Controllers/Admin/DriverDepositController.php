<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DriverDepositRequest;
use App\Models\AgentDeposit;
use App\Models\DriverDeposit;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverDepositController extends Controller
{
    protected $depoRelations = ['user', 'gateway', 'driver'];




    public function create()
    {
        $pageTitle = __('create_new_deposit');
        $geteways = Gateway::where('status', 1)->whereNotNull('input_form')->get();
        $drivers = User::where('status', 1)->where('type', 3)->get();
        return view('admin.driver_deposit.create', compact('pageTitle','drivers', 'geteways'));
    }


    public function store(DriverDepositRequest $request)
    {

        $image = $request->image;
        $image_path = null;
        if ($request->hasFile('image')) {
            try {
                $image_path = uploadImage($image, imagePath()['driver']['path']);

            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $agent_deposit = DriverDeposit::query()->create([
            "user_id" => auth()->guard('admin')->id(),
            "driver_id" => $request->driver_id,
            "gateway_id" => $request->gateway_id,
            "amount" => $request->amount,
            "voucher_number" => $request->voucher_number,
            "image" => $image_path,
            "mobile" => $request->mobile,
            "status" => 2,
            "trx" => getTrx(10),
            "method_code" => 1000,
            "method_currency" => "IQD"
        ]);

        return redirect()->route('admin.driver_deposit.pending')->with('success', 'Driver deposit created successfully');
    }

    public function pending()
    {
        $pageTitle = 'Pending Payment';
        $emptyMessage = 'There is no pending payment';
        $deposits = DriverDeposit::pending()->paginate(getPaginate());
        return view('admin.driver_deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }

    public function successful()
    {
        $pageTitle = 'Successful Payment';
        $emptyMessage = 'There is no successful payment';
        $deposits = DriverDeposit::Successful()->with($this->depoRelations)->paginate(getPaginate());
        return view('admin.driver_deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Payment';
        $emptyMessage = 'There is no rejected payment';
        $deposits = DriverDeposit::rejected()->with($this->depoRelations)->paginate(getPaginate());
        return view('admin.driver_deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }

    public function all()
    {
        $pageTitle = 'All Payment';
        $emptyMessage = 'There is no payment';
        $deposits = DriverDeposit::where('status', '!=', 0)->with($this->depoRelations)->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }

    public function depositViaMethod($method, $type = null)
    {
        $method = Gateway::where('alias', $method)->firstOrFail();
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via ' . $method->name;
            $deposits = DriverDeposit::where('method_code', '>=', 1000)->where('method_code', $method->code)->where('status', 1)->orderBy('id', 'desc')->with($this->depoRelations);
        } elseif ($type == 'rejected') {
            $pageTitle = 'Rejected Payment Via ' . $method->name;
            $deposits = DriverDeposit::where('method_code', '>=', 1000)->where('method_code', $method->code)->where('status', 3)->orderBy('id', 'desc')->with($this->depoRelations);

        } elseif ($type == 'successful') {
            $pageTitle = 'Successful Payment Via ' . $method->name;
            $deposits = DriverDeposit::where('status', 1)->where('method_code', $method->code)->orderBy('id', 'desc')->with($this->depoRelations);
        } elseif ($type == 'pending') {
            $pageTitle = 'Pending Payment Via ' . $method->name;
            $deposits = DriverDeposit::where('method_code', '>=', 1000)->where('method_code', $method->code)->where('status', 2)->orderBy('id', 'desc')->with($this->depoRelations);
        } else {
            $pageTitle = 'Payment Via ' . $method->name;
            $deposits = DriverDeposit::where('status', '!=', 0)->where('method_code', $method->code)->orderBy('id', 'desc')->with($this->depoRelations);
        }
        $deposits = $deposits->paginate(getPaginate());
        $successful = $deposits->where('status', 1)->sum('amount');
        $pending = $deposits->where('status', 2)->sum('amount');
        $rejected = $deposits->where('status', 3)->sum('amount');
        $methodAlias = $method->alias;
        $emptyMessage = 'No Payments Found';
        return view('admin.driver_deposit.log', compact('pageTitle', 'emptyMessage', 'deposits', 'methodAlias', 'successful', 'pending', 'rejected'));
    }

    public function dateSearch(Request $request, $scope = null)
    {
        $search = $request->date;
        if (!$search) {
            return back();
        }
        $date = explode('-', $search);
        $start = @$date[0];
        $end = @$date[1];
        // date validation
        $pattern = "/\d{2}\/\d{2}\/\d{4}/";
        if ($start && !preg_match($pattern, $start)) {
            $notify[] = ['error', 'Invalid date format'];
            return redirect()->route('admin.driver_deposit.list')->withNotify($notify);
        }
        if ($end && !preg_match($pattern, $end)) {
            $notify[] = ['error', 'Invalid date format'];
            return redirect()->route('admin.driver_deposit.list')->withNotify($notify);
        }


        if ($start) {
            $deposits = DriverDeposit::where('status', '!=', 0)->whereDate('created_at', Carbon::parse($start));
        }
        if ($end) {
            $deposits = DriverDeposit::where('status', '!=', 0)->whereDate('created_at', '>=', Carbon::parse($start))->whereDate('created_at', '<=', Carbon::parse($end));
        }
        if ($request->input("method")) {
            $method = Gateway::where('alias', $request->input("method"))->firstOrFail();
            $deposits = $deposits->where('method_code', $method->code);
        }
        if ($scope == 'pending') {
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
        } elseif ($scope == 'approved') {
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
        } elseif ($scope == 'rejected') {
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
        }
        $deposits = $deposits->with($this->depoRelations)->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = ' Payments Log';
        $emptyMessage = 'No Payments Found';
        $dateSearch = $search;
        return view('admin.driver_deposit.log', compact('pageTitle', 'emptyMessage', 'deposits', 'dateSearch', 'scope'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $emptyMessage = 'No search result was found.';
        if ($search)
        $deposits = DriverDeposit::with($this->depoRelations)->where('status', '!=', 0)->where(function ($q) use ($search) {
            $q->where('trx', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%");
            });
        });
        if ($scope == 'pending') {
            $pageTitle = trans('Pending Payments Search');
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
        } elseif ($scope == 'approved') {
            $pageTitle = trans('Approved Payments Search');
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
        } elseif ($scope == 'rejected') {
            $pageTitle = trans('Rejected Payments Search');
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
        } else {
            $pageTitle = 'Payments History Search';
        }

        $deposits = $deposits->paginate(getPaginate());
        $pageTitle .= '-' . $search;

        return view('admin.driver_deposit.log', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'deposits'));
    }

    public function details($id)
    {
        $general = GeneralSetting::first();
        $deposit = DriverDeposit::where('id', $id)->with($this->depoRelations)->firstOrFail();
        $pageTitle = $deposit->user->username . ' requested ' . showAmount($deposit->amount) . ' ' . $general->cur_text;
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;

        return view('admin.driver_deposit.detail', compact('pageTitle', 'deposit', 'details'));
    }

}
