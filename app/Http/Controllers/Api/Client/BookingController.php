<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\DriverTrips;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\Booking\SearchDirectBookingRequest;
use App\Http\Requests\Api\Client\Booking\BookDirectBookingRequest;
use App\Http\Requests\Api\Client\Booking\SearchSpecialBookingRequest;
use App\Http\Requests\Api\Client\Booking\BookSpecialBookingRequest;
use App\Models\Trip;
use Carbon\Carbon;
use App\Models\TicketPrice;
use App\Models\Seat;
use App\Models\VehicleRoute;
use App\Models\History;
use App\Models\BookedTicket;
use App\Models\BookedSeat;
use App\Http\Resources\Api\Client\DirectBookingResource;
use App\Http\Resources\Api\Client\TicketDirectBookingResource;
use App\Http\Resources\Api\Client\SpecialBookingResource;
use App\Http\Resources\Api\Client\TicketSpecialBookingResource;
use App\Http\Resources\Api\Client\bookSpecialBookingResource;
use Illuminate\Support\Facades\DB;


class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.user:1,2');
    }

    /**
     *
     * Search Booking Tickets
     * @param SearchDirectBookingRequest $request
     * @return DirectBookingResource
     */
    public function searchDirectBookings(SearchDirectBookingRequest $request)
    {
        $trips = Trip::with('fleetType')->active();
        if ($request->pickup && $request->destination) {
            $pickup = $request->pickup;
            $destination = $request->destination;

            if ($request->back_date) {
                $trips = $trips->where(['start_from' => $pickup, 'end_to' => $destination])
                    ->orWhere(['start_from' => $destination, 'end_to' => $pickup])
                    ->with('route')
                    ->get();
            } else {
                $trips = $trips->with('route')->get();
            }

            $tripArray = array();
            foreach ($trips as $trip) {
                $startPoint = array_search($trip->start_from, array_values($trip->route->stoppages));
                $endPoint = array_search($trip->end_to, array_values($trip->route->stoppages));
                $pickup_point = array_search($pickup, array_values($trip->route->stoppages));
                $destination_point = array_search($destination, array_values($trip->route->stoppages));
                if ($startPoint < $endPoint) {
                    if ($pickup_point >= $startPoint && $pickup_point < $endPoint && $destination_point > $startPoint && $destination_point <= $endPoint) {
                        array_push($tripArray, $trip->id);
                    }
                } else {
                    $revArray = array_reverse($trip->route->stoppages);
                    $startPoint = array_search($trip->start_from, array_values($revArray));
                    $endPoint = array_search($trip->end_to, array_values($revArray));
                    $pickup_point = array_search($pickup, array_values($revArray));
                    $destination_point = array_search($destination, array_values($revArray));
                    if ($pickup_point >= $startPoint && $pickup_point < $endPoint && $destination_point > $startPoint && $destination_point <= $endPoint) {
                        array_push($tripArray, $trip->id);
                    }
                }
            }

            $trips = Trip::with('fleetType')->active()->whereIn('id', $tripArray);
        } else {
            if ($request->pickup) {
                $pickup = $request->pickup;
                $trips = $trips->whereHas('route', function ($route) use ($pickup) {
                    $route->whereJsonContains('stoppages', $pickup);
                });
            }
            if ($request->destination) {
                $destination = $request->destination;
                $trips = $trips->whereHas('route', function ($route) use ($destination) {
                    $route->whereJsonContains('stoppages', $destination);
                });
            }
        }
        if ($request->go_date) {
            $dayOff = Carbon::parse($request->go_date)->format('w');
            $trips = $trips->whereJsonDoesntContain('day_off', $dayOff);
        }
        if ($request->back_date) {
            $dayOff = Carbon::parse($request->back_date)->format('w');
            $trips = $trips->whereJsonDoesntContain('day_off', $dayOff);
        }
        $trips = $trips->with(['fleetType', 'route', 'schedule', 'startFrom', 'endTo'])->where('status', 1)->paginate(getPaginate());

        // Calculate min_seat_price for each trip
        $trips->each(function ($trip) {
            $ticket = TicketPrice::where('fleet_type_id', $trip->fleetType->id)
                ->where('vehicle_route_id', $trip->route->id)
                ->first();

            if ($ticket) {
                $min_seat_price = $ticket->seats->min('price');
                $trip->min_seat_price = showAmount($min_seat_price);
            } else {
                $trip->min_seat_price = 0.00;
            }
        });
        return response()->json(['status' => 'success', 'data' => DirectBookingResource::collection($trips)->response()->getData(), 'message' => trans('messages.data_found')])->setStatusCode(200);
    }


    /**
     * Show seats
     * @param  $trip_id
     */
    public function showSeats($trip_id)
    {
        $trip = Trip::findOrFail($trip_id);
        $ticket = TicketPrice::where('fleet_type_id', $trip->fleetType->id)
            ->where('vehicle_route_id', $trip->route->id)
            ->first();
        $fleetType = $trip->fleetType->only('name', 'seat_layout', 'deck', 'deck_seats');

        $seats = [];
        if ($ticket) {
            $seats = $ticket->seats->map(function ($seat) {
                return ['id' => $seat->id, 'name' => $seat->name, 'price' => $seat->price,];
            });
        }
        $data = ['fleetType' => $fleetType,
            'seats' => $seats];
        return response()->json(['status' => 'success', 'data' => $data, 'message' => trans('messages.data_found')])->setStatusCode(200);

    }

    /**
     * Book direct booking
     * @param BookDirectBookingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookDirectBooking(BookDirectBookingRequest $request)
    {

        $trip = Trip::findOrFail($request->trip_id);
        $dayGoDate = Carbon::parse($request->go_date)->format('w');
        $dayBackDate = $request->back_date ? Carbon::parse($request->back_date)->format('w') : '';
        $route = $trip->route;
        $stoppages = $trip->route->stoppages;
        $source_pos = array_search($trip->start_from, $stoppages);
        $destination_pos = array_search($trip->end_to, $stoppages);

        if (!empty($trip->day_off)) {
            if (in_array($dayGoDate, $trip->day_off) || in_array($dayBackDate, $trip->day_off)) {
                return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.The trip is not available for these days')])->setStatusCode(400);
            }
        }

        $booked_ticket = BookedTicket::where('trip_id', $trip->id)
            ->where('date_of_journey', Carbon::parse($request->go_date)->format('Y-m-d'))
            ->where('back_date', Carbon::parse($request->back_date)->format('Y-m-d'))
            ->whereIn('status', [1, 2])
            ->where('pickup_point', $trip->start_from)
            ->where('dropping_point', $trip->end_to)
            ->first();

        $driver_trips = DriverTrips::where([
            'trip_id' => $trip->id,
            'date' => Carbon::parse($request->go_date)->format('Y-m-d'),
        ])->first();

        if ($driver_trips != null)
            if ($driver_trips->booked_count + count($request->seats) > $driver_trips->total_seats)
                return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('car_completed')])->setStatusCode(400);


        $route = $trip->route;
        $ticketPrice = TicketPrice::where('fleet_type_id', $trip->fleetType->id)->where('vehicle_route_id', $route->id)->first();

        // calculate sub_total
        $subTotal = 0;
        $seatName = [];

        foreach ($request->seats as $key => $seat) {
            $seatPrice = Seat::where(['ticket_price_id' => $ticketPrice->id,
                'id' => $seat['id']])->pluck('price')->first();
            $subTotal += $seatPrice;
            $seatNames[] = $seat['name'];
        }


        if (auth()->user()->pocket->amount + auth()->user()->pocket->credit_limit < $subTotal) {
            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('you_dont_have_enough_money')])
                ->setStatusCode(400);
        }

        $driver_trips = DriverTrips::query()->updateOrCreate(
            [
                'trip_id' => $trip->id,
                'date' => Carbon::parse($request->go_date)->format('Y-m-d'),
            ],
            [
                'trip_id' => $trip->id,
                'date' => Carbon::parse($request->go_date)->format('Y-m-d'),
            ]
        );
        // Get the seat IDs from $request->seats
        $requestSeatIds = array_map(function ($seat) {
            return $seat['id'];
        }, $request->seats) ?? [];
        $commonValues = array_intersect(json_decode($driver_trips->seats_id) ?? [], $requestSeatIds);

        if (!empty($commonValues)) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('seat_booked')])->setStatusCode(400);

        } else {
            $existingSeatIds = json_decode($driver_trips->seats_id, true) ?? [];
            $mergedSeatIds = array_merge($existingSeatIds, $requestSeatIds);

            $driver_trips->update(['seats_id' => json_encode($mergedSeatIds)]);
            $driver_trips->increment('booked_count', sizeof($request->seats));
        }


        $startPoint = array_search($trip->start_from, array_values($trip->route->stoppages));
        $endPoint = array_search($trip->end_to, array_values($trip->route->stoppages));
        $reverse = ($startPoint < $endPoint) ? false : true;

        if (!$reverse) {
            $can_go = ($source_pos < $destination_pos) ? true : false;
        } else {
            $can_go = ($source_pos > $destination_pos) ? true : false;
        }

        if (!$can_go) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.Select Pickup Point & Dropping Point Properly')])->setStatusCode(400);
        }


        $pnr_number = getTrx(10);
        $bookedTicket = BookedTicket::create([
            'user_id' => auth()->user()->id,
            'gender' => 1,
            'trip_id' => $trip->id,
            'source_destination' => ["$trip->start_from", "$trip->end_to"],
            'pickup_point' => $trip->start_from,
            'dropping_point' => $trip->end_to,
            'seats' => $seatNames,
            'seats_back' => $request->back_date ? $seatNames : null,
            'ticket_count' => sizeof($request->seats),
            'date_of_journey' => Carbon::parse($request->go_date)->format('Y-m-d'),
            'back_date' => $request->back_date ? Carbon::parse($request->back_date)->format('Y-m-d') : null,
            'pnr_number' => $pnr_number,
            'status' => 2,
            'sub_total' => $subTotal,
        ]);


        foreach ($request->seats as $seat) {
            BookedSeat::create([
                'booked_ticket_id' => $bookedTicket->id,
                'seat_id' => $seat['id'],
                'client_name' => $seat['client_name'],
                'client_phone' => $seat['client_phone'],
                'gender' => $seat['gender'],
            ]);
        }


        if (auth()->user()->pocket->amount == 0) {
            auth()->user()->pocket->increment('debt_balance', $bookedTicket->sub_total);
            auth()->user()->pocket->decrement('credit_limit', $bookedTicket->sub_total);
        } elseif (auth()->user()->pocket->amount < $subTotal) {
            // after Subtracts amount it = 0.00 and get Subtracts from  debt_balance
            $debt = $subTotal - auth()->user()->pocket->amount - auth()->user()->pocket->debt_balance;
            auth()->user()->pocket->update(
                [
                    'amount' => 0,
                    'debt_balance' => auth()->user()->pocket->debt_balance + $debt,
                    'credit_limit' => auth()->user()->pocket->credit_limit - $debt
                ]);
        } elseif (auth()->user()->pocket->amount > $subTotal) {
            auth()->user()->pocket->update(['amount' => auth()->user()->pocket->amount - $bookedTicket->sub_total]);

        }

        History::create([
            'booked_ticket_id' => $bookedTicket->id,
            'user_id' => $bookedTicket->user_id,
            'type' => History::BOOK_TICKET,
            'amount' => $bookedTicket->sub_total,
            'debtor' => $subTotal,
            'total' => auth()->user()->pocket->amount
        ]);


        return response()->json(['status' => 'success', 'data' => TicketDirectBookingResource::make($bookedTicket), 'message' => trans('messages.data_found')])->setStatusCode(200);
    }


    /**
     * Book special booking
     * @param BookSpecialBookingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookSpecialBooking(BookSpecialBookingRequest $request)
    {
        $dayGoDate = Carbon::parse($request->go_date)->format('w');
        $dayBackDate = $request->back_date ? Carbon::parse($request->back_date)->format('w') : '';

        $booked_ticket = BookedTicket::where("user_id", auth()->id())
            ->where('date_of_journey', $dayGoDate)
            ->where('back_date', $dayBackDate)
            ->whereIn('status', [1, 2])
            ->where('pickup_point', $request->pickup)
            ->where('dropping_point', $request->destination)
            ->first();


//        if (is_null($booked_ticket)) {
//            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.Why you are choosing those seats which are already booked?')])->setStatusCode(400);
//        }

        $vehicleRoute = VehicleRoute::where(['start_from' => $request->pickup, 'end_to' => $request->destination])->first();
        $ticketPrice = TicketPrice::where('fleet_type_id', $request->fleet_type)->where('vehicle_route_id', $vehicleRoute->id)->first();
        $seats = Seat::where('ticket_price_id', $ticketPrice->id)->get();
        $seatNames = $seats->map(function ($seat) {
            return 1 . '-' . $seat->name;
        })->toArray();


        $sub_total = $seats->sum('price');


        if (auth()->user()->pocket->amount + auth()->user()->pocket->credit_limit < $sub_total) {
            return response()->json(
                [
                    'status' => 'fail',
                    'data' => null,
                    'message' => trans('you_dont_have_enough_money')])
                ->setStatusCode(400);
        }
        $pnr_number = getTrx(10);
        $bookedTicket = BookedTicket::create([
            'user_id' => auth()->user()->id,
            'gender' => 1,
            'source_destination' => ["$request->pickup", "$request->destination"],
            'pickup_point' => $request->pickup,
            'dropping_point' => $request->destination,
            'seats' => $seatNames,
            'seats_back' => $request->back_date ? $seatNames : null,
            'ticket_count' => sizeof($seats),
            'date_of_journey' => Carbon::parse($request->go_date)->format('Y-m-d'),
            'back_date' => $request->back_date ? Carbon::parse($request->back_date)->format('Y-m-d') : null,
            'pnr_number' => $pnr_number,
            'status' => 2,
            'sub_total' => $sub_total,
            'passenger_numbers' => $request->passenger_numbers,
            'responsible_name' => $request->responsible_name,
            'responsible_phone' => $request->responsible_phone,
            'address' => auth()->user()->type == 2 ? $request->address : null,
            'government_id' => auth()->user()->type == 2 ? $request->government_id : null,
            'city_id' => auth()->user()->type == 2 ? $request->city_id : null,

        ]);

        foreach ($request->seats as $seat) {
            BookedSeat::create([
                'booked_ticket_id' => $bookedTicket->id,
                'client_name' => $seat['client_name'],
                'client_phone' => $seat['client_phone'],
                'gender' => $seat['gender'],
            ]);
        }


        if (auth()->user()->pocket->amount == 0) {
            auth()->user()->pocket->increment('debt_balance', $bookedTicket->sub_total);
            auth()->user()->pocket->decrement('credit_limit', $bookedTicket->sub_total);

        } elseif (auth()->user()->pocket->amount < (double)$sub_total) {
            $debt = $sub_total - auth()->user()->pocket->amount;
            auth()->user()->pocket->update(
                [
                    'amount' => 0,
                    'debt_balance' => auth()->user()->pocket->debt_balance + $debt,
                    'credit_limit' => auth()->user()->pocket->credit_limit - $debt
                ]);
        } elseif (auth()->user()->pocket->amount >= (double)$sub_total) {
            auth()->user()->pocket->decrement('amount', $sub_total);
        }

        //history
        History::create([
            'booked_ticket_id' => $bookedTicket->id,
            'user_id' => $bookedTicket->user_id,
            'type' => History::BOOK_TICKET,
            'amount' => $sub_total,
            'debtor' => $sub_total,
            'total' => auth()->user()->pocket->amount
        ]);

        return response()->json(['status' => 'success', 'data' => TicketSpecialBookingResource::make($bookedTicket), 'message' => trans('messages.data_found')])->setStatusCode(200);

    }

    /**
     * get tickets
     * @return TicketSpecialBookingResource
     */
    public function tickets()
    {
        $booked_tickets = BookedTicket::where('user_id', auth()->user()->id)->paginate(getPaginate());
        return response()->json(['status' => 'success', 'data' => TicketSpecialBookingResource::collection($booked_tickets)->response()->getData(), 'message' => ''])->setStatusCode(200);

    }

    /**
     * cancel ticket
     * @return void
     */
    public function cancelTicket($ticket)
    {
        $ticket = BookedTicket::where(['user_id' => auth()->user()->id, 'id' => $ticket, 'status' => 2])->first();
        if (!$ticket) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('Ticket_Does_Not_Exist')])->setStatusCode(400);
        }
        $ticket->update(['status' => 3]);


        if (auth()->user()->pocket->debt_balance > 0 && auth()->user()->pocket->debt_balance <= $ticket->sub_total) {
            $amount = $ticket->sub_total - auth()->user()->pocket->debt_balance;
            auth()->user()->pocket->update(
                [
                    'debt_balance' => 0,
                    'credit_limit' => auth()->user()->pocket->credit_limit + auth()->user()->pocket->debt_balance,
                    'amount' => $amount + auth()->user()->pocket->amount
                ]);

        } elseif (auth()->user()->pocket->debt_balance > 0 && auth()->user()->pocket->debt_balance > $ticket->sub_total) {
            auth()->user()->pocket->decrement('debt_balance', $ticket->sub_total);
            auth()->user()->pocket->increment('credit_limit', $ticket->sub_total);
        } else {
            auth()->user()->pocket->increment('amount', $ticket->sub_total);
        }

        //history
        History::create([
            'booked_ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'type' => History::CANCEL_TICKET,
            'amount' => $ticket->sub_total,
            'creditor' => $ticket->sub_total,
            'total' => auth()->user()->pocket->amount
        ]);
        return response()->json(['status' => 'success', 'data' => null, 'message' => 'message.deleted_successfuly'])->setStatusCode(200);

    }

}

