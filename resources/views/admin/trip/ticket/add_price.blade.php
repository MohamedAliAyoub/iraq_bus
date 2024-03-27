@extends('admin.layouts.app')
<style>
    #seatTable {
        display: none;
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    #seatTable th, #seatTable td {
        border: 1px solid #dddddd;
        padding: 8px;
        text-align: center;
    }

    #seatTable th {
        background-color: #f2f2f2;
    }

    #seatTable input {
        width: 100%;
        box-sizing: border-box;
        padding: 5px;
    }
</style>



@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Information About Ticket Price') </h5>

                <form action="{{ route('admin.trip.ticket.price.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Fleet Type')</label>
                                <select name="fleet_type" id="fleet_type" class="select2-basic" required>
                                    <option value="">@lang('Select an option')</option>
                                    @foreach ($fleetTypes as $item)
                                        <option value="{{ $item->id }}" data-seat-count="{{array_sum($item->deck_seats)}}">{{ __($item->name) }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Route')</label>
                                <select name="route" class="select2-basic" required>
                                    <option value="">@lang('Select an option')</option>
                                    @foreach ($routes as $item)
                                        <option value="{{ $item->id }}">{{ __($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <table id="seatTable">
                                <thead>
                                    <tr>
                                        <th>Seat Label</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody id="seatBody">
                                    <!-- Dynamic content will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Price For Source To Destination')</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="btn--light input-group-text">{{ $general->cur_sym }}</span>
                                    </div>
                                    <input type="text" name="main_price" id="totalPrice" class="form-control" placeholder="@lang('Enter a price')" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 price-error-message">

                        </div>

                        <div class="price-wrapper col-md-12">

                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg submit-button">@lang('Save')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('admin.trip.ticket.price') }}" class="btn btn-sm btn--primary box--shadow1 text--small addBtn"><i class="la la-fw la-backward"></i>@lang('Go Back')</a>
@endpush

@push('script')
<script>
     "use strict";

     (function($){
        $('.select2-basic').select2({
            dropdownParent: $('.card-body')
        });

        $(document).on('change', 'select[name=fleet_type] , select[name=route]', function(){
            var routeId  = $('select[name="route"]').find("option:selected").val();
            var fleetTypeId  = $('select[name="fleet_type"]').find("option:selected").val();
            var seat  = $('select[name="seat"]').find("option:selected").val();

            if(routeId && fleetTypeId){
                var data = {
                    'vehicle_route_id'      : routeId,
                    'fleet_type_id' : fleetTypeId,
                    'seat' : seat
                }
                $.ajax({
                    url: "{{ route('admin.trip.ticket.get_route_data') }}",
                    method: "get",
                    data: data,
                    success: function(result){
                        if(result.error){
                            $('.price-error-message').html(`<h5 class="text--danger">${result.error}</h5>`);
                            $('.price-wrapper').html('');
                            $('.submit-button').attr('disabled', 'disabled');
                        }else{
                            $('.price-error-message').html(``);
                            $('.submit-button').removeAttr('disabled');
                            $('.price-wrapper').html(`<h5>${result}</h5>`);
                        }
                    }
                });
            }else{
                $('.price-wrapper').html('');
            }
        })

     })(jQuery)
     
     
    $(document).ready(function () {
        $('#fleet_type').on('change', function () {
            var seatCount = $(this).find(':selected').data('seat-count');
            populateSeatOptions(seatCount);
            $('#seatTable').show();

        });

        // Function to populate seat options
        function populateSeatOptions(seatCount) {
        $('#seatBody').empty();
        for (var i = 1; i <= seatCount; i++) {
            var seatLabel = 'A' + i;
            // Create table row
            var row = $('<tr>');
            row.append($('<td>').append('<input type="text" readonly value="' + seatLabel + '" name="seat[]">'));
            row.append($('<td>').append($('<input>').attr('type', 'text').attr('name', 'seat_price[]').addClass('seat_price')));
            $('#seatBody').append(row);
        }
        updateTotalPrice();

        }

        function updateTotalPrice() {
            var totalPrice = parseFloat($('#mainPrice').val()) || 0;
            $('.seat_price').each(function () {
                var priceValue = parseFloat($(this).val()) || 0;
                totalPrice += priceValue;
            });
            $('#totalPrice').val(totalPrice);
        }

        $(document).on('change', '.seat_price', function () {
    updateTotalPrice();
    });

});
  
</script>
@endpush
