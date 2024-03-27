<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Counter;
use App\Models\VehicleRoute;
use App\Models\TicketPrice;
use App\Http\Resources\Api\Client\BannerResource;
use App\Http\Resources\Api\Client\FleetTypeResource;
use App\Http\Resources\Api\Client\BookingLocationResource;
use App\Http\Requests\Api\Client\General\FleetTypeRequest;



class GeneralController extends Controller
{
    public function __construct()
    {
      $this->middleware('check.user:1,2')->except(['countries']);
    }

    /**
     *
     * All Countries
     * @return void
     */
    public function countries( )
    {
      $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
      return response()->json(['status' => 'success','data'=> $countries ,'message'=>trans('messages.data_found')])->setStatusCode(200);

    }
    
     /**
     *
     * All Banners
     * @return void
     */
    public function banners( )
    {
      $banners = Banner::orderby('id','desc')->paginate(getPaginate());
      return response()->json(['status' => 'success','data'=> BannerResource::collection($banners)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }
    
    
    /**
     *
     * All booking locations
     * @return void
     */
    public function bookingLocations( )
    {
      $bookingLocations = Counter::paginate(getPaginate());
      return response()->json(['status' => 'success','data'=> BookingLocationResource::collection($bookingLocations)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }
    
    
        /**
     *
     * All fleet Types
     * @return void
     */
    public function fleetTypes(FleetTypeRequest $request)
    {
      $vehicleRoute = VehicleRoute::where(['start_from'=>$request->pickup,'end_to'=>$request->destination])->first();
      $ticketPrice = TicketPrice::where('vehicle_route_id',$vehicleRoute->id)
               ->with('fleetType')->paginate(getPaginate());

      $ticketPrice->getCollection()->transform(function ($item) use ($request) {
                if ($request->type == 'back') {
                    $item->price *= 2; 
                }
                return $item;
            });
      return response()->json(['status' => 'success','data'=> FleetTypeResource::collection($ticketPrice)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }
   
}
