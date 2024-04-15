@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">

        <div class="col-xl-12 col-lg-7 col-md-7 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Add_Driver')</h5>

                    <form action="{{route('admin.drivers.store')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('First Name')<span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="firstname" value="{{ old('firstname') }}">
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="lastname" value="{{ old('lastname') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="username" value="{{ old('username') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Password') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="password" name="password">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Password_Confirmation') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="password" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Email') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Mobile Number') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mobile" value="{{ old('mobile') }}">
                                </div>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Address') </label>
                                    <input class="form-control" type="text" name="address" value="{{ old('address') }}" >
                                </div>
                            </div>

                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Credit_limit')</label>
                                    <input class="form-control" type="text" name="credit_limit" value="{{ old('credit_limit') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('first_id_card_image')</label>
                                    <input class="form-control" type="file" name="first_id_card_image" value="{{ old('first_id_card_image') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('last_id_card_image')</label>
                                    <input class="form-control" type="file" name="last_id_card_image" value="{{ old('second_id_card_image') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('first_residence_card_image')</label>
                                    <input class="form-control" type="file" name="first_residence_card_image" value="{{ old('first_residence_card_image') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('last_residence_card_image')</label>
                                    <input class="form-control" type="file" name="last_residence_card_image" value="{{ old('last_residence_card_image') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('first_license_image')</label>
                                    <input class="form-control" type="file" name="first_license_image" value="{{ old('first_license_image') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('last_license_image')</label>
                                    <input class="form-control" type="file" name="last_license_image" value="{{ old('last_license_image') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('car_images')</label>
                                    <input class="form-control" type="file" name="car_images[]" value="{{ old('car_images') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('car_images')</label>
                                    <input class="form-control" type="file" name="car_images[]" value="{{ old('car_images') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('car_images')</label>
                                    <input class="form-control" type="file" name="car_images[]" value="{{ old('car_images') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('car_images')</label>
                                    <input class="form-control" type="file" name="car_images[]" value="{{ old('car_images') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn--primary btn-block btn-lg">@lang('Save')
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
