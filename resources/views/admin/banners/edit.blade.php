@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Information of Banner') </h5>

                <form action="{{ route('admin.banner.update', $banner->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="form-control-label font-weight-bold"> @lang('Description')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Description')" value="{{ $banner->description }}" name="description" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Link')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Link')" value="{{ $banner->link }}" name="link" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Publish Date')</label>
                                <input type="date" class="form-control" name="publish_date" value="{{ $banner->publish_date }}"  placeholder="@lang('Enter Publish Date')" required>
                            </div>
                        </div>
                        <div class="form-group col-xl-4">
                                <label class="font-weight-bold">@lang('Image')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                        <div class="profilePicPreview logoPicPrev logoPrev" style="height:150px;background-image: url({{ getImage(imagePath()['banner']['path'].'/'.$banner->image, '?'.time()) }})">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" id="profilePicUpload" accept=".png, .jpg, .jpeg" name="image">
                                            <label for="profilePicUpload" class="bg--primary">@lang('Select Image') </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')
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
    <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small addBtn"><i class="la la-fw la-backward"></i>@lang('Go Back')</a>
@endpush
@push('style')
    <style>
        .input-group > .select2-container--default {
            width: auto !important;
            flex: 1 1 auto !important;
        }

        .input-group > .select2-container--default .select2-selection--single {
            height: 100% !important;
            line-height: inherit !important;
        }
    </style>
@endpush

