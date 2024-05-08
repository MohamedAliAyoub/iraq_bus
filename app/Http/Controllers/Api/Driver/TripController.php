<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Requests\Api\Driver\Trip\EidtDriverTripRequest;
use App\Http\Resources\Api\Driver\DriverHistoryResource;
use App\Http\Resources\Api\Driver\DriverTripsDatesResource;
use App\Http\Resources\Api\Driver\EditDriverTripResource;
use App\Models\DriverFinancial;
use App\Models\DriverMoney;
use App\Models\DriverTrips;
use App\Models\EditTripHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\TripResource;
use Carbon\Carbon;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;


class TripController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.user:3');
    }

    /**
     *
     * All Trips
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrips(Request $request)
    {
        $user = auth()->user();

        $query = DriverTrips::query()->filterByDate($request->date)
            ->with('trip')
            ->where('driver_id', $user->id)
            ->where('date', '<=', Carbon::today());

        if ($request->has('bookedTickets')) {
            $trips = $query->with(['trip' => function ($q) {
                $q->with(['bookedTickets', 'bookedTickets.user']);
            }])->paginate(getPaginate());
        } else {
            $trips = $query->paginate(getPaginate());
        }
        return response()->json(['status' => 'success', 'data' => TripResource::collection($trips)->response()->getData(), 'message' => ''])->setStatusCode(200);
    }

    /**
     *
     * All Driver Trips Dates
     * @return \Illuminate\Http\JsonResponse
     */
    public function dates()
    {
        $query = DriverTrips::query()->with('trip')->where('driver_id', auth()->id())->groupBy('date')->paginate(getPaginate());
        return response()->json(['status' => 'success', 'data' => DriverTripsDatesResource::collection($query)->response()->getData(), 'message' => ''])->setStatusCode(200);
    }

    /**
     *
     *  Driver Trips that have status accept and have price not null
     * @return JsonResponse
     */
    public function history(): \Illuminate\Http\JsonResponse
    {

        $query = DriverTrips::query()
            ->with('trip')
            ->where(
                [
                    ['driver_id', auth()->id()],
                    ['status', 3], // success
                ])
            ->whereNotNull('price')
            ->paginate(getPaginate());
        return response()->json(
            [
                'status' => 'success',
                'data' => DriverHistoryResource::collection($query)->response()->getData(),
                'message' => ''
            ])->setStatusCode(200);
    }

    /**
     *
     * All Trips
     * @return JsonResponse
     */
    public function getAllTrips()
    {
        $user = auth()->user();
        $trips = Trip::where(['fleet_type_id' => $user->fleet_type_id,
            'vehicle_route_id' => $user->route_id, 'status' => 1])
            ->with(['bookedTickets', 'bookedTickets.user'])->paginate(getPaginate());
        return response()->json(['status' => 'success', 'data' => TripResource::collection($trips)->response()->getData(), 'message' => ''])->setStatusCode(200);
    }

    public function show(Trip $trip)
    {
        $trips = DriverTrips::with(['trip' => function ($q) {
            $q->with(['bookedTickets', 'bookedTickets.user']);
        }])->paginate(getPaginate());
        return response()->json(['status' => 'success', 'data' => TripResource::collection($trips)->response()->getData(), 'message' => ''])->setStatusCode(200);
    }

    private function getDriverPrice($trip): float
    {
        $totalPrice = $trip->trip->bookedTickets
            ->filter(function ($q) use ($trip) {
                $start_from = intval($q->source_destination[0]);
                $end_to = intval($q->source_destination[1]);
                return $q->date_of_journey == $trip->date
                    && $q->trip->start_from == $start_from
                    && $q->trip->end_to == $end_to;
            })
            ->sum('sub_total');
        // Calculate 90% of the total price
        $driverPrice = $totalPrice * 0.9;

        return $driverPrice;
    }


    public function getDriverFinance()
    {
        $item = DriverFinancial::query()->where(['driver_id' => auth()->id()])->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'suspended_balance' => $item->suspended_balance ?? 0,
                'current_balance' => $item->current_balance ?? 0
            ],
            'message' => __('success')
        ])->setStatusCode(200);
    }

    public function updateStatus(DriverTrips $driverTrip, Request $request)
    {
        $trip = DriverTrips::findOrFail($request->id);

        // Update the status
        $trip->update(['status' => $request->status]);

        // Check if the status is changed to 3 and the price is null
        if ($trip->status == 3 && $trip->price == null) {
            $trip->update(['price' => $this->getDriverPrice($trip)]);
            DriverFinancial::query()->updateOrCreate([
                'driver_id' => auth()->id(),
            ], ['suspended_balance' => DB::raw('suspended_balance + ' . $trip->price)]);
            DriverMoney::query()->create([
                'driver_id' => auth()->id(),
                'driver_trip_id' => $trip->id,
                'price' => $trip->price,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => __('status_changed_successfully')
        ])->setStatusCode(200);
    }

    /**
     *
     * All start Trip
     * @return JsonResponse
     */
    public function startTrip(Request $request)
    {
        DriverTrips::query()->findOrFail($request->id)->update(['status' => 4]);
        TODO: //change the status of trip then send notifacations , sms , whatsapp to users
        return response()->json(['status' => 'success', 'data' => [], 'message' => __('status_changed_successfully')])->setStatusCode(200);

    }

    /**
     *
     * All transfer Trip
     * @return JsonResponse
     */
    public function transferTrip(Request $request)
    {
        $currentTime = time();
        $trip = DriverTrips::query()->findOrFail(intval($request->id));

        if (!$trip) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Trip not found'])->setStatusCode(404);
        }
        $tripDateTime = strtotime($trip->date . ' ' . $trip->trip->schedule->start_from);
        $timeDifference = $tripDateTime - $currentTime;


        $hoursDifference = floor($timeDifference / 3600);


        if ($hoursDifference < 12) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Cannot transfer trip as less than 12 hours remaining'])->setStatusCode(400);
        }
        TODO: //send notifiaction to dashboard
        $trip->update(['status' => 5, 'driver_id' => null]);
        return response()->json(['status' => 'success', 'data' => [], 'message' => __('status_changed_successfully')])->setStatusCode(200);
    }


    /**
     * Retrieve the nearest transfer trip and calculate the remaining time until its deadline.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deadLine(Request $request)
    {
        $currentTime = time();
        $todayDate = date('Y-m-d');

        $trip = DriverTrips::where('driver_id', auth()->id())
            ->where('date', '>=', $todayDate)
            ->join('trips', 'driver_trips.trip_id', '=', 'trips.id')
            ->join('schedules', 'trips.schedule_id', '=', 'schedules.id')
            ->with(['trip', 'trip.schedule'])
            ->when($todayDate != date('Y-m-d'), function ($query) use ($currentTime) {
                $query->whereHas('trip.schedule', function ($query) use ($currentTime) {
                    $query->where('start_from', '>=', $currentTime);
                });
            })
            ->orderBy(DB::raw("TIMEDIFF(schedules.start_from, '$currentTime')"), 'asc')
            ->first();

        if (!$trip) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Trip not found'])->setStatusCode(404);
        }
        $tripDateTime = strtotime($trip->date . ' ' . $trip->trip->schedule->start_from);
        $timeDifference = abs($currentTime - $tripDateTime);

        $hoursDifference = floor($timeDifference / 3600);
        $minutesDifference = floor(($timeDifference % 3600) / 60);

        return response()->json([
            'status' => 'success',
            'data' => [
                'hours' => $hoursDifference,
                'minutes' => $minutesDifference
            ],
            'message' => __('dead_line')])
            ->setStatusCode(200);
    }


    /**
     * Eit driver trip schedule or day_off or route.
     *
     * @param EidtDriverTripRequest $request
     * @return JsonResponse
     */
    public function editDriverTripHistory(EidtDriverTripRequest $request): JsonResponse
    {
        $item = EditTripHistory::query()->create([
            "driver_id" => $request->driver_id,
            "route_id" => $request->route_id,
            "schedule_id" => $request->schedule_id,
            "day_off" => $request->day_off,
        ]);
        $createdItem = EditTripHistory::findOrFail($item->id);


        return response()->json([
            'status' => 'success',
            'data' => EditDriverTripResource::make($createdItem),
            'message' => __('success')])
            ->setStatusCode(200);
    }

}

