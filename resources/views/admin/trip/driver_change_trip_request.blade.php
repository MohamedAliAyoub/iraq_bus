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
                                    <th>@lang('Driver')</th>
                                    <th>@lang('Start From') || @lang('End To')</th>
                                    <th>@lang('Day Off')</th>
                                    <th>@lang('Schedule')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($trips as $item)
                                <tr>
                                    <td data-label="@lang('Driver')">
                                        {{ $item->driver->full_name }}
                                    </td>

                                    <td data-label="@lang('Route')">
                                        {{ $item->route->name  }}
                                    </td>

                                    <td data-label="@lang('Day Off')">
                                        @if($item->day_off)
                                            @foreach ($item->day_off as $day)
                                                {{ __(showDayOff($day)) }} @if(!$loop->last) , @endif
                                            @endforeach
                                        @else
                                            @lang('No Off Day')
                                        @endif
                                    </td>

                                    <td data-label="@lang('Schedule')">
                                        {{ $item->schedule->start_from ." : " .  $item->schedule->end_at }}
                                    </td>
                                    <td data-label="@lang('Status')">
                                        <span class="text--small badge font-weight-normal badge--warning">@lang('Pending')</span>
                                    </td>
                                    <td data-label="@lang('Action')">
                                            <button type="button"
                                                class="icon-btn btn--success ml-1 activeBtn rejectButton"
                                                data-toggle="modal" data-target="#activeModal"
                                                data-id="{{ $item->id }}"
                                                data-trip_title = "{{ $item->title }}"
                                                data-original-title="@lang('Active')">
                                                <i class="la la-eye"></i>
                                            </button>
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
                    {{ paginateLinks($trips) }}
                </div>
            </div>
        </div>
    </div>




    {{-- active METHOD MODAL --}}
    <div id="activeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Active Trip')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.trip.acceptDriverRequest')}}" method="POST">
                    @csrf
                    <input type="text" name="id" hidden="true">
                    <div class="modal-body">
                        <p>@lang('Are_you_sure_to_active_driver_request') <span class="font-weight-bold trip_title"></span> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <!-- Add the Reject button with the trip ID -->
                        <a href="#" class="btn btn--danger rejectBtn" data-id="{{ $item->id }}">
                            @lang('Reject')
                        </a>
                        <button type="submit" class="btn btn--primary">@lang('accept')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   @endsection


@push('script')
    <script>
        (function ($) {
            "use strict";


            $('.activeBtn').on('click', function () {
                var modal = $('#activeModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.trip_title').text($(this).data('trip_title'));
                modal.modal('show');
            });

            $('.rejectBtn').on('click', function (event) {
                event.preventDefault();
                var tripId = $(this).find('.activeBtn').data('id');
                console.log(tripId);
            });
            function redirectToReject(tripId) {
                var rejectUrl = "{{ route('admin.trip.rejectDriverRequest', ':id') }}";
                rejectUrl = rejectUrl.replace(':id', tripId); // Replace placeholder with tripId
                window.location.href = rejectUrl; // Redirect to the rejectUrl
            }




            function makeTitle(modalName){
                var modal = $('#'+ modalName);
                var data1 = modal.find('select[name="fleet_type"]').find("option:selected").data('name');
                var data2 = modal.find('select[name="start_from"]').find("option:selected").data('name');
                var data3 = modal.find('select[name="end_to"]').find("option:selected").data('name');
                var data  = [];
                var title = '';
                if(data1 != undefined){
                    data.push(data1);
                }
                if(data2 != undefined)
                    data.push(data2);
                if(data3 != undefined)
                    data.push(data3);
                if(data1 != undefined && data2 != undefined && data3 != undefined) {
                    var fleet_type_id = modal.find('select[name="fleet_type"]').val();
                    var vehicle_route_id = modal.find('select[name="route"]').val();

                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.trip.ticket.check_price') }}",
                        data: {
                            "fleet_type_id" : fleet_type_id,
                            "vehicle_route_id" : vehicle_route_id
                        },
                        success: function (response) {
                            if(response.error){
                                modal.find('input').val('');
                                modal.find('select').val('').trigger('change');
                                modal.modal('hide');
                                var alertModal = $('#alertModal');
                                alertModal.find('.container-fluid').text(response.error);
                                alertModal.modal('show');
                            }
                        }
                    });
                }

                $.each(data, function (index, value) {
                    if(index > 0){
                        if(index > 3)
                            title += ' to ';
                        else
                            title += ' - ';
                    }
                    title += value;
                });
                $('input[name="title"]').val(title);
            }
        })(jQuery);
    </script>

@endpush
