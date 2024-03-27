<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\HistoryResource;


class HistoryController extends Controller
{
    public function __construct()
    {
      $this->middleware('check.user:1,2');
    }
/**
 * get history
 * @return HistoryResource
 */
public function getHistory()
{
    $user = auth()->user();
    $history = $user->history()->orderby('id','desc')->paginate(getPaginate());
    $history->getCollection()->transform(function ($item)  {
      if ($item->bookedTicket->back_date != null) {

          $item->route = $item->bookedTicket->pickup->name .'-'.$item->bookedTicket->drop->name.'-'.$item->bookedTicket->pickup->name; 
      }
      else{
        $item->route = $item->bookedTicket->pickup->name .'-'.$item->bookedTicket->drop->name;
      }
      return $item;
  });
    return response()->json(['status' => 'success','data'=> HistoryResource::collection($history)->response()->getData() ,'message'=>''])->setStatusCode(200);
}



}

