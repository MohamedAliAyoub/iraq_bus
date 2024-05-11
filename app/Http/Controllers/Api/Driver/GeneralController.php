<?php

namespace App\Http\Controllers\Api\Driver;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Counter;
use App\Models\FleetType;
use App\Models\VehicleRoute;
use App\Http\Resources\Api\Driver\BannerResource;




class GeneralController extends Controller
{
    public function __construct()
    {
      $this->middleware('check.user:3')->except(['countries','fleetTypes','routes']);
    }

    /**
     *
     * All Countries
     * @return JsonResponse
     */
    public function countries( ): JsonResponse
    {
      $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
      return response()->json(['status' => 'success','data'=> $countries ,'message'=>trans('messages.data_found')])->setStatusCode(200);

    }

     /**
     *
     * All Banners
     * @return JsonResponse
     */
    public function banners( ): JsonResponse
    {
      $banners = Banner::orderby('id','desc')->paginate(getPaginate());
      return response()->json(['status' => 'success','data'=> BannerResource::collection($banners)->response()->getData() ,'message'=>''])->setStatusCode(200);
    }


    /**
     *
     * All fleet Types
     * @return JsonResponse
     */
    public function fleetTypes(): JsonResponse
    {
      $fleetType =FleetType::active()->select(['id','name'])->get();
      return response()->json(['status' => 'success','data'=> $fleetType ,'message'=>''])->setStatusCode(200);
    }

      /**
     *
     * All routes
     * @return JsonResponse
     */
    public function routes(): JsonResponse
    {
      $routes = VehicleRoute::active()->select(['id','name'])->get();
      return response()->json(['status' => 'success','data'=> $routes ,'message'=>''])->setStatusCode(200);
    }

    /**
     *
     * All routes
     * @return JsonResponse
     */
    public function schedules(): JsonResponse
    {
        $schedules = Schedule::active()->select(['id','name'])->get();
        return response()->json(['status' => 'success','data'=> $schedules ,'message'=>''])->setStatusCode(200);
    }

}

