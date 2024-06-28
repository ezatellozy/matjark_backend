<div class="row">
    <!-- users edit media object start -->
    <div class="col-12">
        <div class="media mb-2">
            @if (isset($driver) && $driver->image)
            <img src="{{ $driver->avatar }}" alt="{{ $driver->fullname }}" class="user-avatar users-avatar-shadow rounded mr-2 my-25 cursor-pointer image-preview" height="90" width="90" />
            @else
            <img src="{{ asset('dashboardAssets/images/backgrounds/placeholder_image.png') }}" alt="users avatar" class="user-avatar users-avatar-shadow rounded mr-2 my-25 cursor-pointer image-preview" height="90" width="90" />
            @endif
            <div class="media-body mt-50">
                <h4>{{ isset($driver) ? $driver->fullname : trans('dashboard.general.image') }}</h4>
                <div class="col-12 d-flex mt-1 px-0">
                    <label class="btn btn-primary mr-75 mb-0" for="change-picture">
                        <span class="d-none d-sm-block">Change</span>
                        <input class="form-control" type="file" id="change-picture" name="image" hidden accept="image/png, image/jpeg, image/jpg" onchange="readUrl(this)" />
                        <span class="d-block d-sm-none">
                            <i class="mr-0" data-feather="edit"></i>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <!-- users edit media object ends -->

    <div class="col-md-6 col-12">
        <div class="form-group">
            <label for="full-name-column">{{ trans('dashboard.user.fullname') }} <span class="text-danger">*</span></label>
            {!! Form::text('fullname', null , ['class' => 'form-control','id' => "full-name-column" , 'placeholder' => trans('dashboard.user.fullname')]) !!}
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="form-group">
            <label for="phone-column">{{ trans('dashboard.general.phone') }} <span class="text-danger">*</span></label>
            {!! Form::text('phone', null , ['class' => 'form-control','id' => "phone-column" , 'placeholder' => trans('dashboard.general.phone')]) !!}
        </div>
    </div>

    <div class="col-md-6 col-12">
        <div class="form-group">
            <div class="d-flex justify-content-between">
                <label for="login-password">{!! trans('dashboard.general.password') !!}</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                {!! Form::password('password', ['class' => 'form-control form-control-merge', "id" => "login-password",'placeholder' => "&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" ,"aria-describedby" =>
                "login-password", "tabindex" => "2"]) !!}
                <div class="input-group-append">
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="form-group">
            <div class="d-flex justify-content-between">
                <label for="login-password_confirmation">{!! trans('dashboard.general.password_confirmation') !!}</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                {!! Form::password('password_confirmation', ['class' => 'form-control form-control-merge', "id" => "login-password_confirmation",'placeholder' => "&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                ,"aria-describedby" => "login-password_confirmation", "tabindex" => "2"]) !!}

                <div class="input-group-append">
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-6 col-12">
        <div class="form-group">
            <label for="email-id-column">{{ trans('dashboard.general.email') }}</label>
            {!! Form::email('email', null, ['class' => 'form-control' ,"id" => "email-id-column", 'placeholder' => trans('dashboard.general.email')]) !!}
        </div>
    </div>

    <div class="col-md-6 col-12">
        <div class="form-group">
            <label>{{ trans('dashboard.country.country') }}
                <span class="text-danger">*</span>
            </label>
            {!! Form::select('country_id',$countries ,null ,['class' => 'select2 form-control', 'placeholder' => trans('dashboard.country.country')]) !!}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label class="">{{ trans('dashboard.car.car') }} <span class="text-danger">*</span></label>
            <select class="select2 form-control car_select" name="car_id" style="width:100%;" data-placeholder="{{ trans('dashboard.car.car') }}">
                <option></option>
                @foreach ($cars as $car)
                <option value="{{ $car->id }}" {{ isset($driver) && @$driver->car->id == $car->id ? 'selected' : '' }} data-image="{{ $car->car_front_image }}">{{ @$car->carModel->name }} - {{ @$car->carType->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group ">
            <label class="form-label" for="modern-identity_number_image">
                {{ trans('dashboard.driver.identity_number_image') }}
            </label>
            {{-- <div class="col-md-10"> --}}
            <div class="custom-file">
                <input type="file" name="identity_number_image" class="custom-file-input" id="identity_number_image_image" onchange="readUrl(this,'identity_number_image_preview')">
                <label class="custom-file-label" for="identity_number_image_image">Choose file</label>
            </div>
        </div>
        <div class="col-md-1">
            @if (isset($driver))
            <img src="{{ $driver->identity_number_image }}" class="img-thumbnail identity_number_image_preview" style="width: 100%; height: 100px;">
            @else
            <img src="{{ asset('dashboardAssets/images/backgrounds/placeholder_image.png') }}" class="img-thumbnail identity_number_image_preview" style="width: 100%; height: 100px;">
            @endif
        </div>
        {{-- </div> --}}

    </div>

    <div class="form-group col-12">
        <label class="form-label" for="modern-active_state">
            {{ trans('dashboard.user.active_state') }}
        </label>
        <div class="demo-inline-spacing">
            <div class="custom-control custom-control-success custom-radio col-md-6">
                {!! Form::radio('is_active', 1, !isset($driver) || (isset($driver) && $driver->is_active) ? 'checked' : null , ['class' => 'custom-control-input' , 'id' => 'is_active']) !!}
                <label class="custom-control-label" for="is_active">{!! trans('dashboard.user.active') !!}</label>
            </div>
            <div class="custom-control custom-control-danger custom-radio">
                {!! Form::radio('is_active', 0, isset($driver) && !$driver->is_active ? 'checked' : null , ['class' => 'custom-control-input' , 'id' => 'is_deactive']) !!}
                <label class="custom-control-label" for="is_deactive">{!! trans('dashboard.user.not_active') !!}</label>
            </div>

        </div>
    </div>

    <div class="form-group col-12">
        <label class="form-label" for="modern-ban_state">
            {{ trans('dashboard.user.ban_state') }}
        </label>
        <div class="demo-inline-spacing">
            <div class="custom-control custom-control-success custom-radio col-md-6">
                {!! Form::radio('is_ban', 1, !isset($driver) || (isset($driver) && $driver->is_ban) ? 'checked' : null , ['class' => 'custom-control-input' , 'id' => 'is_ban']) !!}
                <label class="custom-control-label" for="is_ban">{!! trans('dashboard.user.ban') !!}</label>
            </div>

            <div class="custom-control custom-control-danger custom-radio">
                {!! Form::radio('is_ban', 0, isset($driver) && !$driver->is_ban ? 'checked' : null , ['class' => 'custom-control-input' , 'id' => 'is_not_ban']) !!}
                <label class="custom-control-label" for="is_not_ban">{!! trans('dashboard.user.not_ban') !!}</label>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label for="ban_reason-column">{{ trans('dashboard.user.ban_reason') }}</label>
            {!! Form::textarea('ban_reason', null, ['class' => 'form-control' ,"id" => "ban_reason-column", 'placeholder' => trans('dashboard.user.ban_reason')]) !!}
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">{{ $btnSubmit }}</button>
    </div>
</div>

@section('vendor_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/forms/select/select2.min.css">
@endsection

@section('vendor_scripts')
<script src="{{ asset('dashboardAssets') }}/vendors/js/forms/select/select2.full.min.js"></script>
@endsection

@section('page_scripts')
<script>
    $(function() {
        $(".car_select").select2({
            width: 'resolve',
            allowClear: true,
            templateResult: formatState,
            templateSelection: formatState,
            placeholder: "{{ trans('dashboard.car.car ') }}"
        });

        function formatState(opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
            if (!optimage) {
                return opt.text.toUpperCase();
            } else {
                var $opt = $(
                    '<span><img src="' + optimage + '" class="rounded-circle" style="width:30px; height:30px; margin-left: 5px;" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        };
    })
</script>
@endsection
