@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">

        <div class="col-xl-12 col-lg-7 col-md-7 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Add')</h5>

                    <form action="{{route('admin.driver_deposit.store')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold"> @lang('getaway')</label>
                                    <select name="gateway_id" class="select2-basic" required>
                                        <option value="">@lang('Select an option')</option>
                                        @foreach ($geteways as $item)
                                            <option value="{{ $item->id }}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Amount') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="amount">
                                </div>
                            </div>


                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Mobile') <span
                                                class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mobile">
                                </div>
                            </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Number_of_remittance_voucher')
                                            <span
                                                    class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="voucher_number">
                                    </div>
                                </div>



                        </div>

                        <div class="row mt-4">
                            <div class="form-group col-xl-6">
                                <label class="font-weight-bold">@lang('Image')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview logoPicPrev logoPrev">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" id="profilePicUpload"
                                                   name="image">
                                            <label for="profilePicUpload"
                                                   class="bg--primary">@lang('Select Image') </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold"> @lang('Driver')</label>
                                    <select name="driver_id" class="select2-basic" required>
                                        <option value="">@lang('Select an option')</option>
                                        @foreach ($drivers as $item)
                                            <option value="{{ $item->id }}">{{$item->fullname}}</option>
                                        @endforeach
                                    </select>
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
