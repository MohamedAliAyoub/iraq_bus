@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('PNR Number')</th>
                                    <th>@lang('Journey Date')</th>
                                    <th>@lang('Back Date')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Pickup Point')</th>
                                    <th>@lang('Dropping Point')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Ticket Count')</th>
                                    <th>@lang('Fare')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($tickets as $item)
                                <tr>
                                    <td data-label="@lang('User')">
                                        <span class="font-weight-bold">{{ __(@$item->user->fullname) }}</span>
                                    <br>
                                    <span class="small"> <a href="{{ route('admin.users.detail', $item->user_id) }}"><span>@</span>{{ __(@$item->user->username) }}</a> </span>

                                    </td>
                                    <td data-label="@lang('PNR Number')">
                                        <span class="text-muted">{{ __($item->pnr_number) }}</span>
                                    </td>
                                    <td data-label="@lang('Journey Date')">
                                        {{ __(showDateTime($item->date_of_journey, 'd M, Y')) }}
                                    </td>
                                      <td data-label="@lang('Back Date')">
                                       @if($item->back_date)
                                            {{ __(showDateTime($item->back_date, 'd M, Y')) }}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td data-label="@lang('Trip')">
                                        <span class="font-weight-bold">{{ $item->trip?->fleetType?->name }}</span>
                                        <br>
                                        <span class="font-weight-bold">  {{ $item->trip?->startFrom?->name ?? '' }} - {{ $item->trip?->endTo?->name ?? '' }}</span>
                                    </td>
                                    <td data-label="@lang('Pickup Point')">
                                        {{ __($item->pickup->name) }}
                                    </td>
                                    <td data-label="@lang('Dropping Point')">
                                        {{ __($item->drop->name) }}
                                    </td>
                                    <td data-label="@lang('Type')">
                                        @if($item->back_date)
                                            @lang('Go and Back')
                                        @else
                                             @lang('Go')
                                        @endif
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if ($item->status == 1)
                                            <span class="badge badge--success font-weight-normal text--samll">@lang('Booked')</span>
                                        @elseif($item->status == 2)
                                            <span class="badge badge--warning font-weight-normal text--samll">@lang('Pending')</span>
                                        @else
                                            <span class="badge badge--danger font-weight-normal text--samll">@lang('Rejected')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Ticket Count')">
                                        {{ $item->ticket_count-1 }}
                                    </td>
                                    <td data-label="@lang('Fare')">
                                        {{ __(showAmount($item->sub_total)) }} {{ __($general->cur_text) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($tickets) }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
<div class="d-flex">
<div class="mr-2">

    <form action="{{route('admin.vehicle.ticket.search', $scope ?? str_replace('admin.vehicle.ticket.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Search PNR Number')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
    </div>

    <div class="mr-2">

    <form action="{{route('admin.vehicle.ticket.search.vehicle', $scope ?? str_replace('admin.vehicle.ticket.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append" >
            <input type="text" name="search_vehicle" class="form-control" placeholder="@lang('Fleet Type')" value="{{ $search_vehicle ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
    </div>

    <div class="mr-2">

    <form action="{{route('admin.vehicle.ticket.search.trip', $scope ?? str_replace('admin.vehicle.ticket.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search_trip" class="form-control" placeholder="@lang('From - To')"  value="{{ $search_trip ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
    </div>

    <div class="mr-2">

    <form action="{{route('admin.vehicle.ticket.search.date',$scope ?? str_replace('admin.vehicle.ticket.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append ">
                <input name="search_date" type="text"  data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Journey Date')" autocomplete="off" value="{{ @$search_date }}">
                <input type="hidden" name="method" value="{{ @$methodAlias }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
</div>
</div>

        @endpush
@push('script-lib')
  <script src="{{ asset('assets/global/js/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/global/js/datepicker.en.js') }}"></script>
@endpush
@push('script')
  <script>
    (function($){
        "use strict";
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
  </script>
@endpush
