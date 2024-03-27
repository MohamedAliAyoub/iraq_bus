<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\FleetType;
use App\Models\VehicleRoute;
use App\Models\TicketPrice;
use App\Models\TicketPriceByStoppage;
use Illuminate\Http\Request;
use App\Models\Seat;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class VehicleTicketController extends Controller
{
    public function booked(){
        $pageTitle = 'Booked Ticket';
        $emptyMessage = 'There is no booked ticket';
        $tickets = BookedTicket::booked()->with(['trip', 'pickup', 'drop', 'user','trip.fleetType','trip.startFrom','trip.endTo'])->paginate(getPaginate());
        return view('admin.ticket.log', compact('pageTitle', 'emptyMessage', 'tickets'));
    }

    public function pending(){
        $pageTitle = 'Pending Ticket';
        $emptyMessage = 'There is no pending ticket';
        $tickets = BookedTicket::pending()->with(['trip', 'pickup', 'drop', 'user','trip.fleetType','trip.startFrom','trip.endTo'])->paginate(getPaginate());
        return view('admin.ticket.log', compact('pageTitle', 'emptyMessage', 'tickets'));
    }

    public function rejected(){
        $pageTitle = 'Rejected Ticket';
        $emptyMessage = 'There is no rejected ticket';
        $tickets = BookedTicket::rejected()->with(['trip', 'pickup', 'drop', 'user','trip.fleetType','trip.startFrom','trip.endTo'])->paginate(getPaginate());
        return view('admin.ticket.log', compact('pageTitle', 'emptyMessage', 'tickets'));
    }

    public function list(){
        $pageTitle = 'All Ticket';
        $emptyMessage = 'There is no ticket found';
        $tickets = BookedTicket::with(['trip', 'pickup', 'drop', 'user','trip.fleetType','trip.startFrom','trip.endTo'])->paginate(getPaginate());
        return view('admin.ticket.log', compact('pageTitle', 'emptyMessage', 'tickets'));
    }

    public function search(Request $request, $scope){
        $search = $request->search;
        $pageTitle = '';
        $emptyMessage = 'No search result was found.';

        $ticket = BookedTicket::where('pnr_number', $search);
        switch ($scope) {
            case 'pending':
                $pageTitle .= 'Pending Ticket Search';
                break;
            case 'booked':
                $pageTitle .= 'Booked Ticket Search';
                break;
            case 'rejected':
                $pageTitle .= 'Rejected Ticket Search';
                break;
            case 'list':
                $pageTitle .= 'Ticket Booking History Search';
                break;
        }
        $tickets = $ticket->with(['trip', 'pickup', 'drop', 'user'])->paginate(getPaginate());
        $pageTitle .= ' - ' . $search;

        return view('admin.ticket.log', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'tickets'));
    }
        public function searchDate(Request $request, $scope){
        $search_date = $request->search_date;
        $pageTitle = '';
        $emptyMessage = 'No search result was found.';
        $formattedDate = Carbon::createFromFormat('m/d/Y', $search_date)->format('Y-m-d');

        $ticket = BookedTicket::whereDate('date_of_journey',$formattedDate);
        switch ($scope) {
            case 'pending':
                $pageTitle .= 'Pending Ticket Search';
                break;
            case 'booked':
                $pageTitle .= 'Booked Ticket Search';
                break;
            case 'rejected':
                $pageTitle .= 'Rejected Ticket Search';
                break;
            case 'list':
                $pageTitle .= 'Ticket Booking History Search';
                break;
        }
        $tickets = $ticket->with(['trip', 'pickup', 'drop', 'user'])->paginate(getPaginate());
        $pageTitle .= ' - ' . $search_date;

        return view('admin.ticket.log', compact('pageTitle', 'search_date', 'scope', 'emptyMessage', 'tickets'));
    }
    public function searchVehicle(Request $request, $scope){
        $search_vehicle = $request->search_vehicle;
        $pageTitle = '';
        $emptyMessage = 'No search result was found.';

        $ticket = BookedTicket::whereHas('trip.fleetType',function($query) use($search_vehicle){
            $query->where('name',$search_vehicle);

        } );
        switch ($scope) {
            case 'pending':
                $pageTitle .= 'Pending Ticket Search';
                break;
            case 'booked':
                $pageTitle .= 'Booked Ticket Search';
                break;
            case 'rejected':
                $pageTitle .= 'Rejected Ticket Search';
                break;
            case 'list':
                $pageTitle .= 'Ticket Booking History Search';
                break;
        }
        $tickets = $ticket->with(['trip', 'pickup', 'drop', 'user'])->paginate(getPaginate());
        $pageTitle .= ' - ' . $search_vehicle;

        return view('admin.ticket.log', compact('pageTitle', 'search_vehicle', 'scope', 'emptyMessage', 'tickets'));
    }
    public function searchTrip(Request $request,$scope = null){
        $search_trip = $request->search_trip;
        $pageTitle = '';
        $emptyMessage = 'No search result was found.';

        if (!$search_trip) {
            return back();
        }
        $trip = explode('-',$search_trip);
        $from = @$trip[0];
        $to = @$trip[1];
        if ($from) {
            $ticket = BookedTicket::whereHas('trip.startFrom',function($query) use($from){
                $query->where('name','like','%'.$from.'%');
            } );     
       }
       if ($to) {
            $ticket = BookedTicket::whereHas('trip.endTo',function($query) use($to){
                $query->where('name','like','%'.$to.'%');
            } );     
       }
        switch ($scope) {
            case 'pending':
                $pageTitle .= 'Pending Ticket Search';
                break;
            case 'booked':
                $pageTitle .= 'Booked Ticket Search';
                break;
            case 'rejected':
                $pageTitle .= 'Rejected Ticket Search';
                break;
            case 'list':
                $pageTitle .= 'Ticket Booking History Search';
                break;
            }
        $tickets = $ticket->with(['trip', 'pickup', 'drop', 'user'])->paginate(getPaginate());
        $pageTitle .= ' - ' . $search_trip;

        return view('admin.ticket.log', compact('pageTitle', 'search_trip', 'scope', 'emptyMessage', 'tickets'));
    }

    public function ticketPriceList(){
        $pageTitle = "All Ticket Price";
        $emptyMessage = "No ticket price found";
        $fleetTypes = FleetType::active()->get();
        $routes = VehicleRoute::active()->get();
        $prices = TicketPrice::with(['fleetType', 'route'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.trip.ticket.price_list', compact('pageTitle', 'emptyMessage', 'prices' ,'fleetTypes', 'routes'));
    }

    public function ticketPriceCreate(){
        $pageTitle = "Add Ticket Price";
        $fleetTypes = FleetType::active()->get();
        $routes = VehicleRoute::active()->get();
        return view('admin.trip.ticket.add_price', compact('pageTitle', 'fleetTypes', 'routes'));
    }

    public function ticketPriceEdit($id){
        $pageTitle = "Update Ticket Price";
        $ticketPrice = TicketPrice::with(['prices', 'route.startFrom' , 'route.endTo','fleetType'])->findOrfail($id);
        $seats = Seat::where('ticket_price_id',$ticketPrice->id)->get();
        $stoppageArr = $ticketPrice->route->stoppages;
        $stoppages = stoppageCombination($stoppageArr, 2);
        return view('admin.trip.ticket.edit_price', compact('pageTitle', 'ticketPrice' ,'seats', 'stoppages'));
    }

    public function getRouteData(Request $request){
        $route      = VehicleRoute::where('id', $request->vehicle_route_id)->where('status', 1)->first();
        $check      = TicketPrice::where('vehicle_route_id', $request->vehicle_route_id)->where('fleet_type_id', $request->fleet_type_id)->first();
        if($check) {
            return response()->json(['error'=> trans('You have added prices for this fleet type on this route')]);
        }
        $stoppages  = array_values($route->stoppages);
        $stoppages  = stoppageCombination($stoppages, 2);
        return view('admin.trip.ticket.route_data', compact('stoppages', 'route'));
    }



    public function ticketPriceStore(Request $request){
        $validation_rule = [
            'fleet_type'    => 'required|integer|gt:0',
            'route'         => 'required|integer|gt:0',
            'seat.*'        => 'required',
            'seat_price.*'  => 'required|numeric',
            'main_price'    => 'required|numeric',
            'price'         => 'sometimes|required|array|min:1',
            'price.*'       => 'sometimes|required|numeric',
        ];
        $messages = [
            'main_price'            => 'Price for Source to Destination',
            'price.*.required'      => 'All Price Fields are Required',
            'price.*.numeric'       => 'All Price Fields Should Be a Number',
        ];

        $validator = Validator::make($request->except('_token'), $validation_rule, $messages);
        $validator->validate();

        $check = TicketPrice::where('fleet_type_id', $request->fleet_type)->where('vehicle_route_id', $request->route)->first();
        if($check){
            $notify[] = ['error', 'Duplicate fleet type and route can\'t be allowed'];
            return back()->withNotify($notify);
        }

        $create = new TicketPrice();
        $create->fleet_type_id = $request->fleet_type;
        $create->vehicle_route_id = $request->route;
        $create->price = $request->main_price;
        $create->save();
        $seatsData = [];

        foreach ($request->input('seat') as $key => $seatLabel) {
            $seatsData[] = [
                'name' => $seatLabel,
                'price' => $request->input('seat_price')[$key],
            ];
        }

       $create->seats()->createMany($seatsData);

       if ($request->has('price')) {


         foreach($request->price as $key=>$val){
                $idArray = explode('-', $key);
                $priceByStoppage = new TicketPriceByStoppage();
                $priceByStoppage->ticket_price_id = $create->id;
                $priceByStoppage->source_destination = $idArray;
                $priceByStoppage->price = $val;
                $priceByStoppage->save();
            }
        }
        $notify[] = ['success', 'Ticket price added successfully'];
        return back()->withNotify($notify);
    }

    public function ticketPriceUpdate(Request $request, $id)
    {
        $request->validate([
              'price'         => 'required|numeric',
            'seat.*'        => 'required',
            'seat_price.*'  => 'required|numeric',

        ]);

        $ticketPrice = TicketPrice::findOrFail($id);
        foreach ($request->input('seat') as $key => $seatLabel) {
            $ticketPrice->seats()->where('name',$seatLabel)
                        ->update(['price' => $request->input('seat_price')[$key]]);
        }

        $ticketPrice->update(['price'=>$request->main_price]);


        if($id == 0){
            $source_destination[0] = $request->source;
            $source_destination[1] = $request->destination;
            $ticketPrice = TicketPriceByStoppage::whereJsonContains('source_destination' , $source_destination)->first();
            if($ticketPrice){
                $ticketPrice->price = $request->price;
                $ticketPrice->save();
            }else{
                $ticketPrice = new TicketPriceByStoppage();
                $ticketPrice->ticket_price_id = $request->ticket_price;
                $ticketPrice->source_destination = $source_destination;
                $ticketPrice->price = $request->price;
                $ticketPrice->save();
            }
        }else{
            $price = TicketPriceByStoppage::with('ticketPrice.route')->findOrFail($id);
            
            $route = $price->ticketPrice->route;
            $source = @$price->source_destination[0] ?? 0;
            $destination = @$price->source_destination[1] ?? 0;
            
            
            if($route->start_from == $source && $route->end_to == $destination){
                $ticketPrice = $price->ticketPrice;
                $ticketPrice->price = $request->price;
                $ticketPrice->save();
            }
            
            $price->price = $request->price;
            $price->save();
        }

        $notify = ['success' => true, 'message'=>'Price Updated Successfully'];
        return response()->json($notify);
    }

    public function ticketPriceDelete(Request $request){
        $request->validate(['id' => 'required|integer']);

        $data = TicketPrice::where('id', $request->id)->first();
        $data->prices()->delete();
        $data->seats()->delete();
        $data->delete();

        $notify[] = ['success', 'Price Deleted Successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function checkTicketPrice(Request $request){
        $check = TicketPrice::where('vehicle_route_id', $request->vehicle_route_id)->where('fleet_type_id', $request->fleet_type_id)->first();

        if(!$check){
            return response()->json(['error' => 'Ticket price not added for this fleet-route combination yet. Please add ticket price before creating a trip.']);
        }
    }
}
