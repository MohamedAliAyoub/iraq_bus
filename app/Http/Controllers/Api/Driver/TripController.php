<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Resources\Api\Driver\DriverTripsDatesResource;
use App\Models\DriverTrips;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\TripResource;
use Carbon\Carbon;
use App\Models\Trip;


class TripController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.user:3');
    }

    /**
     *
     * All Trips
     * @return TripResource
     */
    public function getTrips(Request $request)
    {
        $user = auth()->user();

        $query = DriverTrips::query()->with('trip')->where('driver_id', $user->id);

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
     * @return DriverTripsDatesResource
     */
    public function dates()
    {
        $query = DriverTrips::query()->with('trip')->where('driver_id', auth()->id())->paginate(getPaginate());
        return response()->json(['status' => 'success', 'data' => DriverTripsDatesResource::collection($query)->response()->getData(), 'message' => ''])->setStatusCode(200);
    }

    /**
     *
     * All Trips
     * @return TripResource
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

    public function updateStatus(DriverTrips $driverTrip, Request $request)
    {
        DriverTrips::query()->findOrFail($request->id)->update(['status' => $request->status]);
        return response()->json(['status' => 'success', 'data' => [], 'message' => __('status_changed_successfully')])->setStatusCode(200);

    }

    /**
     *
     * All start Trip
     * @return void
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
     * @return void
     */
    public function transferTrip(Request $request)
    {
        $currentTime = time();
        $trip = DriverTrips::query()->findOrFail($request->id)->with('trip')->first();
        if (!$trip) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Trip not found'])->setStatusCode(404);
        }
        $tripDateTime = strtotime($trip->date . ' ' . $trip->trip->schedule->start_from);
        $timeDifference = abs($currentTime - $tripDateTime);


        $hoursDifference = floor($timeDifference / 3600);

        if ($hoursDifference < 12) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Cannot transfer trip as less than 12 hours remaining'])->setStatusCode(400);
        }
        TODO: //send notifiaction to dashboard
        $trip->update(['status' => 2]);
        return response()->json(['status' => 'success', 'data' => [], 'message' => __('status_changed_successfully')])->setStatusCode(200);
    }
}

