<?php

namespace App\Http\Controllers\Api\Driver;

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

        $query = Trip::whereHas('driverTrips');

        if ($request->has('bookedTickets')) {
            $trips = $query->with(['bookedTickets', 'bookedTickets.user'])->paginate(getPaginate());
        } else {
            $trips = $query->paginate(getPaginate());
        }
      return response()->json(['status' => 'success','data'=> TripResource::collection($trips)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }

    /**
     *
     * All Trips
     * @return TripResource
     */
    public function getAllTrips( )
    {
        $user = auth()->user();
        $trips = Trip::where(['fleet_type_id'=>$user->fleet_type_id ,
            'vehicle_route_id'=>$user->route_id,'status'=>1 ])
            ->with(['bookedTickets','bookedTickets.user'])->paginate(getPaginate());
        return response()->json(['status' => 'success','data'=> TripResource::collection($trips)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }

    public function show(Trip $trip)
    {
        $trips = Trip::with(['bookedTickets','bookedTickets.user'])->paginate(getPaginate());
        return response()->json(['status' => 'success','data'=> TripResource::collection($trips)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }

     /**
     *
     * All start Trip
     * @return void
     */
    public function startTrip( Request $request)
    {
      $user = auth()->user();
      $trip= Trip::where(['fleet_type_id'=>$user->fleet_type_id ,
      'vehicle_route_id'=>$user->vehicle_route_id,'status'=>1 ,'id'=>$request->trip_id])
      ->first();
      TODO: //change the status of trip then send notifacations , sms , whatsapp to users
      return response()->json(['status' => 'success','data'=>[] ,'message'=>'the trip start'])->setStatusCode(200);
    }

         /**
     *
     * All transfer Trip
     * @return void
     */
    public function transferTrip( Request $request)
    {
      $user = auth()->user();
      $now = Carbon::now();
      $trip = Trip::where(['fleet_type_id'=>$user->fleet_type_id ,
      'vehicle_route_id'=>$user->route_id,'status'=>1 ,'id'=>$request->trip_id])
       ->first();
      if (!$trip) {
        return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Trip not found'])->setStatusCode(404);
       }
       $startFrom = Carbon::createFromFormat('H:i:s', $trip->schedule->start_from);
      if ($now->diffInHours($startFrom) < 12) {
          return response()->json(['status' => 'fail', 'data' => null, 'message' => 'Cannot transfer trip as less than 12 hours remaining'])->setStatusCode(400);
      }
      TODO: //send notifiaction to dashboard
      return response()->json(['status' => 'success','data'=>[] ,'message'=>'the trip request to transfer'])->setStatusCode(200);
    }
}

