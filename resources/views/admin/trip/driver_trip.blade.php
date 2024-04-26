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
                                <th>@lang('Title')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Start From')</th>
                                <th>@lang('End To')</th>
                                <th>@lang('Driver')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($trips as $item)
                                <tr>
                                    <td data-label="@lang('Title')">
                                        {{ __($item->trip->title) }}
                                    </td>

                                    <td data-label="@lang('Date')">
                                        {{ $item->date }}
                                    </td>
                                    <td data-label="@lang('Start From')">
                                        {{ $item->trip->schedule->start_from }}
                                    </td>
                                    <td data-label="@lang('End To')">
                                        {{ $item->trip->schedule->end_at }}
                                    </td>

                                    <td data-label="@lang('Driver')">
                                        {{$item->driver?->full_name }}

                                    </td>

                                    <td data-label="@lang('Status')">
                                        @if($item->status == 1)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('DRIVER_ACCEPT')</span>
                                        @elseif($item->status == 2)
                                            <span class="text--small badge font-weight-normal badge--danger">@lang('DRIVER_CANCEL')</span>
                                        @elseif($item->status == 3)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('SUCCESS')</span>
                                        @elseif($item->status == 4)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('IN_PROGRESS')</span>
                                        @elseif($item->status == 5)
                                            <span class="text--small badge font-weight-normal badge--danger">@lang('TRANSFER')</span>
                                        @else
                                            <span class="text--small badge font-weight-normal badge--warning">@lang('PENDING')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <button type="button" class="icon-btn ml-1 editBtn"
                                                data-toggle="modal" data-target="#editModal"
                                                data-trip="{{ $item->driver }}"
                                                data-action="{{ route('admin.trip.driver.assign', $item->id) }}"
                                                data-original-title="@lang('Update')">
                                            <i class="la la-pen"></i>
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


    {{-- Add Driver METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add_Driver')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold"> @lang('Driver')</label>
                            <select name="driver_id" class="select2-basic" required>
                                <option value="">@lang('Select an option')</option>
                                @foreach ($drivers as $item)
                                    <option value="{{ $item->id }}"
                                            data-name="{{ $item->firstname.' ' . $item->lastname }}">{{ $item->firstname .' ' . $item->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('breadcrumb-plugins')
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('#editModal').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });


            $('.disableBtn').on('click', function () {
                var modal = $('#disableModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.trip_title').text($(this).data('trip_title'));
                modal.modal('show');
            });

            $('.activeBtn').on('click', function () {
                var modal = $('#activeModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.trip_title').text($(this).data('trip_title'));
                modal.modal('show');
            });

            $('.addBtn').on('click', function () {
                $('input[name=title]').text('')
                var modal = $('#addModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var trip = $(this).data('trip');
                modal.find('form').attr('action', $(this).data('action'));
                console.log(trip);

                // Find the select element
                var selectElement = modal.find('select[name=driver_id]');

                // Loop through the options and set the selected option
                selectElement.find('option').each(function () {
                    if ($(this).data('name') === trip.firstname + ' ' + trip.lastname) {
                        $(this).prop('selected', true);
                    }
                });

                $('.select2-basic').select2();
                modal.modal('show');
            });


        })(jQuery);
    </script>

@endpush
