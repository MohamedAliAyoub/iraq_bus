@php
$contents = getContent('banner.content',true);
$counters = App\Models\Counter::get();
@endphp
<!-- Banner Section Starts Here -->
<section class="banner-section" style="background: url({{ getImage('assets/images/frontend/banner/'.$contents->data_values->background_image, "1500x88") }}) repeat-x bottom;">
    <div class="container">
        <div class="banner-wrapper">
            <div class="banner-content">
                <h1 class="title">{{ __($contents->data_values->heading) }}</h1>
                <a href="{{ __(@$contents->data_values->link) }}" class="cmn--btn">{{ __(@$contents->data_values->link_title) }}</a>
            </div>
            <div class="ticket-form-wrapper">
                <div class="ticket-header nav-tabs nav border-0">
                    <h4 class="title">@lang('Choose Your Ticket')</h4>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="one-way">
                        <form action="{{ route('search') }}" class="ticket-form row g-3 justify-content-center m-0">
                            <div class="col-md-6">
                                <div class="form--group">
                                    <i class="las la-location-arrow"></i>
                                    <select class="form--control select2" name="pickup">
                                        <option value="">@lang('Pickup Point')</option>
                                        @foreach ($counters as $counter)
                                        <option value="{{ $counter->id }}" @if(request()->pickup == $counter->id) selected @endif>{{ __($counter->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form--group">
                                    <i class="las la-map-marker"></i>
                                    <select name="destination" class="form--control select2">
                                        <option value="">@lang('Dropping Point')</option>
                                        @foreach ($counters as $counter)
                                        <option value="{{ $counter->id }}" @if(request()->destination == $counter->id) selected @endif>{{ __($counter->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form--group">
                                    <i ></i>
                                    <select name="trip_type" class="form--control select2" id="tripTypeSelect">
                                        <option value="">@lang('Trip Type')</option>
                                        <option value="0" @if(request()->trip_type == '0') selected @endif>@lang('Go')</option>
                                        <option value="1" @if(request()->trip_type == '1') selected @endif>@lang('Go and Back')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form--group">
                                    <i class="las la-calendar-check"></i>
                                    <input type="text" name="go_date" class="form--control datepicker" placeholder="@lang('Departure Date')" autocomplete="off" value="{{ request()->go_date }}">
                                </div>
                            </div>
                            <div class="col-md-12" id="dateInputContainer" style="display: none;">
                                <div class="form--group">
                                    <i class="las la-calendar-check"></i>
                                    <input type="text" name="back_date" class="form--control datepicker" placeholder="@lang('Back Date')" autocomplete="off" value="{{ request()->back_date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form--group">
                                    <button>@lang('Find Tickets')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="shape">
        <img src="{{ getImage('assets/images/frontend/banner/'.$contents->data_values->animation_image, "200x69") }}" alt="bg">
    </div>
</section>
<!-- Banner Section Ends Here -->
@push('script')
<script>
    $(document).ready(function() {
    toggleDateInputVisibility();

    $('#tripTypeSelect').change(toggleDateInputVisibility);
    function toggleDateInputVisibility() {
        var selectedValue = $('#tripTypeSelect').val();
        $('#dateInputContainer').toggle(selectedValue === '1');
    }
});
</script>
@endpush
