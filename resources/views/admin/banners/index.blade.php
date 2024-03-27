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
                                    <th>@lang('Description')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Publish Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($banners as $item)
                                <tr>
                                    <td data-label="@lang('Description')">
                                        {{ __($item->description) }}
                                    </td>
                                    <td data-label="@lang('Image')">
                                        <div class="image-upload">
                                            <div class="thumb">
                                                <div class="avatar-preview">
                                                    <div class="profilePicPreview logoPicPrev logoPrev" style="height:150px;background-image: url({{ getImage(imagePath()['banner']['path'].'/'.$item->image, '?'.time()) }})">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </td>
                                    <td data-label="@lang('Publish Date')">
                                        {{ __($item->publish_date) }}
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.banners.edit', $item->id) }}" class="icon-btn ml-1"><i class="la la-pen"></i></a>
                                        <button type="button"
                                                class="icon-btn btn--danger ml-1 removeBtn"
                                                data-toggle="modal" data-target="#removeModal"
                                                data-id="{{ $item->id }}"
                                                data-original-title="@lang('Delete')">
                                            <i class="las la-trash"></i>
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
                    {{ paginateLinks($banners) }}
                </div>
            </div>
        </div>
    </div>
    {{-- remove METHOD MODAL --}}
    <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Delete Seat Layouts')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.banners.delete')}}" method="POST">
                    @csrf
                    @method("DELETE")
                    <input type="text" name="id" hidden="true">
                    <div class="modal-body">
                        <strong>@lang('Are you sure, you want to delete this?')</strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small addBtn"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
@endpush
@push('script')

    <script>
        (function ($) {
            "use strict";
            $('.removeBtn').on('click', function () {
                var modal = $('#removeModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);

    </script>

@endpush

